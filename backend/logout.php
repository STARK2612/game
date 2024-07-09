<?php
include 'config.php';
include 'session.php';
include 'csrf.php';

session_start(); // Démarrer la session

// Ajouter des messages de débogage
echo "Début de la destruction de la session...\n";

// Détruire toutes les variables de session
$_SESSION = array();
echo "Variables de session détruites.\n";

// Si vous souhaitez détruire complètement la session, effacer également le cookie de session.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
    echo "Cookie de session effacé.\n";
}

// Détruire la session.
session_destroy();
echo "Session détruite.\n";

// Rediriger vers la page de connexion
header("Location: ../frontend/index.php");
exit;
?>
