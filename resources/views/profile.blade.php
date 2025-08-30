@extends('layouts.main')

@section('title', 'Meu Perfil - BiblioFlash')

@section('content')
<div class="container py-4">
    <!-- Header do Perfil -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="profile-avatar bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div class="col-md-10">
                            <h2 class="mb-1">{{ $user->name }}</h2>
                            <p class="text-muted mb-2">
                                <i class="fas fa-envelope me-2"></i>{{ $user->email }}
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-{{ $user->tipo === 'admin' ? 'danger' : ($user->tipo === 'bibliotecario' ? 'warning' : 'primary') }}">
                                    <i class="fas fa-user me-1"></i>
                                    {{ ucfirst($user->tipo) }}
                                </span>
                                <span class="badge bg-{{ $user->ativo ? 'success' : 'secondary' }}">
                                    <i class="fas fa-{{ $user->ativo ? 'check-circle' : 'times-circle' }} me-1"></i>
                                    {{ $user->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                                @if($user->age)
                                <span class="badge bg-info">
                                    <i class="fas fa-birthday-cake me-1"></i>
                                    {{ $user->age }} anos
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Estatísticas -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Estatísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h3 text-primary mb-1">{{ $stats['active_loans'] }}</div>
                                <small class="text-muted">Empréstimos Ativos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h3 text-success mb-1">{{ $stats['total_books_borrowed'] }}</div>
                                <small class="text-muted">Livros Devolvidos</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h3 text-warning mb-1">{{ $stats['pending_fines'] }}</div>
                                <small class="text-muted">Multas Pendentes</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h3 text-info mb-1">{{ $stats['favorite_books'] }}</div>
                                <small class="text-muted">Livros Favoritos</small>
                            </div>
                        </div>
                    </div>
                    
                    @if($stats['pending_fines_total'] > 0)
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Total de multas:</strong> R$ {{ number_format($stats['pending_fines_total'], 2, ',', '.') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Informações Pessoais -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>
                        Informações Pessoais
                    </h5>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit me-1"></i>
                        Editar
                    </button>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">CPF</label>
                            <div class="fw-medium">{{ $user->cpf ?: 'Não informado' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Telefone</label>
                            <div class="fw-medium">{{ $user->formatted_phone ?: 'Não informado' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Data de Nascimento</label>
                            <div class="fw-medium">
                                {{ $user->data_nascimento ? $user->data_nascimento->format('d/m/Y') : 'Não informado' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">Último Acesso</label>
                            <div class="fw-medium">
                                {{ $user->ultimo_acesso ? $user->ultimo_acesso->format('d/m/Y H:i') : 'Nunca' }}
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small">Endereço Completo</label>
                            <div class="fw-medium">{{ $user->full_address ?: 'Não informado' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Atividades Recentes -->
    <div class="row">
        <!-- Empréstimos Recentes -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        Empréstimos Recentes
                    </h6>
                </div>
                <div class="card-body p-0">
                    @forelse($user->loans->take(5) as $loan)
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium small">{{ $loan->book->titulo }}</div>
                            <div class="text-muted small">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $loan->data_emprestimo->format('d/m/Y') }}
                            </div>
                        </div>
                        <span class="badge bg-{{ $loan->status === 'ativo' ? 'warning' : 'success' }}">
                            {{ ucfirst($loan->status) }}
                        </span>
                    </div>
                    @empty
                    <div class="p-3 text-center text-muted">
                        <i class="fas fa-book-open fa-2x mb-2 opacity-50"></i>
                        <div>Nenhum empréstimo encontrado</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Reservas Recentes -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-bookmark me-2"></i>
                        Reservas Recentes
                    </h6>
                </div>
                <div class="card-body p-0">
                    @forelse($user->reservations->take(5) as $reservation)
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium small">{{ $reservation->book->titulo }}</div>
                            <div class="text-muted small">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $reservation->data_reserva->format('d/m/Y') }}
                            </div>
                        </div>
                        <span class="badge bg-{{ $reservation->status === 'ativa' ? 'primary' : 'secondary' }}">
                            {{ ucfirst($reservation->status) }}
                        </span>
                    </div>
                    @empty
                    <div class="p-3 text-center text-muted">
                        <i class="fas fa-bookmark fa-2x mb-2 opacity-50"></i>
                        <div>Nenhuma reserva encontrada</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Multas Recentes -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Multas Recentes
                    </h6>
                </div>
                <div class="card-body p-0">
                    @forelse($user->fines->take(5) as $fine)
                    <div class="d-flex align-items-center p-3 border-bottom">
                        <div class="flex-grow-1">
                            <div class="fw-medium small">{{ $fine->loan->book->titulo }}</div>
                            <div class="text-muted small">
                                <i class="fas fa-dollar-sign me-1"></i>
                                R$ {{ number_format($fine->valor, 2, ',', '.') }}
                            </div>
                        </div>
                        <span class="badge bg-{{ $fine->status === 'pendente' ? 'danger' : 'success' }}">
                            {{ ucfirst($fine->status) }}
                        </span>
                    </div>
                    @empty
                    <div class="p-3 text-center text-muted">
                        <i class="fas fa-check-circle fa-2x mb-2 opacity-50"></i>
                        <div>Nenhuma multa encontrada</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Livros Favoritos -->
    @if($user->favorites->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-heart me-2"></i>
                        Meus Livros Favoritos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($user->favorites->take(6) as $favorite)
                        <div class="col-md-4 col-lg-2">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-3 text-center">
                                    <div class="mb-2">
                                        <i class="fas fa-book fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="card-title small mb-1">{{ Str::limit($favorite->book->titulo, 30) }}</h6>
                                    <p class="card-text small text-muted mb-0">{{ $favorite->book->autor }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($user->favorites->count() > 6)
                    <div class="text-center mt-3">
                        <a href="{{ route('books.index') }}?favorites=1" class="btn btn-outline-primary btn-sm">
                            Ver todos os favoritos ({{ $user->favorites->count() }})
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal de Edição do Perfil -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit me-2"></i>
                        Editar Perfil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" value="{{ $user->telefone }}">
                        </div>
                        <div class="col-md-6">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control" id="cpf" name="cpf" value="{{ $user->cpf }}">
                        </div>
                        <div class="col-md-6">
                            <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                            <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" value="{{ $user->data_nascimento?->format('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control" id="endereco" name="endereco" value="{{ $user->endereco }}">
                        </div>
                        <div class="col-md-4">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="cidade" name="cidade" value="{{ $user->cidade }}">
                        </div>
                        <div class="col-md-4">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="">Selecione...</option>
                                <option value="AC" {{ $user->estado === 'AC' ? 'selected' : '' }}>Acre</option>
                                <option value="AL" {{ $user->estado === 'AL' ? 'selected' : '' }}>Alagoas</option>
                                <option value="AP" {{ $user->estado === 'AP' ? 'selected' : '' }}>Amapá</option>
                                <option value="AM" {{ $user->estado === 'AM' ? 'selected' : '' }}>Amazonas</option>
                                <option value="BA" {{ $user->estado === 'BA' ? 'selected' : '' }}>Bahia</option>
                                <option value="CE" {{ $user->estado === 'CE' ? 'selected' : '' }}>Ceará</option>
                                <option value="DF" {{ $user->estado === 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                <option value="ES" {{ $user->estado === 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                <option value="GO" {{ $user->estado === 'GO' ? 'selected' : '' }}>Goiás</option>
                                <option value="MA" {{ $user->estado === 'MA' ? 'selected' : '' }}>Maranhão</option>
                                <option value="MT" {{ $user->estado === 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                <option value="MS" {{ $user->estado === 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                <option value="MG" {{ $user->estado === 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                <option value="PA" {{ $user->estado === 'PA' ? 'selected' : '' }}>Pará</option>
                                <option value="PB" {{ $user->estado === 'PB' ? 'selected' : '' }}>Paraíba</option>
                                <option value="PR" {{ $user->estado === 'PR' ? 'selected' : '' }}>Paraná</option>
                                <option value="PE" {{ $user->estado === 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                <option value="PI" {{ $user->estado === 'PI' ? 'selected' : '' }}>Piauí</option>
                                <option value="RJ" {{ $user->estado === 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                <option value="RN" {{ $user->estado === 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                <option value="RS" {{ $user->estado === 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                <option value="RO" {{ $user->estado === 'RO' ? 'selected' : '' }}>Rondônia</option>
                                <option value="RR" {{ $user->estado === 'RR' ? 'selected' : '' }}>Roraima</option>
                                <option value="SC" {{ $user->estado === 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                <option value="SP" {{ $user->estado === 'SP' ? 'selected' : '' }}>São Paulo</option>
                                <option value="SE" {{ $user->estado === 'SE' ? 'selected' : '' }}>Sergipe</option>
                                <option value="TO" {{ $user->estado === 'TO' ? 'selected' : '' }}>Tocantins</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control" id="cep" name="cep" value="{{ $user->cep }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-js')
@vite('resources/js/pages/profile.js')
@endpush