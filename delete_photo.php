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

$data = json_decode(file_get_contents('php://input'), true);
$photo_url = $data['photo_url'];
$recipe_id = intval($data['recipe_id']);
$type = $data['type'];
$user_id = get_user_id();

// Проверка, принадлежит ли рецепт пользователю
$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this photo']);
    exit;
}
$recipe = $result->fetch_assoc();
$stmt->close();

if ($type === 'main') {
    // Удаление основного фото
    if ($recipe['main_photo'] === $photo_url) {
        $stmt = $conn->prepare("UPDATE recipes SET main_photo = NULL WHERE id = ?");
        $stmt->bind_param("i", $recipe_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Main photo deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting main photo: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Main photo not found']);
    }
} elseif ($type === 'additional') {
    // Удаление дополнительного фото
    $additional_photos = explode(',', $recipe['additional_photos']);
    $key = array_search($photo_url, $additional_photos);
    if ($key !== false) {
        unset($additional_photos[$key]);
        $new_additional_photos = implode(',', $additional_photos);
        $stmt = $conn->prepare("UPDATE recipes SET additional_photos = ? WHERE id = ?");
        $stmt->bind_param("si", $new_additional_photos, $recipe_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Additional photo deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting additional photo: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Additional photo not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid photo type']);
}

$conn->close();

