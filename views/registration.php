<div class="registration-container">
    <h2>Реєстрація нового користувача</h2>

    <?php
    // Відображення помилок валідації
    if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
        echo '<div class="error-message">';
        echo '<p>Будь ласка, виправте наступні помилки:</p>';
        echo '<ul>';
        foreach ($_SESSION['errors'] as $error) {
            echo '<li>' . $error . '</li>';
        }
        echo '</ul>';
        echo '</div>';

        // очищення сесії з помилками
        unset($_SESSION['errors']);
    }
    ?>

    <form action="index.php?action=register_processing" method="POST">
        <div class="form-group">
            <label for="username">Логін</label>
            <input type="text" id="username" name="username" required
                value="<?php echo isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : ''; ?>">
            <small>Не менше 4 символів, тільки літери, цифри, нижнє підкреслення та дефіс</small>
        </div>
        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
            <small>Не менше 7 символів, обов'язково великі та малі літери, цифри</small>
        </div>
        <div class="form-group">
            <label for="confirm_password">Повторіть пароль</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="form-group">
            <label for="email">Електронна пошта</label>
            <input type="email" id="email" name="email" required
                value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
        </div>

        <!-- Google reCAPTCHA -->
        <div class="form-group">
            <div class="g-recaptcha" data-sitekey="6LdGBPgqAAAAALJN66tee-S-DZF1nwIwp0zEqa2N"></div>
            <small>Будь ласка, підтвердіть, що ви не робот</small>
        </div>

        <button type="submit" class="register-button">Зареєструватися</button>
    </form>
</div>

<!-- Підключення Google reCAPTCHA API -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>