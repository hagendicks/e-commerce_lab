<?php

require_once __DIR__ . '/../core/core.php';

require_once __DIR__ . '/../controllers/CartController.php';


$cartPage = BASE_URL . '/views/cart.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    redirect($cartPage);
}


$controller = new CartController();

$ip = get_ip();

$quantities = $_POST['qty'] ?? [];
$productIds = $_POST['update'] ?? [];

if (
    !is_array($quantities) ||
    !is_array($productIds) ||
    count($quantities) === 0 ||
    count($quantities) !== count($productIds)
) {

    cart_respond($controller, $ip, false, 'Nothing to update.', $cartPage);
}


$updates = [];

foreach ($productIds as $index => $rawId) {

    $productId = filter_var($rawId, FILTER_VALIDATE_INT);

    $qty = filter_var(
        $quantities[$index] ?? null,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1, 'max_range' => 99]]
    );

    if (!$productId || $productId <= 0 || $qty === false) {

        cart_respond(
            $controller,
            $ip,
            false,
            'Each quantity must be a whole number between 1 and 99.',
            $cartPage
        );
    }

    $updates[$productId] = $qty;
}


$failed = false;

foreach ($updates as $productId => $qty) {

    if (!$controller->updateQty($productId, $qty, $ip)) {
        $failed = true;
    }
}


if ($failed) {

    cart_respond($controller, $ip, false, 'Some quantities could not be updated.', $cartPage);
}

cart_respond($controller, $ip, true, 'Cart updated.', $cartPage);

?>
