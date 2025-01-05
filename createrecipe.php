<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
if (!is_logged_in() || (get_user_role() != 'author' && get_user_role() != 'admin')) {
    die("You do not have permission to create a recipe");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/stylemain.css">
    <title>Create recipe</title>
</head>
<body>
<div class="content">
    <h2>Create recipe</h2>
    <div id="error-message" style="color: red; margin-bottom: 10px; display: none;"></div>
    <form id="recipeForm" method="POST" enctype="multipart/form-data">
        <label for="dish-name">Name of the dish</label><br>
        <input type="text" id="dish-name" name="dish-name" required><br>
        <span class="error-message" id="dish-name-error"></span><br>

        <label for="description">Description:</label><br />
        <textarea id="description" name="description" rows="4" required></textarea><br>
        <span class="error-message" id="description-error"></span><br>

        <label for="ingredients">Ingredients:</label>
        <div id="ingredients">
            <input type="text" name="ingredient[]" placeholder="Ingredient" style="margin-bottom: 10px;" required>
            <span class="error-message" id="ingredient-error"></span>
        </div>
        <button type="button" class="button" onclick="addIngredient()">Add Ingredient</button><br><br>

        <label for="recipesteps">Recipe steps:</label><br />
        <div id="recipesteps">
            <textarea name="steps[]" rows="4" placeholder="Step" style="margin-bottom: 10px;" required></textarea>
            <span class="error-message" id="steps-error"></span>
        </div>
        <button type="button" class="button" onclick="addStep()">Add Step</button><br><br>

        <label for="mainphoto">Main photo:</label><br><br>
        <input type="file" id="mainphoto" name="mainphoto" accept="image/*" required><br><br>

        <label for="addphoto">Additional photos:</label><br><br>
        <input type="file" id="addphoto" name="addphoto[]" accept="image/*" multiple><br><br>

        <input type="submit" value="Create Recipe" class="button">
    </form>
</div>

<script>
function showError(message) {
    const errorDiv = document.getElementById('error-message');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 5000);
}

function addIngredient() {
    const div = document.getElementById('ingredients');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'ingredient[]';
    input.placeholder = 'Ingredient';
    input.style.marginBottom = '10px';
    input.required = true;

    const ingredientCount = div.getElementsByTagName('input').length;
    if ((ingredientCount + 1) % 5 !== 1) {
        input.style.marginLeft = '10px';
    }

    div.appendChild(input);
}

function addStep() {
    const stepsContainer = document.getElementById('recipesteps');
    const newStep = document.createElement('textarea');
    newStep.name = 'steps[]';
    newStep.rows = 4;
    newStep.required = true;
    newStep.placeholder = 'Step';
    newStep.style.marginBottom = '10px';

    const stepCount = stepsContainer.getElementsByTagName('textarea').length;
    if ((stepCount + 1) % 5 !== 1) {
        newStep.style.marginLeft = '10px';
    }

    stepsContainer.appendChild(newStep);
}

document.getElementById('recipeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('error');
        } else {
            field.classList.remove('error');
        }
    });

    if (!isValid) {
        showError('Please fill in all required fields');
        return;
    }

    // Проверяем размер и формат файлов
    const mainPhoto = document.getElementById('mainphoto').files[0];
    if (mainPhoto && !mainPhoto.type.startsWith('image/')) {
        showError('The main photo must be an image');
        return;
    }

    const addPhotos = document.getElementById('addphoto').files;
    for (let i = 0; i < addPhotos.length; i++) {
        if (!addPhotos[i].type.startsWith('image/')) {
            showError('All additional photos must be images.');
            return;
        }
    }

    let formData = new FormData(this);

    // Отключаем кнопку отправки
    const submitButton = form.querySelector('input[type="submit"]');
    submitButton.disabled = true;
    submitButton.value = 'Sending...';

    fetch('process_recipe.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network error');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.parent.location.reload();
        } else {
            showError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError('An error occurred while submitting the form: ' + error.message);
    })
    .finally(() => {
        // Включаем кнопку отправки
        submitButton.disabled = false;
        submitButton.value = 'Create Recipe';
    });
});
</script>
</body>
</html>





