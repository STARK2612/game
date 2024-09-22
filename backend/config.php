<?php
require_once 'session.php';
require_once 'csrf.php';

// Configuration de la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestionnaireame_game";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Database connection failed.");
}
?>
