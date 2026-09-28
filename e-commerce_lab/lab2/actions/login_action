<?php

require_once __DIR__ . '/../core/core.php';

require_once __DIR__ . '/../controllers/CustomerController.php';

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'success' => false,
        'error' => 'POST request required.'
    ]);

    exit;
}


$email = trim(
    strip_tags($_POST['customer_email'] ?? '')
);

$password =
    $_POST['customer_pass'] ?? '';


if (
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {

    echo json_encode([
        'success' => false,
        'error' => 'Invalid email address.'
    ]);

    exit;
}


if ($password === '') {

    echo json_encode([
        'success' => false,
        'error' => 'Password is required.'
    ]);

    exit;
}


$controller =
    new CustomerController();


$result =
    $controller->login(
        $email,
        $password
    );


if (!$result['success']) {

    echo json_encode($result);

    exit;
}


$customer =
    $result['customer'];


session_regenerate_id(true);


$_SESSION['customer_id'] =
    $customer['customer_id'];

$_SESSION['customer_name'] =
    $customer['customer_name'];

$_SESSION['customer_email'] =
    $customer['customer_email'];

$_SESSION['user_role'] =
    (int) $customer['user_role'];


echo json_encode([
    'success' => true,
    'message' => 'Login successful.'
]);

?>
