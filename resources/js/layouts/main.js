// Main Layout JavaScript

// Search functionality
document.getElementById("searchBtn")?.addEventListener("click", function () {
    const container = document.getElementById("searchContainer");
    container.classList.toggle("hidden");
    if (!container.classList.contains("hidden")) {
        document.getElementById("searchInput").focus();
    }
});

// Categories dropdown
document
    .getElementById("categoriesBtn")
    ?.addEventListener("click", function () {
        document
            .getElementById("categoriesDropdown")
            .classList.toggle("hidden");
    });

// Close dropdowns when clicking outside
document.addEventListener("click", function (e) {
    if (!e.target.closest(".has-search")) {
        document.getElementById("searchContainer")?.classList.add("hidden");
    }
    if (!e.target.closest(".has-dropdown")) {
        document.getElementById("categoriesDropdown")?.classList.add("hidden");
    }
});
