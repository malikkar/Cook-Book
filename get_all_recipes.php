<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

$sql = "SELECT id, dish_name, description, main_photo FROM recipes LIMIT 20"; // Ограничим 20 рецептами для производительности

$result = $conn->query($sql);

if ($result) {
    $recipes = [];
    while ($row = $result->fetch_assoc()) {
        $recipes[] = [
            'id' => $row['id'],
            'dish_name' => $row['dish_name'],
            'description' => mb_substr($row['description'], 0, 100) . '...',
            'main_photo' => $row['main_photo']
        ];
    }
    echo json_encode(['success' => true, 'recipes' => $recipes]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error fetching recipes']);
}

$conn->close();

