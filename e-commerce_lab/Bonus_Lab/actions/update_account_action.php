<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

require_login();

$page = BASE_URL . '/views/account/edit_account.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

$data = [
    'name'    => trim(strip_tags($_POST['customer_name'] ?? '')),
    'country' => trim(strip_tags($_POST['customer_country'] ?? '')),
    'city'    => trim(strip_tags($_POST['customer_city'] ?? '')),
    'contact' => trim(strip_tags($_POST['customer_contact'] ?? ''))
];

$controller = new CustomerController();

$result = $controller->updateProfile((int) $_SESSION['customer_id'], $data);

if (!$result['success']) {
    set_flash('error', $result['error']);
    redirect($page);
}

$_SESSION['customer_name'] = $data['name'];

set_flash('success', 'Your details have been updated.');

redirect(BASE_URL . '/views/account/my_account.php');

?>
