@extends('layouts.app')

@section('body-class', 'simple-layout')

@push('layout-css')
    @vite(['resources/css/pages/auth-login.css'])
@endpush

@push('layout-js')
    @vite(['resources/js/pages/auth-login.js'])
@endpush

@section('header')
    <!-- Simple Header for Auth Pages -->
    <div class="simple-header text-center py-4 bg-dark">
        <img src="{{ asset('biblio-flash/logo-of.png') }}" alt="BiblioFlash Logo" class="img-fluid" style="max-height: 60px;">
    </div>
@endsection

@section('main-class', 'simple-container container-fluid')

@section('content')
    @yield('auth-content')
@endsection