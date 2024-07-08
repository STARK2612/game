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
    if (isset($_POST['menu_color'])) {
        $menu_color = $_POST['menu_color'];
        file_put_contents('../backend/menu_color.txt', $menu_color);
    }
    if (isset($_POST['hover_color'])) {
        $hover_color = $_POST['hover_color'];
        file_put_contents('../backend/hover_color.txt', $hover_color);
    }
    if (isset($_POST['background_color'])) {
        $background_color = $_POST['background_color'];
        file_put_contents('../backend/background_color.txt', $background_color);
    }
}

// Charger les configurations actuelles
$num_debut_articles = file_exists('../backend/num_debut_articles.txt') ? file_get_contents('../backend/num_debut_articles.txt') : '0';
$num_debut_seances = file_exists('../backend/num_debut_seances.txt') ? file_get_contents('../backend/num_debut_seances.txt') : '0';
$menu_color = file_exists('../backend/menu_color.txt') ? file_get_contents('../backend/menu_color.txt') : '#000000'; // Default color black
$hover_color = file_exists('../backend/hover_color.txt') ? file_get_contents('../backend/hover_color.txt') : '#000000'; // Default color black
$background_color = file_exists('../backend/background_color.txt') ? file_get_contents('../backend/background_color.txt') : '#ffffff'; // Default color white
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
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Configuration des Couleurs du Menu</h5>
                    <form method="post" action="gestion.php">
                        <div class="form-group">
                            <label for="menu_color">Couleur de fond des éléments du menu:</label>
                            <input type="color" id="menu_color" name="menu_color" class="form-control" value="<?= htmlspecialchars($menu_color) ?>">
                        </div>
                        <div class="form-group">
                            <label for="hover_color">Couleur de surbrillance des liens du menu:</label>
                            <input type="color" id="hover_color" name="hover_color" class="form-control" value="<?= htmlspecialchars($hover_color) ?>">
                        </div>
                        <div class="form-group">
                            <label for="background_color">Couleur de fond:</label>
                            <input type="color" id="background_color" name="background_color" class="form-control" value="<?= htmlspecialchars($background_color) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
