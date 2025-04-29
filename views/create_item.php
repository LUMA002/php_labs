<?php
// Перевірка авторизації
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit();
}
?>

<div class="container my-5">
    <h2>Додати новий вітамін/мінерал</h2>

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

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form action="index.php?action=item_processing&mode=create" method="post">
        <div class="form-group mb-3">
            <label for="name" class="form-label">Назва</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Тип</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="vitamin" value="vitamin" checked>
                <label class="form-check-label" for="vitamin">
                    Вітамін
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="type" id="mineral" value="mineral">
                <label class="form-check-label" for="mineral">
                    Мінерал
                </label>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="description" class="form-label">Опис</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="benefits" class="form-label">Користь для здоров'я</label>
            <textarea class="form-control" id="benefits" name="benefits" rows="3" required></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="sources" class="form-label">Джерела (продукти харчування)</label>
            <textarea class="form-control" id="sources" name="sources" rows="3" required></textarea>
        </div>

        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="visible" name="visible" value="1">
                <label class="form-check-label" for="visible">Опублікувати відразу</label>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">Додати</button>
    </form>
</div>