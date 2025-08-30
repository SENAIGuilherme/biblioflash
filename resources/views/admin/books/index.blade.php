@extends('layouts.admin')

@section('title', 'Gerenciar Livros')

@push('page-css')
@vite('resources/css/pages/books-management.css')
@endpush

@section('content')
<div class="books-management-container">
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <h1 class="page-title">
                    <i class="fas fa-book"></i>
                    Gerenciar Livros
                </h1>
                <p class="page-subtitle">Visualize, edite e gerencie todos os livros cadastrados no sistema</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('admin.books.register') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Novo Livro
                </a>
                <a href="{{ route('admin.books.panel') }}" class="btn btn-secondary">
                    <i class="fas fa-microchip"></i>
                    Painel
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <form method="GET" action="{{ route('admin.books.index') }}" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="search">Buscar</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" 
                               placeholder="Título, autor, ISBN..." class="form-control">
                    </div>
                    
                    <div class="filter-group">
                        <label for="category">Categoria</label>
                        <select id="category" name="category" class="form-control">
                            <option value="">Todas as categorias</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" 
                                        {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="sort">Ordenar por</label>
                        <select id="sort" name="sort" class="form-control">
                            <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Mais recentes</option>
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Título</option>
                            <option value="author" {{ request('sort') == 'author' ? 'selected' : '' }}>Autor</option>
                            <option value="year" {{ request('sort') == 'year' ? 'selected' : '' }}>Ano</option>
                        </select>
                    </div>
                </div>
                
                <div class="filters-actions">
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-search"></i>
                        Filtrar
                    </button>
                    <a href="{{ route('admin.books.index') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i>
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <!-- Books Table -->
        <div class="table-section">
            <div class="table-header">
                <h3 class="table-title">
                    <i class="fas fa-list"></i>
                    Lista de Livros
                    <span class="table-count">({{ $books->count() ?? 0 }} livros)</span>
                </h3>
            </div>
            
            <div class="table-container">
                @if($books && $books->count() > 0)
                    <table class="books-table">
                        <thead>
                            <tr>
                                <th>Capa</th>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Categoria</th>
                                <th>ISBN/RFID</th>
                                <th>Quantidade</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($books as $book)
                                <tr class="book-row">
                                    <td class="book-cover-cell">
                                        @if($book->capa)
                                            <img src="{{ asset('storage/' . $book->capa) }}" 
                                                 alt="{{ $book->titulo }}" class="book-cover-thumb">
                                        @else
                                            <div class="book-cover-placeholder">
                                                <i class="fas fa-book"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="book-title-cell">
                                        <div class="book-title">{{ $book->titulo }}</div>
                                        <div class="book-meta">ID: {{ $book->id }}</div>
                                    </td>
                                    <td class="book-author-cell">{{ $book->autor }}</td>
                                    <td class="book-category-cell">
                                        <span class="category-badge">{{ $book->category->nome ?? 'Sem categoria' }}</span>
                                    </td>
                                    <td class="book-isbn-cell">
                                        @if($book->isbn)
                                            <div class="isbn-info">
                                                <span class="isbn-label">ISBN:</span>
                                                <span class="isbn-value">{{ $book->isbn }}</span>
                                            </div>
                                        @else
                                            <span class="no-isbn">Não informado</span>
                                        @endif
                                    </td>
                                    <td class="book-quantity-cell">
                                        <div class="quantity-info">
                                            <span class="quantity-available">{{ $book->quantidade_disponivel ?? 0 }}</span>
                                            <span class="quantity-separator">/</span>
                                            <span class="quantity-total">{{ $book->quantidade_total ?? 0 }}</span>
                                        </div>
                                    </td>
                                    <td class="book-status-cell">
                                        <span class="status-badge {{ $book->ativo ? 'status-active' : 'status-inactive' }}">
                                            {{ $book->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="book-actions-cell">
                                        <div class="actions-dropdown">
                                            <button class="actions-toggle" onclick="toggleActions({{ $book->id }})">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="actions-menu" id="actions-{{ $book->id }}">
                                                <a href="{{ route('admin.books.show', $book) }}" class="action-item">
                                                    <i class="fas fa-eye"></i>
                                                    Visualizar
                                                </a>
                                                <a href="{{ route('admin.books.edit', $book) }}" class="action-item">
                                                    <i class="fas fa-edit"></i>
                                                    Editar
                                                </a>
                                                <button onclick="confirmDelete({{ $book->id }}, '{{ $book->titulo }}')" 
                                                        class="action-item action-delete">
                                                    <i class="fas fa-trash"></i>
                                                    Excluir
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3 class="empty-title">Nenhum livro encontrado</h3>
                        <p class="empty-message">
                            @if(request()->hasAny(['search', 'category', 'status']))
                                Não encontramos livros com os filtros aplicados. Tente ajustar os critérios de busca.
                            @else
                                Ainda não há livros cadastrados no sistema. Comece adicionando o primeiro livro!
                            @endif
                        </p>
                        <a href="{{ route('admin.books.register') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Cadastrar Primeiro Livro
                        </a>
                    </div>
                @endif
            </div>
            

        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-exclamation-triangle"></i>
                Confirmar Exclusão
            </h3>
            <button class="modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Tem certeza que deseja excluir o livro <strong id="bookToDelete"></strong>?</p>
            <p class="warning-text">
                <i class="fas fa-warning"></i>
                Esta ação não pode ser desfeita.
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Excluir Livro
                </button>
            </form>
        </div>
    </div>
</div>

@push('page-js')
<script>
// Actions dropdown
function toggleActions(bookId) {
    const menu = document.getElementById(`actions-${bookId}`);
    const allMenus = document.querySelectorAll('.actions-menu');
    
    // Close all other menus
    allMenus.forEach(m => {
        if (m !== menu) m.classList.remove('show');
    });
    
    menu.classList.toggle('show');
}

// Delete confirmation
function confirmDelete(bookId, bookTitle) {
    document.getElementById('bookToDelete').textContent = bookTitle;
    document.getElementById('deleteForm').action = `/admin/books/${bookId}`;
    document.getElementById('deleteModal').classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});
</script>
@endpush
@endsection