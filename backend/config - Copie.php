<?php
$servername = "mysql-gestionnaireame.alwaysdata.net";
$username = "367587";
$password = "Aub1w@n3Ken0b1Master";
$dbname = "gestionnaireame_game";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
