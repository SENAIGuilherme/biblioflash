@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')

@section('content')
<div class="dashboard-container">
    <div class="container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">Dashboard Administrativo</h1>
            <p class="dashboard-subtitle">Bem-vindo ao painel de controle do BiblioFlash</p>
            
            <!-- Action Buttons -->
            <div class="dashboard-actions">
                <a href="{{ route('admin.books.register') }}" class="btn-register-book">
                    <i class="fas fa-plus-circle"></i>
                    Cadastrar Livro
                </a>
                <a href="{{ route('admin.books.index') }}" class="btn-manage-books">
                    <i class="fas fa-list-ul"></i>
                    Gerenciar Livros
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="stat-number">{{ $stats['total_clients'] ?? 0 }}</h3>
                <p class="stat-label">Clientes Cadastrados</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <h3 class="stat-number">{{ Str::limit($stats['most_read_book'] ?? 'Nenhum', 15) }}</h3>
                <p class="stat-label">Livro Mais Lido</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h3 class="stat-number">{{ $stats['reserved_books'] ?? 0 }}</h3>
                <p class="stat-label">Livros Reservados</p>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-undo"></i>
                </div>
                <h3 class="stat-number">{{ $stats['daily_returns'] ?? 0 }}</h3>
                <p class="stat-label">Devoluções do Dia</p>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3 class="chart-title">
                    <i class="fas fa-chart-line"></i>
                    Empréstimos por Mês
                </h3>
                <div class="chart-container">
                    <canvas id="loansChart"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <h3 class="chart-title">
                    <i class="fas fa-chart-bar"></i>
                    Top 10 Livros Mais Lidos
                </h3>
                <div class="chart-container">
                    <canvas id="topBooksChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Data Lists -->
        <div class="data-lists-grid">
            <!-- Clientes Cadastrados -->
            <div class="data-list-card">
                <div class="list-header">
                    <i class="fas fa-users"></i>
                    <h3>Clientes Cadastrados</h3>
                </div>
                <div class="list-content">
                    @forelse($registeredClients as $client)
                        <div class="list-item">
                            <div class="item-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="item-content">
                                <div class="item-title">{{ $client->name }}</div>
                                <div class="item-subtitle">{{ $client->email }}</div>
                                <div class="item-time">Cadastrado em {{ $client->created_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>Nenhum cliente cadastrado</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Devoluções Recentes -->
            <div class="data-list-card">
                <div class="list-header">
                    <i class="fas fa-undo"></i>
                    <h3>Devoluções Recentes</h3>
                </div>
                <div class="list-content">
                    @forelse($recentReturns as $return)
                        <div class="list-item">
                            <div class="item-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="item-content">
                                <div class="item-title">{{ $return->book->titulo ?? 'Livro não encontrado' }}</div>
                                <div class="item-subtitle">Por: {{ $return->user->name ?? 'Usuário não encontrado' }}</div>
                                <div class="item-time">Devolvido em {{ $return->data_devolucao_real ? \Carbon\Carbon::parse($return->data_devolucao_real)->format('d/m/Y H:i') : 'Data não informada' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-undo"></i>
                            <p>Nenhuma devolução recente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de Empréstimos por Mês
        const loansCtx = document.getElementById('loansChart').getContext('2d');
        new Chart(loansCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: 'Empréstimos',
                    data: [65, 59, 80, 81, 56, 55, 40, 65, 75, 85, 70, 90],
                    borderColor: 'rgba(255, 255, 255, 0.8)',
                    backgroundColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // Gráfico dos Top 10 Livros Mais Lidos
        const topBooksCtx = document.getElementById('topBooksChart').getContext('2d');
        const topBooksData = @json($topBooks);
        
        new Chart(topBooksCtx, {
            type: 'bar',
            data: {
                labels: topBooksData.map(book => book.titulo.length > 20 ? book.titulo.substring(0, 20) + '...' : book.titulo),
                datasets: [{
                    label: 'Empréstimos',
                    data: topBooksData.map(book => book.loans_count),
                    backgroundColor: [
                        'rgba(255, 107, 107, 0.8)',
                        'rgba(78, 205, 196, 0.8)',
                        'rgba(69, 183, 209, 0.8)',
                        'rgba(150, 206, 180, 0.8)',
                        'rgba(255, 234, 167, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)'
                    ],
                    borderColor: 'rgba(255, 255, 255, 0.8)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            maxRotation: 45
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush