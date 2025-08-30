@extends('layouts.admin')
@section('title', 'Visualizar Livro')

@push('layout-css')
    @vite(['resources/css/pages/book-show.css'])
@endpush

@section('content')
<div class="book-show-container">
    <div class="book-show-content">
        <!-- Header -->
        <div class="book-show-header">
            <h1 class="book-show-title">{{ $book->titulo }}</h1>
            <p class="book-show-subtitle">Detalhes completos do livro</p>
        </div>

        <!-- Main Content Grid -->
        <div class="book-show-grid">
            <!-- Book Cover Section -->
            <div class="book-cover-section">
                @if($book->foto_capa && file_exists(public_path('storage/' . $book->foto_capa)))
                    <img src="{{ asset('storage/' . $book->foto_capa) }}" alt="Capa do livro {{ $book->titulo }}" class="book-cover-display">
                @else
                    <div class="book-cover-placeholder">
                        <i class="fas fa-book"></i>
                        <span>Sem capa</span>
                    </div>
                @endif
            </div>

            <!-- Book Details Section -->
            <div class="book-details-section">
                <div class="detail-group">
                    <label class="detail-label">Título</label>
                    <div class="detail-value large">{{ $book->titulo }}</div>
                </div>

                <div class="detail-group">
                    <label class="detail-label">Autor</label>
                    <div class="detail-value">{{ $book->autor }}</div>
                </div>

                <div class="detail-group">
                    <label class="detail-label">Categoria</label>
                    <div class="detail-value category">{{ $book->category->nome ?? 'Não categorizado' }}</div>
                </div>

                @if($book->editora)
                <div class="detail-group">
                    <label class="detail-label">Editora</label>
                    <div class="detail-value">{{ $book->editora }}</div>
                </div>
                @endif

                @if($book->ano_publicacao)
                <div class="detail-group">
                    <label class="detail-label">Ano de Publicação</label>
                    <div class="detail-value">{{ $book->ano_publicacao }}</div>
                </div>
                @endif

                @if($book->paginas)
                <div class="detail-group">
                    <label class="detail-label">Páginas</label>
                    <div class="detail-value">{{ number_format($book->paginas, 0, ',', '.') }} páginas</div>
                </div>
                @endif

                <div class="detail-group">
                    <label class="detail-label">ISBN</label>
                    <div class="detail-value">{{ $book->isbn }}</div>
                </div>

                @if($book->localizacao)
                <div class="detail-group">
                    <label class="detail-label">Localização</label>
                    <div class="detail-value">{{ $book->localizacao }}</div>
                </div>
                @endif

                <div class="detail-group">
                    <label class="detail-label">Status</label>
                    <div class="detail-value status {{ $book->status == 'disponivel' ? 'status-available' : ($book->status == 'indisponivel' ? 'status-unavailable' : 'status-maintenance') }}">
                        @switch($book->status)
                            @case('disponivel')
                                <i class="fas fa-check-circle"></i> Disponível
                                @break
                            @case('indisponivel')
                                <i class="fas fa-times-circle"></i> Indisponível
                                @break
                            @case('manutencao')
                                <i class="fas fa-tools"></i> Em Manutenção
                                @break
                            @default
                                <i class="fas fa-question-circle"></i> {{ ucfirst($book->status) }}
                        @endswitch
                    </div>
                </div>

                @if($book->sinopse)
                <div class="detail-group">
                    <label class="detail-label">Sinopse</label>
                    <div class="detail-value">{{ $book->sinopse }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Statistics Section -->
        <div class="book-statistics">
            <h2 class="statistics-title">
                <i class="fas fa-chart-bar"></i> Estatísticas do Livro
            </h2>
            <div class="statistics-grid">
                <div class="stat-card">
                    <div class="stat-number">{{ $book->quantidade_total }}</div>
                    <div class="stat-label">Exemplares Totais</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $book->quantidade_disponivel }}</div>
                    <div class="stat-label">Disponíveis</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $book->loans()->count() }}</div>
                    <div class="stat-label">Total de Empréstimos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $book->loans()->where('status', 'ativo')->count() }}</div>
                    <div class="stat-label">Empréstimos Ativos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $book->favorites()->count() }}</div>
                    <div class="stat-label">Favoritado</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $book->loans()->where('status', 'devolvido')->count() }}</div>
                    <div class="stat-label">Devoluções</div>
                </div>
            </div>
        </div>

        <!-- System Information Section -->
        <div class="system-info">
            <h2 class="system-info-title">
                <i class="fas fa-cog"></i> Informações do Sistema
            </h2>
            <div class="system-info-grid">
                <div class="system-info-item">
                    <div class="system-info-label">ID do Sistema</div>
                    <div class="system-info-value">#{{ str_pad($book->id, 6, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="system-info-item">
                    <div class="system-info-label">Data de Cadastro</div>
                    <div class="system-info-value">{{ $book->created_at->format('d/m/Y H:i:s') }}</div>
                </div>
                <div class="system-info-item">
                    <div class="system-info-label">Última Atualização</div>
                    <div class="system-info-value">{{ $book->updated_at->format('d/m/Y H:i:s') }}</div>
                </div>
                <div class="system-info-item">
                    <div class="system-info-label">Tempo no Sistema</div>
                    <div class="system-info-value">{{ $book->created_at->diffForHumans() }}</div>
                </div>
                @if($book->created_at != $book->updated_at)
                <div class="system-info-item">
                    <div class="system-info-label">Última Modificação</div>
                    <div class="system-info-value">{{ $book->updated_at->diffForHumans() }}</div>
                </div>
                @endif
                <div class="system-info-item">
                    <div class="system-info-label">Código RFID/ISBN</div>
                    <div class="system-info-value">{{ $book->isbn }}</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="book-actions">
            <div class="action-buttons">
                <a href="{{ route('admin.books.edit', $book) }}" class="btn-action btn-edit">
                    <i class="fas fa-edit"></i>
                    Editar Livro
                </a>
                
                <form method="POST" action="{{ route('admin.books.destroy', $book) }}" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir este livro? Esta ação não pode ser desfeita.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-delete">
                        <i class="fas fa-trash"></i>
                        Excluir Livro
                    </button>
                </form>
                
                <a href="{{ route('admin.books.index') }}" class="btn-action btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Voltar à Lista
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('layout-js')
<script>
// Confirmação de exclusão mais elegante
document.addEventListener('DOMContentLoaded', function() {
    const deleteForm = document.querySelector('form[action*="destroy"]');
    if (deleteForm) {
        deleteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (confirm('⚠️ ATENÇÃO!\n\nTem certeza que deseja excluir este livro?\n\n📚 Título: {{ $book->titulo }}\n👤 Autor: {{ $book->autor }}\n\n❌ Esta ação não pode ser desfeita e removerá:\n• Todas as informações do livro\n• Histórico de empréstimos\n• Dados de favoritos\n\n✅ Confirmar exclusão?')) {
                this.submit();
            }
        });
    }
});
</script>
@endpush