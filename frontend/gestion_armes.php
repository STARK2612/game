<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $marque = $_POST['marque'];
        $model = $_POST['model'];
        $calibre = $_POST['calibre'];
        $etat = $_POST['etat'];
        $date_achat = $_POST['date_achat'];

        $stmt = $conn->prepare("INSERT INTO armes (marque, model, calibre, etat, date_achat) VALUES (:marque, :model, :calibre, :etat, :date_achat)");
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':calibre', $calibre);
        $stmt->bindParam(':etat', $etat);
        $stmt->bindParam(':date_achat', $date_achat);
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
        $calibre = $_POST['calibre'];
        $etat = $_POST['etat'];
        $date_achat = $_POST['date_achat'];

        $stmt = $conn->prepare("UPDATE armes SET marque = :marque, model = :model, calibre = :calibre, etat = :etat, date_achat = :date_achat WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':marque', $marque);
        $stmt->bindParam(':model', $model);
        $stmt->bindParam(':calibre', $calibre);
        $stmt->bindParam(':etat', $etat);
        $stmt->bindParam(':date_achat', $date_achat);
        $stmt->execute();

        header("Location: gestion_armes.php");
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM armes");
$stmt->execute();
$armes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<h2>Gestion des Armes</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Marque</th>
            <th>Modèle</th>
            <th>Calibre</th>
            <th>État</th>
            <th>Date d'Achat</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($armes as $arme): ?>
        <tr>
            <td><?= htmlspecialchars($arme['marque']) ?></td>
            <td><?= htmlspecialchars($arme['model']) ?></td>
            <td><?= htmlspecialchars($arme['calibre']) ?></td>
            <td><?= htmlspecialchars($arme['etat']) ?></td>
            <td><?= htmlspecialchars($arme['date_achat']) ?></td>
            <td>
                <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $arme['id'] ?>" data-marque="<?= $arme['marque'] ?>" data-model="<?= $arme['model'] ?>" data-calibre="<?= $arme['calibre'] ?>" data-etat="<?= $arme['etat'] ?>" data-date_achat="<?= $arme['date_achat'] ?>">Modifier</button>
                <form method="post" action="gestion_armes.php" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cette arme ?');">
                    <input type="hidden" name="id" value="<?= $arme['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

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
                        <label for="calibre">Calibre:</label>
                        <input type="text" id="calibre" name="calibre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="etat">État:</label>
                        <select id="etat" name="etat" class="form-control" required>
                            <option value="neuf">Neuf</option>
                            <option value="occasion">D'occasion</option>
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
                        <label for="edit-calibre">Calibre:</label>
                        <input type="text" id="edit-calibre" name="calibre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-etat">État:</label>
                        <select id="edit-etat" name="etat" class="form-control" required>
                            <option value="neuf">Neuf</option>
                            <option value="occasion">D'occasion</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-date_achat">Date d'Achat:</label>
                        <input type="date" id="edit-date_achat" name="date_achat" class="form-control" required>
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
            var calibre = btn.getAttribute('data-calibre');
            var etat = btn.getAttribute('data-etat');
            var date_achat = btn.getAttribute('data-date_achat');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-marque').value = marque;
            document.getElementById('edit-model').value = model;
            document.getElementById('edit-calibre').value = calibre;
            document.getElementById('edit-etat').value = etat;
            document.getElementById('edit-date_achat').value = date_achat;

            editModal.show();
        }
    });
});
</script>

<?php include 'footer.php'; ?>
