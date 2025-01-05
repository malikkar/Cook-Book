document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signinForm');
    const emailInput = form.querySelector('.email');
    const passwordInput = form.querySelector('.password');
    const showHideIcon = form.querySelector('.show-hide');

    // Показать/скрыть пароль
    showHideIcon.addEventListener('click', () => {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            showHideIcon.classList.replace('bx-hide', 'bx-show');
        } else {
            passwordInput.type = 'password';
            showHideIcon.classList.replace('bx-show', 'bx-hide');
        }
    });

    // Функция валидации email
    function validateEmail(input) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(input.value);
    }

    // Функции для отображения и скрытия ошибок
    function showError(input, message) {
        const errorSpan = input.closest('.field').querySelector('.error');
        errorSpan.querySelector('.error-text').textContent = message;
        errorSpan.style.display = 'block';
    }

    function hideError(input) {
        const errorSpan = input.closest('.field').querySelector('.error');
        errorSpan.style.display = 'none';
    }

    // Валидация email в реальном времени
    emailInput.addEventListener('input', () => {
        if (!validateEmail(emailInput)) {
            showError(emailInput, 'Пожалуйста, введите корректный email');
        } else {
            hideError(emailInput);
        }
    });

    // Обработка отправки формы
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateEmail(emailInput)) {
            showError(emailInput, 'Пожалуйста, введите корректный email');
            return;
        }

        if (passwordInput.value.length < 8) {
            showError(passwordInput, 'Пароль должен содержать минимум 8 символов');
            return;
        }

        // Отправка асинхронного запроса
        const formData = new FormData(this);

        fetch('signin_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Перенаправление на главную страницу при успешном входе
                window.location.href = 'index.php';
            } else {
                // Отображение ошибки
                if (data.error === 'email') {
                    showError(emailInput, data.message);
                } else if (data.error === 'password') {
                    showError(passwordInput, data.message);
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при отправке запроса. Пожалуйста, попробуйте еще раз позже.');
        });
    });
});





