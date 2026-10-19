<?php

session_start();

date_default_timezone_set('Africa/Accra');


function detect_base_url()
{
    $normalize = static function ($path) {
        $path = preg_replace('#/+#', '/', str_replace('\\', '/', (string) $path));
        return rtrim($path, '/');
    };

    $appRoot = $normalize(realpath(dirname(__DIR__)) ?: dirname(__DIR__));
    $scriptFile = $normalize(
        isset($_SERVER['SCRIPT_FILENAME'])
            ? (realpath($_SERVER['SCRIPT_FILENAME']) ?: $_SERVER['SCRIPT_FILENAME'])
            : ''
    );
    $scriptName = $normalize($_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '');

    if ($appRoot !== '' && $scriptFile !== '' && $scriptName !== '') {
        if (stripos($scriptFile, $appRoot) === 0) {
            $relative = '/' . ltrim(substr($scriptFile, strlen($appRoot)), '/');
            $scriptNamePath = '/' . ltrim($scriptName, '/');
            $relLen = strlen($relative);

            if ($relLen > 1 && strlen($scriptNamePath) >= $relLen
                && strcasecmp(substr($scriptNamePath, -$relLen), $relative) === 0) {
                $base = substr($scriptNamePath, 0, -$relLen);
                return $base === false || $base === '' ? '' : rtrim($base, '/');
            }
        }
    }

    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    if (preg_match('#^(.*?/shoppn)(?:/|$)#i', $requestPath, $matches)) {
        return rtrim($matches[1], '/');
    }

    return '/shoppn';
}

define('BASE_URL', detect_base_url());

require_once __DIR__ . '/db_class.php';


function redirect($url)
{
    header("Location: " . $url);
    exit;
}


function get_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}


function is_logged_in()
{
    return isset($_SESSION['customer_id']);
}


function is_admin()
{
    return isset($_SESSION['user_role']) &&
           (int) $_SESSION['user_role'] === 1;
}


function require_login()
{
    if (!is_logged_in()) {
        $_SESSION['error'] = 'Please log in first.';
        redirect(BASE_URL . '/views/login.php');
    }
}


function require_admin()
{
    if (!is_admin()) {
        $_SESSION['error'] = 'Administrator access required.';
        redirect(BASE_URL . '/index.php');
    }
}
define('PRODUCT_IMAGE_DIR', dirname(__DIR__) . '/images/products/');
define('PRODUCT_IMAGE_MAX_BYTES', 2 * 1024 * 1024);


function product_image_url($filename)
{
    if (empty($filename)) {
        return null;
    }

    return BASE_URL . '/images/products/' . rawurlencode(basename($filename));
}


function save_product_image($file)
{
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'The image could not be uploaded.'];
    }

    if ($file['size'] > PRODUCT_IMAGE_MAX_BYTES) {
        return ['success' => false, 'error' => 'Image must be 2 MB or smaller.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);

    if (!isset($allowed[$mime])) {
        return ['success' => false, 'error' => 'Only JPG, PNG, GIF or WEBP images are allowed.'];
    }

    if (!is_dir(PRODUCT_IMAGE_DIR)) {
        mkdir(PRODUCT_IMAGE_DIR, 0755, true);
    }

    $filename = 'prod_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];

    if (!move_uploaded_file($file['tmp_name'], PRODUCT_IMAGE_DIR . $filename)) {
        return ['success' => false, 'error' => 'Unable to save the uploaded image.'];
    }

    return ['success' => true, 'filename' => $filename];
}


function delete_product_image($filename)
{
    if (empty($filename)) {
        return;
    }

    $path = PRODUCT_IMAGE_DIR . basename($filename);

    if (is_file($path)) {
        unlink($path);
    }
}
function set_flash($type, $text)
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}


function pull_flash()
{
    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];

    unset($_SESSION['flash']);

    return $flash;
}


function back_url($fallback)
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '';

    if ($referer === '') {
        return $fallback;
    }

    $parts = parse_url($referer);
    $host = explode(':', $_SERVER['HTTP_HOST'] ?? '')[0];

    if (empty($parts['host']) || strcasecmp($parts['host'], $host) !== 0) {
        return $fallback;
    }

    return $referer;
}
function is_ajax()
{
    return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
}


function json_response($data)
{
    header('Content-Type: application/json');

    echo json_encode($data);

    exit;
}


function cart_respond($controller, $ip, $success, $message, $redirectUrl, $extra = [])
{
    if (is_ajax()) {

        $summary = $controller->getCartSummary($ip);

        json_response(array_merge([
            'success' => $success,
            'message' => $message,
            'cart_count' => $summary['count'],
            'cart_total' => $summary['total'],
            'lines' => $summary['lines']
        ], $extra));
    }

    set_flash($success ? 'success' : 'error', $message);

    redirect($redirectUrl);
}

?>
