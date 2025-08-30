@extends('layouts.totem')

@php
$totemLayout = true;
@endphp

@section('title', 'Tótem BiblioFlash - Bem-vindo')

@section('totem-content')
<div class="totem-card">
    <div class="totem-logo">
        <img src="{{ asset('biblio-flash/logo-of.png') }}" alt="Logo Biblioteca">
    </div>

    <h2>Bem-vindo ao Tótem</h2>

    @if(isset($user) && $user)
    <p>
        Olá, <strong>{{ $user->name ?? 'Usuário' }}</strong>!<br>
        O que deseja fazer hoje?
    </p>
    @else
    <p>
        Escolha uma das opções abaixo para continuar
    </p>
    @endif

    <div class="d-grid gap-3">
        <a href="{{ route('totem.loan') }}" class="btn btn-success btn-lg">
            <i class="fas fa-book"></i>
            Fazer Empréstimo
        </a>

        <a href="{{ route('totem.return') }}" class="btn btn-warning btn-lg">
            <i class="fas fa-undo"></i>
            Devolver Livro
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success mt-4">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger mt-4">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
    </div>
    @endif

    <div>
        @if(isset($user) && $user)
        <a href="{{ route('totem.logout') }}" class="btn-link">
            <i class="fas fa-sign-out-alt"></i> Sair
        </a>
        @else
        <a href="{{ route('totem.login') }}" class="btn-link">
            <i class="fas fa-sign-in-alt"></i> Fazer Login
        </a>
        @endif
    </div>

    <div>
        <p>
            <i class="fas fa-clock"></i>
            {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>
</div>
@endsection

@section('totem-scripts')
<script>
    // Auto-refresh da página a cada 5 minutos para manter a sessão ativa
    let refreshTimer;

    function startRefreshTimer() {
        refreshTimer = setTimeout(() => {
            window.location.reload();
        }, 300000); // 5 minutos
    }

    function resetRefreshTimer() {
        clearTimeout(refreshTimer);
        startRefreshTimer();
    }

    // Inicia o timer quando a página carrega
    document.addEventListener('DOMContentLoaded', function() {
        startRefreshTimer();

        // Reset timer em qualquer interação do usuário
        document.addEventListener('click', resetRefreshTimer);
        document.addEventListener('keypress', resetRefreshTimer);
        document.addEventListener('touchstart', resetRefreshTimer);
    });

    // Confirmação antes de sair (logout)
    const logoutLink = document.querySelector('a[href*="logout"]');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            if (!confirm('Tem certeza que deseja sair do sistema?')) {
                e.preventDefault();
            }
        });
    }

    // Animação de hover nos botões
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.02)';
        });

        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Feedback visual ao clicar nos botões
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Adiciona efeito de ripple
            const ripple = document.createElement('span');
            ripple.style.cssText = `
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255,255,255,0.3);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                `;

            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size / 2) + 'px';

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // CSS para animação de ripple
    const style = document.createElement('style');
    style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
    document.head.appendChild(style);

    // Atualizar horário a cada minuto
    function updateTime() {
        const timeElement = document.querySelector('.fa-clock').parentElement;
        if (timeElement) {
            const now = new Date();
            const timeString = now.toLocaleString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            timeElement.innerHTML = '<i class="fas fa-clock"></i> ' + timeString;
        }
    }

    // Atualiza o horário a cada minuto
    setInterval(updateTime, 60000);
</script>
@endsection