<?php
include 'config.php';
include 'session.php';
include 'csrf.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && validate_csrf($_POST['csrf_token'])) {
    if (isset($_POST['add'])) {
        $arme_id = $_POST['arme_id'];
        $nombre_munitions = $_POST['nombre_munitions'];
        $stock = $_POST['stock'];
        $stand_id = $_POST['stand_id'];
        $date_seance = $_POST['date_seance'];
        $heure_seance = $_POST['heure_seance'];
        $commentaire = $_POST['commentaire'];
        $invite_nom = $_POST['invite_nom'];
        $invite_prenom = $_POST['invite_prenom'];
        $invite_date_naissance = $_POST['invite_date_naissance'];
        $munitions_tirees = $_POST['munitions_tirees'];

        $stmt = $conn->prepare("INSERT INTO seances (arme_id, nombre_munitions, stock, stand_id, date_seance, heure_seance, commentaire, invite_nom, invite_prenom, invite_date_naissance, munitions_tirees) VALUES (:arme_id, :nombre_munitions, :stock, :stand_id, :date_seance, :heure_seance, :commentaire, :invite_nom, :invite_prenom, :invite_date_naissance, :munitions_tirees)");
        $stmt->bindParam(':arme_id', $arme_id);
        $stmt->bindParam(':nombre_munitions', $nombre_munitions);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':stand_id', $stand_id);
        $stmt->bindParam(':date_seance', $date_seance);
        $stmt->bindParam(':heure_seance', $heure_seance);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->bindParam(':invite_nom', $invite_nom);
        $stmt->bindParam(':invite_prenom', $invite_prenom);
        $stmt->bindParam(':invite_date_naissance', $invite_date_naissance);
        $stmt->bindParam(':munitions_tirees', $munitions_tirees);
        $stmt->execute();
        header("Location: ../frontend/gestion_seances.php");
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM seances WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_seances.php");
    }

    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $arme_id = $_POST['arme_id'];
        $nombre_munitions = $_POST['nombre_munitions'];
        $stock = $_POST['stock'];
        $stand_id = $_POST['stand_id'];
        $date_seance = $_POST['date_seance'];
        $heure_seance = $_POST['heure_seance'];
        $commentaire = $_POST['commentaire'];
        $invite_nom = $_POST['invite_nom'];
        $invite_prenom = $_POST['invite_prenom'];
        $invite_date_naissance = $_POST['invite_date_naissance'];
        $munitions_tirees = $_POST['munitions_tirees'];

        $stmt = $conn->prepare("UPDATE seances SET arme_id = :arme_id, nombre_munitions = :nombre_munitions, stock = :stock, stand_id = :stand_id, date_seance = :date_seance, heure_seance = :heure_seance, commentaire = :commentaire, invite_nom = :invite_nom, invite_prenom = :invite_prenom, invite_date_naissance = :invite_date_naissance, munitions_tirees = :munitions_tirees WHERE id = :id");
        $stmt->bindParam(':arme_id', $arme_id);
        $stmt->bindParam(':nombre_munitions', $nombre_munitions);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':stand_id', $stand_id);
        $stmt->bindParam(':date_seance', $date_seance);
        $stmt->bindParam(':heure_seance', $heure_seance);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->bindParam(':invite_nom', $invite_nom);
        $stmt->bindParam(':invite_prenom', $invite_prenom);
        $stmt->bindParam(':invite_date_naissance', $invite_date_naissance);
        $stmt->bindParam(':munitions_tirees', $munitions_tirees);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_seances.php");
    }
}
?>
