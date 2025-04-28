<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Головна</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css">
</head>

<body>

    <header>
        <h1>Вітаміни та мінерали</h1>
    </header>

    <nav>
        <a href="index.php?action=main" class="nav-link">Головна</a>
        <a href="index.php?action=about" class="nav-link">Про сайт</a>
        <a href="index.php?action=vitamins_list" class="nav-link">Вітаміни та мінерали</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php?action=create_vitamin" class="nav-link">Додати вітамін/мінерал</a>
        <?php endif; ?>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="index.php?action=registration" class="nav-link">Реєстрація</a>
            <a href="index.php?action=login" class="nav-link">Увійти</a>
        <?php else: ?>
            <a href="index.php?action=logout" class="nav-link">Вийти</a>
        <?php endif; ?>
    </nav>