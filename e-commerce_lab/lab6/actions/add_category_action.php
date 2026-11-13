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


$name =
    trim(
        strip_tags(
            $_POST['cat_name'] ?? ''
        )
    );


if ($name === '') {

    echo json_encode([
        'success' => false,
        'error' => 'Category name is required.'
    ]);

    exit;
}


if (strlen($name) > 100) {

    echo json_encode([
        'success' => false,
        'error' => 'Category name is too long.'
    ]);

    exit;
}


$controller =
    new ProductController();


$success =
    $controller->addCategory($name);


if ($success) {

    echo json_encode([
        'success' => true,
        'message' => 'Category added successfully.'
    ]);

    exit;
}


echo json_encode([
    'success' => false,
    'error' => 'Unable to add category.'
]);

?>
