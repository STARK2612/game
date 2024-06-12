<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $identifiant = $_POST['identifiant'];
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, prenom, identifiant, mot_de_passe, role) VALUES (:nom, :prenom, :identifiant, :mot_de_passe, :role)");
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':identifiant', $identifiant);
        $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        header("Location: gestion_utilisateurs.php");
        exit;
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header("Location: gestion_utilisateurs.php");
        exit;
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $identifiant = $_POST['identifiant'];
        $role = $_POST['role'];

        if (!empty($_POST['mot_de_passe'])) {
            $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, identifiant = :identifiant, mot_de_passe = :mot_de_passe, role = :role WHERE id = :id");
            $stmt->bindParam(':mot_de_passe', $mot_de_passe);
        } else {
            $stmt = $conn->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, identifiant = :identifiant, role = :role WHERE id = :id");
        }

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':identifiant', $identifiant);
        $stmt->bindParam(':role', $role);
        $stmt->execute();

        header("Location: gestion_utilisateurs.php");
        exit;
    }
}

$stmt = $conn->prepare("SELECT * FROM utilisateurs");
$stmt->execute();
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<h2>Gestion des Utilisateurs</h2>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Identifiant</th>
            <th>Rôle</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($utilisateurs as $utilisateur): ?>
        <tr>
            <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
            <td><?= htmlspecialchars($utilisateur['prenom']) ?></td>
            <td><?= htmlspecialchars($utilisateur['identifiant']) ?></td>
            <td><?= htmlspecialchars($utilisateur['role']) ?></td>
            <td>
                <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $utilisateur['id'] ?>" data-nom="<?= $utilisateur['nom'] ?>" data-prenom="<?= $utilisateur['prenom'] ?>" data-identifiant="<?= $utilisateur['identifiant'] ?>" data-role="<?= $utilisateur['role'] ?>">Modifier</button>
                <form method="post" action="gestion_utilisateurs.php" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                    <input type="hidden" name="id" value="<?= $utilisateur['id'] ?>">
                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button id="add-btn" class="btn btn-primary">Ajouter un Utilisateur</button>

<!-- Modal Ajouter -->
<div id="add-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un Utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_utilisateurs.php">
                    <div class="form-group">
                        <label for="nom">Nom:</label>
                        <input type="text" id="nom" name="nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prénom:</label>
                        <input type="text" id="prenom" name="prenom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="identifiant">Identifiant:</label>
                        <input type="text" id="identifiant" name="identifiant" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="mot_de_passe">Mot de Passe:</label>
                        <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Rôle:</label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="administrateur">Administrateur</option>
                            <option value="utilisateur">Utilisateur</option>
                        </select>
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
                <h5 class="modal-title">Modifier un Utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="gestion_utilisateurs.php">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="form-group">
                        <label for="edit-nom">Nom:</label>
                        <input type="text" id="edit-nom" name="nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-prenom">Prénom:</label>
                        <input type="text" id="edit-prenom" name="prenom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-identifiant">Identifiant:</label>
                        <input type="text" id="edit-identifiant" name="identifiant" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-mot_de_passe">Mot de Passe:</label>
                        <input type="password" id="edit-mot_de_passe" name="mot_de_passe" class="form-control">
                        <small>Laissez vide pour ne pas changer le mot de passe</small>
                    </div>
                    <div class="form-group">
                        <label for="edit-role">Rôle:</label>
                        <select id="edit-role" name="role" class="form-control" required>
                            <option value="administrateur">Administrateur</option>
                            <option value="utilisateur">Utilisateur</option>
                        </select>
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
            var prenom = btn.getAttribute('data-prenom');
            var identifiant = btn.getAttribute('data-identifiant');
            var role = btn.getAttribute('data-role');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nom').value = nom;
            document.getElementById('edit-prenom').value = prenom;
            document.getElementById('edit-identifiant').value = identifiant;
            document.getElementById('edit-role').value = role;

            editModal.show();
        }
    });
});
</script>

<?php include 'footer.php'; ?>
