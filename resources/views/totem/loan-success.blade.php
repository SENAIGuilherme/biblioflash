@extends('layouts.totem')

@php
$totemLayout = true;
@endphp
@section('title', 'Empréstimo Realizado')
@section('content')

<div class="success-container">
    <div class="success-icon">✅</div>
    <h1 class="success-title">Empréstimo Realizado com Sucesso!</h1>
    <p class="success-message">
        Seus livros foram emprestados com sucesso.<br>
        Lembre-se de devolvê-los na data prevista.
    </p>
    <a href="{{ route('totem.home') }}" class="return-btn">Voltar ao Início</a>
    <p class="auto-redirect" id="autoRedirect">Redirecionando automaticamente em <span id="countdown">10</span> segundos...</p>
</div>

<script>
    let countdown = 10;
    const countdownEl = document.getElementById('countdown');
    const autoRedirectEl = document.getElementById('autoRedirect');

    const timer = setInterval(() => {
        countdown--;
        countdownEl.textContent = countdown;

        if (countdown <= 0) {
            clearInterval(timer);
            window.location.href = '{{ route("totem.home") }}';
        }
    }, 1000);

    // Limpar localStorage após sucesso
    localStorage.removeItem('cartItems');
    localStorage.removeItem('cliente_id');

    // Parar redirecionamento se usuário interagir
    document.addEventListener('click', () => {
        clearInterval(timer);
        autoRedirectEl.style.display = 'none';
    });

    document.addEventListener('keydown', () => {
        clearInterval(timer);
        autoRedirectEl.style.display = 'none';
    });
</script>
@endsection