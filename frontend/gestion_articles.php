<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $type = $_POST['type'];
        $fournisseur = $_POST['fournisseur'];
        $prix = $_POST['prix'];
        $quantite = $_POST['quantite'];
        $date_achat = $_POST['date_achat'];
        $marque = $_POST['marque'];
        $model = $_POST['model'];

        $stmt = $conn->prepare("INSERT INTO articles (type, fournisseur, prix, quantite, date_achat, marque, model) VALUES (:type, :fournisseur, :prix, :quantite, :date_achat, :marque, :model)");
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':fournisseur', $fournisseur);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':date_achat', $date_achat);
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->execute();

        header("Location: gestion_articles.php");
        exit;
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: gestion_articles.php");
        exit;
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $type = $_POST['type'];
        $fournisseur = $_POST['fournisseur'];
        $prix = $_POST['prix'];
        $quantite = $_POST['quantite'];
        $date_achat = $_POST['date_achat'];
        $marque = $_POST['marque'];
        $model = $_POST['model'];

        $stmt = $conn->prepare("UPDATE articles SET type = :type, fournisseur = :fournisseur, prix = :prix, quantite = :quantite, date_achat = :date_achat, marque = :marque, model = :model WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':fournisseur', $fournisseur);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':date_achat', $date_achat);
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->execute();

        header("Location: gestion_articles.php");
        exit;
    }
}

$stmt = $conn->prepare("SELECT articles.*, fournisseurs.nom AS fournisseur_nom FROM articles JOIN fournisseurs ON articles.fournisseur = fournisseurs.id");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT id, nom FROM fournisseurs");
$stmt->execute();
$fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<h2>Gestion des Articles</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Type</th>
            <th>Marque</th>
            <th>Modèle</th>
            <th>Prix</th>
            <th>Quantité</th>
            <th>Date d'Achat</th>
            <th>Fournisseur</th>
            <th>Total Prix</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $article): ?>
        <tr>
            <td><?= htmlspecialchars($article['type']) ?></td>
            <td><?= htmlspecialchars($article['marque']) ?></td>
            <td><?= htmlspecialchars($article['model']) ?></td>
            <td><?= htmlspecialchars($article['prix']) ?> €</td>
            <td><?= htmlspecialchars($article['quantite']) ?></td>
            <td><?= htmlspecialchars($article['date_achat']) ?></td>
            <td><?= htmlspecialchars($article['fournisseur_nom']) ?></td>
            <td><?= htmlspecialchars($article['prix'] * $article['quantite']) ?> €</td>
            <td>
                <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $article['id'] ?>" data-type="<?= $article['type'] ?>" data-fournisseur="<?= $article['fournisseur'] ?>" data-prix="<?= $article['prix'] ?>" data-quantite="<?= $article['quantite'] ?>" data-date_achat="<?= $article['date_achat'] ?>" data-marque="<?= $article['marque'] ?>" data-model="<?= $article['model'] ?>">Modifier</button>
                <form method="post" action="gestion_articles.php" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cet article ?');">
                    <input type="hidden" name="id" value="<?= $article['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button id="add-btn" class="btn btn-primary">Ajouter un Article</button>

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
                            <option value="munition">Munition</option>
                            <option value="equipement">Équipement</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="fournisseur">Fournisseur:</label>
                        <select id="fournisseur" name="fournisseur" class="form-control" required>
                            <?php if (count($fournisseurs) > 0): ?>
                                <?php foreach ($fournisseurs as $fournisseur): ?>
                                    <option value="<?= $fournisseur['id'] ?>"><?= htmlspecialchars($fournisseur['nom']) ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">Aucun fournisseurs enregistrés.</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="prix">Prix:</label>
                        <input type="number" id="prix" name="prix" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="quantite">Quantité:</label>
                        <input type="number" id="quantite" name="quantite" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="date_achat">Date d'Achat:</label>
                        <input type="date" id="date_achat" name="date_achat" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="marque">Marque:</label>
                        <input type="text" id="marque" name="marque" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="model">Modèle:</label>
                        <input type="text" id="model" name="model" class="form-control" required>
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
                            <option value="munition">Munition</option>
                            <option value="equipement">Équipement</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-fournisseur">Fournisseur:</label>
                        <select id="edit-fournisseur" name="fournisseur" class="form-control" required>
                            <?php if (count($fournisseurs) > 0): ?>
                                <?php foreach ($fournisseurs as $fournisseur): ?>
                                    <option value="<?= $fournisseur['id'] ?>"><?= htmlspecialchars($fournisseur['nom']) ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="">Aucun fournisseurs enregistrés.</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-prix">Prix:</label>
                        <input type="number" id="edit-prix" name="prix" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-quantite">Quantité:</label>
                        <input type="number" id="edit-quantite" name="quantite" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-date_achat">Date d'Achat:</label>
                        <input type="date" id="edit-date_achat" name="date_achat" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-marque">Marque:</label>
                        <input type="text" id="edit-marque" name="marque" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-model">Modèle:</label>
                        <input type="text" id="edit-model" name="model" class="form-control" required>
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
            var fournisseur = btn.getAttribute('data-fournisseur');
            var prix = btn.getAttribute('data-prix');
            var quantite = btn.getAttribute('data-quantite');
            var date_achat = btn.getAttribute('data-date_achat');
            var marque = btn.getAttribute('data-marque');
            var model = btn.getAttribute('data-model');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-type').value = type;
            document.getElementById('edit-fournisseur').value = fournisseur;
            document.getElementById('edit-prix').value = prix;
            document.getElementById('edit-quantite').value = quantite;
            document.getElementById('edit-date_achat').value = date_achat;
            document.getElementById('edit-marque').value = marque;
            document.getElementById('edit-model').value = model;

            editModal.show();
        }
    });
});
</script>

<?php include 'footer.php'; ?>
