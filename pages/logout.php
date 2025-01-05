<?php
include $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';
include $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Начинаем сессию, если она еще не начата
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Получаем текущий токен из куки
$current_token = isset($_COOKIE['auth_token']) ? $_COOKIE['auth_token'] : null;

if ($current_token) {
    // Удаляем токен из базы данных
    $stmt = $conn->prepare("UPDATE users SET token = NULL WHERE token = ?");
    $stmt->bind_param("s", $current_token);
    $stmt->execute();
    $stmt->close();

    // Удаляем куки
    $cookie_options = array(
        'expires' => time() - 3600, // Устанавливаем время истечения в прошлом
        'path' => '/',
        'domain' => '', // Оставьте пустым для текущего домена
        'secure' => false, // Используйте true, если у вас есть HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    );
    setcookie("auth_token", "", $cookie_options);
}

// Уничтожаем все данные сессии
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Закрываем соединение с базой данных
$conn->close();

// Перенаправляем на главную страницу
header("Location: /");
exit();
?>

