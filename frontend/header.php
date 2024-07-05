<?php
require_once '../backend/session.php';
is_logged_in();
check_inactivity();

// Charger les couleurs configurées
$menu_color = file_get_contents('../backend/menu_color.txt');
$hover_color = file_get_contents('../backend/hover_color.txt');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gestionnaire d'Armes, de Munitions et d'Equipements</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nav-item {
            background-color: <?= htmlspecialchars($menu_color) ?>;
        }
        .nav-link:hover {
            background-color: <?= htmlspecialchars($hover_color) ?>;
        }
        thead {
            background-color: <?= htmlspecialchars($menu_color) ?>;
            color: white;
        }
        header {
            background-color: <?= htmlspecialchars($menu_color) ?>;
        }
        footer {
            background-color: <?= htmlspecialchars($menu_color) ?>;
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
                    <li class="nav-item"><a class="nav-link text-white" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_articles.php">Munitions</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_achats.php">Achats</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_armes.php">Armes</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_seances.php">Séances de Tir</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_fournisseurs.php">Fournisseurs</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_stands.php">Stands</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion_statistiques.php">Statistiques</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="gestion.php">Gestion</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="../backend/logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-4">
