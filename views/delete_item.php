<?php
// filepath: d:\xampp\htdocs\VitaminsAndMineralsSite\views\delete_item.php
require_once './includes/db_config.php';

// Перевірка авторизації
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit();
}

// Отримання ID вітаміну/мінералу з URL
$id = (int) ($_GET['id'] ?? 0);

// Перевірка, чи користувач є адміністратором
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Перевірка підключення
if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}

// Отримання даних про вітамін/мінерал для перевірки прав доступу
$sql = "SELECT author_id FROM vitamins_minerals WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Перевірка, чи існує запис
if ($result->num_rows === 0) {
    $conn->close();
    $stmt->close();
    $_SESSION['errors'] = "Запис не знайдено";
    header("Location: index.php?action=vitamins_list");
    exit();
}

// Отримання ID автора запису
$vitamin = $result->fetch_assoc();

// Перевірка прав доступу (адміністратор або автор)
if (!$is_admin && $_SESSION['user_id'] != $vitamin['author_id']) {
    $conn->close();
    $stmt->close();
    $_SESSION['errors'] = "У вас немає прав для видалення цього запису";
    header("Location: index.php?action=vitamins_list");
    exit();
}

// Видалення запису
$delete_sql = "DELETE FROM vitamins_minerals WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $id);

if ($delete_stmt->execute()) {
    $_SESSION['success'] = "Запис успішно видалено";
} else {
    $_SESSION['errors'] = "Помилка при видаленні запису: " . $delete_stmt->error;
}

$delete_stmt->close();
$stmt->close();
$conn->close();

// Перенаправлення на список вітамінів/мінералів
header("Location: index.php?action=vitamins_list");
exit();
?>