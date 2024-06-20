<?php
require_once '../backend/session.php';
is_logged_in();
check_inactivity();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestionnaire d'armes</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-dark text-white p-3">
        <div class="container">
            <h1 class="h3">Gestionnaire d'armes</h1>
            <p>Bienvenue, <?= $_SESSION['user_name'] ?></p>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link text-white" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_articles.php">Articles</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_achats.php">Achats</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_armes.php">Armes</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_seances.php">Séances de Tir</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_fournisseurs.php">Fournisseurs</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_stands.php">Stands</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_statistiques.php">Statistiques</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion.php">Gestion</a></li>
                    <?php if ($_SESSION['user_role'] == 'administrateur'): ?>
                        <li class="nav-item"><a class="nav-link text-white" href="gestion_utilisateurs.php">Utilisateurs</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link text-white" href="../backend/logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-4">
