<?php
session_start();

// функція для очищення вхідних даних
function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


$errors = [];


$username = sanitize_input($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$email = sanitize_input($_POST['email'] ?? '');
$recaptcha_response = $_POST['g-recaptcha-response'] ?? '';


$_SESSION['form_data'] = [
    'username' => $username,
    'email' => $email
];

// Валідація логіну
if (empty($username)) {
    $errors[] = "Логін є обов'язковим";
} elseif (!preg_match('/^[a-zA-Z0-9_-]{4,}$/', $username)) {
    $errors[] = "Логін має бути не менше 4 символів і містити тільки літери, цифри, нижнє підкреслення та дефіс";
}


if (empty($password)) {
    $errors[] = "Пароль є обов'язковим";
} elseif (strlen($password) < 7) {
    $errors[] = "Пароль має бути не менше 7 символів";
} elseif (!preg_match('/[A-Z]/', $password)) {
    $errors[] = "Пароль має містити хоча б одну велику літеру";
} elseif (!preg_match('/[a-z]/', $password)) {
    $errors[] = "Пароль має містити хоча б одну малу літеру";
} elseif (!preg_match('/[0-9]/', $password)) {
    $errors[] = "Пароль має містити хоча б одну цифру";
}


if (empty($confirm_password)) {
    $errors[] = "Будь ласка, підтвердіть пароль";
} elseif ($password !== $confirm_password) {
    $errors[] = "Паролі не співпадають";
}

if (empty($email)) {
    $errors[] = "Email є обов'язковим";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Будь ласка, введіть коректну електронну пошту";
}

// Перевірка reCAPTCHA
if (empty($recaptcha_response)) {
    $errors[] = "Будь ласка, підтвердіть, що ви не робот";
} else {
    $secret_key = "6LdGBPgqAAAAAP7Ir7nzuJxl68fHIej0QAa9I5CK"; // Замініть на ваш секретний ключ
    $verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $recaptcha_response);
    $response_data = json_decode($verify_response);

    if (!$response_data->success) {
        $errors[] = "Помилка перевірки reCAPTCHA. Спробуйте ще раз.";
    }
}


if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: index.php?action=registration");
    exit();
}


header("Location: index.php?action=registration_successful");
exit();
