<?php
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function is_logged_in() {
    return isset($_COOKIE['auth_token']);
}

function upload_file($file, $upload_dir) {
    $target_file = $upload_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Проверка, является ли файл изображением
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return "Файл не является изображением.";
    }

    // Проверка, существует ли уже файл
    if (file_exists($target_file)) {
        $target_file = $upload_dir . uniqid() . '_' . basename($file["name"]);
    }

    // Проверка размера файла
    if ($file["size"] > 5000000) {
        return "Извините, ваш файл слишком большой.";
    }

    // Разрешить определенные форматы файлов
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        return "Извините, разрешены только файлы JPG, JPEG, PNG & GIF.";
    }

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $target_file);
    } else {
        return "Извините, произошла ошибка при загрузке файла.";
    }
}

function get_user_role() {
    global $conn;
    if (isset($_COOKIE['auth_token'])) {
        $token = $_COOKIE['auth_token'];
        $stmt = $conn->prepare("SELECT role FROM users WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['role'];
        }
    }
    return null;
}

function get_user_id() {
    global $conn;
    if (isset($_COOKIE['auth_token'])) {
        $token = $_COOKIE['auth_token'];
        $stmt = $conn->prepare("SELECT id FROM users WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['id'];
        }
    }
    return null;
}
?>


