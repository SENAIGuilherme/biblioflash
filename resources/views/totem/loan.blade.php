@extends('layouts.totem')

@php
$totemLayout = true;
@endphp

@section('title', 'Empréstimo de Livros - Tótem BiblioFlash')

@section('head')
@parent
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('totem-content')
<div class="totem-acao-card">
    <div class="totem-logo">
        <img src="{{ asset('biblio-flash/logo-of.png') }}" alt="Logo Biblioteca">
    </div>

    @auth
    <h2>Bem-vindo(a) {{ Auth::user()->name ?? Auth::user()->nome }}!</h2>
    @else
    <script>
        window.location.href = '{{ route('
        totem.login ') }}';
    </script>
    @endauth

    <h2>Apresente seu(s) Livro(s)!</h2>

    <div class="rfid-section">
        <p>Aproxime seu livro do leitor RFID e clique no botão abaixo:</p>
        <div id="rfid-alert"></div>
        <button onclick="lerLivroRFID()" id="btnLerLivro" class="btn-ler-livro">
            📡 Ler Livro
        </button>
        <div id="uidLivro"></div>
    </div>

    <div class="book-list" id="bookList">
        <div class="book-list-empty">Nenhum livro adicionado</div>
    </div>
</div>

<div class="footer">
    <div>
        <div>Quantidade</div>
        <div>
            <span id="total">0</span>
        </div>
    </div>
    <button class="btn-prosseguir" id="btnProsseguir" disabled>
        📚 Prosseguir
    </button>
    <div>
        <a href="{{ route('totem.home') }}" class="btn btn-link">
            🏠 Voltar
        </a>
    </div>
</div>
@endsection

@section('totem-scripts')
<script src="{{ asset('assets/totem-verifica-reserva.js') }}"></script>
<script>
    const bookList = document.getElementById("bookList");
    const totalDisplay = document.getElementById("total");
    const btnProsseguir = document.getElementById("btnProsseguir");
    const btnLerLivro = document.getElementById("btnLerLivro");

    let livrosAdicionados = [];

    // Nova função: leitura do UID via Ethernet (HTTP para Arduino)
    async function lerLivroRFID() {
        const statusEl = document.getElementById('rfid-alert');
        const uidEl = document.getElementById('uidLivro');

        // Desabilita o botão durante a leitura
        btnLerLivro.disabled = true;
        btnLerLivro.textContent = '⏳ Lendo...';

        statusEl.innerText = '⏳ Aguardando leitura do livro...';
        statusEl.style.color = '#FFD700';
        statusEl.style.display = 'block';
        uidEl.textContent = '';
        uidEl.style.display = 'none';

        try {
            const response = await fetch('http://192.168.1.150', {
                method: 'GET',
                cache: 'no-cache',
                timeout: 10000
            });

            if (!response.ok) throw new Error("Falha na resposta do Arduino");

            const texto = await response.text();
            const resultado = texto.trim();

            if (resultado && resultado !== 'Nenhum cartão lido js') {
                statusEl.innerText = '✅ Livro detectado!';
                statusEl.style.color = '#00e676';
                uidEl.textContent = resultado.toUpperCase();
                uidEl.style.display = 'block';
                mostrarAlerta('UID lido: ' + resultado.toUpperCase(), '#FFD700');
                await adicionarLivroPorRFID(resultado);
            } else {
                statusEl.innerText = '⚠️ Nenhum livro lido. Tente novamente.';
                statusEl.style.color = '#e74c3c';
                uidEl.textContent = '';
                uidEl.style.display = 'none';
            }
        } catch (erro) {
            console.error('Erro ao ler RFID:', erro);
            statusEl.innerText = '❌ Erro ao conectar ao leitor: ' + erro.message;
            statusEl.style.color = '#e74c3c';
            uidEl.textContent = '';
            uidEl.style.display = 'none';
        } finally {
            // Reabilita o botão
            btnLerLivro.disabled = false;
            btnLerLivro.textContent = '📡 Ler Livro';

            setTimeout(() => {
                statusEl.style.display = 'none';
                uidEl.style.display = 'none';
            }, 3000);
        }
    }

    // Feedback visual
    function mostrarAlerta(msg, cor) {
        const alertDiv = document.getElementById('rfid-alert');
        alertDiv.innerText = msg;
        alertDiv.style.color = cor;
        alertDiv.style.display = 'block';
        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 3000);
    }

    // Função para adicionar livro via RFID
    async function adicionarLivroPorRFID(rfid) {
        try {
            const response = await fetch('/api/totem/add-book', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    rfid
                })
            });

            const data = await response.json();

            if (data.status === 'success' || data.status === 'adicionado') {
                mostrarAlerta('Livro adicionado!', '#00e676');

                // Adiciona à lista local
                if (data.livro && !livrosAdicionados.find(l => l.rfid === data.livro.rfid)) {
                    livrosAdicionados.push(data.livro);
                    atualizarListaLivros();
                }
            } else {
                mostrarAlerta(data.message || data.mensagem || 'Livro não encontrado', '#e74c3c');
            }
        } catch (error) {
            console.error('Erro ao adicionar livro:', error);
            mostrarAlerta('Erro ao processar livro', '#e74c3c');
        }
    }

    // Função para atualizar a lista de livros na tela
    function atualizarListaLivros() {
        bookList.innerHTML = '';

        if (!livrosAdicionados.length) {
            bookList.innerHTML = '<div class="book-list-empty">Nenhum livro adicionado</div>';
            totalDisplay.textContent = 0;
            btnProsseguir.disabled = true;
            return;
        }

        livrosAdicionados.forEach((livro, index) => {
            const card = document.createElement('div');
            card.className = 'book-card';
            card.dataset.rfid = livro.rfid;
            card.innerHTML = `
                    <div class="book-info">
                        <span class="book-title">${livro.titulo || 'Título não disponível'}</span>
                        <span class="book-rfid">RFID: ${livro.rfid}</span>
                    </div>
                    <button class="remove-btn" onclick="removerLivro('${livro.rfid}')" title="Remover livro">
                        ×
                    </button>
                `;
            bookList.appendChild(card);
        });

        totalDisplay.textContent = livrosAdicionados.length;
        btnProsseguir.disabled = livrosAdicionados.length === 0;
    }

    // Função para remover livro da lista
    function removerLivro(rfid) {
        livrosAdicionados = livrosAdicionados.filter(livro => livro.rfid !== rfid);
        atualizarListaLivros();
        mostrarAlerta('Livro removido da lista', '#f39c12');
    }

    // Ao clicar em Prosseguir, redireciona para a página de verificação
    btnProsseguir.addEventListener('click', function() {
        if (!livrosAdicionados.length) {
            alert('Adicione pelo menos um livro!');
            return;
        }

        // Monta a query string com cliente_id e livros
        const clienteId = '{{ Auth::id() }}';
        if (!clienteId) {
            alert('Cliente não identificado. Faça login novamente.');
            window.location.href = '{{ route('
            totem.login ') }}';
            return;
        }

        // Prepara os dados dos livros para a URL
        const livrosData = livrosAdicionados.map(livro => {
            return `${livro.id || ''},${livro.rfid},${encodeURIComponent(livro.titulo || '')}`;
        }).join(';');

        // Redireciona para a página de verificação
        const url = `{{ route('totem.loan.verification') }}?cliente_id=${clienteId}&livros=${livrosData}`;
        window.location.href = url;
    });

    // Garante que o cliente_id está disponível
    (function garantirClienteLogado() {
        const clienteId = '{{ Auth::id() }}';
        if (!clienteId) {
            alert('ATENÇÃO: Não foi possível identificar o cliente logado. Faça login novamente.');
            window.location.href = '{{ route('
            totem.login ') }}';
        }
    })();

    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            lerLivroRFID();
        } else if (e.key === 'Escape') {
            window.location.href = '{{ route('
            totem.home ') }}';
        }
    });

    // Auto-focus no botão de ler livro
    document.addEventListener('DOMContentLoaded', function() {
        btnLerLivro.focus();
    });
</script>
@endsection