<?php
include 'config.php';
include 'session.php';
include 'csrf.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && validate_csrf($_POST['csrf_token'])) {
    if (isset($_POST['add'])) {
        $role = $_POST['role'];

        $stmt = $conn->prepare("INSERT INTO roles (role) VALUES (:role)");
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        header("Location: ../frontend/gestion_roles.php");
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM roles WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_roles.php");
    }

    if (isset($_POST['update'])) {
        $id = $_POST['id'];
        $role = $_POST['role'];

        $stmt = $conn->prepare("UPDATE roles SET role = :role WHERE id = :id");
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ../frontend/gestion_roles.php");
    }
}
?>
