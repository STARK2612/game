<?php
require_once '../backend/session.php';
is_logged_in();
check_inactivity();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestionnaire d'Armes, de Munitions et d'Equipements</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .custom-nav-item {
            background-color: <?= isset($_SESSION['nav_item_color']) ? $_SESSION['nav_item_color'] : '#343a40'; ?>;
        }
        .custom-nav-link:hover {
            background-color: <?= isset($_SESSION['nav_link_hover_color']) ? $_SESSION['nav_link_hover_color'] : '#f8f9fa'; ?>;
            color: #007bff;
            border-radius: 5px;
        }
        .nav-link {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
<body>
    <header class="custom-nav-item text-white p-3">
        <div class="container">
            <h1 class="h3">Gestionnaire d'Armes, de Munitions et d'Equipements (G.A.M.E)</h1>
            <p>Bienvenue, <?= $_SESSION['user_name'] ?></p>
            <nav>
                <ul class="nav">
                    <li class="nav-item custom-nav-item"><a class="nav-link text-white custom-nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item custom-nav-item"><a class="nav-link text-white custom-nav-link" href="gestion_articles.php">Articles</a></li>
                    <li class="nav-item custom-nav-item"><a class="nav-link text-white custom-nav-link" href="gestion_achats.php">Achats</a></li>
                    <li class="nav-item custom-nav-item"><a class="nav-link text-white custom-nav-link" href="gestion_armes.php">Armes</a></li>
                    <li class="nav-item custom-nav-item"><a class="nav-link text-white custom-nav-link" href="gestion_seances.php">Séances de Tir</a></li>
                    <li class="nav-item custom-nav-item"><a class="nav-link text-white custom-nav-link" href="gestion_fournisseurs.php">Fournisseurs</a></li>
                    <li class="nav-item custom-nav-item"><a class="nav-link text-white custom-nav-link" href="gestion_stands.php">Stands</a></li>
                    <li class="nav-item custom-nav-item"><a class="nav-link text-white custom-nav-link" href="gestion_statistiques.php">Statistiques</a></li>
                    <li class="nav-item custom-nav-item"><a class="nav-link text-white custom-nav-link" href="gestion.php">Gestion</a></li>
                    <li class="nav-item custom-nav-item"><a class="nav-link text-white custom-nav-link" href="../backend/logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-4">
