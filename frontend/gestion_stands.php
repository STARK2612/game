<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $nom = $_POST['nom'];
        $adresse = $_POST['adresse'];
        $code_postal = $_POST['code_postal'];
        $ville = $_POST['ville'];
        $pays = $_POST['pays'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        $prix_par_invite = $_POST['prix_par_invite'];

        $stmt = $conn->prepare("INSERT INTO stands (nom, adresse, code_postal, ville, pays, telephone, email, prix_par_invite) VALUES (:nom, :adresse, :code_postal, :ville, :pays, :telephone, :email, :prix_par_invite)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':code_postal', $code_postal);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':pays', $pays);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':prix_par_invite', $prix_par_invite);
        $stmt->execute();

        header("Location: gestion_stands.php");
        exit;
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        // Supprimer les enregistrements dépendants dans la table seance_tir
        $stmt = $conn->prepare("DELETE FROM seance_tir WHERE stand_de_tir = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Ensuite, supprimer l'enregistrement dans la table stands
        $stmt = $conn->prepare("DELETE FROM stands WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: gestion_stands.php");
        exit;
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $adresse = $_POST['adresse'];
        $code_postal = $_POST['code_postal'];
        $ville = $_POST['ville'];
        $pays = $_POST['pays'];
        $telephone = $_POST['telephone'];
        $email = $_POST['email'];
        $prix_par_invite = $_POST['prix_par_invite'];

        $stmt = $conn->prepare("UPDATE stands SET nom = :nom, adresse = :adresse, code_postal = :code_postal, ville = :ville, pays = :pays, telephone = :telephone, email = :email, prix_par_invite = :prix_par_invite WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':code_postal', $code_postal);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':pays', $pays);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':prix_par_invite', $prix_par_invite);
        $stmt->execute();

        header("Location: gestion_stands.php");
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM stands");
$stmt->execute();
$stands = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
<h2>Gestion des Stands de Tir</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Adresse</th>
            <th>Code Postal</th>
            <th>Ville</th>
            <th>Pays</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Prix par Invité</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stands as $stand): ?>
        <tr>
            <td><?= htmlspecialchars($stand['nom']) ?></td>
            <td><?= htmlspecialchars($stand['adresse']) ?></td>
            <td><?= htmlspecialchars($stand['code_postal']) ?></td>
            <td><?= htmlspecialchars($stand['ville']) ?></td>
            <td><?= htmlspecialchars($stand['pays']) ?></td>
            <td><?= htmlspecialchars($stand['telephone']) ?></td>
            <td><?= htmlspecialchars($stand['email']) ?></td>
            <td><?= htmlspecialchars($stand['prix_par_invite']) ?></td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-warning edit-btn mb-1" data-id="<?= $stand['id'] ?>" data-nom="<?= $stand['nom'] ?>" data-adresse="<?= $stand['adresse'] ?>" data-code_postal="<?= $stand['code_postal'] ?>" data-ville="<?= $stand['ville'] ?>" data-pays="<?= $stand['pays'] ?>" data-telephone="<?= $stand['telephone'] ?>" data-email="<?= $stand['email'] ?>" data-prix_par_invite="<?= $stand['prix_par_invite'] ?>">Modifier</button>
                <form method="post" action="gestion_stands.php" onsubmit="return confirm('Voulez-vous vraiment supprimer ce stand de tir ?');">
                    <input type="hidden" name="id" value="<?= $stand['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button id="add-btn" class="btn btn-primary">Ajouter un Stand</button>

<!-- Modal Ajouter -->
<div id="add-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Stand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_stands.php">
                    <div class="form-group">
                        <label for="nom">Nom:</label>
                        <input type="text" id="nom" name="nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="adresse">Adresse:</label>
                        <input type="text" id="adresse" name="adresse" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="code_postal">Code Postal:</label>
                        <input type="text" id="code_postal" name="code_postal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="ville">Ville:</label>
                        <input type="text" id="ville" name="ville" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="pays">Pays:</label>
                        <input type="text" id="pays" name="pays" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="telephone">Téléphone:</label>
                        <input type="text" id="telephone" name="telephone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="prix_par_invite">Prix par Invité:</label>
                        <input type="number" id="prix_par_invite" name="prix_par_invite" class="form-control" step="0.01" required>
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
                <h5 class="modal-title">Modifier un Stand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_stands.php">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-nom">Nom:</label>
                        <input type="text" id="edit-nom" name="nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-adresse">Adresse:</label>
                        <input type="text" id="edit-adresse" name="adresse" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-code_postal">Code Postal:</label>
                        <input type="text" id="edit-code_postal" name="code_postal" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-ville">Ville:</label>
                        <input type="text" id="edit-ville" name="ville" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-pays">Pays:</label>
                        <input type="text" id="edit-pays" name="pays" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-telephone">Téléphone:</label>
                        <input type="text" id="edit-telephone" name="telephone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email:</label>
                        <input type="email" id="edit-email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-prix_par_invite">Prix par Invité:</label>
                        <input type="number" id="edit-prix_par_invite" name="prix_par_invite" class="form-control" step="0.01" required>
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
            var nom = btn.getAttribute('data-nom');
            var adresse = btn.getAttribute('data-adresse');
            var code_postal = btn.getAttribute('data-code_postal');
            var ville = btn.getAttribute('data-ville');
            var pays = btn.getAttribute('data-pays');
            var telephone = btn.getAttribute('data-telephone');
            var email = btn.getAttribute('data-email');
            var prix_par_invite = btn.getAttribute('data-prix_par_invite');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nom').value = nom;
            document.getElementById('edit-adresse').value = adresse;
            document.getElementById('edit-code_postal').value = code_postal;
            document.getElementById('edit-ville').value = ville;
            document.getElementById('edit-pays').value = pays;
            document.getElementById('edit-telephone').value = telephone;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-prix_par_invite').value = prix_par_invite;

            editModal.show();
        }
    });
});
</script>

<?php include 'footer.php'; ?>
