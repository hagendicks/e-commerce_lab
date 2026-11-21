<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

require_login();

$page = BASE_URL . '/views/account/delete_account.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

if (empty($_POST['confirm_delete'])) {
    set_flash('error', 'Please tick the box to confirm.');
    redirect($page);
}

$controller = new CustomerController();

$result = $controller->deleteAccount((int) $_SESSION['customer_id'], $_POST['password'] ?? '');

if (!$result['success']) {
    set_flash('error', $result['error']);
    redirect($page);
}

session_unset();
session_destroy();

session_start();
set_flash('success', 'Your account has been deleted.');

redirect(BASE_URL . '/index.php');

?>
