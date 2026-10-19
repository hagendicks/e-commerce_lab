<?php

require_once __DIR__ . '/../core/core.php';

require_once __DIR__ . '/../controllers/CartController.php';


$cartPage = BASE_URL . '/views/cart.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    redirect($cartPage);
}


$controller = new CartController();

$ip = get_ip();

$selected = $_POST['remove'] ?? [];

if (!is_array($selected) || count($selected) === 0) {

    cart_respond($controller, $ip, false, 'Select at least one item to remove.', $cartPage);
}


$removedIds = [];

foreach ($selected as $value) {

    $productId = filter_var($value, FILTER_VALIDATE_INT);

    if (!$productId || $productId <= 0) {
        continue;
    }

    if ($controller->removeFromCart($productId, $ip)) {
        $removedIds[] = $productId;
    }
}


if (count($removedIds) > 0) {

    $message = count($removedIds) === 1
        ? 'Item removed from your cart.'
        : count($removedIds) . ' items removed from your cart.';

    cart_respond($controller, $ip, true, $message, $cartPage, ['removed' => $removedIds]);
}

cart_respond($controller, $ip, false, 'Unable to remove the selected items.', $cartPage);

?>
