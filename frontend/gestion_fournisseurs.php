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

        $stmt = $conn->prepare("INSERT INTO fournisseurs (nom, adresse, code_postal, ville, pays, telephone, email) VALUES (:nom, :adresse, :code_postal, :ville, :pays, :telephone, :email)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':code_postal', $code_postal);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':pays', $pays);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        header("Location: gestion_fournisseurs.php");
        exit;
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM fournisseurs WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: gestion_fournisseurs.php");
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

        $stmt = $conn->prepare("UPDATE fournisseurs SET nom = :nom, adresse = :adresse, code_postal = :code_postal, ville = :ville, pays = :pays, telephone = :telephone, email = :email WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':code_postal', $code_postal);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':pays', $pays);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        header("Location: gestion_fournisseurs.php");
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM fournisseurs");
$stmt->execute();
$fournisseurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<h2>Gestion des Fournisseurs</h2>
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
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fournisseurs as $fournisseur): ?>
        <tr>
            <td><?= htmlspecialchars($fournisseur['nom']) ?></td>
            <td><?= htmlspecialchars($fournisseur['adresse']) ?></td>
            <td><?= htmlspecialchars($fournisseur['code_postal']) ?></td>
            <td><?= htmlspecialchars($fournisseur['ville']) ?></td>
            <td><?= htmlspecialchars($fournisseur['pays']) ?></td>
            <td><?= htmlspecialchars($fournisseur['telephone']) ?></td>
            <td><?= htmlspecialchars($fournisseur['email']) ?></td>
            <td>
                <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $fournisseur['id'] ?>" data-nom="<?= $fournisseur['nom'] ?>" data-adresse="<?= $fournisseur['adresse'] ?>" data-code_postal="<?= $fournisseur['code_postal'] ?>" data-ville="<?= $fournisseur['ville'] ?>" data-pays="<?= $fournisseur['pays'] ?>" data-telephone="<?= $fournisseur['telephone'] ?>" data-email="<?= $fournisseur['email'] ?>">Modifier</button>
                <form method="post" action="gestion_fournisseurs.php" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer ce fournisseur ?');">
                    <input type="hidden" name="id" value="<?= $fournisseur['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button id="add-btn" class="btn btn-primary">Ajouter un Fournisseur</button>

<!-- Modal Ajouter -->
<div id="add-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Fournisseur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_fournisseurs.php">
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
                <h5 class="modal-title">Modifier un Fournisseur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_fournisseurs.php">
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

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nom').value = nom;
            document.getElementById('edit-adresse').value = adresse;
            document.getElementById('edit-code_postal').value = code_postal;
            document.getElementById('edit-ville').value = ville;
            document.getElementById('edit-pays').value = pays;
            document.getElementById('edit-telephone').value = telephone;
            document.getElementById('edit-email').value = email;

            editModal.show();
        }
    });
});
</script>

<?php include 'footer.php'; ?>
