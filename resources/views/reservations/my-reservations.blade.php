@extends('layouts.public')

@section('title', 'Minhas Reservas - BiblioFlash')

@push('styles')
<style>
/* Tema Escuro Global */
body {
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
    color: #e2e8f0;
    min-height: 100vh;
}

.my-reservations-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    position: relative;
}

/* Efeitos de Fundo */
.my-reservations-container::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.1) 0%, transparent 50%);
    pointer-events: none;
    z-index: -1;
}

/* Header da Página */
.page-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    border-radius: 2px;
}

.page-header h1 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    text-shadow: 0 0 30px rgba(102, 126, 234, 0.5);
    letter-spacing: -1px;
}

.page-header p {
    color: #94a3b8;
    font-size: 1.2rem;
    font-weight: 300;
    opacity: 0.9;
}

/* Seção de Filtros */
.filters-section {
    background: rgba(30, 41, 59, 0.8);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(148, 163, 184, 0.1);
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 3rem;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.filter-form {
    display: flex;
    gap: 1.5rem;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 250px;
}

.filter-group label {
    display: block;
    margin-bottom: 0.75rem;
    font-weight: 600;
    color: #e2e8f0;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-group select {
    width: 100%;
    padding: 1rem;
    background: rgba(15, 23, 42, 0.8);
    border: 2px solid rgba(148, 163, 184, 0.2);
    border-radius: 12px;
    color: #e2e8f0;
    font-size: 1rem;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.filter-group select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
}

.filter-group select option {
    background: #1e293b;
    color: #e2e8f0;
}

.filter-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 12px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
}

.filter-btn:active {
    transform: translateY(-1px);
}

/* Grid de Reservas */
.reservations-grid {
    display: grid;
    gap: 2rem;
}

.reservation-card {
    background: rgba(30, 41, 59, 0.8);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(148, 163, 184, 0.1);
    border-radius: 20px;
    padding: 2rem;
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.reservation-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.reservation-card:hover {
    transform: translateY(-8px);
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.4),
        0 0 0 1px rgba(102, 126, 234, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.reservation-card:hover::before {
    opacity: 1;
}

.reservation-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 1.5rem;
    gap: 1rem;
}

.book-info h3 {
    color: #f1f5f9;
    margin: 0 0 0.75rem 0;
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1.3;
}

.book-info p {
    color: #94a3b8;
    margin: 0.5rem 0;
    font-size: 0.95rem;
}

.book-info p strong {
    color: #cbd5e1;
    font-weight: 600;
}

/* Status Badges */
.status-badge {
    padding: 0.75rem 1.25rem;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(10px);
}

.status-ativa {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
}

.status-expirada {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
}

.status-atendida {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
}

.status-cancelada {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
}

/* Detalhes da Reserva */
.reservation-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(148, 163, 184, 0.2);
}

.detail-item {
    display: flex;
    flex-direction: column;
    padding: 1rem;
    background: rgba(15, 23, 42, 0.5);
    border-radius: 12px;
    border: 1px solid rgba(148, 163, 184, 0.1);
    transition: all 0.3s ease;
}

.detail-item:hover {
    background: rgba(15, 23, 42, 0.8);
    transform: translateY(-2px);
}

.detail-label {
    font-size: 0.85rem;
    color: #94a3b8;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.detail-value {
    font-weight: 600;
    color: #f1f5f9;
    font-size: 1rem;
}

/* Estado Vazio */
.no-reservations {
    text-align: center;
    padding: 4rem 2rem;
    background: rgba(30, 41, 59, 0.8);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(148, 163, 184, 0.1);
    border-radius: 20px;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.no-reservations i {
    font-size: 5rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 2rem;
    display: block;
}

.no-reservations h3 {
    color: #f1f5f9;
    margin-bottom: 1rem;
    font-size: 1.8rem;
    font-weight: 700;
}

.no-reservations p {
    color: #94a3b8;
    margin-bottom: 2.5rem;
    font-size: 1.1rem;
    line-height: 1.6;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: 12px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
    color: white;
    text-decoration: none;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
}

/* Paginação */
.pagination-wrapper {
    margin-top: 3rem;
    display: flex;
    justify-content: center;
}

.pagination-wrapper .pagination {
    background: rgba(30, 41, 59, 0.8);
    backdrop-filter: blur(20px);
    border-radius: 15px;
    padding: 0.5rem;
    border: 1px solid rgba(148, 163, 184, 0.1);
}

.pagination-wrapper .page-link {
    background: transparent;
    border: none;
    color: #94a3b8;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.pagination-wrapper .page-link:hover {
    background: rgba(102, 126, 234, 0.2);
    color: #e2e8f0;
}

.pagination-wrapper .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

/* Responsividade */
@media (max-width: 768px) {
    .my-reservations-container {
        padding: 1rem;
    }
    
    .page-header h1 {
        font-size: 2.5rem;
    }
    
    .filter-form {
        flex-direction: column;
    }
    
    .filter-group {
        min-width: 100%;
    }
    
    .reservation-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .reservation-details {
        grid-template-columns: 1fr;
    }
    
    .filters-section {
        padding: 1.5rem;
    }
    
    .reservation-card {
        padding: 1.5rem;
    }
}

@media (max-width: 480px) {
    .page-header h1 {
        font-size: 2rem;
    }
    
    .no-reservations {
        padding: 2rem 1rem;
    }
    
    .no-reservations i {
        font-size: 3rem;
    }
}
</style>
@endpush

@section('content')
<div class="my-reservations-container">
    <div class="page-header">
        <h1>📚 Minhas Reservas</h1>
        <p>Acompanhe o status das suas reservas de livros</p>
    </div>

    <!-- Filtros -->
    <div class="filters-section">
        <form method="GET" class="filter-form">
            <div class="filter-group">
                <label for="status">Status da Reserva</label>
                <select name="status" id="status">
                    <option value="">Todos os status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativas</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expiradas</option>
                    <option value="attended" {{ request('status') == 'attended' ? 'selected' : '' }}>Atendidas</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Canceladas</option>
                </select>
            </div>
            <button type="submit" class="filter-btn">
                <i class="fas fa-filter"></i> Filtrar
            </button>
        </form>
    </div>

    <!-- Lista de Reservas -->
    @if($reservations->count() > 0)
        <div class="reservations-grid">
            @foreach($reservations as $reservation)
                <div class="reservation-card">
                    <div class="reservation-header">
                        <div class="book-info">
                            <h3>{{ $reservation->book->titulo }}</h3>
                            <p><strong>Autor:</strong> {{ $reservation->book->autor }}</p>
                            @if($reservation->book->category)
                                <p><strong>Categoria:</strong> {{ $reservation->book->category->nome }}</p>
                            @endif
                        </div>
                        <div class="status-badge status-{{ $reservation->status }}">
                            @switch($reservation->status)
                                @case('ativa')
                                    ✅ Ativa
                                    @break
                                @case('expirada')
                                    ⏰ Expirada
                                    @break
                                @case('atendida')
                                    📖 Atendida
                                    @break
                                @case('cancelada')
                                    ❌ Cancelada
                                    @break
                                @default
                                    {{ ucfirst($reservation->status) }}
                            @endswitch
                        </div>
                    </div>

                    <div class="reservation-details">
                        <div class="detail-item">
                            <span class="detail-label">Data da Reserva</span>
                            <span class="detail-value">{{ $reservation->data_reserva->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Data de Expiração</span>
                            <span class="detail-value">{{ $reservation->data_expiracao->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($reservation->observacoes)
                            <div class="detail-item">
                                <span class="detail-label">Observações</span>
                                <span class="detail-value">{{ $reservation->observacoes }}</span>
                            </div>
                        @endif
                        @if($reservation->status == 'cancelada' && $reservation->motivo_cancelamento)
                            <div class="detail-item">
                                <span class="detail-label">Motivo do Cancelamento</span>
                                <span class="detail-value">{{ $reservation->motivo_cancelamento }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        @if($reservations->hasPages())
            <div class="pagination-wrapper">
                {{ $reservations->links() }}
            </div>
        @endif
    @else
        <div class="no-reservations">
            <i class="fas fa-bookmark"></i>
            <h3>Nenhuma reserva encontrada</h3>
            <p>Você ainda não fez nenhuma reserva ou não há reservas que correspondam aos filtros selecionados.</p>
            <a href="{{ route('books.index') }}" class="btn-primary">
                <i class="fas fa-search"></i> Explorar Livros
            </a>
        </div>
    @endif
</div>
@endsection