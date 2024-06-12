<?php
include 'header.php';

// Vérifiez si l'utilisateur est un administrateur
if ($_SESSION['user_role'] != 'administrateur') {
    header("Location: dashboard.php");
    exit;
}
?>

<h2>Gestion</h2>
<p>Cette section est réservée aux administrateurs.</p>

<?php include 'footer.php'; ?>
