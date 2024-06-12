<?php
include 'config.php';
include 'session.php';
include 'csrf.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && validate_csrf($_POST['csrf_token'])) {
    if (isset($_POST['add'])) {
        $marque = $_POST['marque'];
        $model = $_POST['model'];
        $calibre = $_POST['calibre'];
        $etat = $_POST['etat'];
        $date_achat = $_POST['date_achat'];

        $stmt = $conn->prepare("INSERT INTO armes (marque, model, calibre, etat, date_achat) VALUES (:marque, :model, :calibre, :etat, :date_achat)");
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':calibre', $calibre);
        $stmt->bindParam(':etat', $etat);
        $stmt->bindParam(':date_achat', $date_achat);
        $stmt->execute();
        header("Location: ../frontend/gestion_armes.php");
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM armes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_armes.php");
    }

    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $marque = $_POST['marque'];
        $model = $_POST['model'];
        $calibre = $_POST['calibre'];
        $etat = $_POST['etat'];
        $date_achat = $_POST['date_achat'];

        $stmt = $conn->prepare("UPDATE armes SET marque = :marque, model = :model, calibre = :calibre, etat = :etat, date_achat = :date_achat WHERE id = :id");
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':calibre', $calibre);
        $stmt->bindParam(':etat', $etat);
        $stmt->bindParam(':date_achat', $date_achat);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_armes.php");
    }
}
?>
