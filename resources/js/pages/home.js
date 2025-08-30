// Home page specific JavaScript - Modern carousel and interactions

// Modern Carousel functionality
let currentSlide = 0;
let slides, totalSlides, indicators;
let autoSlideInterval;

function initializeCarousel() {
    slides = document.querySelectorAll(".carousel-slide");
    indicators = document.querySelectorAll(".indicator");
    totalSlides = slides.length;

    if (totalSlides > 0) {
        // Start auto-advance carousel
        startAutoSlide();
        
        // Pause auto-slide on hover
        const carouselContainer = document.querySelector('.carousel-container');
        if (carouselContainer) {
            carouselContainer.addEventListener('mouseenter', stopAutoSlide);
            carouselContainer.addEventListener('mouseleave', startAutoSlide);
        }
    }
}

function startAutoSlide() {
    stopAutoSlide();
    autoSlideInterval = setInterval(() => {
        changeSlide(1);
    }, 8000);
}

function stopAutoSlide() {
    if (autoSlideInterval) {
        clearInterval(autoSlideInterval);
    }
}

function updateSlide() {
    // Hide all slides
    slides.forEach(slide => {
        slide.classList.remove('active');
    });
    
    // Show current slide
    if (slides[currentSlide]) {
        slides[currentSlide].classList.add('active');
    }
    
    // Update indicators
    indicators.forEach((indicator, index) => {
        indicator.classList.toggle('active', index === currentSlide);
    });
}

function changeSlide(direction) {
    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
    updateSlide();
}

function goToSlide(slideIndex) {
    currentSlide = slideIndex;
    updateSlide();
    stopAutoSlide();
    startAutoSlide();
}

// Categories scroll
function scrollCategories(direction) {
    const container = document.getElementById("carouselCategorias");
    if (container) {
        const scrollAmount = 200;
        container.scrollBy({
            left: direction * scrollAmount,
            behavior: "smooth",
        });
    }
}

// Cart functionality
let cartItems = JSON.parse(localStorage.getItem("cartItems") || "[]");

function updateCartUI() {
    const cartItemsContainer = document.getElementById("cartItems");
    const cartCount = document.getElementById("cartCount");
    const floatingCartCount = document.querySelector(".cart-count");

    if (cartItemsContainer) {
        if (cartItems.length === 0) {
            cartItemsContainer.innerHTML = `
                <div class="empty-cart">
                    <i class="fas fa-book-open"></i>
                    <p>Nenhum livro reservado ainda.</p>
                    <small>Adicione livros à sua reserva para começar!</small>
                </div>
            `;
        } else {
            cartItemsContainer.innerHTML = cartItems
                .map(
                    (item) => `
                <div class="cart-item-modern">
                    <div class="cart-item-image">
                        <img src="${
                        item.foto
                            ? "/storage/" + item.foto
                            : "/imagens/livros/default.jpg"
                    }" alt="${item.titulo}">
                    </div>
                    <div class="cart-item-info">
                        <h5>${item.titulo}</h5>
                        <p>${item.autor}</p>
                    </div>
                    <button onclick="removeFromCart(${
                        item.id
                    })" class="cart-remove-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `
                )
                .join("");
        }
    }

    // Update cart count in multiple places
    const count = cartItems.length;
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.style.display = count > 0 ? "flex" : "none";
    }
    if (floatingCartCount) {
        floatingCartCount.textContent = count;
        floatingCartCount.style.display = count > 0 ? "flex" : "none";
    }
}

function addToCart(id, titulo, autor, foto) {
    const existingItem = cartItems.find((item) => item.id === id);
    if (!existingItem) {
        cartItems.push({ id, titulo, autor, foto });
        localStorage.setItem("cartItems", JSON.stringify(cartItems));
        updateCartUI();
        showNotification("Livro adicionado ao carrinho!");
    } else {
        showNotification("Este livro já está no carrinho!");
    }
}

function removeFromCart(id) {
    cartItems = cartItems.filter((item) => item.id !== id);
    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    updateCartUI();
}

function toggleCartPanel() {
    const panel = document.getElementById("cartPanel");
    if (panel) {
        panel.classList.toggle("open");
        
        // Add backdrop when cart is open
        if (panel.classList.contains('open')) {
            createCartBackdrop();
        } else {
            removeCartBackdrop();
        }
    }
}

function createCartBackdrop() {
    // Remove existing backdrop if any
    removeCartBackdrop();
    
    const backdrop = document.createElement('div');
    backdrop.className = 'cart-backdrop';
    backdrop.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
        backdrop-filter: blur(2px);
    `;
    backdrop.onclick = toggleCartPanel;
    document.body.appendChild(backdrop);
}

function removeCartBackdrop() {
    const backdrop = document.querySelector('.cart-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
}

function finalizeReservation() {
    if (cartItems.length === 0) {
        showNotification("Nenhum livro reservado para finalizar.");
        return;
    }

    const livrosIds = cartItems.map((livro) => livro.id);
    const csrfToken = document.querySelector('meta[name="csrf-token"]');

    if (!csrfToken) {
        showNotification("Erro: Token CSRF não encontrado.");
        return;
    }

    // Get the route URL from a data attribute or global variable
    const reservationUrl = window.reservationStoreUrl || "/reservations";

    fetch(reservationUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken.getAttribute("content"),
        },
        body: JSON.stringify({ livros: livrosIds }),
    })
        .then((response) => {
            if (response.ok) {
                localStorage.removeItem("cartItems");
                cartItems = [];
                updateCartUI();
                toggleCartPanel();
                showNotification(
                    "Reserva realizada com sucesso! Você tem 3 dias para retirar os livros."
                );
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                throw new Error("Erro na reserva");
            }
        })
        .catch((err) => {
            console.error("Erro:", err);
            showNotification("Erro ao finalizar reserva. Tente novamente.");
        });
}

function showNotification(message) {
    // Simple notification system
    const notification = document.createElement("div");
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #27ae60;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 10000;
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    `;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function showBookDetails(bookId) {
    // Redirect to book details page
    window.location.href = `/books/${bookId}`;
}

// Initialize everything on page load
document.addEventListener("DOMContentLoaded", function () {
    initializeCarousel();
    updateCartUI();

    // Set global variables for routes if they exist
    const routeElement = document.querySelector("[data-reservation-store-url]");
    if (routeElement) {
        window.reservationStoreUrl = routeElement.getAttribute(
            "data-reservation-store-url"
        );
    }
});

// Make functions globally available
window.changeSlide = changeSlide;
window.goToSlide = goToSlide;
window.scrollCategories = scrollCategories;
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.toggleCartPanel = toggleCartPanel;
window.finalizeReservation = finalizeReservation;
window.showBookDetails = showBookDetails;
window.createCartBackdrop = createCartBackdrop;
window.removeCartBackdrop = removeCartBackdrop;
