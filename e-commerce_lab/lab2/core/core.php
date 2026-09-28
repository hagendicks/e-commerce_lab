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

?>
