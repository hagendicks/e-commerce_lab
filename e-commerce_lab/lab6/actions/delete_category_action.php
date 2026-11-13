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


$id =
    filter_var(
        $_POST['cat_id'] ?? null,
        FILTER_VALIDATE_INT
    );


if (!$id || $id <= 0) {

    echo json_encode([
        'success' => false,
        'error' => 'Invalid category ID.'
    ]);

    exit;
}


$controller =
    new ProductController();


$result =
    $controller->deleteCategory($id);


echo json_encode($result);

exit;

?>
