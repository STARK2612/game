<?php
require_once '../backend/session.php';
is_logged_in();
check_inactivity();

if ($_SESSION['user_role'] != 'administrateur') {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['num_debut_articles'])) {
        $num_debut_articles = $_POST['num_debut_articles'];
        file_put_contents('../backend/num_debut_articles.txt', $num_debut_articles);
    }
    if (isset($_POST['num_debut_seances'])) {
        $num_debut_seances = $_POST['num_debut_seances'];
        file_put_contents('../backend/num_debut_seances.txt', $num_debut_seances);
    }
    if (isset($_POST['nav_item_color']) && isset($_POST['nav_link_hover_color'])) {
        $_SESSION['nav_item_color'] = $_POST['nav_item_color'];
        $_SESSION['nav_link_hover_color'] = $_POST['nav_link_hover_color'];
        $_SESSION['footer_bg_color'] = $_POST['nav_item_color']; // Utiliser la même couleur pour le pied de page
    }
}

// Charger les numéros de départ actuels
$num_debut_articles = file_get_contents('../backend/num_debut_articles.txt');
$num_debut_seances = file_get_contents('../backend/num_debut_seances.txt');
?>

<?php include 'header.php'; ?>

<h2>Gestion</h2>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestion des Suffixes de Numéro de Référence Article</h5>
                    <form method="post" action="gestion.php">
                        <div class="form-group">
                            <label for="prefixe_articles">Préfixe:</label>
                            <input type="text" id="prefixe_articles" name="prefixe_articles" class="form-control" value="ART-" readonly>
                        </div>
                        <div class="form-group">
                            <label for="num_debut_articles">Numéro de départ:</label>
                            <input type="number" id="num_debut_articles" name="num_debut_articles" class="form-control" value="<?= htmlspecialchars($num_debut_articles) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Gestion des Suffixes de Numéro de Référence Séance</h5>
                    <form method="post" action="gestion.php">
                        <div class="form-group">
                            <label for="prefixe_seances">Préfixe:</label>
                            <input type="text" id="prefixe_seances" name="prefixe_seances" class="form-control" value="SEA-" readonly>
                        </div>
                        <div class="form-group">
                            <label for="num_debut_seances">Numéro de départ:</label>
                            <input type="number" id="num_debut_seances" name="num_debut_seances" class="form-control" value="<?= htmlspecialchars($num_debut_seances) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Changer les couleurs du menu</h5>
                    <form method="post" action="gestion.php">
                        <div class="form-group">
                            <label for="nav_item_color">Couleur de fond des éléments du menu:</label>
                            <input type="color" id="nav_item_color" name="nav_item_color" class="form-control" value="<?= isset($_SESSION['nav_item_color']) ? $_SESSION['nav_item_color'] : '#343a40'; ?>">
                        </div>
                        <div class="form-group">
                            <label for="nav_link_hover_color">Couleur de surbrillance des liens du menu:</label>
                            <input type="color" id="nav_link_hover_color" name="nav_link_hover_color" class="form-control" value="<?= isset($_SESSION['nav_link_hover_color']) ? $_SESSION['nav_link_hover_color'] : '#f8f9fa'; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Mettre à jour les couleurs</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS pour le bloc carré -->
<style>
    .card-body {
        background-color: orange;
        border-radius: 10px;
        padding: 10px;
    }

    .card-title {
        margin-bottom: 20px;
        text-align: center;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        text-align: center;
    }
</style>

<?php include 'footer.php'; ?>
