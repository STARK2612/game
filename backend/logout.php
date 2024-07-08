<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Détruire toutes les variables de session
$_SESSION = array();

// Si vous souhaitez détruire complètement la session, effacer également le cookie de session.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalement, détruire la session.
session_destroy();

// Ajoutez des messages de débogage
error_log("Session destroyed. Redirecting to login page.");

// Rediriger vers la page de connexion
header("Location: ../frontend/index.php");
exit;
?>
