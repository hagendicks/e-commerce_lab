<?php

require_once __DIR__ . '/../core/core.php';

require_once __DIR__ . '/../controllers/CartController.php';

require_once __DIR__ . '/../controllers/ProductController.php';


$back = back_url(BASE_URL . '/index.php');

$cartController = new CartController();

$ip = get_ip();

$productId = filter_input(INPUT_GET, 'add_cart', FILTER_VALIDATE_INT);

if (!$productId || $productId <= 0) {

    cart_respond($cartController, $ip, false, 'Invalid product.', $back);
}


$productController = new ProductController();

if (!$productController->getProductById($productId)) {

    cart_respond($cartController, $ip, false, 'That product no longer exists.', $back);
}


if ($cartController->isInCart($productId, $ip)) {

    cart_respond(
        $cartController,
        $ip,
        false,
        'This item is already in your cart. Go to cart to update quantity.',
        $back
    );
}


$customerId = $_SESSION['customer_id'] ?? null;

if ($cartController->addToCart($productId, 1, $ip, $customerId)) {

    cart_respond($cartController, $ip, true, 'Item added to your cart.', $back);
}

cart_respond($cartController, $ip, false, 'Unable to add the item to your cart.', $back);

?>
