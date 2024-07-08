<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generate_csrf() {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    return $token;
}

function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        throw new Exception('Invalid CSRF token');
    }
    unset($_SESSION['csrf_token']);
}
?>
