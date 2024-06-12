<?php
session_start();

if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../frontend/index.php");
            exit;
        }
    }
}

if (!function_exists('check_inactivity')) {
    function check_inactivity() {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 600)) {
            session_unset();
            session_destroy();
            header("Location: ../frontend/index.php");
            exit;
        }
        $_SESSION['last_activity'] = time();
    }
}
?>
