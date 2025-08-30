@extends('layouts.totem')

@php
$totemLayout = true;
@endphp

@section('title', 'Tótem - Biblioteca')

@section('totem-content')
<div class="main-container">
    <div class="totem-login-card">
        <div class="totem-logo">
            <img src="{{ asset('biblio-flash/logo-of.png') }}" alt="Logo Biblioteca">
        </div>
        <h1 class="totem-title">Login do Cliente</h1>
        <p class="totem-subtitle">Acesse o sistema com suas credenciais</p>

        <form method="POST" action="{{ route('totem.login') }}" id="loginForm">
            @csrf
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf"
                    placeholder="Digite seu CPF" required maxlength="14"
                    value="{{ old('cpf') }}">
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha"
                    placeholder="Digite sua senha" required>
            </div>
            <button type="submit" class="btn btn-totem" id="loginBtn">
                <div class="loading-spinner" id="loadingSpinner"></div>
                <span id="btnText">Entrar</span>
            </button>
        </form>

        @if(session('erro'))
        <div class="alert alert-danger" id="errorAlert">
            {{ session('erro') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger" id="validationAlert">
            @foreach($errors->all() as $error)
            {{ $error }}<br>
            @endforeach
        </div>
        @endif
    </div>
</div>

<footer class="totem-footer">
    &copy; {{ date('Y') }} BiblioFlash - Biblioteca Universitária
</footer>
@endsection

@section('totem-scripts')
<script>
    // Função para voltar página
    function voltarPagina() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/';
        }
    }

    // Formatação automática do CPF
    document.getElementById('cpf').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        }
    });

    // Auto-focus no campo CPF
    window.addEventListener('load', function() {
        document.getElementById('cpf').focus();
    });

    // Validação do formulário
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        const cpf = document.getElementById('cpf').value.replace(/\D/g, '');
        const senha = document.getElementById('senha').value;

        if (cpf.length !== 11) {
            e.preventDefault();
            showAlert('Por favor, digite um CPF válido com 11 dígitos.', 'danger');
            return;
        }

        if (senha.length < 3) {
            e.preventDefault();
            showAlert('A senha deve ter pelo menos 3 caracteres.', 'danger');
            return;
        }

        // Mostrar loading
        showLoading(true);
    });

    // Função para mostrar/ocultar loading
    function showLoading(show) {
        const spinner = document.getElementById('loadingSpinner');
        const btnText = document.getElementById('btnText');
        const btn = document.getElementById('loginBtn');

        if (show) {
            spinner.style.display = 'block';
            btnText.textContent = 'Entrando...';
            btn.disabled = true;
        } else {
            spinner.style.display = 'none';
            btnText.textContent = 'Entrar';
            btn.disabled = false;
        }
    }

    // Função para mostrar alertas temporários
    function showAlert(message, type = 'danger') {
        // Remove alertas existentes
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        alertDiv.style.animation = 'fadeIn 0.3s ease-out';

        const form = document.getElementById('loginForm');
        form.appendChild(alertDiv);

        // Remove o alerta após 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.style.animation = 'fadeOut 0.3s ease-out';
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.remove();
                    }
                }, 300);
            }
        }, 5000);
    }

    // Adicionar animação de fadeOut
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Remover loading se houver erro
    window.addEventListener('load', function() {
        showLoading(false);
    });

    // Controles de teclado
    document.addEventListener('keydown', function(e) {
        // Enter para submeter formulário
        if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey) {
            const activeElement = document.activeElement;
            if (activeElement.tagName === 'INPUT') {
                if (activeElement.id === 'cpf') {
                    document.getElementById('senha').focus();
                    e.preventDefault();
                } else if (activeElement.id === 'senha') {
                    document.getElementById('loginForm').submit();
                    e.preventDefault();
                }
            }
        }

        // Escape para voltar
        if (e.key === 'Escape') {
            voltarPagina();
        }
    });

    // Auto-remover alertas após um tempo
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(function() {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 500);
        });
    }, 5000);
</script>
@endsection