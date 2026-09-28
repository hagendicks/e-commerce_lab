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


$name = trim(
    strip_tags($_POST['customer_name'] ?? '')
);

$email = trim(
    strip_tags($_POST['customer_email'] ?? '')
);

$password = $_POST['customer_pass'] ?? '';

$country = trim(
    strip_tags($_POST['customer_country'] ?? '')
);

$city = trim(
    strip_tags($_POST['customer_city'] ?? '')
);

$contact = trim(
    strip_tags($_POST['customer_contact'] ?? '')
);


if ($name === '') {

    echo json_encode([
        'success' => false,
        'error' => 'Please enter your full name.'
    ]);

    exit;
}


if (
    !filter_var(
        $email,
        FILTER_VALIDATE_EMAIL
    )
) {

    echo json_encode([
        'success' => false,
        'error' => 'Please enter a valid email.'
    ]);

    exit;
}


if (strlen($password) < 8) {

    echo json_encode([
        'success' => false,
        'error' => 'Password must be at least 8 characters.'
    ]);

    exit;
}


if ($country === '') {

    echo json_encode([
        'success' => false,
        'error' => 'Please select your country.'
    ]);

    exit;
}


if ($city === '') {

    echo json_encode([
        'success' => false,
        'error' => 'Please enter your city.'
    ]);

    exit;
}


if (!preg_match('/^[0-9+\-\s]{7,15}$/', $contact)) {

    echo json_encode([
        'success' => false,
        'error' => 'Please enter a valid contact number.'
    ]);

    exit;
}


$controller = new CustomerController();


$result = $controller->register([
    'name' => $name,
    'email' => $email,
    'password' => $password,
    'country' => $country,
    'city' => $city,
    'contact' => $contact
]);


if ($result['success']) {

    echo json_encode([
        'success' => true,
        'message' => 'Registration successful.'
    ]);

    exit;
}


echo json_encode($result);

?>
