<?php
// Настройки базы данных
define('DB_HOST', 'localhost');
define('DB_USER', 'malikkar');
define('DB_PASS', 'webove aplikace');
define('DB_NAME', 'malikkar');

// Настройки сайта
define('SITE_NAME', 'Cook Book');
define('BASE_URL', 'https://zwa.toad.cz/~malikkar/www');

ob_start();
// Настройки сессии
session_start();

// Установка временной зоны
date_default_timezone_set('Europe/Prague');

//$conn = new PDO('mysql:host=localhost;dbname=malikkar;charset=utf8', 'malikkar', 'webove aplikace');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
// Включение отображения ошибок (только для разработки)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
