```php
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

$id = filter_var(
    $_POST['brand_id'] ?? null,
    FILTER_VALIDATE_INT
);

if (!$id || $id <= 0) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid brand ID.'
    ]);

    exit;
}

$controller = new ProductController();

$success = $controller->deleteBrand($id);

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Brand deleted successfully.'
    ]);

    exit;
}

echo json_encode([
    'success' => false,
    'error' => 'Unable to delete brand. It may be linked to existing products.'
]);

?>
```
