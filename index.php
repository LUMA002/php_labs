<?php require_once './layout/header.php'; ?>
<?php require_once './layout/container_start.php'; ?>
<?php require_once './layout/left_menu.php'; ?>

<?php
$action = isset($_GET['action']) ? $_GET['action'] : 'main';

$file = "./views/{$action}.php";

if (file_exists($file)) {
    require_once $file;
} else {
    require_once './views/main.php'; 
}
?>

<?php require_once './layout/container_end.php'; ?>
<?php require_once './layout/footer.php'; ?>
