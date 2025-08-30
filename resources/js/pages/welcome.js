// Card flip functionality
function girarCard() {
    const cardWrapper = document.getElementById("card-wrapper");
    cardWrapper.classList.toggle("flipped");
}

// Initialize page functionality
document.addEventListener("DOMContentLoaded", function () {
    // Auto-focus no primeiro campo
    const emailField = document.getElementById("email");
    if (emailField) {
        emailField.focus();
    }

    // Enter para submeter formulários
    document.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            const activeForm = document.querySelector(
                ".card-front:not(.card-wrapper.flipped .card-front) form, .card-wrapper.flipped .card-back form"
            );
            if (activeForm) {
                activeForm.submit();
            }
        }
    });

    // Validação em tempo real
    const senhaField = document.getElementById("senha_cadastro");
    const confirmSenhaField = document.getElementById("senha_confirmation");

    if (confirmSenhaField) {
        confirmSenhaField.addEventListener("input", function () {
            if (senhaField.value !== confirmSenhaField.value) {
                confirmSenhaField.setCustomValidity("As senhas não coincidem");
            } else {
                confirmSenhaField.setCustomValidity("");
            }
        });
    }
});

// Make functions globally available
window.girarCard = girarCard;
