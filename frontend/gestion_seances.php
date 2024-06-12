<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $arme = $_POST['arme'];
        $nombre_munitions = $_POST['nombre_munitions'];
        $nombre_munitions_tirees = $_POST['nombre_munitions_tirees'];
        $stock = $_POST['stock'];
        $stand_de_tir = $_POST['stand_de_tir'];
        $date_seance = $_POST['date_seance'];
        $heure_seance = $_POST['heure_seance'];
        $commentaire = $_POST['commentaire'];
        $nom_invite = $_POST['nom_invite'];
        $prenom_invite = $_POST['prenom_invite'];
        $date_naissance_invite = $_POST['date_naissance_invite'];

        $stmt = $conn->prepare("INSERT INTO seance_tir (arme, nombre_munitions, nombre_munitions_tirees, stock, stand_de_tir, date_seance, heure_seance, commentaire, nom_invite, prenom_invite, date_naissance_invite) VALUES (:arme, :nombre_munitions, :nombre_munitions_tirees, :stock, :stand_de_tir, :date_seance, :heure_seance, :commentaire, :nom_invite, :prenom_invite, :date_naissance_invite)");
        $stmt->bindParam(':arme', $arme);
        $stmt->bindParam(':nombre_munitions', $nombre_munitions);
        $stmt->bindParam(':nombre_munitions_tirees', $nombre_munitions_tirees);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':stand_de_tir', $stand_de_tir);
        $stmt->bindParam(':date_seance', $date_seance);
        $stmt->bindParam(':heure_seance', $heure_seance);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->bindParam(':nom_invite', $nom_invite);
        $stmt->bindParam(':prenom_invite', $prenom_invite);
        $stmt->bindParam(':date_naissance_invite', $date_naissance_invite);
        $stmt->execute();

        header("Location: gestion_seances.php");
        exit;
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM seance_tir WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: gestion_seances.php");
        exit;
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $arme = $_POST['arme'];
        $nombre_munitions = $_POST['nombre_munitions'];
        $nombre_munitions_tirees = $_POST['nombre_munitions_tirees'];
        $stock = $_POST['stock'];
        $stand_de_tir = $_POST['stand_de_tir'];
        $date_seance = $_POST['date_seance'];
        $heure_seance = $_POST['heure_seance'];
        $commentaire = $_POST['commentaire'];
        $nom_invite = $_POST['nom_invite'];
        $prenom_invite = $_POST['prenom_invite'];
        $date_naissance_invite = $_POST['date_naissance_invite'];

        $stmt = $conn->prepare("UPDATE seance_tir SET arme = :arme, nombre_munitions = :nombre_munitions, nombre_munitions_tirees = :nombre_munitions_tirees, stock = :stock, stand_de_tir = :stand_de_tir, date_seance = :date_seance, heure_seance = :heure_seance, commentaire = :commentaire, nom_invite = :nom_invite, prenom_invite = :prenom_invite, date_naissance_invite = :date_naissance_invite WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':arme', $arme);
        $stmt->bindParam(':nombre_munitions', $nombre_munitions);
        $stmt->bindParam(':nombre_munitions_tirees', $nombre_munitions_tirees);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':stand_de_tir', $stand_de_tir);
        $stmt->bindParam(':date_seance', $date_seance);
        $stmt->bindParam(':heure_seance', $heure_seance);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->bindParam(':nom_invite', $nom_invite);
        $stmt->bindParam(':prenom_invite', $prenom_invite);
        $stmt->bindParam(':date_naissance_invite', $date_naissance_invite);
        $stmt->execute();

        header("Location: gestion_seances.php");
        exit;
    }
}

$stmt = $conn->prepare("
    SELECT 
        seance_tir.*, 
        armes.marque, 
        armes.model, 
        stands.nom AS nom_stand 
    FROM 
        seance_tir 
    JOIN 
        armes ON seance_tir.arme = armes.id 
    JOIN 
        stands ON seance_tir.stand_de_tir = stands.id
");
$stmt->execute();
$seances = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des armes
$stmt = $conn->prepare("SELECT id, marque, model FROM armes");
$stmt->execute();
$armes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des stands
$stmt = $conn->prepare("SELECT id, nom FROM stands");
$stmt->execute();
$stands = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<style>
.table td {
    word-wrap: break-word;
    max-width: 150px; /* Vous pouvez ajuster cette valeur selon vos besoins */
    white-space: pre-wrap; /* Cette propriété assure que les espaces sont respectés et le texte s'affiche correctement */
}
</style>

<h2>Gestion des Séances de Tir</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Arme</th>
            <th>Nombre de Munitions</th>
            <th>Nombre de Munitions Tirées</th>
            <th>Stock</th>
            <th>Stand de Tir</th>
            <th>Date de la Séance</th>
            <th>Heure de la Séance</th>
            <th>Commentaire</th>
            <th>Nom de l'Invité</th>
            <th>Prénom de l'Invité</th>
            <th>Date de Naissance de l'Invité</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($seances as $seance): ?>
        <tr>
            <td><?= htmlspecialchars($seance['marque'] . ' ' . $seance['model']) ?></td>
            <td><?= htmlspecialchars($seance['nombre_munitions']) ?></td>
            <td><?= htmlspecialchars($seance['nombre_munitions_tirees']) ?></td>
            <td><?= htmlspecialchars($seance['stock']) ?></td>
            <td><?= htmlspecialchars($seance['nom_stand']) ?></td>
            <td><?= htmlspecialchars($seance['date_seance']) ?></td>
            <td><?= htmlspecialchars($seance['heure_seance']) ?></td>
            <td><?= htmlspecialchars($seance['commentaire']) ?></td>
            <td><?= htmlspecialchars($seance['nom_invite']) ?></td>
            <td><?= htmlspecialchars($seance['prenom_invite']) ?></td>
            <td><?= htmlspecialchars($seance['date_naissance_invite']) ?></td>
            <td>
                <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $seance['id'] ?>" data-arme="<?= $seance['arme'] ?>" data-nombre_munitions="<?= $seance['nombre_munitions'] ?>" data-nombre_munitions_tirees="<?= $seance['nombre_munitions_tirees'] ?>" data-stock="<?= $seance['stock'] ?>" data-stand_de_tir="<?= $seance['stand_de_tir'] ?>" data-date_seance="<?= $seance['date_seance'] ?>" data-heure_seance="<?= $seance['heure_seance'] ?>" data-commentaire="<?= $seance['commentaire'] ?>" data-nom_invite="<?= $seance['nom_invite'] ?>" data-prenom_invite="<?= $seance['prenom_invite'] ?>" data-date_naissance_invite="<?= $seance['date_naissance_invite'] ?>">Modifier</button>
                <form method="post" action="gestion_seances.php" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cette séance ?');">
                    <input type="hidden" name="id" value="<?= $seance['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button id="add-btn" class="btn btn-primary">Ajouter une Séance</button>

<!-- Modal Ajouter -->
<div id="add-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une Séance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_seances.php">
                    <div class="form-group">
                        <label for="arme">Arme:</label>
                        <select id="arme" name="arme" class="form-control" required>
                            <?php foreach ($armes as $arme): ?>
                                <option value="<?= $arme['id'] ?>"><?= htmlspecialchars($arme['marque'] . ' ' . $arme['model']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nombre_munitions">Nombre de Munitions:</label>
                        <input type="number" id="nombre_munitions" name="nombre_munitions" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre_munitions_tirees">Nombre de Munitions Tirées:</label>
                        <input type="number" id="nombre_munitions_tirees" name="nombre_munitions_tirees" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock:</label>
                        <select id="stock" name="stock" class="form-control" required>
                            <option value="achete">Acheté</option>
                            <option value="reglementaire">Réglementaire</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stand_de_tir">Stand de Tir:</label>
                        <select id="stand_de_tir" name="stand_de_tir" class="form-control" required>
                            <?php foreach ($stands as $stand): ?>
                                <option value="<?= $stand['id'] ?>"><?= htmlspecialchars($stand['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date_seance">Date de la Séance:</label>
                        <input type="date" id="date_seance" name="date_seance" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="heure_seance">Heure de la Séance:</label>
                        <input type="time" id="heure_seance" name="heure_seance" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="commentaire">Commentaire:</label>
                        <textarea id="commentaire" name="commentaire" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="nom_invite">Nom de l'Invité:</label>
                        <input type="text" id="nom_invite" name="nom_invite" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom_invite">Prénom de l'Invité:</label>
                        <input type="text" id="prenom_invite" name="prenom_invite" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="date_naissance_invite">Date de Naissance de l'Invité:</label>
                        <input type="date" id="date_naissance_invite" name="date_naissance_invite" class="form-control" required>
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
                <h5 class="modal-title">Modifier une Séance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_seances.php">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-arme">Arme:</label>
                        <select id="edit-arme" name="arme" class="form-control" required>
                            <?php foreach ($armes as $arme): ?>
                                <option value="<?= $arme['id'] ?>"><?= htmlspecialchars($arme['marque'] . ' ' . $arme['model']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-nombre_munitions">Nombre de Munitions:</label>
                        <input type="number" id="edit-nombre_munitions" name="nombre_munitions" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-nombre_munitions_tirees">Nombre de Munitions Tirées:</label>
                        <input type="number" id="edit-nombre_munitions_tirees" name="nombre_munitions_tirees" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-stock">Stock:</label>
                        <select id="edit-stock" name="stock" class="form-control" required>
                            <option value="achete">Acheté</option>
                            <option value="reglementaire">Réglementaire</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-stand_de_tir">Stand de Tir:</label>
                        <select id="edit-stand_de_tir" name="stand_de_tir" class="form-control" required>
                            <?php foreach ($stands as $stand): ?>
                                <option value="<?= $stand['id'] ?>"><?= htmlspecialchars($stand['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-date_seance">Date de la Séance:</label>
                        <input type="date" id="edit-date_seance" name="date_seance" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-heure_seance">Heure de la Séance:</label>
                        <input type="time" id="edit-heure_seance" name="heure_seance" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-commentaire">Commentaire:</label>
                        <textarea id="edit-commentaire" name="commentaire" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit-nom_invite">Nom de l'Invité:</label>
                        <input type="text" id="edit-nom_invite" name="nom_invite" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-prenom_invite">Prénom de l'Invité:</label>
                        <input type="text" id="edit-prenom_invite" name="prenom_invite" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-date_naissance_invite">Date de Naissance de l'Invité:</label>
                        <input type="date" id="edit-date_naissance_invite" name="date_naissance_invite" class="form-control" required>
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
            var arme = btn.getAttribute('data-arme');
            var nombre_munitions = btn.getAttribute('data-nombre_munitions');
            var nombre_munitions_tirees = btn.getAttribute('data-nombre_munitions_tirees');
            var stock = btn.getAttribute('data-stock');
            var stand_de_tir = btn.getAttribute('data-stand_de_tir');
            var date_seance = btn.getAttribute('data-date_seance');
            var heure_seance = btn.getAttribute('data-heure_seance');
            var commentaire = btn.getAttribute('data-commentaire');
            var nom_invite = btn.getAttribute('data-nom_invite');
            var prenom_invite = btn.getAttribute('data-prenom_invite');
            var date_naissance_invite = btn.getAttribute('data-date_naissance_invite');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-arme').value = arme;
            document.getElementById('edit-nombre_munitions').value = nombre_munitions;
            document.getElementById('edit-nombre_munitions_tirees').value = nombre_munitions_tirees;
            document.getElementById('edit-stock').value = stock;
            document.getElementById('edit-stand_de_tir').value = stand_de_tir;
            document.getElementById('edit-date_seance').value = date_seance;
            document.getElementById('edit-heure_seance').value = heure_seance;
            document.getElementById('edit-commentaire').value = commentaire;
            document.getElementById('edit-nom_invite').value = nom_invite;
            document.getElementById('edit-prenom_invite').value = prenom_invite;
            document.getElementById('edit-date_naissance_invite').value = date_naissance_invite;

            editModal.show();
        }
    });
});
</script>

<?php include 'footer.php'; ?>
