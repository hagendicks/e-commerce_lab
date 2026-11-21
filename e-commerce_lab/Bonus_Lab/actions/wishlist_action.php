<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/WishlistController.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$back = back_url(BASE_URL . '/index.php');

function wishlist_respond($success, $message, $back, $extra = [])
{
    if (is_ajax()) {

        $count = is_logged_in()
            ? (new WishlistController())->getCount((int) $_SESSION['customer_id'])
            : 0;

        json_response(array_merge([
            'success' => $success,
            'message' => $message,
            'wish_count' => $count
        ], $extra));
    }

    set_flash($success ? 'success' : 'error', $message);

    redirect($back);
}


if (!is_logged_in()) {

    $loginUrl = BASE_URL . '/views/login.php';
    $message = 'Please log in to save items to your wishlist.';

    if (is_ajax()) {

        json_response([
            'success' => false,
            'login_required' => true,
            'message' => $message,
            'redirect' => $loginUrl
        ]);
    }

    set_flash('error', $message);

    redirect($loginUrl);
}


$customerId = (int) $_SESSION['customer_id'];

$toggleId = filter_input(INPUT_GET, 'toggle', FILTER_VALIDATE_INT);
$removeId = filter_input(INPUT_GET, 'remove', FILTER_VALIDATE_INT);

$productId = $toggleId ?: $removeId;

if (!$productId || $productId <= 0) {
    wishlist_respond(false, 'Invalid product.', $back);
}

$productController = new ProductController();

if (!$productController->getProductById($productId)) {
    wishlist_respond(false, 'That product no longer exists.', $back);
}

$controller = new WishlistController();

if ($removeId) {

    if ($controller->remove($customerId, $productId)) {
        wishlist_respond(true, 'Removed from your wishlist.', $back, ['added' => false]);
    }

    wishlist_respond(false, 'Unable to update your wishlist.', $back);
}

$result = $controller->toggle($customerId, $productId);

if (!$result['success']) {
    wishlist_respond(false, 'Unable to update your wishlist.', $back);
}

wishlist_respond(
    true,
    $result['added'] ? 'Saved to your wishlist.' : 'Removed from your wishlist.',
    $back,
    ['added' => $result['added']]
);

?>
