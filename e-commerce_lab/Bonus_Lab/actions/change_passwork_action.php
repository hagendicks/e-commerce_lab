<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

require_login();

$page = BASE_URL . '/views/account/change_pass.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

$current = $_POST['current_password'] ?? '';
$new     = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

$controller = new CustomerController();

$result = $controller->changePassword((int) $_SESSION['customer_id'], $current, $new, $confirm);

if (!$result['success']) {
    set_flash('error', $result['error']);
    redirect($page);
}

session_regenerate_id(true);

set_flash('success', 'Your password has been changed.');

redirect(BASE_URL . '/views/account/my_account.php');

?>
