<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Проверяем, авторизован ли пользователь
if (!is_logged_in()) {
    header("Location: /signin.php");
    exit();
}

// Получаем данные пользователя
$user_id = get_user_id();
if ($user_id === null) {
    header("Location: /signin.php");
    exit();
}

$stmt = $conn->prepare("SELECT first_name, last_name, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: /signin.php");
    exit();
}

// Получаем рецепты пользователя, если он автор
$recipes = [];
if ($user['role'] == 'author' || $user['role'] == 'admin') {
    $stmt = $conn->prepare("SELECT id, dish_name, description, main_photo FROM recipes WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile</title>
    <link rel="stylesheet" href="/assets/css/stylemain.css" />
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
</head>
<body>
<!-- Navigation Bar -->
<div class="topnav">
    <div class="logo">
        <img src="/food/logo.png" alt="Cook Book.">
    </div>
    <div>
        <a href="/">Home</a>
        <a href="/profile.php">Profile</a>
        <a href="/pages/logout.php">Logout</a>
        <a href="/aboutus.php">About us</a>
    </div>
</div>

<div class="containerProfile">
    <div class="profile-details">
        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" readonly>

        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" readonly>

        <label for="email">Email Address:</label>
        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>

        <label for="role">Role:</label>
        <input type="text" id="role" value="<?php echo htmlspecialchars($user['role'] ?? ''); ?>" readonly>
    </div>

    <?php if (isset($user['role']) && ($user['role'] == 'author' || $user['role'] == 'admin')): ?>
    <div class="recipe-section">
        <h2>My Recipes</h2>
        <div id="recipes-container">
            <?php foreach ($recipes as $recipe): ?>
            <div class="recipe-card" data-recipe-id="<?php echo $recipe['id']; ?>">
                <img src="<?php echo htmlspecialchars($recipe['main_photo'] ?? ''); ?>" alt="<?php echo htmlspecialchars($recipe['dish_name'] ?? ''); ?>">
                <h3><?php echo htmlspecialchars($recipe['dish_name'] ?? ''); ?></h3>
                <p><?php echo htmlspecialchars($recipe['description'] ?? ''); ?></p>
                <div class="recipe-actions">
                    <button class="edit-recipe">Edit</button>
                    <button class="delete-recipe">Delete</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <button class="add-recipe-btn" onclick="openModal()">Add Recipe</button>
    </div>
    <?php endif; ?>
</div>

<!-- Modal for adding a new recipe -->
<div id="recipeModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <iframe src="createrecipe.php" id="recipeFrame"></iframe>
    </div>
</div>

<!-- Modal for editing a recipe -->
<div id="editRecipeModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <form id="editRecipeForm">
            <input type="hidden" id="editRecipeId" name="id">
            <label for="editDishName">Dish name:</label>
            <input type="text" id="editDishName" name="dish_name" required>
            
            <label for="editDescription">Description:</label>
            <textarea id="editDescription" name="description" required></textarea>
            
            <label for="editIngredients">Ingredients:</label>
            <textarea id="editIngredients" name="ingredients" required></textarea>
            
            <label for="editSteps">Preparation steps:</label>
            <textarea id="editSteps" name="steps" required></textarea>
            
            <label for="editMainPhoto">Main photo:</label>
            <input type="file" id="editMainPhoto" name="main_photo">
            
            <div id="currentPhotos">
                <!-- Здесь будут отображаться текущие фото -->
            </div>
            
            <label for="editAdditionalPhotos">Additional photos:</label>
            <input type="file" id="editAdditionalPhotos" name="additional_photos[]" multiple>
            
            <button type="submit">Save changes</button>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('recipeModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('recipeModal').style.display = 'none';
}

function openEditModal(recipeId) {
    const modal = document.getElementById('editRecipeModal');
    modal.style.display = 'block';
    
    // Загрузка данных рецепта
    fetch(`get_recipe.php?id=${recipeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.recipe) {
                const recipe = data.recipe;
                document.getElementById('editRecipeId').value = recipe.id;
                document.getElementById('editDishName').value = recipe.dish_name;
                document.getElementById('editDescription').value = recipe.description;
                document.getElementById('editIngredients').value = recipe.ingredients;
                document.getElementById('editSteps').value = recipe.steps;
                
                // Отображение текущих фото
                const currentPhotos = document.getElementById('currentPhotos');
                currentPhotos.innerHTML = '';
                if (recipe.main_photo) {
                    currentPhotos.innerHTML += `
                        <div>
                            <img src="${recipe.main_photo}" alt="Main Photo" style="width: 100px; height: 100px;">
                            <button type="button" onclick="deletePhoto('${recipe.main_photo}', ${recipe.id}, 'main')">Delete</button>
                        </div>
                    `;
                }
                if (recipe.additional_photos) {
                    recipe.additional_photos.split(',').forEach((photo, index) => {
                        if (photo.trim() !== '') {
                            currentPhotos.innerHTML += `
                                <div>
                                    <img src="${photo}" alt="Additional Photo ${index + 1}" style="width: 100px; height: 100px;">
                                    <button type="button" onclick="deletePhoto('${photo}', ${recipe.id}, 'additional')">Delete</button>
                                </div>
                            `;
                        }
                    });
                }
            } else {
                console.error('Failed to load recipe data:', data.message);
                alert('Failed to load recipe data. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading recipe data. Please try again.');
        });
}

function closeEditModal() {
    document.getElementById('editRecipeModal').style.display = 'none';
}

function deletePhoto(photoUrl, recipeId, type) {
    if (confirm('Are you sure you want to delete this photo?')) {
        fetch('delete_photo.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ photo_url: photoUrl, recipe_id: recipeId, type: type }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Photo deleted successfully');
                openEditModal(recipeId); // Перезагрузка модального окна
            } else {
                alert('Error when deleting photos');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const recipesContainer = document.getElementById('recipes-container');
    
    recipesContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('edit-recipe')) {
            const recipeId = e.target.closest('.recipe-card').dataset.recipeId;
            openEditModal(recipeId);
        } else if (e.target.classList.contains('delete-recipe')) {
            const recipeId = e.target.closest('.recipe-card').dataset.recipeId;
            if (confirm('Are you sure you want to delete this recipe?')) {
                deleteRecipe(recipeId);
            }
        }
    });

    document.getElementById('editRecipeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('edit_recipe.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Recipe successfully updated');
                closeEditModal();
                location.reload(); // Перезагрузка страницы для отображения изменений
            } else {
                alert('Error updating recipe');
            }
        });
    });
});

function deleteRecipe(recipeId) {
    fetch('delete_recipe.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: recipeId }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Recipe successfully deleted');
            location.reload(); // Перезагрузка страницы для отображения изменений
        } else {
            alert('Error when deleting recipe');
        }
    });
}

// Закрыть модальное окно при клике вне его
window.onclick = function(event) {
    if (event.target == document.getElementById('recipeModal')) {
        closeModal();
    }
    if (event.target == document.getElementById('editRecipeModal')) {
        closeEditModal();
    }
}
</script>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
</body>
</html>





