 <?php
header('Content-Type: application/json; charset=utf-8');


// Отключаем вывод ошибок в браузер
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Функция для логирования ошибок
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'error.log');
}

// Функция для отправки JSON-ответа
function sendJsonResponse($success, $message) {
    // Очищаем весь предыдущий вывод
    ob_clean();
    
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

try {
    // Проверяем, авторизован ли пользователь
    if (!is_logged_in()) {
        sendJsonResponse(false, 'You are not logged in');
    }

    // Проверяем права пользователя
    $userRole = get_user_role();
    if ($userRole != 'author' && $userRole != 'admin') {
        sendJsonResponse(false, 'You do not have permission to create a recipe');
    }

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        sendJsonResponse(false, 'Invalid request method');
    }

    // Получаем и очищаем данные
    $dish_name = isset($_POST['dish-name']) ? clean_input($_POST['dish-name']) : '';
    $description = isset($_POST['description']) ? clean_input($_POST['description']) : '';
    $ingredients = isset($_POST['ingredient']) ? array_map('clean_input', $_POST['ingredient']) : [];
    $steps = isset($_POST['steps']) ? array_map('clean_input', $_POST['steps']) : [];

    // Валидация данных
    if (empty($dish_name)) {
        sendJsonResponse(false, 'Please enter the name of the dish');
    }

    if (empty($description)) {
        sendJsonResponse(false, 'Please add a description of the dish');
    }

    if (empty($ingredients)) {
        sendJsonResponse(false, 'Please add at least one ingredient');
    }

    if (empty($steps)) {
        sendJsonResponse(false, 'Please add at least one cooking step');
    }

    // Проверяем директорию для загрузки
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/recipes/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Обработка основного фото
    $main_photo = '';
    if (!isset($_FILES['mainphoto']) || $_FILES['mainphoto']['error'] != 0) {
        sendJsonResponse(false, 'Please upload the main photo of the recipe');
    }

    $main_photo = upload_file($_FILES['mainphoto'], $upload_dir);
    if (strpos($main_photo, 'Ошибка') !== false) {
        sendJsonResponse(false, $main_photo);
    }

    // Обработка дополнительных фото
    $additional_photos = [];
    if (isset($_FILES['addphoto']) && is_array($_FILES['addphoto']['tmp_name'])) {
        foreach ($_FILES['addphoto']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['addphoto']['error'][$key] == 0) {
                $photo = upload_file([
                    'name' => $_FILES['addphoto']['name'][$key],
                    'type' => $_FILES['addphoto']['type'][$key],
                    'tmp_name' => $tmp_name,
                    'error' => $_FILES['addphoto']['error'][$key],
                    'size' => $_FILES['addphoto']['size'][$key]
                ], $upload_dir);
                
                if (strpos($photo, 'Ошибка') === false) {
                    $additional_photos[] = $photo;
                }
            }
        }
    }
    $additional_photos = implode(',', $additional_photos);

    // Получаем ID пользователя
    $user_id = get_user_id();
    if (!$user_id) {
        sendJsonResponse(false, 'Authorization error');
    }

    // Определяем статус рецепта
    $status = ($userRole == 'admin') ? 'approved' : 'pending';

    // Подготовка данных для вставки в базу данных
    $ingredients_str = implode("\n", $ingredients);
    $steps_str = implode("\n", $steps);

    // Вставка рецепта в базу данных
    $stmt = $conn->prepare("INSERT INTO recipes (user_id, dish_name, description, ingredients, steps, main_photo, additional_photos, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception('Error preparing request: ' . $conn->error);
    }

    $stmt->bind_param("isssssss", $user_id, $dish_name, $description, $ingredients_str, $steps_str, $main_photo, $additional_photos, $status);

    if (!$stmt->execute()) {
        throw new Exception('Error adding recipe: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    sendJsonResponse(true, 'Recipe added successfully');

} catch (Exception $e) {
    logError($e->getMessage());
    sendJsonResponse(false, 'An error occurred while processing the request: ' . $e->getMessage());
}
?>




