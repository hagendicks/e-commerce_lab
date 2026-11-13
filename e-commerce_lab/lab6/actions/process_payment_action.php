<?php
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../core/paystack.php';
require_once __DIR__ . '/../core/mailer.php';
require_once __DIR__ . '/../controllers/CartController.php';

cart_guard();

$failedUrl = BASE_URL . '/views/payment_failed.php';

function payment_fail($message, $url)
{
    set_flash('error', $message);
    redirect($url);
}

$reference = trim($_GET['ref'] ?? $_GET['reference'] ?? '');

if (!preg_match('/^[A-Za-z0-9._=-]{5,100}$/', $reference)) {
    payment_fail('Invalid payment reference.', $failedUrl);
}

$controller = new CartController();
$ip         = get_ip();
$customerId = (int) $_SESSION['customer_id'];

if ($controller->getCartCount($ip) < 1) {
    payment_fail('Your cart is empty, so there is nothing to pay for.', $failedUrl);
}

$total    = $controller->getCartTotal($ip);
$expected = (int) round($total * 100);

$verify = paystack_verify($reference);

if (!$verify['success']) {
    payment_fail($verify['error'] . ' Reference: ' . $reference, $failedUrl);
}

if ($verify['amount'] !== $expected || strtoupper($verify['currency']) !== PAYSTACK_CURRENCY) {
    error_log("Payment mismatch ref=$reference paid={$verify['amount']} expected=$expected");
    payment_fail('The amount paid does not match your cart total. Reference: ' . $reference, $failedUrl);
}

$orderId = $controller->processPayment($customerId, $ip, $reference, $total, PAYSTACK_CURRENCY);

if (!$orderId) {
    payment_fail('Payment received but the order could not be saved. Please contact support with reference ' . $reference . '.', $failedUrl);
}

$order = $controller->getOrderById($orderId, $customerId);
send_order_confirmation($_SESSION['customer_email'] ?? '', $_SESSION['customer_name'] ?? '', $order);

redirect(BASE_URL . '/views/payment_success.php?order_id=' . $orderId);
?>
