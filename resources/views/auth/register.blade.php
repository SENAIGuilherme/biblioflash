@extends('layouts.auth')

@section('title', 'Cadastro - BiblioFlash')

@section('auth-content')
<div class="main-login">
    <div class="left-login">
        <h1>Cadastre-se<br>e faça parte do nosso time</h1>
    </div>

    <div class="right-login">
        <div class="card-wrapper">
            <div class="card-login">
                <div class="card-front">
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
                                @error('name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="textfield">
                                <label for="cpf">CPF</label>
                                <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" value="{{ old('cpf') }}">
                                @error('cpf')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="textfield">
                            <label for="foto">Foto de Perfil</label>
                            <input type="file" name="foto" id="foto" accept="image/*">
                            @error('foto')
                            <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="textfield-group">
                            <div class="textfield">
                                <label for="telefone">Telefone</label>
                                <input type="text" name="telefone" id="telefone" placeholder="(00) 00000-0000" value="{{ old('telefone') }}">
                                @error('telefone')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="textfield">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" placeholder="seu@email.com" required value="{{ old('email') }}">
                                @error('email')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="textfield-group">
                            <div class="textfield">
                                <label for="password">Senha</label>
                                <input type="password" name="password" id="password" placeholder="Mínimo 8 caracteres" required>
                                @error('password')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="textfield">
                                <label for="password_confirmation">Confirmar Senha</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirme sua senha" required>
                            </div>
                        </div>

                        <input type="hidden" name="tipo" value="cliente">

                        <button class="btn-login" type="submit">Cadastrar</button>
                    </form>

                    <p>
                        <a href="{{ route('login') }}" class="btn-switch">Já tem conta? Faça login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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

// Formatação automática do telefone
document.getElementById('telefone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length <= 11) {
        if (value.length <= 10) {
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        } else {
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
        e.target.value = value;
    }
});
</script>

@endsection