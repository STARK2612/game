<?php
include 'config.php';
include 'session.php';
include 'csrf.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && validate_csrf($_POST['csrf_token'])) {
    if (isset($_POST['add'])) {
        $type = $_POST['type'];
        $fournisseur = $_POST['fournisseur'];
        $prix = $_POST['prix'];
        $quantite = $_POST['quantite'];
        $date_achat = $_POST['date_achat'];
        $marque = $_POST['marque'];
        $model = $_POST['model'];
        $stock = $_POST['stock'];

        $stmt = $conn->prepare("INSERT INTO articles (type, fournisseur, prix, quantite, date_achat, marque, model, stock) VALUES (:type, :fournisseur, :prix, :quantite, :date_achat, :marque, :model, :stock)");
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':fournisseur', $fournisseur);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':date_achat', $date_achat);
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':stock', $stock);
        $stmt->execute();
        header("Location: ../frontend/gestion_articles.php");
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_articles.php");
    }

    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $type = $_POST['type'];
        $fournisseur = $_POST['fournisseur'];
        $prix = $_POST['prix'];
        $quantite = $_POST['quantite'];
        $date_achat = $_POST['date_achat'];
        $marque = $_POST['marque'];
        $model = $_POST['model'];
        $stock = $_POST['stock'];

        $stmt = $conn->prepare("UPDATE articles SET type = :type, fournisseur = :fournisseur, prix = :prix, quantite = :quantite, date_achat = :date_achat, marque = :marque, model = :model, stock = :stock WHERE id = :id");
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':fournisseur', $fournisseur);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':date_achat', $date_achat);
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_articles.php");
    }
}
?>
