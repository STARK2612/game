<?php
require_once 'session.php';
require_once 'csrf.php';

// Configuration de la base de données
$servername = "mysql-gestionnaireame.alwaysdata.net";
$username = "367587";
$password = "Aub1w@n3Ken0b1Master";
$dbname = "gestionnaireame_game";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Connection failed: " . $e->getMessage());
    die("Database connection failed.");
}
?>
