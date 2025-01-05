document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const nameInput = form.querySelector('.name');
    const surnameInput = form.querySelector('.surname');
    const emailInput = form.querySelector('.email');
    const passwordInput = form.querySelector('.password');
    const cPasswordInput = form.querySelector('.cPassword');
    const showHideIcons = form.querySelectorAll('.show-hide');

    // Show/Hide password
    showHideIcons.forEach(icon => {
        icon.addEventListener('click', () => {
            const input = icon.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bx-hide', 'bx-show');
            } else {
                input.type = 'password';
                icon.classList.replace('bx-show', 'bx-hide');
            }
        });
    });

    // Validation functions
    function validateName(input) {
        const nameRegex = /^[A-Za-zА-Яа-я0-9\s]+$/;
        return nameRegex.test(input.value);
    }

    function validateEmail(input) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(input.value);
    }

    function validatePassword(input) {
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        return passwordRegex.test(input.value);
    }

    function showError(input, message) {
        const errorSpan = input.closest('.field').querySelector('.error');
        errorSpan.querySelector('.error-text').textContent = message;
        errorSpan.style.display = 'block';
    }

    function hideError(input) {
        const errorSpan = input.closest('.field').querySelector('.error');
        errorSpan.style.display = 'none';
    }

    // Event listeners for real-time validation
    nameInput.addEventListener('input', () => {
        if (!validateName(nameInput)) {
            showError(nameInput, 'Please enter a valid name (only letters)');
        } else {
            hideError(nameInput);
        }
    });

    surnameInput.addEventListener('input', () => {
        if (!validateName(surnameInput)) {
            showError(surnameInput, 'Please enter a valid surname (only letters)');
        } else {
            hideError(surnameInput);
        }
    });

    emailInput.addEventListener('input', () => {
        if (!validateEmail(emailInput)) {
            showError(emailInput, 'Please enter a valid email address');
        } else {
            hideError(emailInput);
        }
    });

    passwordInput.addEventListener('input', () => {
        if (!validatePassword(passwordInput)) {
            showError(passwordInput, 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character');
        } else {
            hideError(passwordInput);
        }
    });

    cPasswordInput.addEventListener('input', () => {
        if (cPasswordInput.value !== passwordInput.value) {
            showError(cPasswordInput, 'Passwords do not match');
        } else {
            hideError(cPasswordInput);
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (validateName(nameInput) &&
            validateName(surnameInput) &&
            validateEmail(emailInput) &&
            validatePassword(passwordInput) &&
            cPasswordInput.value === passwordInput.value) {
            this.submit();
        } else {
            alert('Please correct the errors in the form before submitting.');
        }
    });
});

