<?php
// Ініціалізація сесії для збереження повідомлень про помилки
session_start();

require_once './layout/header.php';
?>
<?php require_once './layout/container_start.php'; ?>

<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'main';

$file = "./views/{$action}.php";

if (
    file_exists($file) && $action != 'vitamins_list' && $action != 'create_vitamin' &&
    $action != 'view_vitamin' && $action != 'update_vitamin' && $action != 'delete_vitamin' &&
    $action != 'item_processing'
) {
    require_once $file;
} else {
    switch ($action) {
        case 'vitamins_list':
            include_once 'views/items_list.php';
            break;

        case 'create_vitamin':
            if (!isset($_SESSION['user_id'])) {
                header("Location: index.php?action=login");
                exit();
            }
            include_once 'views/create_item.php';
            break;

        case 'view_vitamin':
            include_once 'views/view_item.php';
            break;

        case 'update_vitamin':
            if (!isset($_SESSION['user_id'])) {
                header("Location: index.php?action=login");
                exit();
            }
            include_once 'views/update_item.php';
            break;

        case 'delete_vitamin':
            if (!isset($_SESSION['user_id'])) {
                header("Location: index.php?action=login");
                exit();
            }
            include_once 'views/delete_item.php';
            break;

        case 'item_processing':
            if (!isset($_SESSION['user_id'])) {
                header("Location: index.php?action=login");
                exit();
            }
            include_once 'views/item_processing.php';
            break;

        default:
            include_once 'views/main.php';
            break;
    }
}

if ($action !== 'registration' && $action !== 'registration_successful') {
    require_once './layout/left_menu.php';
}
?>
<?php require_once './layout/container_end.php'; ?>
<?php require_once './layout/footer.php'; ?>