// Global variables
let currentBookId = null;
let cartItems = JSON.parse(localStorage.getItem("cartItems") || "[]");

// Sample book data - replace with actual API call
const books = {
    1: {
        title: "1984",
        author: "George Orwell",
        genre: "Ficção Científica",
        pages: "328",
        cover: "https://a-static.mlcdn.com.br/1500x1500/livro-1984-george-orwell/magazineluiza/231307900/995af0bbe8b5843a15f74d89ff7e84e3.jpg",
        description:
            "Um romance distópico que retrata uma sociedade totalitária onde o governo controla todos os aspectos da vida dos cidadãos.",
    },
    2: {
        title: "O Hobbit",
        author: "J.R.R. Tolkien",
        genre: "Fantasia",
        pages: "310",
        cover: "https://img.bertrand.pt/images/o-hobbit-j-r-r-tolkien/NDV8MjcyNTM0Nzl8MjM1ODkzODF8MTY1ODkxMzI4MjAwMHx3ZWJw/250x",
        description:
            "A jornada de Bilbo Bolseiro em uma aventura inesperada com anões em busca do tesouro guardado pelo dragão Smaug.",
    },
    // Add more books as needed
};

// Carousel functionality
function initializeCarousel() {
    const scrollLeftBtn = document.getElementById("scrollLeft");
    const scrollRightBtn = document.getElementById("scrollRight");
    const carousel = document.getElementById("carousel");

    if (scrollLeftBtn && carousel) {
        scrollLeftBtn.addEventListener("click", () => {
            carousel.scrollBy({
                left: -200,
                behavior: "smooth",
            });
        });
    }

    if (scrollRightBtn && carousel) {
        scrollRightBtn.addEventListener("click", () => {
            carousel.scrollBy({
                left: 200,
                behavior: "smooth",
            });
        });
    }
}

// Book modal functionality
function showBookModal(bookId) {
    currentBookId = bookId;

    const book = books[bookId] || books[1];

    const bookCover = document.getElementById("bookCover");
    const bookTitle = document.getElementById("bookTitle");
    const bookAuthor = document.getElementById("bookAuthor");
    const bookGenre = document.getElementById("bookGenre");
    const bookPages = document.getElementById("bookPages");
    const bookDescription = document.getElementById("bookDescription");
    const bookModal = document.getElementById("bookModal");

    if (bookCover) bookCover.src = book.cover;
    if (bookTitle) bookTitle.textContent = book.title;
    if (bookAuthor) bookAuthor.textContent = book.author;
    if (bookGenre) bookGenre.textContent = book.genre;
    if (bookPages) bookPages.textContent = book.pages;
    if (bookDescription) bookDescription.textContent = book.description;

    if (bookModal) bookModal.style.display = "block";
}

function closeBookModal() {
    const bookModal = document.getElementById("bookModal");
    if (bookModal) {
        bookModal.style.display = "none";
    }
}

function reserveBook() {
    if (!currentBookId) return;

    const book = getCurrentBookData();
    addToCart(currentBookId, book.title, book.author, book.cover);
    closeBookModal();
}

function getCurrentBookData() {
    const bookTitle = document.getElementById("bookTitle");
    const bookAuthor = document.getElementById("bookAuthor");
    const bookCover = document.getElementById("bookCover");

    return {
        title: bookTitle ? bookTitle.textContent : "",
        author: bookAuthor ? bookAuthor.textContent : "",
        cover: bookCover ? bookCover.src : "",
    };
}

// Cart functionality
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

function updateCartUI() {
    const cartItemsContainer = document.getElementById("cartItems");
    const cartCount = document.querySelector(".cart-count");

    if (cartItemsContainer) {
        if (cartItems.length === 0) {
            cartItemsContainer.innerHTML =
                '<p class="empty-cart">Nenhum livro reservado ainda.</p>';
        } else {
            cartItemsContainer.innerHTML = cartItems
                .map(
                    (item) => `
                <div class="cart-item">
                    <img src="${item.foto}" alt="${item.titulo}">
                    <div>
                        <strong>${item.titulo}</strong><br>
                        <small>${item.autor}</small>
                    </div>
                    <button onclick="removeFromCart(${item.id})" style="margin-left: auto; background: #e74c3c; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer;">&times;</button>
                </div>
            `
                )
                .join("");
        }
    }

    if (cartCount) {
        cartCount.textContent = cartItems.length;
        cartCount.style.display = cartItems.length > 0 ? "block" : "none";
    }
}

function toggleCartPanel() {
    const panel = document.getElementById("cartPanel");
    if (panel) {
        panel.classList.toggle("open");
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
        showNotification("Erro de segurança. Recarregue a página.");
        return;
    }

    // Note: This route needs to be passed from the Blade template
    const reservationRoute = window.reservationStoreRoute || "/reservations";

    fetch(reservationRoute, {
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
            } else {
                throw new Error("Erro na reserva");
            }
        })
        .catch((err) => {
            console.error("Erro:", err);
            showNotification("Erro ao finalizar reserva. Tente novamente.");
        });
}

function filterByCategory(category) {
    // Note: This route needs to be passed from the Blade template
    const booksIndexRoute = window.booksIndexRoute || "/books";
    window.location.href = `${booksIndexRoute}?categoria=${category}`;
}

function showNotification(message) {
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

// Event listeners
function initializeEventListeners() {
    // Close modal when clicking outside
    window.onclick = function (event) {
        const modal = document.getElementById("bookModal");
        if (event.target === modal) {
            closeBookModal();
        }
    };
}

// Initialize everything when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
    initializeCarousel();
    initializeEventListeners();
    updateCartUI();
});

// Make functions globally available
window.showBookModal = showBookModal;
window.closeBookModal = closeBookModal;
window.reserveBook = reserveBook;
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.toggleCartPanel = toggleCartPanel;
window.finalizeReservation = finalizeReservation;
window.filterByCategory = filterByCategory;
