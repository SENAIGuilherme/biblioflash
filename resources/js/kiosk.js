/* totem/totem JavaScript - migrated from OLD/assets/totem.js and totem-verifica-reserva.js */

// Navigation function
function voltarPagina() {
    window.history.back();
}

// totem-specific functionality
class totemManager {
    constructor() {
        this.currentUser = null;
        this.sessionTimeout = 300000; // 5 minutes
        this.timeoutId = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.startSessionTimer();
        this.setupKeyboardNavigation();
    }

    setupEventListeners() {
        // CPF input formatting
        const cpfInput = document.getElementById("cpf");
        if (cpfInput) {
            cpfInput.addEventListener("input", this.formatCPF.bind(this));
        }

        // Form submission
        const loginForm = document.getElementById("totemLoginForm");
        if (loginForm) {
            loginForm.addEventListener("submit", this.handleLogin.bind(this));
        }

        // Back button
        const backBtn = document.querySelector(".btn-back");
        if (backBtn) {
            backBtn.addEventListener("click", voltarPagina);
        }

        // Reset session timer on user activity
        document.addEventListener("click", this.resetSessionTimer.bind(this));
        document.addEventListener(
            "keypress",
            this.resetSessionTimer.bind(this)
        );
    }

    setupKeyboardNavigation() {
        document.addEventListener("keydown", (e) => {
            switch (e.key) {
                case "Escape":
                    this.logout();
                    break;
                case "F1":
                    this.showHelp();
                    break;
                case "F5":
                    e.preventDefault();
                    this.refreshContent();
                    break;
            }
        });
    }

    formatCPF(event) {
        let value = event.target.value.replace(/\D/g, "");

        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, "$1.$2");
            value = value.replace(/(\d{3})(\d)/, "$1.$2");
            value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        }

        event.target.value = value;
    }

    async handleLogin(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const cpf = formData.get("cpf");
        const senha = formData.get("senha");

        if (!this.validateCPF(cpf)) {
            this.showError("CPF inválido. Por favor, verifique os dados.");
            return;
        }

        this.showLoading(true);

        try {
            const response = await fetch("/api/totem/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content"),
                },
                body: JSON.stringify({ cpf, senha }),
            });

            const data = await response.json();

            if (response.ok) {
                this.currentUser = data.user;
                this.showSuccess("Login realizado com sucesso!");
                setTimeout(() => {
                    window.location.href = "/totem/dashboard";
                }, 1500);
            } else {
                this.showError(data.message || "Erro ao fazer login.");
            }
        } catch (error) {
            this.showError("Erro de conexão. Tente novamente.");
        } finally {
            this.showLoading(false);
        }
    }

    validateCPF(cpf) {
        cpf = cpf.replace(/\D/g, "");

        if (cpf.length !== 11) return false;
        if (/^(\d)\1{10}$/.test(cpf)) return false;

        let sum = 0;
        for (let i = 0; i < 9; i++) {
            sum += parseInt(cpf.charAt(i)) * (10 - i);
        }
        let remainder = (sum * 10) % 11;
        if (remainder === 10 || remainder === 11) remainder = 0;
        if (remainder !== parseInt(cpf.charAt(9))) return false;

        sum = 0;
        for (let i = 0; i < 10; i++) {
            sum += parseInt(cpf.charAt(i)) * (11 - i);
        }
        remainder = (sum * 10) % 11;
        if (remainder === 10 || remainder === 11) remainder = 0;
        if (remainder !== parseInt(cpf.charAt(10))) return false;

        return true;
    }

    startSessionTimer() {
        this.timeoutId = setTimeout(() => {
            this.showSessionExpired();
        }, this.sessionTimeout);
    }

    resetSessionTimer() {
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
        }
        this.startSessionTimer();
    }

    showSessionExpired() {
        this.showError(
            "Sessão expirada. Redirecionando para a tela inicial..."
        );
        setTimeout(() => {
            window.location.href = "/totem";
        }, 3000);
    }

    logout() {
        this.currentUser = null;
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
        }
        window.location.href = "/totem";
    }

    showHelp() {
        const helpModal = document.getElementById("helpModal");
        if (helpModal) {
            helpModal.style.display = "flex";
        } else {
            alert(
                "Ajuda:\n\nF1 - Mostrar ajuda\nF5 - Atualizar\nESC - Sair\n\nPara mais informações, procure um funcionário."
            );
        }
    }

    refreshContent() {
        window.location.reload();
    }

    showLoading(show) {
        const loader = document.getElementById("loadingSpinner");
        if (loader) {
            loader.style.display = show ? "flex" : "none";
        }
    }

    showError(message) {
        this.showAlert(message, "error");
    }

    showSuccess(message) {
        this.showAlert(message, "success");
    }

    showAlert(message, type = "info") {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll(".totem-alert");
        existingAlerts.forEach((alert) => alert.remove());

        const alert = document.createElement("div");
        alert.className = `totem-alert totem-alert-${type}`;
        alert.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: ${
                type === "error"
                    ? "#dc3545"
                    : type === "success"
                    ? "#28a745"
                    : "#007bff"
            };
            color: white;
            padding: 30px 40px;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: bold;
            z-index: 1000;
            text-align: center;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            max-width: 80%;
        `;
        alert.textContent = message;

        document.body.appendChild(alert);

        // Auto remove after delay
        setTimeout(
            () => {
                alert.remove();
            },
            type === "error" ? 5000 : 3000
        );
    }
}

// Reservation verification functionality
class ReservationVerifier {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
    }

    setupEventListeners() {
        const verifyForm = document.getElementById("verifyReservationForm");
        if (verifyForm) {
            verifyForm.addEventListener(
                "submit",
                this.handleVerification.bind(this)
            );
        }
    }

    async handleVerification(event) {
        event.preventDefault();

        const formData = new FormData(event.target);
        const reservationCode = formData.get("reservation_code");

        if (!reservationCode || reservationCode.trim().length < 6) {
            this.showError("Código de reserva inválido.");
            return;
        }

        this.showLoading(true);

        try {
            const response = await fetch("/api/totem/verify-reservation", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute("content"),
                },
                body: JSON.stringify({ code: reservationCode }),
            });

            const data = await response.json();

            if (response.ok) {
                this.displayReservationDetails(data.reservation);
            } else {
                this.showError(data.message || "Reserva não encontrada.");
            }
        } catch (error) {
            this.showError("Erro de conexão. Tente novamente.");
        } finally {
            this.showLoading(false);
        }
    }

    displayReservationDetails(reservation) {
        const detailsContainer = document.getElementById("reservationDetails");
        if (detailsContainer) {
            detailsContainer.innerHTML = `
                <div class="reservation-card">
                    <h3>Detalhes da Reserva</h3>
                    <p><strong>Código:</strong> ${reservation.code}</p>
                    <p><strong>Livro:</strong> ${reservation.book_title}</p>
                    <p><strong>Data da Reserva:</strong> ${reservation.created_at}</p>
                    <p><strong>Status:</strong> ${reservation.status}</p>
                    <p><strong>Válida até:</strong> ${reservation.expires_at}</p>
                </div>
            `;
            detailsContainer.style.display = "block";
        }
    }

    showLoading(show) {
        const loader = document.getElementById("loadingSpinner");
        if (loader) {
            loader.style.display = show ? "flex" : "none";
        }
    }

    showError(message) {
        const alert = document.createElement("div");
        alert.className = "totem-alert totem-alert-error";
        alert.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            font-weight: bold;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        `;
        alert.textContent = message;

        document.body.appendChild(alert);

        setTimeout(() => {
            alert.remove();
        }, 5000);
    }
}

// Initialize on DOM load
document.addEventListener("DOMContentLoaded", function () {
    // Initialize totem manager if on totem pages
    if (document.body.classList.contains("totem-page")) {
        window.totemManager = new totemManager();
    }

    // Initialize reservation verifier if on verification page
    if (document.getElementById("verifyReservationForm")) {
        window.reservationVerifier = new ReservationVerifier();
    }
});

// Export functions for global access
window.voltarPagina = voltarPagina;
window.totemManager = totemManager;
window.ReservationVerifier = ReservationVerifier;
