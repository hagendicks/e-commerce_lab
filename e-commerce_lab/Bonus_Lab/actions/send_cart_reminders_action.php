<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../core/mailer.php';
require_once __DIR__ . '/../controllers/CartController.php';

require_admin();

$hours = filter_input(INPUT_POST, 'hours', FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 720]]);

if ($hours === false || $hours === null) {
    $hours = 24;
}

$page = BASE_URL . '/views/admin/reminders.php?hours=' . $hours;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect($page);
}

$controller = new CartController();

$sent   = 0;
$failed = 0;

foreach ($controller->getAbandonedCarts($hours) as $row) {

    $customerId = (int) $row['customer_id'];

    $items = $controller->getCartItemsByCustomer($customerId);

    if (send_cart_reminder($row['customer_email'], $row['customer_name'], $items, (float) $row['total'])) {

        $controller->markReminderSent($customerId);

        $sent++;

    } else {

        $failed++;
    }
}

if ($sent === 0 && $failed === 0) {
    set_flash('error', 'There were no carts to remind.');
} elseif ($failed === 0) {
    set_flash('success', $sent . ' reminder email' . ($sent === 1 ? '' : 's') . ' sent.');
} else {
    set_flash('error', $sent . ' sent, ' . $failed . ' failed. Check that the server can send email.');
}

redirect($page);

?>
