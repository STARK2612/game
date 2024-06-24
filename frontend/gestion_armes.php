<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $marque = $_POST['marque'];
        $model = $_POST['model'];
        $prix = $_POST['prix'];
        $calibre = $_POST['calibre'];
        $fournisseur = $_POST['fournisseur'];
        $etat_achat = $_POST['etat_achat'];
        $date_achat = $_POST['date_achat'];
        $num_serie = $_POST['num_serie'];
        $date_revente = !empty($_POST['date_revente']) ? $_POST['date_revente'] : null;
        $prix_revente = !empty($_POST['prix_revente']) ? $_POST['prix_revente'] : null;
        $etat_revente = !empty($_POST['etat_revente']) ? $_POST['etat_revente'] : null;
        $date_reparation = !empty($_POST['date_reparation']) ? $_POST['date_reparation'] : null;
        $prix_reparation = !empty($_POST['prix_reparation']) ? $_POST['prix_reparation'] : null;

        $stmt = $conn->prepare("INSERT INTO armes (marque, model, prix, calibre, fournisseur, etat_achat, date_achat, num_serie, date_revente, prix_revente, etat_revente, date_reparation, prix_reparation) VALUES (:marque, :model, :prix, :calibre, :fournisseur, :etat_achat, :date_achat, :num_serie, :date_revente, :prix_revente, :etat_revente, :date_reparation, :prix_reparation)");
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':calibre', $calibre);
        $stmt->bindParam(':fournisseur', $fournisseur);
        $stmt->bindParam(':etat_achat', $etat_achat);
        $stmt->bindParam(':date_achat', $date_achat);
        $stmt->bindParam(':num_serie', $num_serie);
        $stmt->bindParam(':date_revente', $date_revente);
        $stmt->bindParam(':prix_revente', $prix_revente);
        $stmt->bindParam(':etat_revente', $etat_revente);
        $stmt->bindParam(':date_reparation', $date_reparation);
        $stmt->bindParam(':prix_reparation', $prix_reparation);
        $stmt->execute();

        header("Location: gestion_armes.php");
        exit;
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM armes WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: gestion_armes.php");
        exit;
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $marque = $_POST['marque'];
        $model = $_POST['model'];
        $prix = $_POST['prix'];
        $calibre = $_POST['calibre'];
        $fournisseur = $_POST['fournisseur'];
        $etat_achat = $_POST['etat_achat'];
        $date_achat = $_POST['date_achat'];
        $num_serie = $_POST['num_serie'];
        $date_revente = !empty($_POST['date_revente']) ? $_POST['date_revente'] : null;
        $prix_revente = !empty($_POST['prix_revente']) ? $_POST['prix_revente'] : null;
        $etat_revente = !empty($_POST['etat_revente']) ? $_POST['etat_revente'] : null;
        $date_reparation = !empty($_POST['date_reparation']) ? $_POST['date_reparation'] : null;
        $prix_reparation = !empty($_POST['prix_reparation']) ? $_POST['prix_reparation'] : null;

        $stmt = $conn->prepare("UPDATE armes SET marque = :marque, model = :model, prix = :prix, calibre = :calibre, fournisseur = :fournisseur, etat_achat = :etat_achat, date_achat = :date_achat, num_serie = :num_serie, date_revente = :date_revente, prix_revente = :prix_revente, etat_revente = :etat_revente, date_reparation = :date_reparation, prix_reparation = :prix_reparation WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':prix', $prix);
        $stmt->bindParam(':calibre', $calibre);
        $stmt->bindParam(':fournisseur', $fournisseur);
        $stmt->bindParam(':etat_achat', $etat_achat);
        $stmt->bindParam(':date_achat', $date_achat);
        $stmt->bindParam(':num_serie', $num_serie);
        $stmt->bindParam(':date_revente', $date_revente);
        $stmt->bindParam(':prix_revente', $prix_revente);
        $stmt->bindParam(':etat_revente', $etat_revente);
        $stmt->bindParam(':date_reparation', $date_reparation);
        $stmt->bindParam(':prix_reparation', $prix_reparation);
        $stmt->execute();

        header("Location: gestion_armes.php");
        exit;
    }
}

// Récupérer toutes les armes
$stmt = $conn->prepare("SELECT armes.*, fournisseurs.nom AS fournisseur_nom FROM armes LEFT JOIN fournisseurs ON armes.fournisseur = fournisseurs.id");
$stmt->execute();
$armes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculer le total des armes
$total_armes = count($armes);

// Stocker le total des armes dans la session pour l'utiliser dans le dashboard
$_SESSION['total_armes'] = $total_armes;
?>

<?php include 'header.php'; ?>

<style>
.center-table {
    margin-left: 2%; /* Diminue la marge de gauche */
    width: 96%; /* Largeur ajustée */
    overflow-x: auto;
}
.red-block {
    background-color: #f8d7da;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
}
.green-block {
    background-color: #d4edda;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 10px;
}
</style>

<h2>Gestion des Armes</h2>
<div class="center-table">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Marque</th>
                <th>Modèle</th>
                <th>Prix</th>
                <th>Calibre</th>
                <th>Fournisseur</th>
                <th>État à l'achat</th>
                <th>Date d'achat</th>
                <th>Numéro de série</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($armes as $arme): ?>
            <tr>
                <td><?= htmlspecialchars($arme['marque']) ?></td>
                <td><?= htmlspecialchars($arme['model']) ?></td>
                <td><?= htmlspecialchars($arme['prix']) ?> €</td>
                <td><?= htmlspecialchars($arme['calibre']) ?></td>
                <td><?= htmlspecialchars($arme['fournisseur_nom']) ?></td>
                <td><?= htmlspecialchars($arme['etat_achat']) ?></td>
                <td><?= !empty($arme['date_achat']) ? date('d/m/Y', strtotime($arme['date_achat'])) : '' ?></td>
                <td><?= htmlspecialchars($arme['num_serie']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $arme['id'] ?>" data-marque="<?= $arme['marque'] ?>" data-model="<?= $arme['model'] ?>" data-prix="<?= $arme['prix'] ?>" data-calibre="<?= $arme['calibre'] ?>" data-fournisseur="<?= $arme['fournisseur'] ?>" data-etat_achat="<?= $arme['etat_achat'] ?>" data-date_achat="<?= $arme['date_achat'] ?>" data-num_serie="<?= $arme['num_serie'] ?>" data-date_revente="<?= $arme['date_revente'] ?>" data-prix_revente="<?= $arme['prix_revente'] ?>" data-etat_revente="<?= $arme['etat_revente'] ?>" data-date_reparation="<?= $arme['date_reparation'] ?>" data-prix_reparation="<?= $arme['prix_reparation'] ?>">Modifier</button>
                    <button class="btn btn-sm btn-info fiche-vente-btn" data-id="<?= $arme['id'] ?>" data-etat_revente="<?= $arme['etat_revente'] ?>" data-date_reparation="<?= $arme['date_reparation'] ?>">Fiche de Vente</button>
                    <form method="post" action="gestion_armes.php" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cette arme ?');">
                        <input type="hidden" name="id" value="<?= $arme['id'] ?>">
                        <button type="submit" name="delete" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<button id="add-btn" class="btn btn-primary">Ajouter une Arme</button>

<!-- Modal Ajouter -->
<div id="add-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une Arme</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_armes.php">
                    <div class="form-group">
                        <label for="marque">Marque:</label>
                        <input type="text" id="marque" name="marque" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="model">Modèle:</label>
                        <input type="text" id="model" name="model" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="prix">Prix:</label>
                        <input type="number" id="prix" name="prix" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="calibre">Calibre:</label>
                        <input type="text" id="calibre" name="calibre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="fournisseur">Fournisseur:</label>
                        <select id="fournisseur" name="fournisseur" class="form-control" required>
                            <?php
                            $stmt = $conn->prepare("SELECT id, nom FROM fournisseurs");
                            $stmt->execute();
                            $fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($fournisseurs as $fournisseur) {
                                echo "<option value=\"" . htmlspecialchars($fournisseur['id']) . "\">" . htmlspecialchars($fournisseur['nom']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="etat_achat">État à l'achat:</label>
                        <select id="etat_achat" name="etat_achat" class="form-control" required>
                            <option value="neuf">Neuf</option>
                            <option value="occasion">Occasion</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date_achat">Date d'achat:</label>
                        <input type="date" id="date_achat" name="date_achat" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="num_serie">Numéro de série:</label>
                        <input type="text" id="num_serie" name="num_serie" class="form-control" required>
                    </div>
                    <div class="green-block">
                        <div class="form-group">
                            <label for="etat_revente">État pour la vente:</label>
                            <select id="etat_revente" name="etat_revente" class="form-control">
                                <option value="neuf">Neuf</option>
                                <option value="occasion">Occasion</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="date_revente">Date de revente:</label>
                            <input type="date" id="date_revente" name="date_revente" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="prix_revente">Prix de revente:</label>
                            <input type="number" id="prix_revente" name="prix_revente" class="form-control">
                        </div>
                    </div>
                    <div class="red-block">
                        <div class="form-group">
                            <label for="date_reparation">Date de réparation:</label>
                            <input type="date" id="date_reparation" name="date_reparation" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="prix_reparation">Prix de réparation:</label>
                            <input type="number" id="prix_reparation" name="prix_reparation" class="form-control">
                        </div>
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
                <h5 class="modal-title">Modifier une Arme</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_armes.php">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-marque">Marque:</label>
                        <input type="text" id="edit-marque" name="marque" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-model">Modèle:</label>
                        <input type="text" id="edit-model" name="model" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-prix">Prix:</label>
                        <input type="number" id="edit-prix" name="prix" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-calibre">Calibre:</label>
                        <input type="text" id="edit-calibre" name="calibre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-fournisseur">Fournisseur:</label>
                        <select id="edit-fournisseur" name="fournisseur" class="form-control" required>
                            <?php
                            foreach ($fournisseurs as $fournisseur) {
                                echo "<option value=\"" . htmlspecialchars($fournisseur['id']) . "\">" . htmlspecialchars($fournisseur['nom']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-etat_achat">État à l'achat:</label>
                        <select id="edit-etat_achat" name="etat_achat" class="form-control" required>
                            <option value="neuf">Neuf</option>
                            <option value="occasion">Occasion</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-date_achat">Date d'achat:</label>
                        <input type="date" id="edit-date_achat" name="date_achat" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-num_serie">Numéro de série:</label>
                        <input type="text" id="edit-num_serie" name="num_serie" class="form-control" required>
                    </div>
                    <div class="green-block">
                        <div class="form-group">
                            <label for="edit-etat_revente">État pour la vente:</label>
                            <select id="edit-etat_revente" name="etat_revente" class="form-control">
                                <option value="neuf">Neuf</option>
                                <option value="occasion">Occasion</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-date_revente">Date de revente:</label>
                            <input type="date" id="edit-date_revente" name="date_revente" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit-prix_revente">Prix de revente:</label>
                            <input type="number" id="edit-prix_revente" name="prix_revente" class="form-control">
                        </div>
                    </div>
                    <div class="red-block">
                        <div class="form-group">
                            <label for="edit-date_reparation">Date de réparation:</label>
                            <input type="date" id="edit-date_reparation" name="date_reparation" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="edit-prix_reparation">Prix de réparation:</label>
                            <input type="number" id="edit-prix_reparation" name="prix_reparation" class="form-control">
                        </div>
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
            var marque = btn.getAttribute('data-marque');
            var model = btn.getAttribute('data-model');
            var prix = btn.getAttribute('data-prix');
            var calibre = btn.getAttribute('data-calibre');
            var fournisseur = btn.getAttribute('data-fournisseur');
            var etat_achat = btn.getAttribute('data-etat_achat');
            var date_achat = btn.getAttribute('data-date_achat');
            var num_serie = btn.getAttribute('data-num_serie');
            var date_revente = btn.getAttribute('data-date_revente') || '';
            var prix_revente = btn.getAttribute('data-prix_revente') || '';
            var etat_revente = btn.getAttribute('data-etat_revente') || '';
            var date_reparation = btn.getAttribute('data-date_reparation') || '';
            var prix_reparation = btn.getAttribute('data-prix_reparation') || '';

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-marque').value = marque;
            document.getElementById('edit-model').value = model;
            document.getElementById('edit-prix').value = prix;
            document.getElementById('edit-calibre').value = calibre;
            document.getElementById('edit-fournisseur').value = fournisseur;
            document.getElementById('edit-etat_achat').value = etat_achat;
            document.getElementById('edit-date_achat').value = date_achat;
            document.getElementById('edit-num_serie').value = num_serie;
            document.getElementById('edit-date_revente').value = date_revente;
            document.getElementById('edit-prix_revente').value = prix_revente;
            document.getElementById('edit-etat_revente').value = etat_revente;
            document.getElementById('edit-date_reparation').value = date_reparation;
            document.getElementById('edit-prix_reparation').value = prix_reparation;

            if (etat_revente || date_revente || prix_revente) {
                document.querySelector('.green-block').style.display = 'block';
            } else {
                document.querySelector('.green-block').style.display = 'none';
            }

            if (date_reparation || prix_reparation) {
                document.querySelector('.red-block').style.display = 'block';
            } else {
                document.querySelector('.red-block').style.display = 'none';
            }

            editModal.show();
        }
    });

    // Vérification avant génération du PDF
    var ficheVenteBtns = document.querySelectorAll(".fiche-vente-btn");

    ficheVenteBtns.forEach(function(btn) {
        btn.onclick = function() {
            var etatRevente = btn.getAttribute('data-etat_revente');
            var dateReparation = btn.getAttribute('data-date_reparation');
            if (!etatRevente || !dateReparation) {
                alert('Veuillez compléter les champs "État pour la vente" et "Date de réparation" avant de générer la fiche de vente.');
                return false;
            }
            var id = btn.getAttribute('data-id');
            window.open('generate_pdf.php?id=' + id, '_blank');
        }
    });
});
</script>

<?php include 'footer.php'; ?>
