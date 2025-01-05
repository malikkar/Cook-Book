<?php
// Включаем отображение всех ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Отключаем вывод ошибок в браузер
ob_start();

require_once 'includes/config.php';
require_once 'includes/functions.php';

// Функция для отправки JSON-ответа
function send_json_response($data) {
    ob_clean(); // Очищаем буфер вывода
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

try {
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception('Invalid request method');
    }

    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Проверка наличия email и пароля
    if (empty($email) || empty($password)) {
        throw new Exception('Please fill in all fields');
    }

    // Проверка формата email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format', 1);
    }

    // Поиск пользователя в базе данных
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('User with this email was not found', 1);
    }

    $user = $result->fetch_assoc();
    if (!password_verify($password, $user['password'])) {
        throw new Exception('Invalid password', 2);
    }

    // Пароль верный, создаем сессию
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];

    // Генерация нового токена
    $token = bin2hex(random_bytes(32));

    // Сохранение токена в базе данных
    $update_stmt = $conn->prepare("UPDATE users SET token = ? WHERE id = ?");
    $update_stmt->bind_param("si", $token, $user['id']);
    $update_stmt->execute();
    $update_stmt->close();

    // Установка куки с токеном
    $cookie_options = array(
        'expires' => time() + (86400 * 30), // 30 дней
        'path' => '/',
        'domain' => '', // Оставьте пустым для текущего домена
        'secure' => false, // Используйте true, если у вас есть HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    );
    setcookie("auth_token", $token, $cookie_options);

    send_json_response(['success' => true]);

} catch (Exception $e) {
    $error_type = $e->getCode() == 1 ? 'email' : ($e->getCode() == 2 ? 'password' : 'general');
    send_json_response([
        'success' => false,
        'error' => $error_type,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>





