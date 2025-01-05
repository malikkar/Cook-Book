<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid recipe ID']);
    exit;
}

$recipe_id = intval($_GET['id']);
$user_id = get_user_id();

$stmt = $conn->prepare("SELECT * FROM recipes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $recipe_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Recipe not found or you do not have permission to edit it']);
    exit;
}

$recipe = $result->fetch_assoc();
$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'recipe' => $recipe]);

