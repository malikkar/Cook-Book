<?php
$title = 'Cook Book';
include 'includes/header.php';

// Проверяем, предоставлен ли ID рецепта
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid recipe ID");
}

$recipe_id = intval($_GET['id']);

// Получаем данные рецепта из базы данных
$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Recipe not found");
}

$recipe = $result->fetch_assoc();
$stmt->close();

$conn->close();

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
<div class="content">
    <h1><?php echo htmlspecialchars($recipe['dish_name']); ?></h1>
    <img src="<?php echo htmlspecialchars($recipe['main_photo']); ?>" alt="Main photo" class="main-photo">

    <p class="small-description"><?php echo htmlspecialchars($recipe['description']); ?></p><br>

<h2>Ingredients</h2>
<ul class="ingredients-list">
    <?php
    $ingredients = preg_split('/\r\n|\r|\n/', $recipe['ingredients']);
    foreach ($ingredients as $ingredient) {
        $ingredient = trim($ingredient);
        if (!empty($ingredient)) {
            echo "<li>" . htmlspecialchars($ingredient) . "</li>";
        }
    }
    ?>
</ul>

<br><h2>Steps to Prepare the Dish</h2>
<ol class="steps-list">
    <?php
    $steps = preg_split('/\r\n|\r|\n/', $recipe['steps']);
    foreach ($steps as $step) {
        $step = trim($step);
        if (!empty($step)) {
            echo "<li>" . htmlspecialchars($step) . "</li>";
        }
    }
    ?>
</ol>

    <?php if (!empty($recipe['additional_photos'])): ?>
    <br><h2>Additional Photos</h2>
    <div class="additional-photos">
        <?php
        $additional_photos = explode(',', $recipe['additional_photos']);
        foreach ($additional_photos as $photo) {
            echo '<img src="' . htmlspecialchars(trim($photo)) . '" alt="Additional Photos">';
        }
        ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>


