@extends('layouts.public')

@section('title', 'BiblioFlash - Biblioteca Digital')

@section('content')
<!-- Hero Section -->
<section class="hero-modern">
    <div class="hero-background">
        <div class="floating-elements">
            <div class="floating-book"></div>
            <div class="floating-book"></div>
            <div class="floating-book"></div>
        </div>
    </div>
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">
                        Bem-vindo ao <span class="gradient-text">BiblioFlash</span>
                    </h1>
                    <p class="hero-subtitle">
                        Sua biblioteca digital com milhares de livros disponíveis para reserva. 
                        Descubra um mundo de conhecimento ao seu alcance.
                    </p>
                    <div class="hero-buttons">
                        @auth
                        <a href="{{ route('books.index') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-book-open me-2"></i>Explorar Livros
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-home me-2"></i>Ir para Home
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-rocket me-2"></i>Começar Agora
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Criar Conta
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-visual">
                    <div class="book-stack">
                        <div class="book book-1"></div>
                        <div class="book book-2"></div>
                        <div class="book book-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-modern">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Por que escolher o <span class="gradient-text">BiblioFlash</span>?</h2>
            <p class="section-subtitle">Descubra as vantagens da nossa plataforma digital</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="feature-card-modern">
                    <div class="feature-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3>Vasto Acervo</h3>
                    <p>Milhares de livros de diversos gêneros e autores disponíveis para você explorar e reservar.</p>
                    <div class="feature-glow"></div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card-modern">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Reserva Rápida</h3>
                    <p>Sistema de reserva intuitivo e rápido. Reserve seus livros favoritos em poucos cliques.</p>
                    <div class="feature-glow"></div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card-modern">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Busca Avançada</h3>
                    <p>Encontre exatamente o que procura com nosso sistema de busca por título, autor ou categoria.</p>
                    <div class="feature-glow"></div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card-modern">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Acesso Mobile</h3>
                    <p>Plataforma responsiva que funciona perfeitamente em qualquer dispositivo.</p>
                    <div class="feature-glow"></div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card-modern">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Comunidade</h3>
                    <p>Faça parte de uma comunidade de leitores apaixonados por livros.</p>
                    <div class="feature-glow"></div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="feature-card-modern">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Segurança</h3>
                    <p>Seus dados estão protegidos com as melhores práticas de segurança digital.</p>
                    <div class="feature-glow"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-modern">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card-modern">
                    <div class="stat-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-content">
                        <span class="number">{{ $stats['total_books'] ?? '5000+' }}</span>
                        <div class="label">Livros Disponíveis</div>
                    </div>
                    <div class="stat-glow"></div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card-modern">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <span class="number">{{ $stats['total_users'] ?? '1200+' }}</span>
                        <div class="label">Usuários Ativos</div>
                    </div>
                    <div class="stat-glow"></div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card-modern">
                    <div class="stat-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="stat-content">
                        <span class="number">{{ $stats['active_loans'] ?? '8500+' }}</span>
                        <div class="label">Empréstimos Ativos</div>
                    </div>
                    <div class="stat-glow"></div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card-modern">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <span class="number">{{ $stats['total_libraries'] ?? '25+' }}</span>
                        <div class="label">Bibliotecas</div>
                    </div>
                    <div class="stat-glow"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-modern">
    <div class="cta-background">
        <div class="cta-particles"></div>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="cta-title">Pronto para começar sua <span class="gradient-text">jornada literária</span>?</h2>
                <p class="cta-subtitle">Junte-se a milhares de leitores que já descobriram o BiblioFlash e transformaram sua experiência de leitura</p>
                <div class="cta-buttons">
                    @auth
                    <a href="{{ route('books.index') }}" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-book-open me-2"></i>Explorar Acervo
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-home me-2"></i>Ir para Home
                    </a>
                    @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-user-plus me-2"></i>Criar Conta Grátis
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Fazer Login
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('page-css')
<style>
/* Hero Section */
.hero-modern {
    position: relative;
    min-height: 100vh;
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
    overflow: hidden;
    display: flex;
    align-items: center;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.floating-elements {
    position: absolute;
    width: 100%;
    height: 100%;
}

.floating-book {
    position: absolute;
    width: 60px;
    height: 80px;
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    opacity: 0.1;
    animation: float 6s ease-in-out infinite;
}

.floating-book:nth-child(1) {
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.floating-book:nth-child(2) {
    top: 60%;
    right: 15%;
    animation-delay: 2s;
}

.floating-book:nth-child(3) {
    bottom: 30%;
    left: 20%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

.hero-content {
    position: relative;
    z-index: 2;
    padding: 2rem 0;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-subtitle {
    font-size: 1.25rem;
    color: #b8c5d6;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.hero-buttons {
    margin-top: 2rem;
}

.hero-visual {
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 500px;
}

.book-stack {
    position: relative;
    width: 200px;
    height: 300px;
}

.book {
    position: absolute;
    width: 150px;
    height: 200px;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    animation: bookFloat 4s ease-in-out infinite;
}

.book-1 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transform: rotate(-10deg);
    z-index: 3;
}

.book-2 {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    transform: rotate(5deg) translateX(30px) translateY(20px);
    z-index: 2;
    animation-delay: 1s;
}

.book-3 {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    transform: rotate(-5deg) translateX(-20px) translateY(40px);
    z-index: 1;
    animation-delay: 2s;
}

@keyframes bookFloat {
    0%, 100% { transform: translateY(0px) rotate(var(--rotation, 0deg)); }
    50% { transform: translateY(-10px) rotate(var(--rotation, 0deg)); }
}

/* Features Section */
.features-modern {
    padding: 100px 0;
    background: #0a0a1a;
    position: relative;
}

.section-header {
    margin-bottom: 4rem;
}

.section-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 1rem;
}

.section-subtitle {
    font-size: 1.1rem;
    color: #b8c5d6;
    max-width: 600px;
    margin: 0 auto;
}

.feature-card-modern {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 2.5rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    height: 100%;
}

.feature-card-modern:hover {
    transform: translateY(-10px);
    border-color: rgba(102, 126, 234, 0.5);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
}

.feature-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: white;
    position: relative;
    z-index: 2;
}

.feature-card-modern h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 1rem;
}

.feature-card-modern p {
    color: #b8c5d6;
    line-height: 1.6;
    margin-bottom: 0;
}

.feature-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.feature-card-modern:hover .feature-glow {
    opacity: 1;
}

/* Stats Section */
.stats-modern {
    padding: 100px 0;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    position: relative;
}

.stat-card-modern {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    height: 100%;
}

.stat-card-modern:hover {
    transform: translateY(-5px);
    border-color: rgba(102, 126, 234, 0.5);
    box-shadow: 0 15px 30px rgba(102, 126, 234, 0.2);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: white;
}

.stat-content .number {
    display: block;
    font-size: 2.5rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 0.5rem;
}

.stat-content .label {
    font-size: 1rem;
    color: #b8c5d6;
    font-weight: 500;
}

.stat-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card-modern:hover .stat-glow {
    opacity: 1;
}

/* CTA Section */
.cta-modern {
    padding: 100px 0;
    background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%);
    position: relative;
    overflow: hidden;
}

.cta-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.cta-particles {
    position: absolute;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(2px 2px at 20px 30px, rgba(102, 126, 234, 0.3), transparent),
        radial-gradient(2px 2px at 40px 70px, rgba(118, 75, 162, 0.3), transparent),
        radial-gradient(1px 1px at 90px 40px, rgba(102, 126, 234, 0.5), transparent),
        radial-gradient(1px 1px at 130px 80px, rgba(118, 75, 162, 0.5), transparent);
    background-repeat: repeat;
    background-size: 150px 100px;
    animation: particles 20s linear infinite;
}

@keyframes particles {
    0% { transform: translate(0, 0); }
    100% { transform: translate(-150px, -100px); }
}

.cta-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 1.5rem;
    position: relative;
    z-index: 2;
}

.cta-subtitle {
    font-size: 1.1rem;
    color: #b8c5d6;
    margin-bottom: 2.5rem;
    position: relative;
    z-index: 2;
}

.cta-buttons {
    position: relative;
    z-index: 2;
}

/* Buttons */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50px;
    padding: 12px 30px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
}

.btn-outline-light {
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50px;
    padding: 10px 28px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    background: transparent;
    color: #ffffff;
}

.btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
    color: #ffffff;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .section-title {
        font-size: 2rem;
    }
    
    .cta-title {
        font-size: 2rem;
    }
    
    .hero-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .cta-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .book-stack {
        transform: scale(0.8);
    }
}
</style>
@endpush

@push('page-js')
@vite('resources/js/pages/index.js')
@endpush

@section('scripts')
@parent
@endsection