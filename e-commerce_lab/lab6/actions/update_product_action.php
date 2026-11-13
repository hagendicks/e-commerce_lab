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


$id       = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
$cat      = filter_var($_POST['product_cat'] ?? null, FILTER_VALIDATE_INT);
$brand    = filter_var($_POST['product_brand'] ?? null, FILTER_VALIDATE_INT);
$title    = trim(strip_tags($_POST['product_title'] ?? ''));
$price    = filter_var($_POST['product_price'] ?? null, FILTER_VALIDATE_FLOAT);
$desc     = trim(strip_tags($_POST['product_desc'] ?? ''));
$keywords = trim(strip_tags($_POST['product_keywords'] ?? ''));


$error = null;

if (!$id || $id <= 0) {
    $error = 'Invalid product ID.';
} elseif (!$cat || $cat <= 0) {
    $error = 'Please choose a category.';
} elseif (!$brand || $brand <= 0) {
    $error = 'Please choose a brand.';
} elseif ($title === '') {
    $error = 'Product title is required.';
} elseif (strlen($title) > 200) {
    $error = 'Product title must be 200 characters or fewer.';
} elseif ($price === false || $price === null || $price <= 0) {
    $error = 'Enter a valid price greater than 0.';
} elseif (strlen($desc) > 500) {
    $error = 'Description must be 500 characters or fewer.';
} elseif (strlen($keywords) > 100) {
    $error = 'Keywords must be 100 characters or fewer.';
}

if ($error !== null) {

    echo json_encode([
        'success' => false,
        'error' => $error
    ]);

    exit;
}


$controller = new ProductController();

$existing = $controller->getProductById($id);

if (!$existing) {

    echo json_encode([
        'success' => false,
        'error' => 'Product not found.'
    ]);

    exit;
}

$filename = $existing['product_image'];
$newImage = null;


if (
    isset($_FILES['product_image']) &&
    $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE
) {

    $upload = save_product_image($_FILES['product_image']);

    if (!$upload['success']) {

        echo json_encode([
            'success' => false,
            'error' => $upload['error']
        ]);

        exit;
    }

    $newImage = $upload['filename'];
    $filename = $newImage;
}


$success = $controller->updateProduct(
    $id,
    $cat,
    $brand,
    $title,
    $price,
    $desc,
    $filename,
    $keywords
);


if ($success) {

    if ($newImage !== null) {
        delete_product_image($existing['product_image']);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Product updated successfully.'
    ]);

    exit;
}


if ($newImage !== null) {
    delete_product_image($newImage);
}

echo json_encode([
    'success' => false,
    'error' => 'Unable to update product.'
]);

?>
