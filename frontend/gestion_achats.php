<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $article_id = $_POST['article'];
        $quantite = $_POST['quantite'];
        $fournisseur = $_POST['fournisseur'];
        $date_achat = isset($_POST['date_achat']) ? $_POST['date_achat'] : null;

        // Validation des données
        if (empty($article_id) || empty($quantite) || empty($fournisseur) || empty($date_achat)) {
            echo "Tous les champs sont obligatoires.";
            exit;
        }

        // Ajouter l'achat à la table des achats
        $stmt = $conn->prepare("INSERT INTO achats (article_id, quantite, fournisseur_id, date_achat) VALUES (:article_id, :quantite, :fournisseur, :date_achat)");
        $stmt->bindParam(':article_id', $article_id);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':fournisseur', $fournisseur);
        $stmt->bindParam(':date_achat', $date_achat);
        $stmt->execute();

        // Mettre à jour le stock
        $stmt = $conn->prepare("SELECT type FROM articles WHERE id = :article_id");
        $stmt->bindParam(':article_id', $article_id);
        $stmt->execute();
        $article = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($article['type'] == 'munition') {
            // Ajouter au stock réglementaire
            $stmt = $conn->prepare("SELECT * FROM stock_reglementaire WHERE article_id = :article_id");
            $stmt->bindParam(':article_id', $article_id);
            $stmt->execute();
            $stock = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($stock) {
                $nouvelle_quantite_boites = $stock['quantite_boites'] + $quantite;
                $nouvelle_quantite_cartouches = $nouvelle_quantite_boites * 50;

                $stmt = $conn->prepare("UPDATE stock_reglementaire SET quantite_boites = :quantite_boites, quantite_cartouches = :quantite_cartouches WHERE article_id = :article_id");
                $stmt->bindParam(':quantite_boites', $nouvelle_quantite_boites);
                $stmt->bindParam(':quantite_cartouches', $nouvelle_quantite_cartouches);
                $stmt->bindParam(':article_id', $article_id);
                $stmt->execute();
            } else {
                $quantite_cartouches = $quantite * 50;

                $stmt = $conn->prepare("INSERT INTO stock_reglementaire (article_id, quantite_boites, quantite_cartouches) VALUES (:article_id, :quantite_boites, :quantite_cartouches)");
                $stmt->bindParam(':article_id', $article_id);
                $stmt->bindParam(':quantite_boites', $quantite);
                $stmt->bindParam(':quantite_cartouches', $quantite_cartouches);
                $stmt->execute();
            }
        }

        header("Location: gestion_achats.php");
        exit;
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM achats WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: gestion_achats.php");
        exit;
    }
}

$stmt = $conn->prepare("SELECT achats.*, articles.marque, articles.model, fournisseurs.nom FROM achats JOIN articles ON achats.article_id = articles.id JOIN fournisseurs ON achats.fournisseur_id = fournisseurs.id");
$stmt->execute();
$achats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des articles
$stmt = $conn->prepare("SELECT id, marque, model FROM articles");
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des fournisseurs
$stmt = $conn->prepare("SELECT id, nom FROM fournisseurs");
$stmt->execute();
$fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<h2>Gestion des Achats</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Article</th>
            <th>Quantité</th>
            <th>Fournisseur</th>
            <th>Date d'Achat</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($achats as $achat): ?>
        <tr>
            <td><?= htmlspecialchars($achat['marque'] . ' ' . $achat['model']) ?></td>
            <td><?= htmlspecialchars($achat['quantite']) ?></td>
            <td><?= htmlspecialchars($achat['nom']) ?></td>
            <td><?= htmlspecialchars($achat['date_achat'] ? date('d/m/Y', strtotime($achat['date_achat'])) : '') ?></td>
            <td>
                <form method="post" action="gestion_achats.php" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cet achat ?');">
                    <input type="hidden" name="id" value="<?= $achat['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button id="add-btn" class="btn btn-primary">Ajouter un Achat</button>

<!-- Modal Ajouter -->
<div id="add-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Achat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_achats.php">
                    <div class="form-group">
                        <label for="article">Article:</label>
                        <select id="article" name="article" class="form-control" required>
                            <option value="">Sélectionner un article</option>
                            <?php foreach ($articles as $article): ?>
                                <option value="<?= $article['id'] ?>"><?= htmlspecialchars($article['marque'] . ' ' . $article['model']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantite">Quantité (Nombre de boîtes):</label>
                        <input type="number" id="quantite" name="quantite" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="fournisseur">Fournisseur:</label>
                        <select id="fournisseur" name="fournisseur" class="form-control" required>
                            <option value="">Sélectionner un fournisseur</option>
                            <?php foreach ($fournisseurs as $fournisseur): ?>
                                <option value="<?= $fournisseur['id'] ?>"><?= htmlspecialchars($fournisseur['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date_achat">Date d'Achat:</label>
                        <input type="date" id="date_achat" name="date_achat" class="form-control" required>
                    </div>
                    <button type="submit" name="add" class="btn btn-primary">Ajouter</button>
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

    var addBtn = document.getElementById("add-btn");

    addBtn.onclick = function() {
        addModal.show();
    }
});
</script>

<?php include 'footer.php'; ?>
