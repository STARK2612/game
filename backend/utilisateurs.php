<?php
include 'config.php';
include 'session.php';
include 'csrf.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && validate_csrf($_POST['csrf_token'])) {
    if (isset($_POST['add'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $identifiant = $_POST['identifiant'];
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, prenom, identifiant, mot_de_passe, role) VALUES (:nom, :prenom, :identifiant, :mot_de_passe, :role)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':identifiant', $identifiant);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        header("Location: ../frontend/gestion_utilisateurs.php");
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_utilisateurs.php");
    }

    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $identifiant = $_POST['identifiant'];
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $stmt = $conn->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, identifiant = :identifiant, mot_de_passe = :mot_de_passe, role = :role WHERE id = :id");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':identifiant', $identifiant);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_utilisateurs.php");
    }
}
?>
