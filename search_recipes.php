<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $search_query = clean_input($_GET['query']);
    
    $sql = "SELECT id, dish_name, description, main_photo FROM recipes 
            WHERE dish_name LIKE ? OR description LIKE ? OR ingredients LIKE ?
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $search_param = "%{$search_query}%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    
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
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();


