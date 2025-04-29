<?php

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

// Отримання даних про вітамін/мінерал
$sql = "SELECT * FROM vitamins_minerals WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Перевірка, чи існує запис
if ($result->num_rows === 0) {
    $conn->close();
    $stmt->close();
    ?>
    <div class="container my-5">
        <div class="alert alert-danger">
            <h4>Помилка</h4>
            <p>Запис не знайдено</p>
        </div>
        <a href="index.php?action=vitamins_list" class="btn btn-primary">Повернутися до списку</a>
    </div>
    <?php
    exit();
}

// Отримання даних запису
$vitamin = $result->fetch_assoc();

// Перевірка прав доступу (адміністратор або автор)
if (!$is_admin && $_SESSION['user_id'] != $vitamin['author_id']) {
    $conn->close();
    $stmt->close();
    ?>
    <div class="container my-5">
        <div class="alert alert-danger">
            <h4>Помилка</h4>
            <p>У вас немає прав для редагування цього запису</p>
        </div>
        <a href="index.php?action=vitamins_list" class="btn btn-primary">Повернутися до списку</a>
    </div>
    <?php
    exit();
}
?>

<div class="container my-5">
    <h2>Редагувати вітамін/мінерал</h2>

    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <form action="index.php?action=item_processing&mode=update" method="post">
        <input type="hidden" name="id" value="<?= $vitamin['id'] ?>">

        <div class="form-group mb-3">
            <label for="name" class="form-label">Назва</label>
            <input type="text" class="form-control" id="name" name="name"
                value="<?= htmlspecialchars($vitamin['name']) ?>" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Тип</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="vitamin" value="vitamin"
                    <?= $vitamin['type'] == 'vitamin' ? 'checked' : '' ?>>
                <label class="form-check-label" for="vitamin">
                    Вітамін
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="mineral" value="mineral"
                    <?= $vitamin['type'] == 'mineral' ? 'checked' : '' ?>>
                <label class="form-check-label" for="mineral">
                    Мінерал
                </label>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="description" class="form-label">Опис</label>
            <textarea class="form-control" id="description" name="description" rows="3"
                required><?= htmlspecialchars($vitamin['description']) ?></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="benefits" class="form-label">Користь для здоров'я</label>
            <textarea class="form-control" id="benefits" name="benefits" rows="3"
                required><?= htmlspecialchars($vitamin['benefits']) ?></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="sources" class="form-label">Джерела (продукти харчування)</label>
            <textarea class="form-control" id="sources" name="sources" rows="3"
                required><?= htmlspecialchars($vitamin['sources']) ?></textarea>
        </div>

        <?php if ($is_admin): ?>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="visible" name="visible" value="1"
                    <?= $vitamin['visible'] == 1 ? 'checked' : '' ?>>
                <label class="form-check-label" for="visible">Опублікувати</label>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Зберегти зміни</button>
        <a href="index.php?action=vitamins_list" class="btn btn-secondary">Скасувати</a>
    </form>
</div>

<?php
$stmt->close();
$conn->close();
?>