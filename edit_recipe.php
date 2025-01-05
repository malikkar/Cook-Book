<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$recipe_id = intval($_POST['id']);
$user_id = get_user_id();

// Проверка, принадлежит ли рецепт пользователю
$stmt = $conn->prepare("SELECT user_id FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0 || $result->fetch_assoc()['user_id'] !== $user_id) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to edit this recipe']);
    exit;
}
$stmt->close();

$dish_name = clean_input($_POST['dish_name']);
$description = clean_input($_POST['description']);
$ingredients = clean_input($_POST['ingredients']);
$steps = clean_input($_POST['steps']);

// Обработка основного фото
$main_photo = '';
if (isset($_FILES['main_photo']) && $_FILES['main_photo']['error'] == 0) {
    $main_photo = upload_file($_FILES['main_photo'], $_SERVER['DOCUMENT_ROOT'] . '/uploads/recipes/');
    if (strpos($main_photo, 'Ошибка') !== false) {
        echo json_encode(['success' => false, 'message' => $main_photo]);
        exit;
    }
}

// Обработка дополнительных фото
$additional_photos = [];
if (isset($_FILES['additional_photos'])) {
    foreach ($_FILES['additional_photos']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['additional_photos']['error'][$key] == 0) {
            $photo = upload_file([
                'name' => $_FILES['additional_photos']['name'][$key],
                'type' => $_FILES['additional_photos']['type'][$key],
                'tmp_name' => $tmp_name,
                'error' => $_FILES['additional_photos']['error'][$key],
                'size' => $_FILES['additional_photos']['size'][$key]
            ], $_SERVER['DOCUMENT_ROOT'] . '/uploads/recipes/');
            if (strpos($photo, 'Ошибка') === false) {
                $additional_photos[] = $photo;
            }
        }
    }
}

// Подготовка SQL запроса
$sql = "UPDATE recipes SET dish_name = ?, description = ?, ingredients = ?, steps = ?";
$params = [$dish_name, $description, $ingredients, $steps];
$types = "ssss";

if (!empty($main_photo)) {
    $sql .= ", main_photo = ?";
    $params[] = $main_photo;
    $types .= "s";
}

if (!empty($additional_photos)) {
    $sql .= ", additional_photos = ?";
    $params[] = implode(',', $additional_photos);
    $types .= "s";
}

$sql .= " WHERE id = ? AND user_id = ?";
$params[] = $recipe_id;
$params[] = $user_id;
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Recipe updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating recipe: ' . $stmt->error]);
}

$stmt->close();
$conn->close();

