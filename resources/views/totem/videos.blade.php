@extends('layouts.totem')

@php
$totemLayout = true;
@endphp
@section('title', 'Vídeos Salvos')
@section('content')

<div class="videos-container">
    <div class="videos-header">
        <h1 class="videos-title">Vídeos de Verificação</h1>
        <p class="videos-subtitle">Vídeos salvos das verificações de segurança</p>
    </div>

    @if(isset($videos) && count($videos) > 0)
    <div class="videos-grid">
        @foreach($videos as $video)
        <div class="video-card">
            <div class="video-name">📹 {{ $video }}</div>
            <video class="video-player" controls preload="metadata">
                <source src="{{ asset('storage/verificacoes/videos/' . $video) }}" type="video/webm">
                <source src="{{ asset('storage/verificacoes/videos/' . $video) }}" type="video/mp4">
                Seu navegador não suporta o elemento de vídeo.
            </video>
            <div class="video-actions">
                <a href="{{ asset('storage/verificacoes/videos/' . $video) }}" download class="download-btn">
                    ⬇️ Baixar
                </a>
                <button onclick="openFullscreen('{{ asset('storage/verificacoes/videos/' . $video) }}', '{{ $video }}')" class="fullscreen-btn">
                    🔍 Tela Cheia
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <div class="empty-icon">📹</div>
        <h2 class="empty-title">Nenhum vídeo encontrado</h2>
        <p class="empty-message">
            Ainda não há vídeos de verificação salvos.<br>
            Os vídeos aparecerão aqui após as verificações de segurança dos empréstimos.
        </p>
    </div>
    @endif

    <div>
        <a href="{{ route('totem.home') }}" class="back-btn">
            ← Voltar ao Início
        </a>
    </div>
</div>

<!-- Modal para tela cheia -->
<div id="fullscreenModal">
    <div>
        <video id="fullscreenVideo" controls autoplay>
            Seu navegador não suporta o elemento de vídeo.
        </video>
        <button onclick="closeFullscreen()">
            ×
        </button>
        <div id="fullscreenTitle"></div>
    </div>
</div>

<script>
    function openFullscreen(videoSrc, videoName) {
        const modal = document.getElementById('fullscreenModal');
        const video = document.getElementById('fullscreenVideo');
        const title = document.getElementById('fullscreenTitle');

        video.src = videoSrc;
        title.textContent = videoName;
        modal.style.display = 'flex';

        // Pausar todos os outros vídeos
        document.querySelectorAll('.video-player').forEach(v => v.pause());
    }

    function closeFullscreen() {
        const modal = document.getElementById('fullscreenModal');
        const video = document.getElementById('fullscreenVideo');

        video.pause();
        video.src = '';
        modal.style.display = 'none';
    }

    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFullscreen();
        }
    });

    // Fechar modal clicando fora do vídeo
    document.getElementById('fullscreenModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFullscreen();
        }
    });

    // Controles de teclado para navegação
    document.addEventListener('keydown', function(e) {
        // Voltar com Backspace ou seta esquerda
        if ((e.key === 'Backspace' || e.key === 'ArrowLeft') && !document.getElementById('fullscreenModal').style.display.includes('flex')) {
            e.preventDefault();
            window.location.href = '{{ route("totem.home") }}';
        }
    });

    // Auto-focus no primeiro vídeo se existir
    window.addEventListener('load', function() {
        const firstVideo = document.querySelector('.video-player');
        if (firstVideo) {
            firstVideo.focus();
        }
    });
</script>
@endsection