// Auth login page specific JavaScript - extracted from inline scripts

function girarCard() {
    const cardWrapper = document.getElementById("card-wrapper");
    if (cardWrapper) {
        cardWrapper.classList.toggle("flipped");
        
        // Add smooth transition effect
        const cardLogin = document.getElementById("card-login");
        if (cardLogin) {
            cardLogin.style.transform = cardWrapper.classList.contains("flipped") 
                ? "rotateY(180deg)" 
                : "rotateY(0deg)";
        }
    }
}

// CPF mask function
function applyCpfMask(input) {
    input.addEventListener("input", function (e) {
        let v = e.target.value.replace(/\D/g, "");
        if (v.length > 11) v = v.slice(0, 11);
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        e.target.value = v;
    });
}

// Phone mask function (Brazilian format)
function applyPhoneMask(input) {
    input.addEventListener("input", function (e) {
        let v = e.target.value.replace(/\D/g, "");
        if (v.length > 11) v = v.slice(0, 11);
        v = v.replace(/(\d{2})(\d)/, "($1) $2");
        v = v.replace(/(\d{5})(\d{1,4})$/, "$1-$2");
        e.target.value = v;
    });
}

// Initialize masks and functionality
document.addEventListener("DOMContentLoaded", function () {
    // Apply CPF mask
    const cpfInput = document.querySelector('input[name="cpf"]');
    if (cpfInput) {
        applyCpfMask(cpfInput);
    }

    // Apply phone mask
    const telInput = document.querySelector('input[name="telefone"]');
    if (telInput) {
        applyPhoneMask(telInput);
    }

    // Add input focus effects
    const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Check if input has value on load
        if (input.value) {
            input.parentElement.classList.add('focused');
        }
    });

    // Add form validation and loading states
    const forms = document.querySelectorAll("form");
    forms.forEach(form => {
        form.addEventListener("submit", function (e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                
                // Re-enable button after 3 seconds in case of error
                setTimeout(() => {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                }, 3000);
            }
        });
    });

    // Add entrance animation
     const mainLogin = document.querySelector('.main-login');
     if (mainLogin) {
         mainLogin.classList.add('animate-in');
     }
});

// Make functions globally available
window.girarCard = girarCard;
window.applyCpfMask = applyCpfMask;
window.applyPhoneMask = applyPhoneMask;
