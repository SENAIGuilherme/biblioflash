@extends('layouts.public')

@section('title', 'Biblioteca - BiblioFlash')

@push('page-css')
@vite('resources/css/pages/books.css')
@endpush

@section('content')
<div class="books-container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Descubra Seu Próximo Livro Favorito</h1>
            <p class="hero-subtitle">Explore nossa vasta coleção de livros e encontre histórias que vão transformar sua perspectiva de mundo</p>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number">{{ $books->count() ?? '1000+' }}</span>
                    <span class="stat-label">Livros</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">{{ $categories->count() ?? '25+' }}</span>
                    <span class="stat-label">Categorias</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Leitores</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="section">
        <div class="section-header">
            <h2 class="section-title">Explore por Categoria</h2>
            <p class="section-subtitle">Encontre livros organizados por gênero e descubra novos mundos literários</p>
        </div>
        
        <div class="categories-grid">
            <div class="category-card" onclick="filterByCategory('Terror')">
                <div class="category-icon">👻</div>
                <h3 class="category-name">Terror</h3>
                <p class="category-count">Histórias de arrepiar</p>
            </div>
            
            <div class="category-card" onclick="filterByCategory('Fantasia')">
                <div class="category-icon">🧙‍♂️</div>
                <h3 class="category-name">Fantasia</h3>
                <p class="category-count">Mundos mágicos</p>
            </div>
            
            <div class="category-card" onclick="filterByCategory('Infantil')">
                <div class="category-icon">🧸</div>
                <h3 class="category-name">Infantil</h3>
                <p class="category-count">Para os pequenos</p>
            </div>
            
            <div class="category-card" onclick="filterByCategory('Romance')">
                <div class="category-icon">💕</div>
                <h3 class="category-name">Romance</h3>
                <p class="category-count">Histórias de amor</p>
            </div>
            
            <div class="category-card" onclick="filterByCategory('Religião')">
                <div class="category-icon">📿</div>
                <h3 class="category-name">Religião</h3>
                <p class="category-count">Espiritualidade</p>
            </div>
        </div>
    </section>

    <!-- Recommendations Section -->
    <section class="recommendations-section">
        <div class="section-header">
            <h2 class="section-title">Recomendados para Você</h2>
            <p class="section-subtitle">Livros selecionados especialmente baseados em suas preferências de leitura</p>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-wrapper">
                <button class="carousel-nav prev" id="scrollLeft">‹</button>
                <div class="carousel-track" id="carousel">
                    @forelse($recommendedBooks ?? [] as $book)
                    <div class="book-card" onclick="showBookModal({{ $book->id }})">
                        <img class="book-cover" src="{{ $book->foto ? asset('storage/' . $book->foto) : asset('imagens/livros/default.jpg') }}" alt="{{ $book->titulo }}">
                        <h4 class="book-title">{{ $book->titulo }}</h4>
                        <p class="book-author">{{ $book->autor ?? 'Autor Desconhecido' }}</p>
                    </div>
                    @empty
                    <div class="book-card" onclick="showBookModal(1)">
                        <img class="book-cover" src="https://a-static.mlcdn.com.br/1500x1500/livro-1984-george-orwell/magazineluiza/231307900/995af0bbe8b5843a15f74d89ff7e84e3.jpg" alt="1984">
                        <h4 class="book-title">1984</h4>
                        <p class="book-author">George Orwell</p>
                    </div>
                    <div class="book-card" onclick="showBookModal(2)">
                        <img class="book-cover" src="https://img.bertrand.pt/images/o-hobbit-j-r-r-tolkien/NDV8MjcyNTM0Nzl8MjM1ODkzODF8MTY1ODkxMzI4MjAwMHx3ZWJw/250x" alt="O Hobbit">
                        <h4 class="book-title">O Hobbit</h4>
                        <p class="book-author">J.R.R. Tolkien</p>
                    </div>
                    <div class="book-card" onclick="showBookModal(3)">
                        <img class="book-cover" src="https://m.media-amazon.com/images/I/71-ghLb8qML.jpg" alt="Sapiens">
                        <h4 class="book-title">Sapiens</h4>
                        <p class="book-author">Yuval Noah Harari</p>
                    </div>
                    <div class="book-card" onclick="showBookModal(4)">
                        <img class="book-cover" src="https://m.media-amazon.com/images/I/71Ils+Co9fL.jpg" alt="Mindset">
                        <h4 class="book-title">Mindset</h4>
                        <p class="book-author">Carol S. Dweck</p>
                    </div>
                    <div class="book-card" onclick="showBookModal(5)">
                        <img class="book-cover" src="https://m.media-amazon.com/images/I/61Z2bMhGicL._AC_UF1000,1000_QL80_.jpg" alt="Dom Casmurro">
                        <h4 class="book-title">Dom Casmurro</h4>
                        <p class="book-author">Machado de Assis</p>
                    </div>
                    <div class="book-card" onclick="showBookModal(6)">
                        <img class="book-cover" src="https://m.media-amazon.com/images/I/81VHY140rLL._UF894,1000_QL80_.jpg" alt="Memórias Póstumas">
                        <h4 class="book-title">Memórias Póstumas de Brás Cubas</h4>
                        <p class="book-author">Machado de Assis</p>
                    </div>
                    <div class="book-card" onclick="showBookModal(7)">
                        <img class="book-cover" src="https://m.media-amazon.com/images/I/719esIW3D7L.jpg" alt="Orgulho e Preconceito">
                        <h4 class="book-title">Orgulho e Preconceito</h4>
                        <p class="book-author">Jane Austen</p>
                    </div>
                    @endforelse
                </div>
                <button class="carousel-nav next" id="scrollRight">›</button>
            </div>
        </div>
    </section>

    <!-- Book Modal -->
    <div id="bookModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeBookModal()">&times;</span>
            <div class="modal-header">
                <img id="bookCover" src="" alt="Capa do Livro">
                <div class="book-info">
                    <h2 id="bookTitle"></h2>
                    <p><strong>Autor:</strong> <span id="bookAuthor"></span></p>
                    <p><strong>Gênero:</strong> <span id="bookGenre"></span></p>
                    <p><strong>Páginas:</strong> <span id="bookPages"></span></p>
                    @auth
                    <button class="reserve-btn" onclick="reserveBook()">Reservar Livro</button>
                    @else
                    <a href="{{ route('login') }}" class="reserve-btn">Fazer Login para Reservar</a>
                    @endauth
                </div>
            </div>
            <div class="modal-body">
                <h3>Sinopse</h3>
                <p id="bookDescription"></p>
            </div>
        </div>
    </div>

    <!-- Cart Panel -->
    <div id="cartPanel" class="cart-panel">
        <div class="cart-header">
            <h3>📚 Livros Reservados</h3>
            <span class="close-cart" onclick="toggleCartPanel()">&times;</span>
        </div>
        <div id="cartItems" class="cart-items">
            <p class="empty-cart">Nenhum livro reservado ainda.</p>
        </div>
        <button class="finalize-btn" onclick="finalizeReservation()">✨ Finalizar Reserva</button>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>📚 BiblioFlash</h3>
                <p>Sua biblioteca digital favorita</p>
                <div class="footer-stats">
                    <span>📖 10.000+ Livros</span>
                    <span>👥 5.000+ Leitores</span>
                </div>
            </div>
            <div class="footer-section">
                <h4>🔗 Links Úteis</h4>
                <ul>
                    <li><a href="{{ route('home') }}">🏠 Início</a></li>
                    <li><a href="{{ route('books.index') }}">📚 Livros</a></li>
                    <li><a href="{{ route('about') }}">ℹ️ Sobre</a></li>
                    <li><a href="{{ route('contact') }}">📞 Contato</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>🌐 Redes Sociais</h4>
                <div class="social-icons">
                    <a href="#" title="Facebook">📘</a>
                    <a href="#" title="Twitter">🐦</a>
                    <a href="#" title="Instagram">📷</a>
                    <a href="#" title="LinkedIn">💼</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2024 BiblioFlash. Todos os direitos reservados. ✨</p>
        </div>
    </footer>
@endsection

@push('page-js')
<script>
    // Pass Laravel routes to JavaScript
    window.booksIndexRoute = '{{ route("books.index") }}';
</script>
@vite('resources/js/pages/books-index.js')
@endpush