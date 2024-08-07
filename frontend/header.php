<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
require_once '../backend/csrf.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        color: white;
        padding: 20px 0;
    }
    footer {
        background-color: <?= htmlspecialchars($menu_color) ?>;
        color: white;
        padding: 10px 0;
    }
    .custom-nav-item {
        background-color: <?= htmlspecialchars($menu_color) ?>;
    }
    .custom-nav-item .nav-link {
        color: white;
    }
    .custom-nav-item .nav-link:hover {
        background-color: <?= htmlspecialchars($hover_color) ?>;
    }
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    h1.h3 {
        margin: 0;
        padding: 0;
        text-align: center;
    }
    p {
        text-align: center;
        margin-bottom: 10px;
    }
    nav ul.nav {
        list-style: none;
        padding: 0;
        display: flex;
        justify-content: left;
        margin: 0;
    }
    nav ul.nav li.nav-item {
        margin: 0 10px;
    }
    nav ul.nav li.nav-item a.nav-link {
        color: white;
        text-decoration: none;
        padding: 10px 15px;
        display: block;
    }
    nav ul.nav li.nav-item a.nav-link:hover {
        background-color: <?= htmlspecialchars($hover_color) ?>;
    }
    .mt-4 {
        margin-top: 1.5rem !important;
    }
</style>

</head>
<body>
    <header class="custom-nav-item text-white p-3">
        <div class="container">
            <h1 class="h3 text-center">Gestionnaire d'Armes, de Munitions et d'Equipements (G.A.M.E)</h1>
            <p>
                Bienvenue, 
                <?= isset($_SESSION['user_firstname']) && isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_firstname']) . ' ' . htmlspecialchars($_SESSION['user_name']) : '' ?>
            </p>
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
                    <li class="nav-item">
                        <a class="nav-link text-white" href="#" id="logout-btn">Déconnexion</a>
                        <form id="logout-form" method="POST" action="index.php" style="display: none;">
                            <input type="hidden" name="action" value="logout">
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-4">
    <script>
        document.getElementById('logout-btn').addEventListener('click', function(event) {
            event.preventDefault();
            document.getElementById('logout-form').submit();
        });
    </script>
</body>
</html>
