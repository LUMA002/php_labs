<?php
require_once './includes/db_config.php';

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
$sql = "SELECT vm.*, u.username FROM vitamins_minerals vm 
        LEFT JOIN users u ON vm.author_id = u.id 
        WHERE vm.id = ?";
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

// Якщо запис існує, але не опублікований, перевіряємо права доступу
$vitamin = $result->fetch_assoc();

if ($vitamin['visible'] != 1 && !$is_admin && (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $vitamin['author_id'])) {
    $conn->close();
    $stmt->close();
    ?>
    <div class="container my-5">
        <div class="alert alert-warning">
            <h4>Доступ обмежено</h4>
            <p>Цей запис ще не опубліковано</p>
        </div>
        <a href="index.php?action=vitamins_list" class="btn btn-primary">Повернутися до списку</a>
    </div>
    <?php
    exit();
}
?>

<div class="container my-5">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>
                <?= htmlspecialchars($vitamin['name']) ?>
                <?php if (!$vitamin['visible']): ?>
                    <span class="badge bg-warning">Неопубліковано</span>
                <?php endif; ?>
            </h2>
            <span class="badge bg-primary"><?= $vitamin['type'] == 'vitamin' ? 'Вітамін' : 'Мінерал' ?></span>
        </div>

        <div class="card-body">
            <div class="mb-4">
                <h5>Опис:</h5>
                <p><?= nl2br(htmlspecialchars($vitamin['description'])) ?></p>
            </div>

            <div class="mb-4">
                <h5>Користь для здоров'я:</h5>
                <p><?= nl2br(htmlspecialchars($vitamin['benefits'])) ?></p>
            </div>

            <div class="mb-4">
                <h5>Джерела (продукти харчування):</h5>
                <p><?= nl2br(htmlspecialchars($vitamin['sources'])) ?></p>
            </div>

            <div class="text-muted">
                <p>Автор: <?= htmlspecialchars($vitamin['username']) ?></p>
                <p>Додано: <?= date('d.m.Y H:i', strtotime($vitamin['date'])) ?></p>
            </div>
        </div>

        <div class="card-footer">
            <a href="index.php?action=vitamins_list" class="btn btn-primary">Повернутися до списку</a>

            <?php if ($is_admin || (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $vitamin['author_id'])): ?>
                <a href="index.php?action=update_vitamin&id=<?= $vitamin['id'] ?>" class="btn btn-secondary">Редагувати</a>
                <a href="javascript:void(0);" onclick="confirmDelete(<?= $vitamin['id'] ?>)"
                    class="btn btn-danger">Видалити</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm("Ви впевнені, що хочете видалити цей запис?")) {
            window.location.href = "index.php?action=delete_vitamin&id=" + id;
        }
    }
</script>

<?php
$stmt->close();
$conn->close();
?>