
<div class="registration-container">
    <h2>Авторизація</h2>

    <?php
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
        echo '<div class="error-message">';
        echo '<p>' . $_SESSION['errors'] . '</p>';
        echo '</div>';
        unset($_SESSION['errors']);
    }
    ?>

    <form action="index.php?action=login_processing" method="POST">
        <div class="form-group">
            <label for="username">Логін</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="register-button">Увійти</button>
    </form>
</div>