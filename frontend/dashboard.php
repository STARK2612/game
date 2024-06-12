<?php include 'header.php'; ?>

<h2>Dashboard</h2>
<p>Bienvenue sur le tableau de bord. Sélectionnez une rubrique dans le menu pour commencer.</p>

<?php if ($_SESSION['user_role'] == 'administrateur'): ?>
    <p>Vous êtes connecté en tant qu'administrateur.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
