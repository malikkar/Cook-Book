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
$recipe_id = intval($data['id']);
$user_id = get_user_id();

// Проверка, принадлежит ли рецепт пользователю
$stmt = $conn->prepare("SELECT user_id FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0 || $result->fetch_assoc()['user_id'] !== $user_id) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this recipe']);
    exit;
}
$stmt->close();

// Удаление рецепта
$stmt = $conn->prepare("DELETE FROM recipes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $recipe_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Recipe deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting recipe: ' . $stmt->error]);
}

$stmt->close();
$conn->close();

