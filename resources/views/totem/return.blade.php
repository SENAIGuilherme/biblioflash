@extends('layouts.totem')

@php
$totemLayout = true;
@endphp

@section('title', 'Devolução de Livros - Tótem BiblioFlash')

@section('head')
@parent
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('totem-content')
<!-- Overlay para conectar leitor RFID -->
<div id="conectar-leitor-container" class="conectar-leitor-overlay">
    <button onclick="conectarArduino()" id="btnConectarLeitor" class="btn-conectar-leitor">
        🔌 Toque para ativar o leitor RFID
    </button>
    <div class="conectar-leitor-text">
        Para sua segurança, é necessário ativar o leitor manualmente.<br>
        Toque no botão acima para iniciar.
    </div>
</div>

<div class="totem-acao-card">
    <div class="totem-logo">
        <img src="{{ asset('biblio-flash/logo-of.png') }}" alt="Logo Biblioteca">
    </div>

    <h2>Devolva seu(s) Livro(s)!</h2>

    <div class="rfid-section">
        <p>Aproxime seu livro do leitor RFID para devolver.</p>
        <div id="rfid-alert"></div>
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
        📚 Devolver
    </button>
    <div>
        <a href="{{ route('totem.home') }}" class="btn btn-link">
            🏠 Voltar
        </a>
    </div>
</div>
@endsection

@section('totem-scripts')
<script>
    const bookList = document.getElementById("bookList");
    const totalDisplay = document.getElementById("total");
    const btnProsseguir = document.getElementById("btnProsseguir");

    let portaSerial = null;
    let leitor = null;
    let uidAtual = "";
    let livrosParaDevolucao = [];

    // Função para adicionar livro por RFID
    function addBookByRFID(isbn, name = "") {
        if (!isbn) return;

        // Verifica se o livro já foi adicionado
        const exists = livrosParaDevolucao.some(livro => livro.isbn === isbn);
        if (exists) {
            mostrarAlerta("Livro já adicionado.", "#e74c3c");
            return;
        }

        // Se não tem nome e é o UID atual, mostra erro
        if (!name && uidAtual === isbn && name !== undefined) {
            mostrarAlerta("Livro não encontrado.", "#e74c3c");
            return;
        }

        // Se não tem nome e não é o UID atual, sai
        if (!name && uidAtual !== isbn) return;

        // Adiciona o livro à lista
        const livro = {
            isbn: isbn,
            titulo: name || 'Título não disponível'
        };

        livrosParaDevolucao.push(livro);
        atualizarListaLivros();
        mostrarAlerta(`Livro "${livro.titulo}" adicionado para devolução!`, "#00e676");
    }

    // Função para remover livro
    function removeBook(btn) {
        const card = btn.closest(".book-card");
        const isbn = card.dataset.isbn;

        // Remove da lista
        livrosParaDevolucao = livrosParaDevolucao.filter(livro => livro.isbn !== isbn);

        // Remove do DOM
        card.remove();
        atualizarListaLivros();
        mostrarAlerta("Livro removido da lista", "#f39c12");
    }

    // Função para limpar a tabela
    function clearTable() {
        livrosParaDevolucao = [];
        atualizarListaLivros();
    }

    // Função para atualizar o total e a interface
    function atualizarListaLivros() {
        bookList.innerHTML = '';

        if (livrosParaDevolucao.length === 0) {
            bookList.innerHTML = '<div class="book-list-empty">Nenhum livro adicionado</div>';
            totalDisplay.textContent = 0;
            btnProsseguir.disabled = true;
            return;
        }

        livrosParaDevolucao.forEach(livro => {
            const card = document.createElement("div");
            card.className = "book-card";
            card.dataset.isbn = livro.isbn;
            card.innerHTML = `
                    <div class="book-info">
                        <span class="book-title">${livro.titulo}</span>
                        <span class="book-isbn">ISBN: ${livro.isbn}</span>
                    </div>
                    <span class="remove-btn" onclick="removeBook(this)" title="Remover">×</span>
                `;
            bookList.appendChild(card);
        });

        totalDisplay.textContent = livrosParaDevolucao.length;
        btnProsseguir.disabled = livrosParaDevolucao.length === 0;
    }

    // Função para conectar ao Arduino via Serial
    async function conectarArduino() {
        try {
            if (!navigator.serial) {
                throw new Error('Web Serial API não suportada neste navegador');
            }

            portaSerial = await navigator.serial.requestPort();
            await portaSerial.open({
                baudRate: 9600
            });
            leitor = portaSerial.readable.getReader();

            lerUIDs();
            mostrarAlerta('Leitor conectado! Aproxime o livro para ler o RFID.', '#1e90ff');
            document.getElementById('conectar-leitor-container').style.display = 'none';
        } catch (err) {
            console.error('Erro ao conectar:', err);
            mostrarAlerta('Erro ao conectar: ' + err.message, '#e74c3c');

            // Fallback: esconder overlay e permitir uso manual
            setTimeout(() => {
                document.getElementById('conectar-leitor-container').style.display = 'none';
                mostrarAlerta('Modo manual ativado. Use a API para adicionar livros.', '#f39c12');
            }, 3000);
        }
    }

    // Função para ler UIDs continuamente
    async function lerUIDs() {
        try {
            while (true) {
                const {
                    value,
                    done
                } = await leitor.read();
                if (done) break;

                const texto = new TextDecoder().decode(value).trim();
                if (texto && texto !== uidAtual) {
                    uidAtual = texto;
                    buscarELancarLivroRFID(uidAtual);
                }
            }
        } catch (error) {
            console.error('Erro na leitura RFID:', error);
            mostrarAlerta('Erro na leitura RFID: ' + error.message, '#e74c3c');
        }
    }

    // Função para buscar livro por RFID
    function buscarELancarLivroRFID(isbn) {
        fetch(`/api/livro/${isbn}`)
            .then(r => r.json())
            .then((data) => {
                if (data && data.titulo) {
                    addBookByRFID(isbn, data.titulo);
                } else {
                    addBookByRFID(isbn, "");
                }
            })
            .catch(() => addBookByRFID(isbn, ""));
    }

    // Função para mostrar alertas
    function mostrarAlerta(msg, cor) {
        const alertDiv = document.getElementById('rfid-alert');
        alertDiv.innerText = msg;
        alertDiv.style.color = cor;
        alertDiv.style.display = 'block';
        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 3000);
    }

    // Event listener para o botão de devolver
    btnProsseguir.addEventListener('click', async function() {
        if (livrosParaDevolucao.length === 0) {
            alert('Adicione pelo menos um livro para devolução!');
            return;
        }

        // Confirma a devolução
        if (!confirm(`Confirma a devolução de ${livrosParaDevolucao.length} livro(s)?`)) {
            return;
        }

        try {
            // Desabilita o botão durante o processamento
            btnProsseguir.disabled = true;
            btnProsseguir.innerHTML = '⏳ Processando...';

            // Envia para a API
            const response = await fetch('/api/totem/return-books', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    livros: livrosParaDevolucao.map(l => l.isbn)
                })
            });

            const data = await response.json();

            if (data.status === 'success') {
                mostrarAlerta('Devolução realizada com sucesso!', '#00e676');

                // Limpa a lista após sucesso
                setTimeout(() => {
                    clearTable();
                }, 2000);
            } else {
                throw new Error(data.message || 'Erro na devolução');
            }
        } catch (error) {
            console.error('Erro na devolução:', error);
            mostrarAlerta('Erro na devolução: ' + error.message, '#e74c3c');
        } finally {
            // Reabilita o botão
            btnProsseguir.innerHTML = '📚 Devolver';
            atualizarListaLivros(); // Isso vai reabilitar o botão se houver livros
        }
    });

    // Expor função globalmente para compatibilidade
    window.addBookByRFID = addBookByRFID;

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        atualizarListaLivros();
    });

    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.location.href = '{{ route('
            totem.home ') }}';
        } else if (e.key === 'Enter' && !btnProsseguir.disabled) {
            btnProsseguir.click();
        }
    });
</script>
@endsection