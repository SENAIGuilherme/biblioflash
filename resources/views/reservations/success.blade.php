@extends('layouts.main')

@php
$simpleLayout = true;
@endphp

@section('title', 'Reserva Realizada - BiblioFlash')

@section('head')
@parent
@endsection

@section('content')
<div class="success-container">
    <span class="success-icon">✅</span>
    <h2 class="success-title">Reserva realizada com sucesso!</h2>

    <p class="success-message">
        Seus livros estão reservados e aguardando retirada.
    </p>

    @if(session('reserved_books'))
    <div class="reservation-details">
        <h4>Livros Reservados:</h4>
        <ul>
            @foreach(session('reserved_books') as $book)
            <li>{{ $book['titulo'] ?? 'Livro' }} - {{ $book['autor'] ?? 'Autor não informado' }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="info-box">
        <p><strong>Importante:</strong> Você tem até 3 dias para retirar os livros no totem da biblioteca. Após esse prazo, a reserva será cancelada automaticamente.</p>
    </div>

    <a href="{{ route('home') }}" class="btn-home">Voltar para a Home</a>
</div>
@endsection

@section('scripts')
@parent
<script>
    // Auto-redirect após 10 segundos (opcional)
    setTimeout(function() {
        const redirectBtn = document.querySelector('.btn-home');
        if (redirectBtn) {
            redirectBtn.style.background = '#27ae60';
            redirectBtn.textContent = 'Redirecionando...';
            setTimeout(() => {
                window.location.href = redirectBtn.href;
            }, 2000);
        }
    }, 8000);

    // Limpar localStorage após sucesso
    if (localStorage.getItem('cartItems')) {
        localStorage.removeItem('cartItems');
    }
</script>
@endsection