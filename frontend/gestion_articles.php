<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

// Charger les couleurs configurées
$menu_color = file_get_contents('../backend/menu_color.txt');
$hover_color = file_get_contents('../backend/hover_color.txt');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $type = $_POST['type'];
        $prix_unite = $_POST['prix_unite'];
        $marque = $_POST['marque'];
        $model = $_POST['model'];
        $cartouches_par_boite = ($type == 'munition') ? $_POST['cartouches_par_boite'] : null;

        // Récupérer le suffixe actuel
        $stmt = $conn->prepare("SELECT * FROM reference_suffix WHERE id = 1");
        $stmt->execute();
        $suffix = $stmt->fetch(PDO::FETCH_ASSOC);
        $prefix = $suffix['prefix'];
        $current_number = $suffix['current_number'];

        // Générer le numéro de référence
        $reference = $prefix . $current_number;

        // Mettre à jour le numéro actuel
        $new_number = $current_number + 1;
        $stmt = $conn->prepare("UPDATE reference_suffix SET current_number = :current_number WHERE id = 1");
        $stmt->bindParam(':current_number', $new_number);
        $stmt->execute();

        $stmt = $conn->prepare("INSERT INTO articles (type, prix_unite, marque, model, reference, cartouches_par_boite) VALUES (:type, :prix_unite, :marque, :model, :reference, :cartouches_par_boite)");
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':prix_unite', $prix_unite);
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':reference', $reference);
        $stmt->bindParam(':cartouches_par_boite', $cartouches_par_boite);
        $stmt->execute();

        header("Location: gestion_articles.php");
        exit;
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        // Supprimer d'abord les enregistrements associés dans la table achats
        $stmt = $conn->prepare("DELETE FROM achats WHERE article_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Supprimer l'article
        $stmt = $conn->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: gestion_articles.php");
        exit;
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $type = $_POST['type'];
        $prix_unite = $_POST['prix_unite'];
        $marque = $_POST['marque'];
        $model = $_POST['model'];
        $cartouches_par_boite = ($type == 'munition') ? $_POST['cartouches_par_boite'] : null;

        $stmt = $conn->prepare("UPDATE articles SET type = :type, prix_unite = :prix_unite, marque = :marque, model = :model, cartouches_par_boite = :cartouches_par_boite WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':prix_unite', $prix_unite);
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':cartouches_par_boite', $cartouches_par_boite);
        $stmt->execute();

        header("Location: gestion_articles.php");
        exit;
    }
}

// Calcul du stock total pour chaque article
$stmt = $conn->prepare("
    SELECT articles.*, 
           COALESCE(SUM(achats.quantite), 0) AS total_boites,
           COALESCE(SUM(achats.quantite * IFNULL(articles.cartouches_par_boite, 1)), 0) - COALESCE(SUM(seance_tir.nombre_munitions_tirees), 0) AS stock 
    FROM articles 
    LEFT JOIN achats ON articles.id = achats.article_id 
    LEFT JOIN seance_tir ON articles.id = seance_tir.arme 
    GROUP BY articles.id
");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculer la valeur totale des munitions en stock et le stock total des cartouches
$valeur_totale_munitions = 0;
$stock_total_cartouches = 0;

foreach ($articles as $article) {
    if ($article['type'] == 'munition') {
        $valeur_totale_munitions += $article['prix_unite'] * $article['total_boites'];
        $stock_total_cartouches += $article['total_boites'] * $article['cartouches_par_boite'];
    }
}

// Stocker les valeurs calculées dans la session pour les utiliser dans le dashboard
$_SESSION['valeur_totale_munitions'] = $valeur_totale_munitions;
$_SESSION['stock_total_cartouches'] = $stock_total_cartouches;

?>

<?php include 'header.php'; ?>

<style>
    .table td {
        word-wrap: break-word;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
    }

    .action-buttons form {
        margin: 0;
    }
</style>

<div class="container">
    <h2>Gestion des Articles</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Marque</th>
                    <th>Modèle</th>
                    <th>Prix Unité</th>
                    <th>Valeur Stock</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?= htmlspecialchars($article['type']) ?></td>
                    <td><?= htmlspecialchars($article['marque']) ?></td>
                    <td><?= htmlspecialchars($article['model']) ?></td>
                    <td><?= htmlspecialchars($article['prix_unite'] ?? '') ?> €</td>
                    <td><?= htmlspecialchars(($article['prix_unite'] ?? 0) * ($article['total_boites'] ?? 0)) ?> €</td>
                    <td>
                        <?php if ($article['type'] == 'munition' && !empty($article['cartouches_par_boite'])): ?>
                            <?php 
                            $total_cartouches = $article['total_boites'] * $article['cartouches_par_boite'];
                            $boites = $article['total_boites'];
                            ?>
                            <?= htmlspecialchars($boites) ?> boîte(s) ou <?= htmlspecialchars($total_cartouches) ?> cartouche(s)
                        <?php else: ?>
                            <?= htmlspecialchars($article['total_boites']) ?>
                        <?php endif; ?>
                    </td>
                    <td class="action-buttons">
                        <button class="btn btn-sm btn-warning edit-btn mb-1" data-id="<?= $article['id'] ?>" data-type="<?= $article['type'] ?>" data-prix_unite="<?= $article['prix_unite'] ?? '' ?>" data-marque="<?= $article['marque'] ?>" data-model="<?= $article['model'] ?>" data-cartouches_par_boite="<?= $article['cartouches_par_boite'] ?? '' ?>">Modifier</button>
                        <form method="post" action="gestion_articles.php" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cet article ?');">
                            <input type="hidden" name="id" value="<?= $article['id'] ?>">
                            <button type="submit" name="delete" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <button id="add-btn" class="btn btn-primary">Ajouter un Article</button>
</div>

<!-- Modal Ajouter -->
<div id="add-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Article</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_articles.php">
                    <div class="form-group">
                        <label for="type">Type:</label>
                        <select id="type" name="type" class="form-control" required>
                            <option value="" disabled selected>Sélectionner un type d'article</option>
                            <option value="munition">Munition</option>
                            <option value="consommable">Consommable</option>
                            <option value="fixe">Fixe</option>
                        </select>
                    </div>
                    <div class="form-group" id="cartouches_par_boite_group" style="display: none;">
                        <label for="cartouches_par_boite">Nombre de cartouches par boîte:</label>
                        <input type="number" id="cartouches_par_boite" name="cartouches_par_boite" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="marque">Marque:</label>
                        <input type="text" id="marque" name="marque" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="model">Modèle:</label>
                        <input type="text" id="model" name="model" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="prix_unite">Prix Unité:</label>
                        <input type="number" id="prix_unite" name="prix_unite" class="form-control" step="0.01" required>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Modifier -->
<div id="edit-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier un Article</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_articles.php">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-type">Type:</label>
                        <select id="edit-type" name="type" class="form-control" required>
                            <option value="" disabled selected>Sélectionner un type d'article</option>
                            <option value="munition">Munition</option>
                            <option value="consommable">Consommable</option>
                            <option value="fixe">Fixe</option>
                        </select>
                    </div>
                    <div class="form-group" id="edit-cartouches_par_boite_group" style="display: none;">
                        <label for="edit-cartouches_par_boite">Nombre de cartouches par boîte:</label>
                        <input type="number" id="edit-cartouches_par_boite" name="cartouches_par_boite" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="edit-marque">Marque:</label>
                        <input type="text" id="edit-marque" name="marque" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-model">Modèle:</label>
                        <input type="text" id="edit-model" name="model" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-prix_unite">Prix Unité:</label>
                        <input type="number" id="edit-prix_unite" name="prix_unite" class="form-control" step="0.01" required>
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Modifier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var addModal = new bootstrap.Modal(document.getElementById('add-modal'));
    var editModal = new bootstrap.Modal(document.getElementById('edit-modal'));

    var addBtn = document.getElementById("add-btn");
    var editBtns = document.querySelectorAll(".edit-btn");

    addBtn.onclick = function() {
        addModal.show();
    }

    editBtns.forEach(function(btn) {
        btn.onclick = function() {
            var id = btn.getAttribute('data-id');
            var type = btn.getAttribute('data-type');
            var prix_unite = btn.getAttribute('data-prix_unite') || '';
            var marque = btn.getAttribute('data-marque');
            var model = btn.getAttribute('data-model');
            var cartouches_par_boite = btn.getAttribute('data-cartouches_par_boite') || '';

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-type').value = type;
            document.getElementById('edit-prix_unite').value = prix_unite;
            document.getElementById('edit-marque').value = marque;
            document.getElementById('edit-model').value = model;
            document.getElementById('edit-cartouches_par_boite').value = cartouches_par_boite;

            if (type === 'munition') {
                document.getElementById('edit-cartouches_par_boite_group').style.display = 'block';
            } else {
                document.getElementById('edit-cartouches_par_boite_group').style.display = 'none';
            }

            editModal.show();
        }
    });

    document.getElementById('type').addEventListener('change', function() {
        if (this.value === 'munition') {
            document.getElementById('cartouches_par_boite_group').style.display = 'block';
        } else {
            document.getElementById('cartouches_par_boite_group').style.display = 'none';
        }
    });

    document.getElementById('edit-type').addEventListener('change', function() {
        if (this.value === 'munition') {
            document.getElementById('edit-cartouches_par_boite_group').style.display = 'block';
        } else {
            document.getElementById('edit-cartouches_par_boite_group').style.display = 'none';
        }
    });
});
</script>

<?php include 'footer.php'; ?>
