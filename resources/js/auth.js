/* Authentication JavaScript - migrated from OLD/assets/login.js */

// Modal functionality
let modal = document.getElementById("loginModal");
let btn = document.querySelector(".btn-login");
let span = document.getElementsByClassName("close")[0];

// Ensure modal is hidden on page load
window.onload = function () {
    if (modal) {
        modal.style.display = "none";
    }
};

// Open modal when button is clicked
if (btn && modal) {
    btn.onclick = function () {
        modal.style.display = "flex";
    };
}

// Close modal when X is clicked
if (span && modal) {
    span.onclick = function () {
        modal.style.display = "none";
    };
}

// Close modal when clicking outside
window.onclick = function (event) {
    if (modal && event.target == modal) {
        modal.style.display = "none";
    }
};

// Card flip functionality
function girarCard() {
    const card = document.getElementById("card-wrapper");
    if (card) {
        card.classList.toggle("girar");
    }
}

// User registration function
function cadastrarUsuario() {
    // Show success message
    const msg = document.createElement("div");
    msg.className = "mensagem-sucesso";
    msg.innerText = "Cadastro efetuado com sucesso! BEM-VINDO à BIBLIOFLASH.";

    document.body.appendChild(msg);

    // Redirect after 2 seconds
    setTimeout(() => {
        window.location.href = "/home";
    }, 2000);
}

// Form validation
function validateLoginForm() {
    const email = document.getElementById("loginEmail");
    const password = document.getElementById("loginPassword");

    if (!email || !password) {
        return false;
    }

    if (email.value.trim() === "") {
        showError("Por favor, insira seu email.");
        return false;
    }

    if (password.value.trim() === "") {
        showError("Por favor, insira sua senha.");
        return false;
    }

    return true;
}

function validateRegisterForm() {
    const email = document.getElementById("registerEmail");
    const password = document.getElementById("registerPassword");
    const confirmPassword = document.getElementById("confirmPassword");

    if (!email || !password || !confirmPassword) {
        return false;
    }

    if (email.value.trim() === "") {
        showError("Por favor, insira seu email.");
        return false;
    }

    if (password.value.trim() === "") {
        showError("Por favor, insira sua senha.");
        return false;
    }

    if (password.value !== confirmPassword.value) {
        showError("As senhas não coincidem.");
        return false;
    }

    return true;
}

// Error display function
function showError(message) {
    // Remove existing error messages
    const existingErrors = document.querySelectorAll(".error-message");
    existingErrors.forEach((error) => error.remove());

    // Create new error message
    const errorDiv = document.createElement("div");
    errorDiv.className = "error-message";
    errorDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #dc3545;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 1002;
        font-weight: bold;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    `;
    errorDiv.textContent = message;

    document.body.appendChild(errorDiv);

    // Remove error after 5 seconds
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
}

// Success message function
function showSuccess(message) {
    const successDiv = document.createElement("div");
    successDiv.className = "success-message";
    successDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 1002;
        font-weight: bold;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    `;
    successDiv.textContent = message;

    document.body.appendChild(successDiv);

    // Remove success message after 3 seconds
    setTimeout(() => {
        successDiv.remove();
    }, 3000);
}

// Form submission handlers
document.addEventListener("DOMContentLoaded", function () {
    // Login form - allow normal submission
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            if (!validateLoginForm()) {
                e.preventDefault();
            }
            // Allow form to submit normally if validation passes
        });
    }

    // Register form - allow normal submission
    const registerForm = document.getElementById("registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", function (e) {
            if (!validateRegisterForm()) {
                e.preventDefault();
            }
            // Allow form to submit normally if validation passes
        });
    }

    // Real-time password confirmation validation
    const confirmPassword = document.getElementById("confirmPassword");
    const password = document.getElementById("registerPassword");

    if (confirmPassword && password) {
        confirmPassword.addEventListener("input", function () {
            if (this.value !== password.value) {
                this.setCustomValidity("As senhas não coincidem");
            } else {
                this.setCustomValidity("");
            }
        });
    }

    // Enter key handling
    document.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            const activeForm = document.querySelector("form:not(.hidden)");
            if (activeForm) {
                const submitBtn = activeForm.querySelector(
                    'button[type="submit"], input[type="submit"]'
                );
                if (submitBtn) {
                    submitBtn.click();
                }
            }
        }
    });
});

// Export functions for global access
window.girarCard = girarCard;
window.cadastrarUsuario = cadastrarUsuario;
window.validateLoginForm = validateLoginForm;
window.validateRegisterForm = validateRegisterForm;
window.showError = showError;
window.showSuccess = showSuccess;
