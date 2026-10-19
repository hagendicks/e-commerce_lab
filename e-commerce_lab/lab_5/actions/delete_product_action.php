<?php

require_once __DIR__ . '/../core/core.php';

require_once __DIR__ . '/../controllers/ProductController.php';

header('Content-Type: application/json');


if (!is_admin()) {

    http_response_code(403);

    echo json_encode([
        'success' => false,
        'error' => 'Administrator access required.'
    ]);

    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'error' => 'POST request required.'
    ]);

    exit;
}


$id = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {

    echo json_encode([
        'success' => false,
        'error' => 'Invalid product ID.'
    ]);

    exit;
}


$controller = new ProductController();

$product = $controller->getProductById($id);

if (!$product) {

    echo json_encode([
        'success' => false,
        'error' => 'Product not found.'
    ]);

    exit;
}


if ($controller->deleteProduct($id)) {

    delete_product_image($product['product_image']);

    echo json_encode([
        'success' => true,
        'message' => 'Product deleted successfully.'
    ]);

    exit;
}


echo json_encode([
    'success' => false,
    'error' => 'Unable to delete this product. It may be part of an existing order.'
]);

?>
