
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

header('Content-Type: application/json');

function sendResponse($success, $message)
{
    echo json_encode([
        'success' => $success,
        'error' => $success ? null : $message,
        'message' => $success ? $message : null
    ]);

    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    sendResponse(false, 'POST request required.');
}

$name = trim(strip_tags($_POST['customer_name'] ?? ''));

$email = trim(strip_tags($_POST['customer_email'] ?? ''));

$password = $_POST['customer_pass'] ?? '';

$confirmPassword = $_POST['confirm_password'] ?? '';

$country = trim(strip_tags($_POST['customer_country'] ?? ''));

$city = trim(strip_tags($_POST['customer_city'] ?? ''));

$contact = trim(strip_tags($_POST['customer_contact'] ?? ''));

if ($name === '') {
    sendResponse(false, 'Please enter your full name.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(false, 'Please enter a valid email.');
}


if (strlen($password) < 8) {
    sendResponse(false, 'Password must contain at least 8 characters.');
}

if (!preg_match('/[A-Z]/', $password)) {
    sendResponse(false, 'Password must contain at least one uppercase letter.');
}

if (!preg_match('/[a-z]/', $password)) {
    sendResponse(false, 'Password must contain at least one lowercase letter.');
}

if (!preg_match('/[0-9]/', $password)) {
    sendResponse(false, 'Password must contain at least one number.');
}


if (!preg_match('/[^A-Za-z0-9]/', $password)) {
    sendResponse(false, 'Password must contain at least one special character.');
}


if ($password !== $confirmPassword) {
    sendResponse(false, 'Passwords do not match.');
}

if ($country === '') {
    sendResponse(false, 'Please select your country.');
}


if ($city === '') {
    sendResponse(false, 'Please enter your city.');
}

if (!preg_match('/^[0-9+\-\s]{7,15}$/', $contact)) {
    sendResponse(false, 'Please enter a valid contact number.');
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


// Return response
if ($result['success']) {

    sendResponse(true, 'Registration successful.');

}

sendResponse(false, $result['error']);

?>
