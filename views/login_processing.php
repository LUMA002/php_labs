<?php

require_once './includes/db_config.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Помилка підключення до бази даних: " . $conn->connect_error);
}

$sql = "SELECT id, password, admin FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        // Успішна авторизація
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = $user['admin'];

        header("Location: index.php?action=main");
        exit();
    } else {
        $_SESSION['errors'] = "Невірний логін або пароль";
    }
} else {
    $_SESSION['errors'] = "Невірний логін або пароль";
}

$stmt->close();
$conn->close();

header("Location: index.php?action=login");
exit();