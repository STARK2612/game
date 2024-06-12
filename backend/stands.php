<?php
include 'config.php';
include 'session.php';
include 'csrf.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && validate_csrf($_POST['csrf_token'])) {
    if (isset($_POST['add'])) {
        $nom = $_POST['nom'];
        $adresse = $_POST['adresse'];
        $code_postal = $_POST['code_postal'];
        $ville = $_POST['ville'];
        $pays = $_POST['pays'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        $prix_invite = $_POST['prix_invite'];

        $stmt = $conn->prepare("INSERT INTO stands (nom, adresse, code_postal, ville, pays, telephone, email, prix_invite) VALUES (:nom, :adresse, :code_postal, :ville, :pays, :telephone, :email, :prix_invite)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':code_postal', $code_postal);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':pays', $pays);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':prix_invite', $prix_invite);
        $stmt->execute();
        header("Location: ../frontend/gestion_stands.php");
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM stands WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_stands.php");
    }

    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $adresse = $_POST['adresse'];
        $code_postal = $_POST['code_postal'];
        $ville = $_POST['ville'];
        $pays = $_POST['pays'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        $prix_invite = $_POST['prix_invite'];

        $stmt = $conn->prepare("UPDATE stands SET nom = :nom, adresse = :adresse, code_postal = :code_postal, ville = :ville, pays = :pays, telephone = :telephone, email = :email, prix_invite = :prix_invite WHERE id = :id");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':code_postal', $code_postal);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':pays', $pays);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':prix_invite', $prix_invite);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_stands.php");
    }
}
?>
