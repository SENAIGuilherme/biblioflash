@extends('layouts.app')

@section('body-class', 'public-layout')

@push('layout-css')
    @vite(['resources/css/pages/home.css'])
@endpush

@push('layout-js')
    @vite(['resources/js/layouts/main.js'])
@endpush

@section('header')
    <!-- Main Navigation -->
    <header class="bg-dark text-white">
        <div class="container-fluid">
            <div class="row align-items-center py-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('biblio-flash/logo-of.png') }}" alt="BiblioFlash Logo" class="img-fluid me-3" style="max-height: 50px;">
                        <h4 class="mb-0">BiblioFlash</h4>
                    </div>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('search') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Buscar livros..." value="{{ request('q') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        @auth
                            <div class="dropdown">
                                <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user me-1"></i> {{ Auth::user()->nome ?? Auth::user()->name }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if(Auth::user()->tipo === 'admin')
                                        <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-cogs me-2"></i>Admin</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i>Perfil</a></li>
                                    <li><a class="dropdown-item" href="{{ route('reservations.my') }}"><i class="fas fa-calendar-check me-2"></i>Minhas Reservas</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('register') }}" class="nav-link btn btn-primary me-2">
                                <i class="fas fa-user-plus me-1"></i> Cadastrar
                            </a>
                            <a href="{{ route('login') }}" class="nav-link btn btn-outline-light ms-2">
                                <i class="fas fa-sign-in-alt me-1"></i> Entrar
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('footer')
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-book me-2"></i>BiblioFlash</h5>
                    <p class="mb-0">Sistema de gerenciamento de biblioteca moderno e eficiente.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; {{ date('Y') }} BiblioFlash. Todos os direitos reservados.</p>
                </div>
            </div>
        </div>
    </footer>
@endsection