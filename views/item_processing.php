<?php
require_once './includes/db_config.php';

// Перевірка авторизації
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit();
}

// Функція для очищення вхідних даних
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Перевірка підключення
if ($conn->connect_error) {
    die("Помилка підключення: " . $conn->connect_error);
}

// Отримання режиму обробки
$mode = isset($_GET['mode']) ? $_GET['mode'] : '';
$errors = [];

// Режим створення нового запису
if ($mode === 'create') {
    // Отримання і валідація даних
    $name = sanitize_input($_POST['name'] ?? '');
    $type = sanitize_input($_POST['type'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $benefits = sanitize_input($_POST['benefits'] ?? '');
    $sources = sanitize_input($_POST['sources'] ?? '');
    $visible = isset($_POST['visible']) && $_SESSION['is_admin'] == 1 ? 1 : 0;
    $author_id = $_SESSION['user_id'];

    // Валідація
    if (empty($name)) {
        $errors[] = "Назва є обов'язковою";
    }

    if (empty($type) || ($type != 'vitamin' && $type != 'mineral')) {
        $errors[] = "Тип має бути вказаний правильно";
    }

    if (empty($description)) {
        $errors[] = "Опис є обов'язковим";
    }

    if (empty($benefits)) {
        $errors[] = "Користь для здоров'я є обов'язковою";
    }

    if (empty($sources)) {
        $errors[] = "Джерела є обов'язковими";
    }

    // Якщо немає помилок, додаємо запис до бази даних
    if (empty($errors)) {
        $sql = "INSERT INTO vitamins_minerals (name, type, description, benefits, sources, visible, author_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssii", $name, $type, $description, $benefits, $sources, $visible, $author_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Новий запис успішно додано";
            header("Location: index.php?action=vitamins_list");
            exit();
        } else {
            $errors[] = "Помилка при додаванні запису: " . $stmt->error;
        }

        $stmt->close();
    }

    // Якщо є помилки, зберігаємо їх у сесії і повертаємося на форму
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: index.php?action=create_vitamin");
        exit();
    }
}
// Режим оновлення запису
else if ($mode === 'update') {
    // Отримання ID запису
    $id = (int) $_POST['id'];

    // Перевірка, чи існує запис і чи має користувач право редагувати
    $check_sql = "SELECT author_id FROM vitamins_minerals WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['errors'] = ["Запис не знайдено"];
        header("Location: index.php?action=vitamins_list");
        exit();
    }

    $vitamin = $result->fetch_assoc();

    // Перевірка прав доступу (адміністратор або автор)
    if ($_SESSION['is_admin'] != 1 && $vitamin['author_id'] != $_SESSION['user_id']) {
        $_SESSION['errors'] = ["У вас немає прав для редагування цього запису"];
        header("Location: index.php?action=vitamins_list");
        exit();
    }

    // Отримання і валідація даних
    $name = sanitize_input($_POST['name'] ?? '');
    $type = sanitize_input($_POST['type'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $benefits = sanitize_input($_POST['benefits'] ?? '');
    $sources = sanitize_input($_POST['sources'] ?? '');
    $visible = isset($_POST['visible']) && $_SESSION['is_admin'] == 1 ? 1 : 0;

    // Валідація як і при створенні
    if (empty($name)) {
        $errors[] = "Назва є обов'язковою";
    }

    if (empty($type) || ($type != 'vitamin' && $type != 'mineral')) {
        $errors[] = "Тип має бути вказаний правильно";
    }

    if (empty($description)) {
        $errors[] = "Опис є обов'язковим";
    }

    if (empty($benefits)) {
        $errors[] = "Користь для здоров'я є обов'язковою";
    }

    if (empty($sources)) {
        $errors[] = "Джерела є обов'язковими";
    }

    // Якщо немає помилок, оновлюємо запис
    if (empty($errors)) {
        $sql = "UPDATE vitamins_minerals 
                SET name = ?, type = ?, description = ?, benefits = ?, sources = ?, visible = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssii", $name, $type, $description, $benefits, $sources, $visible, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Запис успішно оновлено";
            header("Location: index.php?action=vitamins_list");
            exit();
        } else {
            $errors[] = "Помилка при оновленні запису: " . $stmt->error;
        }

        $stmt->close();
    }

    // Якщо є помилки, зберігаємо їх у сесії і повертаємося на форму
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: index.php?action=update_vitamin&id=" . $id);
        exit();
    }
}

$conn->close();
?>