@extends('layouts.app')

@section('body-class', 'totem-layout')

@push('layout-js')
    @vite(['resources/js/totem.js'])
@endpush

@section('header')
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
@endsection

@section('content')
    @yield('totem-content')
@endsection

@section('scripts')
    @yield('totem-scripts')
@endsection