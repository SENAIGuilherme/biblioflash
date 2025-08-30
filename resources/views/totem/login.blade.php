@extends('layouts.totem')

@php
$totemLayout = true;
@endphp

@section('title', 'Login do Tótem - BiblioFlash')

@section('totem-content')
<div class="totem-card">
    <div class="totem-logo">
        <img src="{{ asset('biblio-flash/logo-of.png') }}" alt="Logo Biblioteca">
    </div>

    <h2>Login do Cliente</h2>
    <p>Digite suas credenciais para acessar o sistema</p>

    <form method="POST" action="{{ route('totem.login.submit') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                name="email"
                placeholder="Digite seu Email"
                value="{{ old('email') }}"
                required
                maxlength="200"
                autocomplete="email">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password"
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                name="password"
                placeholder="Digite sua senha"
                required
                autocomplete="current-password">
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-totem">Entrar</button>
    </form>

    @if(session('erro') || session('error'))
    <div class="alert alert-danger mt-3">
        {{ session('erro') ?? session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger mt-3">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div>
        <p>
            <i class="fas fa-info-circle"></i>
            Use suas credenciais da biblioteca para acessar
        </p>
    </div>
</div>
@endsection

@section('totem-scripts')
<script>
    // Auto-focus no campo email quando a página carregar
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.focus();
        }
    });

    // Validação básica do formulário
    document.querySelector('form').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!email || !password) {
            e.preventDefault();
            showAlert('Por favor, preencha todos os campos.', 'danger');
            return;
        }

        if (!isValidEmail(email)) {
            e.preventDefault();
            showAlert('Por favor, digite um email válido.', 'danger');
            return;
        }
    });

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function showAlert(message, type = 'danger') {
        // Remove alertas existentes
        const existingAlerts = document.querySelectorAll('.alert-temp');
        existingAlerts.forEach(alert => alert.remove());

        // Cria novo alerta
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} mt-3 alert-temp`;
        alertDiv.textContent = message;

        // Adiciona após o formulário
        const form = document.querySelector('form');
        form.parentNode.insertBefore(alertDiv, form.nextSibling);

        // Remove após 5 segundos
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Limpar mensagens de erro ao digitar
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const feedback = this.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.style.display = 'none';
            }
        });
    });
</script>
@endsection