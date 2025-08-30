// Animate numbers on scroll
function animateNumbers() {
    const numbers = document.querySelectorAll(".stat-item .number");

    numbers.forEach((number) => {
        const finalNumber = parseInt(number.textContent.replace(/\D/g, ""));
        const suffix = number.textContent.replace(/\d/g, "");
        let currentNumber = 0;
        const increment = finalNumber / 50;

        const timer = setInterval(() => {
            currentNumber += increment;
            if (currentNumber >= finalNumber) {
                number.textContent = finalNumber + suffix;
                clearInterval(timer);
            } else {
                number.textContent = Math.floor(currentNumber) + suffix;
            }
        }, 30);
    });
}

// Trigger animation when stats section is visible
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            animateNumbers();
            observer.unobserve(entry.target);
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const statsSection = document.querySelector(".stats-section");
    if (statsSection) {
        observer.observe(statsSection);
    }
});

// Make functions globally available if needed
window.animateNumbers = animateNumbers;
