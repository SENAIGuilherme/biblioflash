@extends('layouts.auth')

@section('title', 'Login - BiblioFlash')

@section('auth-content')
<div class="main-login">
    <div class="left-login">
        <h1>Faça login<br>para entrar no nosso time</h1>
    </div>

    <div class="right-login">
        <div class="card-wrapper" id="card-wrapper">
            <div class="card-login" id="card-login">
                <!-- Card Front - Login -->
                <div class="card-front">
                    <h1>Login</h1>

                    @if (session('erro') || session('error'))
                    <div class="alert alert-danger">
                        {{ session('erro') ?? session('error') }}
                    </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf
                        <div class="textfield">
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email" placeholder="Digite seu email" required value="{{ old('email') }}">
                            @error('email')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="textfield">
                            <label for="password">Senha</label>
                            <input type="password" name="password" id="password" placeholder="Digite sua senha" required>
                            @error('password')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="textfield">
                            <label>
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                Lembrar-me
                            </label>
                        </div>

                        <button class="btn-login" type="submit">Entrar</button>
                    </form>

                    <p>
                        <button onclick="girarCard()" class="btn-switch">Não tem conta? Cadastre-se</button>
                    </p>

                    @if(Route::has('password.request'))
                    <p>
                        <a href="{{ route('password.request') }}" class="btn-switch">Esqueceu sua senha?</a>
                    </p>
                    @endif
                </div>

                <!-- Card Back - Registro -->
                <div class="card-back">
                    <button type="button" class="btn-volvol" aria-label="Voltar" onclick="girarCard()">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                    </button>

                    <h1>Cadastro</h1>

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
                        @csrf

                        <div class="textfield-group">
                            <div class="textfield">
                                <label for="name">Nome</label>
                                <input type="text" name="name" id="name" placeholder="Nome completo" required value="{{ old('name') }}">
                            </div>
                            <div class="textfield">
                                <label for="cpf">CPF</label>
                                <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" value="{{ old('cpf') }}">
                            </div>
                        </div>

                        <div class="textfield">
                            <label for="foto">Foto de Perfil</label>
                            <input type="file" name="foto" id="foto" accept="image/*">
                        </div>

                        <div class="textfield-group">
                            <div class="textfield">
                                <label for="telefone">Telefone</label>
                                <input type="text" name="telefone" id="telefone" placeholder="(00) 00000-0000" value="{{ old('telefone') }}">
                            </div>
                            <div class="textfield">
                                <label for="register_email">Email</label>
                                <input type="email" name="email" id="register_email" placeholder="seu@email.com" required value="{{ old('email') }}">
                            </div>
                        </div>

                        <div class="textfield-group">
                            <div class="textfield">
                                <label for="senha">Senha</label>
                                <input type="password" name="password" id="senha" placeholder="Mínimo 8 caracteres" required>
                            </div>
                            <div class="textfield">
                                <label for="password_confirmation">Confirmar Senha</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirme sua senha" required>
                            </div>
                        </div>

                        <input type="hidden" name="tipo" value="cliente">

                        <button class="btn-login" type="submit">Criar Conta</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection