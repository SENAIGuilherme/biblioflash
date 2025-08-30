/* Main application JavaScript - migrated from OLD/assets/principal.js */

// Search functionality
const searchBtn = document.getElementById("searchBtn");
const searchContainer = document.getElementById("searchContainer");
const searchForm = document.getElementById("searchForm");
const searchInput = document.getElementById("searchInput");

// Categories functionality
const categoriesBtn = document.getElementById("categoriesBtn");
const categoriesDropdown = document.getElementById("categoriesDropdown");

// Show/hide search bar
if (searchBtn) {
    searchBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        searchContainer.classList.toggle("hidden");
        if (categoriesDropdown) {
            categoriesDropdown.classList.add("hidden"); // close categories
        }
        if (searchInput) {
            searchInput.focus();
        }
    });
}

// Show/hide categories
if (categoriesBtn) {
    categoriesBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        categoriesDropdown.classList.toggle("hidden");
        if (searchContainer) {
            searchContainer.classList.add("hidden"); // close search
        }
    });
}

// Search form submission
if (searchForm) {
    searchForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const query = searchInput.value.trim();
        if (query) {
            window.location.href = `/buscar?query=${encodeURIComponent(query)}`;
        }
    });
}

// Click outside closes both
document.addEventListener("click", () => {
    if (searchContainer) {
        searchContainer.classList.add("hidden");
    }
    if (categoriesDropdown) {
        categoriesDropdown.classList.add("hidden");
    }
});

// Carousel functionality
const carousel = document.getElementById("carousel");
const scrollLeftBtn = document.getElementById("scrollLeft");
const scrollRightBtn = document.getElementById("scrollRight");

if (scrollLeftBtn && carousel) {
    scrollLeftBtn.addEventListener("click", () => {
        carousel.scrollBy({ left: -200, behavior: "smooth" });
    });
}

if (scrollRightBtn && carousel) {
    scrollRightBtn.addEventListener("click", () => {
        carousel.scrollBy({ left: 200, behavior: "smooth" });
    });
}

// Book data
const bookData = {
    "Harry Potter": {
        title: "Harry Potter",
        author: "J.K. Rowling",
        genre: "Fantasia, Aventura",
        pages: "320",
        description:
            "O jovem bruxo Harry Potter embarca em uma jornada mágica em Hogwarts.",
        image: "/imagens/livros/harry.png",
    },
    "O Hobbit": {
        title: "O Hobbit",
        author: "J.R.R. Tolkien",
        genre: "Fantasia, Aventura",
        pages: "310",
        description:
            "Bilbo Bolseiro embarca em uma aventura épica para recuperar o tesouro guardado por um dragão.",
        image: "https://img.bertrand.pt/images/o-hobbit-j-r-r-tolkien/NDV8MjcyNTM0Nzl8MjM1ODkzODF8MTY1ODkxMzI4MjAwMHx3ZWJw/250x",
    },
    Sapiens: {
        title: "Sapiens",
        author: "Yuval Noah Harari",
        genre: "História, Antropologia",
        pages: "464",
        description:
            "Uma jornada pela história da humanidade, desde os primórdios até os dias atuais.",
        image: "https://m.media-amazon.com/images/I/71-ghLb8qML.jpg",
    },
    Mindset: {
        title: "Mindset",
        author: "Carol S. Dweck",
        genre: "Psicologia, Autoajuda",
        pages: "320",
        description:
            "Descubra como a mentalidade influencia o sucesso pessoal e profissional.",
        image: "https://m.media-amazon.com/images/I/71Ils+Co9fL.jpg",
    },
    "Dom Casmurro": {
        title: "Dom Casmurro",
        author: "Machado de Assis",
        genre: "Romance Clássico",
        pages: "256",
        description:
            "Bentinho relembra sua juventude e o suposto adultério de Capitu, sua esposa.",
        image: "https://m.media-amazon.com/images/I/61Z2bMhGicL._AC_UF1000,1000_QL80_.jpg",
    },
    "Machado de Assis": {
        title: "Machado de Assis",
        author: "Machado de Assis",
        genre: "Ficção, Literatura Brasileira",
        pages: "300",
        description:
            "Coletânea com obras marcantes de um dos maiores autores brasileiros.",
        image: "https://m.media-amazon.com/images/I/81VHY140rLL._UF894,1000_QL80_.jpg",
    },
    "Orgulho e Preconceito": {
        title: "Orgulho e Preconceito",
        author: "Jane Austen",
        genre: "Romance de Época",
        pages: "416",
        description:
            "A clássica história de amor e mal-entendidos entre Elizabeth Bennet e Mr. Darcy.",
        image: "https://m.media-amazon.com/images/I/719esIW3D7L.jpg",
    },
};

// Book modal functions
function openBookModal(bookTitle) {
    const book = bookData[bookTitle];
    if (book) {
        // Implementation for opening book modal
        console.log("Opening book modal for:", book.title);
    }
}

function closeBookModal() {
    // Implementation for closing book modal
    console.log("Closing book modal");
}

function readBook() {
    // Implementation for reading book
    console.log("Opening book reader");
}

// Cart functionality
function toggleCartPanel() {
    const cartPanel = document.getElementById("cartPanel");
    if (cartPanel) {
        cartPanel.classList.toggle("open");
    }
}

function updateCartBadge() {
    const cartBadge = document.querySelector(".cart-badge");
    if (cartBadge) {
        const cartItems = JSON.parse(localStorage.getItem("cartItems") || "[]");
        cartBadge.textContent = cartItems.length;
        cartBadge.style.display = cartItems.length > 0 ? "block" : "none";
    }
}

// Initialize cart badge on page load
document.addEventListener("DOMContentLoaded", updateCartBadge);
window.addEventListener("storage", updateCartBadge);

// Cart icon event listener
const cartIcon = document.querySelector(".cart-icon");
if (cartIcon) {
    cartIcon.addEventListener("click", toggleCartPanel);
}

// Categories carousel
const scrollLeftCategorias = document.getElementById("scrollLeftCategorias");
const scrollRightCategorias = document.getElementById("scrollRightCategorias");
const categoriasCarousel = document.getElementById("categoriasCarousel");

if (scrollLeftCategorias && categoriasCarousel) {
    scrollLeftCategorias.addEventListener("click", function () {
        categoriasCarousel.scrollBy({
            left: -200,
            behavior: "smooth",
        });
    });
}

if (scrollRightCategorias && categoriasCarousel) {
    scrollRightCategorias.addEventListener("click", function () {
        categoriasCarousel.scrollBy({
            left: 200,
            behavior: "smooth",
        });
    });
}

// Export functions for global access
window.openBookModal = openBookModal;
window.closeBookModal = closeBookModal;
window.readBook = readBook;
window.toggleCartPanel = toggleCartPanel;
window.updateCartBadge = updateCartBadge;
