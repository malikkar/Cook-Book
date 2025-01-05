<?php
$title = 'Cook Book';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php';
?>

<div class="topnav">
    <div class="logo">
        <img src="/food/logo.png" alt="Cook Book.">
    </div>
    <div>
        <a href="/">Home</a>
        <?php if (is_logged_in()): ?>
            <a href="/profile.php">Profile</a>
        <?php else: ?>
            <a href="/signin.php">Login</a>
        <?php endif; ?>
        <a href="/aboutus.php">About us</a>
    </div>
</div>
<!-- title -->
<div class="content">
    <h1>Recipe book</h1>
    <p class="intro-text">Find and save recipes from all over the world.</p>
</div>

<div class="search">
    <!-- <h2>Search Recipes</h2> -->
    <input class="search-input" type="text" id="searchQuery" placeholder="Search for recipes...">
    <button class="search-button" onclick="searchRecipes()"><i class='bx bx-search'></i></button>
</div>
<div id="searchResults"></div>
<!-- PAGE CONTENT -->
<div class="content content-main">
    <p class="intro-text">All recipes:</p>
<?php
$recipes_per_page = 8;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Вычисляем смещение для SQL-запроса
$offset = ($current_page - 1) * $recipes_per_page;

// Получаем общее количество рецептов
$total_recipes_query = "SELECT COUNT(*) as total FROM recipes WHERE status = 'approved'";
$total_result = $conn->query($total_recipes_query);
$total_recipes = $total_result->fetch_assoc()['total'];

// Вычисляем общее количество страниц
$total_pages = ceil($total_recipes / $recipes_per_page);

// Получаем рецепты для текущей страницы
$sql = "SELECT * FROM recipes WHERE status = 'approved' ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $recipes_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<div class="grid" id="food">
    <?php while ($recipe = $result->fetch_assoc()): ?>
        <a class="recipe" href="/describerecipe.php?id=<?php echo htmlspecialchars($recipe['id']); ?>">
            <img src="<?php echo htmlspecialchars($recipe['main_photo']); ?>" alt="<?php echo htmlspecialchars($recipe['dish_name']); ?>">
            <h3><?php echo htmlspecialchars($recipe['dish_name']); ?></h3>
            <p><?php echo htmlspecialchars($recipe['description']); ?></p>
        </a>
    <?php endwhile; ?>
</div>

    <!-- Pagination -->
<div class="pagination">
    <?php if ($current_page > 1): ?>
        <a href="?page=<?php echo $current_page - 1; ?>" class="page-item">&laquo;</a>
    <?php endif; ?>

    <?php
    $start_page = max(1, $current_page - 2);
    $end_page = min($total_pages, $current_page + 2);

    for ($i = $start_page; $i <= $end_page; $i++):
    ?>
        <a href="?page=<?php echo $i; ?>" class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>

    <?php if ($current_page < $total_pages): ?>
        <a href="?page=<?php echo $current_page + 1; ?>" class="page-item">&raquo;</a>
    <?php endif; ?>
</div>
</div>
<script>
function searchRecipes() {
    const query = document.getElementById('searchQuery').value;
    const searchResults = document.getElementById('searchResults');

    if (query.length < 3) {
        searchResults.innerHTML = '<p>Please enter at least 3 characters</p>';
        return;
    }

    searchResults.innerHTML = '<p>Searching...</p>';

    fetch(`search_recipes.php?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.recipes.length > 0) {
                    let html = '<div class="grid">';
                    data.recipes.forEach(recipe => {
                        html += `
                            <a class="recipe" href="describerecipe.php?id=${recipe.id}">
                                <img src="${recipe.main_photo}" alt="${recipe.dish_name}" style="width:100%">
                                <h3>${recipe.dish_name}</h3>
                                <p>${recipe.description}</p>
                            </a>
                        `;
                    });
                    html += '</div>';
                    searchResults.innerHTML = html;
                } else {
                    searchResults.innerHTML = '<p>No recipes found</p>';
                }
            } else {
                searchResults.innerHTML = '<p>Error occurred while searching</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            searchResults.innerHTML = '<p>An error occurred</p>';
        });
}

document.getElementById('searchQuery').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchRecipes();
    }
});
</script>
<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php';
?>