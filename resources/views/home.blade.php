@extends('layouts.public')

@section('title', 'BiblioFlash - Biblioteca Digital')

@section('content')
<!-- Hero Section with Modern Dark Theme -->
<section class="hero-modern">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="container">
            <div class="hero-text">
                <h1 class="hero-title">
                    <span class="gradient-text">BiblioFlash</span>
                    <br>Sua Biblioteca Digital
                </h1>
                <p class="hero-subtitle">
                    Descubra milhares de livros incríveis e mergulhe em mundos extraordinários.
                    Sua próxima aventura literária começa aqui.
                </p>
                <div class="hero-actions">
                    @auth
                    <a href="{{ route('books.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-book-open"></i>
                        Explorar Biblioteca
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i>
                        Começar Agora
                    </a>
                    @endauth
                    <a href="#featured-books" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-star"></i>
                        Livros em Destaque
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Animated Background Elements -->
    <div class="floating-elements">
        <div class="floating-book"></div>
        <div class="floating-book"></div>
        <div class="floating-book"></div>
    </div>
</section>

<!-- Featured Books Carousel -->
<section id="featured-books" class="featured-carousel-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-fire"></i>
                Livros em Destaque
            </h2>
            <p class="section-subtitle">Os livros mais populares e aclamados da nossa biblioteca</p>
        </div>
        
        <div class="modern-carousel">
            <div class="carousel-container">
                <div class="carousel-track" id="featuredCarousel">
                    <!-- Slide 1 - Harry Potter -->
                    <div class="carousel-slide active">
                        <div class="book-showcase" data-book-id="1">
                            <div class="book-image">
                                <img src="https://m.media-amazon.com/images/I/81ibfYk4qmL.jpg" alt="Harry Potter" data-book-image="https://m.media-amazon.com/images/I/81ibfYk4qmL.jpg">
                                <div class="book-overlay">
                                    <div class="book-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <span>4.9</span>
                                    </div>
                                </div>
                            </div>
                            <div class="book-info">
                                <div class="book-category">Fantasia</div>
                                <h3 class="book-title" data-book-title="Harry Potter e a Pedra Filosofal">Harry Potter e a Pedra Filosofal</h3>
                                <p class="book-author" data-book-author="J.K. Rowling">J.K. Rowling</p>
                                <p class="book-description">
                                    Uma aventura mágica no mundo da feitiçaria, onde Harry descobre seu verdadeiro destino 
                                    enfrentando as forças do mal em Hogwarts.
                                </p>
                                <div class="book-actions">
                                    @auth
                                    <button class="btn btn-primary btn-reservar" data-book-id="1" onclick="addToCartFromButton(this)">Reservar Agora</button>
                                    @else
                                    <a href="{{ route('login') }}" class="btn btn-primary">Fazer Login</a>
                                    @endauth
                                    <button class="btn btn-outline-secondary">Ver Detalhes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Slide 2 - O Hobbit -->
                    <div class="carousel-slide">
                        <div class="book-showcase" data-book-id="2">
                            <div class="book-image">
                                <img src="https://upload.wikimedia.org/wikipedia/en/3/30/Hobbit_cover.JPG" alt="O Hobbit" data-book-image="https://upload.wikimedia.org/wikipedia/en/3/30/Hobbit_cover.JPG">
                                <div class="book-overlay">
                                    <div class="book-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <span>(4.8)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="book-info">
                                <div class="book-category">Aventura</div>
                                <h3 class="book-title" data-book-title="O Hobbit">O Hobbit</h3>
                                <p class="book-author" data-book-author="J.R.R. Tolkien">J.R.R. Tolkien</p>
                                <p class="book-description">
                                    Bilbo Bolseiro embarca numa jornada inesperada com anões em busca do tesouro 
                                    guardado pelo temível dragão Smaug.
                                </p>
                                <div class="book-actions">
                                    @auth
                                    <button class="btn btn-primary btn-reservar" data-book-id="2" onclick="addToCartFromButton(this)">Reservar Agora</button>
                                    @else
                                    <a href="{{ route('login') }}" class="btn btn-primary">Fazer Login</a>
                                    @endauth
                                    <button class="btn btn-outline-secondary">Ver Detalhes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Slide 3 - IT -->
                    <div class="carousel-slide">
                        <div class="book-showcase" data-book-id="3">
                            <div class="book-image">
                                <img src="https://m.media-amazon.com/images/I/91g9Dvtf+jL.jpg" alt="IT - A Coisa" data-book-image="https://m.media-amazon.com/images/I/91g9Dvtf+jL.jpg">
                                <div class="book-overlay">
                                    <div class="book-rating">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                        <span>(4.2)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="book-info">
                                <div class="book-category">Terror</div>
                                <h3 class="book-title" data-book-title="IT - A Coisa">IT - A Coisa</h3>
                                <p class="book-author" data-book-author="Stephen King">Stephen King</p>
                                <p class="book-description">
                                    Um grupo de amigos enfrenta uma criatura que aparece como palhaço e aterroriza sua cidade. 
                                    Eles lutam contra ela na infância e voltam anos depois para vencê-la de vez.
                                </p>
                                <div class="book-actions">
                                    @auth
                                    <button class="btn btn-primary btn-reservar" data-book-id="3" onclick="addToCartFromButton(this)">Reservar Agora</button>
                                    @else
                                    <a href="{{ route('login') }}" class="btn btn-primary">Fazer Login</a>
                                    @endauth
                                    <button class="btn btn-outline-secondary">Ver Detalhes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Carousel Controls -->
                <button class="carousel-btn carousel-prev" onclick="changeSlide(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-btn carousel-next" onclick="changeSlide(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
                
                <!-- Carousel Indicators -->
                <div class="carousel-indicators">
                    <button class="indicator active" onclick="goToSlide(0)"></button>
                    <button class="indicator" onclick="goToSlide(1)"></button>
                    <button class="indicator" onclick="goToSlide(2)"></button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-th-large"></i>
                Explore por Categoria
            </h2>
            <p class="section-subtitle">Encontre o gênero perfeito para sua próxima leitura</p>
        </div>
        
        <div class="categories-grid">
            <a href="{{ route('books.index') }}" class="category-card featured">
                <div class="category-image">
                    <img src="{{ asset('imagens/livros/todas.jpg') }}" alt="Todas as Categorias">
                    <div class="category-overlay">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
                <div class="category-info">
                    <h3>Todas as Categorias</h3>
                    <p>Explore nossa biblioteca completa</p>
                    <span class="category-count">1000+ livros</span>
                </div>
            </a>
            
            <a href="{{ route('books.index', ['categoria' => 'Terror']) }}" class="category-card">
                <div class="category-image">
                    <img src="{{ asset('imagens/livros/terror2.jpeg') }}" alt="Terror">
                    <div class="category-overlay">
                        <i class="fas fa-ghost"></i>
                    </div>
                </div>
                <div class="category-info">
                    <h3>Terror</h3>
                    <p>Histórias que arrepiam</p>
                    <span class="category-count">85+ livros</span>
                </div>
            </a>
            
            <a href="{{ route('books.index', ['categoria' => 'Fantasia']) }}" class="category-card">
                <div class="category-image">
                    <img src="{{ asset('imagens/livros/fantasia2.jpeg') }}" alt="Fantasia">
                    <div class="category-overlay">
                        <i class="fas fa-magic"></i>
                    </div>
                </div>
                <div class="category-info">
                    <h3>Fantasia</h3>
                    <p>Mundos mágicos e épicos</p>
                    <span class="category-count">120+ livros</span>
                </div>
            </a>
            
            <a href="{{ route('books.index', ['categoria' => 'Romance']) }}" class="category-card">
                <div class="category-image">
                    <img src="{{ asset('imagens/livros/romance2.jpeg') }}" alt="Romance">
                    <div class="category-overlay">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>
                <div class="category-info">
                    <h3>Romance</h3>
                    <p>Histórias de amor emocionantes</p>
                    <span class="category-count">95+ livros</span>
                </div>
            </a>
            
            <a href="{{ route('books.index', ['categoria' => 'Aventura']) }}" class="category-card">
                <div class="category-image">
                    <img src="{{ asset('imagens/livros/aventura2.jpeg') }}" alt="Aventura">
                    <div class="category-overlay">
                        <i class="fas fa-compass"></i>
                    </div>
                </div>
                <div class="category-info">
                    <h3>Aventura</h3>
                    <p>Jornadas épicas e emocionantes</p>
                    <span class="category-count">110+ livros</span>
                </div>
            </a>
            
            <a href="{{ route('books.index', ['categoria' => 'História']) }}" class="category-card">
                <div class="category-image">
                    <img src="{{ asset('imagens/livros/historia2.jpeg') }}" alt="História">
                    <div class="category-overlay">
                        <i class="fas fa-landmark"></i>
                    </div>
                </div>
                <div class="category-info">
                    <h3>História</h3>
                    <p>Conhecimento do passado</p>
                    <span class="category-count">75+ livros</span>
                </div>
            </a>
            
            <a href="{{ route('books.index', ['categoria' => 'Biografia']) }}" class="category-card">
                <div class="category-image">
                    <img src="{{ asset('imagens/livros/biografia2.jpeg') }}" alt="Biografia">
                    <div class="category-overlay">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="category-info">
                    <h3>Biografia</h3>
                    <p>Vidas extraordinárias</p>
                    <span class="category-count">60+ livros</span>
                </div>
            </a>
            
            <a href="{{ route('books.index', ['categoria' => 'Autoajuda']) }}" class="category-card">
                <div class="category-image">
                    <img src="{{ asset('imagens/livros/autoajuda2.jpeg') }}" alt="Autoajuda">
                    <div class="category-overlay">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                </div>
                <div class="category-info">
                    <h3>Autoajuda</h3>
                    <p>Desenvolvimento pessoal</p>
                    <span class="category-count">90+ livros</span>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Popular Books Section -->
@if(isset($books) && $books->count() > 0)
<section class="popular-books-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">
                <i class="fas fa-trophy"></i>
                Livros Populares
            </h2>
            <p class="section-subtitle">Os mais reservados pelos nossos usuários</p>
        </div>
        
        <div class="books-grid-modern">
            @foreach($books->take(8) as $book)
            <div class="book-card-modern" onclick="showBookDetails({{ $book->id }})">
                <div class="book-cover">
                    <img src="{{ $book->foto ? asset('storage/' . $book->foto) : asset('imagens/livros/default.jpg') }}" alt="{{ $book->titulo }}">
                    <div class="book-hover-overlay">
                        <div class="book-actions-overlay">
                            @auth
                            <button class="btn btn-primary btn-sm btn-reservar" data-book-id="{{ $book->id }}" onclick="event.stopPropagation(); addToCartFromButton(this)">Reservar</button>
                            @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Login</a>
                            @endauth
                            <button class="btn btn-outline-secondary btn-sm">Detalhes</button>
                        </div>
                    </div>
                </div>
                <div class="book-details">
                    <div class="book-category-tag">{{ $book->categoria }}</div>
                    <h4 class="book-title">{{ Str::limit($book->titulo, 30) }}</h4>
                    <p class="book-author">{{ $book->autor }}</p>
                    <div class="book-rating">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= ($book->avaliacao_media ?? 4))
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                        <span>({{ $book->avaliacao_media ?? '4.0' }})</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('books.index') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-arrow-right"></i>
                Ver Todos os Livros
            </a>
        </div>
    </div>
</section>
@endif

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-number">1000+</h3>
                    <p class="stat-label">Livros Disponíveis</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-number">5000+</h3>
                    <p class="stat-label">Usuários Ativos</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-download"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-number">15000+</h3>
                    <p class="stat-label">Empréstimos Realizados</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-info">
                    <h3 class="stat-number">4.8</h3>
                    <p class="stat-label">Avaliação Média</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('page-js')
@vite('resources/js/pages/home.js')
@endpush