<?php
session_start();

function is_logged_in() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../frontend/login.php");
        exit;
    }
}

function check_inactivity() {
    $timeout = 1800; // Durée d'inactivité en secondes (30 minutes)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
        session_unset();
        session_destroy();
        header("Location: ../frontend/login.php");
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// Charger les paramètres de configuration des couleurs
$config_file = '../backend/config.json';
if (file_exists($config_file)) {
    $config = json_decode(file_get_contents($config_file), true);
    if (isset($config['nav_item_color'])) {
        $_SESSION['nav_item_color'] = $config['nav_item_color'];
    }
    if (isset($config['nav_link_hover_color'])) {
        $_SESSION['nav_link_hover_color'] = $config['nav_link_hover_color'];
    }
    if (isset($config['footer_bg_color'])) {
        $_SESSION['footer_bg_color'] = $config['footer_bg_color'];
    }
}
?>
