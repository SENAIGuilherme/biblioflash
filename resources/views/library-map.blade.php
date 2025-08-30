@extends('layouts.public')

@section('title', 'Mapa de Bibliotecas')

@push('page-css')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endpush

@section('content')

<div class="mapa-bg">
    <div class="mapa-card">
        <div class="mapa-titulo">Localize uma biblioteca perto de você!</div>
        <div class="mapa-desc">Use os filtros para encontrar bibliotecas por tema, distância e cidade.</div>
        <div class="mapa-filtros">
            <input type="text" id="keyword" placeholder="Tema (ex: tecnologia)">
            <select id="estado" onchange="atualizarCidades(true)">
                <option value="">Estado</option>
                <option value="MG">MG</option>
                <option value="SP">SP</option>
                <option value="RJ">RJ</option>
                <option value="ES">ES</option>
            </select>
            <select id="cidade" onchange="centralizarCidade()">
                <option value="">Cidade</option>
            </select>
            <select id="distance">
                <option value="0">Distância</option>
                <option value="1">1 km</option>
                <option value="3">3 km</option>
                <option value="5">5 km</option>
                <option value="10">10 km</option>
            </select>
            <button onclick="filtrarBibliotecas()"><i class="fas fa-search"></i> Filtrar</button>
        </div>
        <div class="map-container">
            <div id="map"></div>
            <div class="loading-overlay" id="loading-overlay">
                <div class="loading-spinner"></div>
            </div>
        </div>
    </div>
</div>

@push('page-js')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@vite('resources/js/pages/library-map.js')
@endpush
@endsection