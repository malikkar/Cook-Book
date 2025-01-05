<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = clean_input($_POST['name']);
    $surname = clean_input($_POST['surname']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $cPassword = $_POST['cPassword'];
    $role = clean_input($_POST['role']);

    $allowed_roles = ['reader', 'author', 'admin'];
    if (!in_array($role, $allowed_roles)) {
        die("Выбрана недопустимая роль");
    }

    if (empty($name) || empty($surname) || empty($email) || empty($password) || empty($cPassword)) {
        die("Please fill in all fields");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    if ($password !== $cPassword) {
        die("Passwords don't match");
    }

    // Проверка сложности пароля
    if (strlen($password) < 8 || !preg_match("#[0-9]+#", $password) || !preg_match("#[a-zA-Z]+#", $password)) {
        die("The password must be at least 8 characters and contain letters and numbers");
    }

    // Хеширование пароля
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Вставка пользователя в базу данных
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $surname, $email, $hashed_password, $role);

    if ($stmt->execute()) {
        // Генерация токена
        $user_id = $stmt->insert_id;
        $token = bin2hex(random_bytes(32));

        // Сохранение токена в базе данных
        $update_stmt = $conn->prepare("UPDATE users SET token = ? WHERE id = ?");
        $update_stmt->bind_param("si", $token, $user_id);
    
        if ($update_stmt->execute()) {
            // Установка куки с токеном
            $cookie_options = array(
                'expires' => time() + (86400 * 30), // 30 дней
                'path' => '/',
                'domain' => '', // Оставьте пустым для текущего домена
                'secure' => false, // Используйте true, если у вас есть HTTPS
                'httponly' => true,
                'samesite' => 'Strict'
            );
            
            if (setcookie("auth_token", $token, $cookie_options)) {
                // Перенаправление на главную страницу
                header("Location: index.php");
                exit();
            } else {
                echo "Ошибка при установке куки: " . error_get_last()['message'];
            }
        } else {
            echo "Ошибка обновления токена: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        echo "Ошибка при регистрации: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>




