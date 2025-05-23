<?php
require_once './includes/db_config.php';

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// визначаємо ім'я кеш-файлу в залежності від прав користувача
$cache_file = './cache/items_list_' . ($is_admin ? 'admin' : 'user') . '.json';

// отримання даних з БД
function getDataFromDatabase($is_admin)
{
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    // перевірка підключення
    if ($conn->connect_error) {
        die("Помилка підключення: " . $conn->connect_error);
    }

    // формуємо SQL запит в залежності від прав користувача
    if ($is_admin) {
        // адміністратор бачить всі записи, включно з неопублікованими
        $sql = "SELECT vm.*, u.username FROM vitamins_minerals vm 
                LEFT JOIN users u ON vm.author_id = u.id 
                ORDER BY vm.date DESC";
    } else {
        // звичайні користувачі бачать лише опубліковані записи
        $sql = "SELECT vm.*, u.username FROM vitamins_minerals vm 
                LEFT JOIN users u ON vm.author_id = u.id 
                WHERE vm.visible = 1 
                ORDER BY vm.date DESC";
    }

    $result = $conn->query($sql);
    $data = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    $conn->close();
    return $data;
}

// перевіряємо, чи існує кеш-файл
$items = [];
$from_cache = false;

if (file_exists($cache_file)) {
    $cache_content = file_get_contents($cache_file);
    $items = json_decode($cache_content, true);
    $from_cache = true;
}

// якщо кеш не існує або порожній, отримуємо дані з БД
if (empty($items)) {
    $items = getDataFromDatabase($is_admin);

    // зберігаємо дані в кеш
    if (!empty($items)) {
        if (!is_dir('./cache')) {
            mkdir('./cache', 0755, true);
        }
        file_put_contents($cache_file, json_encode($items));
    }
}
?>

<div class="container my-5">
    <h2>Вітаміни та мінерали</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['errors'] ?>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <?php if ($from_cache): ?>
        <div class="text-muted small mb-3">Дані завантажено з кешу</div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
        <div class="row">
            <?php foreach ($items as $row): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 <?= !$row['visible'] ? 'bg-light' : '' ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?>
                                <?php if (!$row['visible']): ?>
                                    <span class="badge bg-warning">Неопубліковано</span>
                                <?php endif; ?>
                            </h5>
                            <h6 class="card-subtitle mb-2 text-muted">
                                <?= $row['type'] == 'vitamin' ? 'Вітамін' : 'Мінерал' ?>
                            </h6>
                            <p class="card-text">
                                <?= substr(htmlspecialchars($row['description']), 0, 100) ?>...
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Автор: <?= htmlspecialchars($row['username']) ?></small>
                                <small class="text-muted">Додано: <?= date('d.m.Y', strtotime($row['date'])) ?></small>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <a href="index.php?action=view_vitamin&id=<?= $row['id'] ?>" class="btn btn-primary">Перегляд</a>

                            <?php if ($is_admin || (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['author_id'])): ?>
                                <div>
                                    <a href="index.php?action=update_vitamin&id=<?= $row['id'] ?>"
                                        class="btn btn-secondary">Редагувати</a>
                                    <a href="javascript:void(0);" onclick="confirmDelete(<?= $row['id'] ?>)"
                                        class="btn btn-danger">Видалити</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="alert alert-info">Наразі немає доданих вітамінів або мінералів.</p>
    <?php endif; ?>
</div>

<script>
    function confirmDelete(id) {
        if (confirm("Ви впевнені, що хочете видалити цей запис?")) {
            window.location.href = "index.php?action=delete_vitamin&id=" + id;
        }
    }
</script>