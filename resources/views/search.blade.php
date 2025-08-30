@extends('layouts.public')

@section('title', 'Pesquisar - BiblioFlash')

@push('page-css')
@vite('resources/css/pages/search.css')
@endpush

@section('content')

<div class="search-container">
    <div class="search-header">
        <h1>Resultados da Busca</h1>
        <div class="search-query">
            <p>Você pesquisou por: <strong>{{ $query }}</strong></p>
        </div>
    </div>

    @if(isset($results) && count($results) > 0)
    <div class="resultados-busca">
        @foreach($results as $livro)
        <div class="livro-busca">
            <div class="livro-busca-img-info">
                @if($livro->foto)
                <img src="{{ asset('/imagens/livros/' . $livro->foto) }}" alt="Capa do livro {{ $livro->titulo }}">
                @else
                <div class="livro-placeholder">
                    <i class="fas fa-book"></i>
                    <span>Sem Imagem</span>
                </div>
                @endif
                <div class="livro-busca-info">
                    <h3>{{ $livro->titulo }}</h3>
                    <div class="livro-busca-dados">
                        <span><strong>Autor:</strong> {{ $livro->autor }}</span>
                        <span><strong>Editora:</strong> {{ $livro->editora }}</span>
                        <span><strong>Ano:</strong> {{ $livro->ano_publicacao }}</span>
                        <span><strong>ISBN:</strong> {{ $livro->isbn }}</span>
                    </div>
                    <div class="livro-busca-actions">
                        @auth
                            @if($livro->quantidade_disponivel > 0)
                                <button class="btn btn-primary btn-reservar" data-book-id="{{ $livro->id }}">
                                    <i class="fas fa-bookmark"></i> Reservar Livro
                                </button>
                            @else
                                <button class="btn btn-secondary" disabled>
                                    <i class="fas fa-times"></i> Indisponível
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">
                                <i class="fas fa-sign-in-alt"></i> Faça login para reservar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @if($livro->descricao)
            <p class="livro-busca-desc">{{ $livro->descricao }}</p>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <div class="resultados-busca">
        <div class="no-results">
            <i class="fas fa-search"></i>
            <p>Nenhum resultado encontrado para sua busca.</p>
            <p>Tente usar palavras-chave diferentes ou verifique a ortografia.</p>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função para reservar livro
    function reserveBook(bookId, button) {
        // Desabilita o botão e mostra loading
        button.disabled = true;
        button.classList.add('loading');
        
        // Simula uma requisição AJAX
        fetch('/books/reserve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                book_id: bookId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Sucesso - atualiza o botão
                button.innerHTML = '<i class="fas fa-check"></i> Reservado';
                button.classList.remove('btn-reserve');
                button.classList.add('btn-success');
                
                // Mostra notificação de sucesso
                showNotification('Livro reservado com sucesso!', 'success');
            } else {
                throw new Error(data.message || 'Erro ao reservar livro');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            
            // Reabilita o botão
            button.disabled = false;
            button.classList.remove('loading');
            
            // Mostra notificação de erro
            showNotification(error.message || 'Erro ao reservar livro. Tente novamente.', 'error');
        });
    }
    
    // Função para mostrar notificações
    function showNotification(message, type) {
        // Remove notificações existentes
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());
        
        // Cria nova notificação
        const notification = document.createElement('div');
        notification.className = `notification alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Adiciona ao body
        document.body.appendChild(notification);
        
        // Remove automaticamente após 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Adiciona event listeners aos botões de reserva
    document.querySelectorAll('.btn-reserve').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const bookId = this.getAttribute('data-book-id');
            if (bookId) {
                reserveBook(bookId, this);
            }
        });
    });
});
</script>
@endpush