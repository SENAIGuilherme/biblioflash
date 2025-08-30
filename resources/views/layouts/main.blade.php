<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'BiblioFlash'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600,800&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">

    <!-- Custom Styles -->
    @vite(['resources/css/app.css'])
    @vite(['resources/css/pages/home.css'])

    <!-- Page-specific CSS -->
    @stack('page-css')
    @stack('styles')
    @yield('head')
</head>

<body class="@yield('body-class', 'main-layout')">
    <!-- Navigation -->
    @if(!isset($hideNavigation) || !$hideNavigation)
        @if(request()->routeIs('dashboard'))
            <!-- Admin Navigation -->
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 admin-nav">
                <div class="container-fluid">
                    <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                        <i class="fas fa-cogs me-2"></i>
                        Admin - BiblioFlash
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="adminNavbar">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}" href="{{ route('books.index') }}">
                                    <i class="fas fa-book me-1"></i> Livros
                                </a>
                            </li>
                            <li class="nav-item">
                                 <a class="nav-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}" href="{{ route('reservations.my') }}">
                                     <i class="fas fa-calendar-check me-1"></i> Reservas
                                 </a>
                             </li>
                        </ul>

                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-1"></i>
                                    {{ Auth::user()->nome ?? Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('home') }}"><i class="fas fa-home me-2"></i>Ver Site</a></li>
                                    <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i>Perfil</a></li>
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
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        @elseif(request()->routeIs('totem.*'))
            <!-- totem Navigation -->
            <nav class="totem-nav bg-dark text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <button type="button" onclick="voltarPagina()" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </button>
                    <div class="nav-title">
                        <h4 class="mb-0"><i class="fas fa-desktop me-2"></i>BiblioFlash - Sistema totem</h4>
                    </div>
                    <button type="button" onclick="window.totemManager?.showHelp()" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-question-circle me-1"></i> Ajuda (F1)
                    </button>
                </div>
            </nav>
        @elseif(in_array(request()->route()->getName(), ['login', 'register']) || isset($simpleLayout))
            <!-- Simple Layout Navigation -->
            @if(!isset($hideHeader) || !$hideHeader)
            <div class="simple-header text-center py-4 bg-dark">
                <img src="{{ asset('biblio-flash/logo-of.png') }}" alt="BiblioFlash Logo" class="img-fluid" style="max-height: 60px;">
            </div>
            @endif
        @else
            <!-- Main Navigation -->
            <header class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
                <div class="container-fluid px-4">
                    <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                        <img src="{{ asset('biblio-flash/logo-of.png') }}" alt="BiblioFlash Logo" class="me-2" style="height: 40px;">
                        <span class="fw-bold">BiblioFlash</span>
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="mainNavbar">
                        <ul class="navbar-nav me-auto">
                            <li class="nav-item">
                                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                                    <i class="fas fa-home me-1"></i> Início
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-search me-1"></i> Buscar
                                </a>
                                <div class="dropdown-menu p-3" style="min-width: 300px;">
                                    <form id="searchForm" class="d-flex">
                                        <input type="text" id="searchInput" class="form-control me-2" placeholder="Pesquisar livros..." autocomplete="off">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="fas fa-heart me-1"></i> Favoritos
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-list me-1"></i> Categorias
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-magic me-2"></i>Ficção</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-heart me-2"></i>Romance</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-dragon me-2"></i>Fantasia</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-ghost me-2"></i>Terror</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Biografia</a></li>
                                </ul>
                            </li>
                        </ul>

                        <div class="navbar-nav">
                            @auth
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-1"></i>
                                    {{ Auth::user()->nome ?? Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="fas fa-user me-2"></i>Perfil</a></li>
                                    @if(Auth::user()->tipo === 'admin' || Auth::user()->tipo === 'bibliotecario')
                                    <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-cogs me-2"></i>Painel Admin</a></li>
                                    @endif
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
                            <a href="{{ route('login') }}" class="nav-link btn btn-outline-light ms-2">
                                <i class="fas fa-sign-in-alt me-1"></i> Entrar
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>
        @endif
    @endif

    <!-- Main Content -->
    <main class="flex-grow-1">
        @if(isset($simpleLayout) || in_array(request()->route()->getName(), ['login', 'register']))
            <div class="simple-container container-fluid">
                @yield('simple-content')
                @yield('content')
            </div>
        @elseif(request()->routeIs('totem.*'))
            @yield('totem-content')
            @yield('content')
        @else
            @yield('content')
        @endif
    </main>

    <!-- Footer -->
    @if(!isset($hideFooter) || !$hideFooter)
        @if(!request()->routeIs('totem.*') && !in_array(request()->route()->getName(), ['login', 'register']) && !isset($simpleLayout))
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
        @endif
    @endif

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <!-- Custom Scripts -->
    @vite(['resources/js/app.js'])
    @vite(['resources/js/layouts/main.js'])

    <!-- Page-specific JS -->
    @stack('page-js')
    @stack('scripts')
    @yield('scripts')
    @yield('totem-scripts')

    <!-- totem specific scripts -->
    @if(request()->routeIs('totem.*'))
        @vite(['resources/js/totem.js'])
    @endif
</body>

</html>