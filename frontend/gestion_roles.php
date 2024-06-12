<?php
include 'header.php';
include 'backend/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    // Code pour ajouter un rôle
}

$stmt = $conn->prepare("SELECT * FROM roles");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Gestion des Rôles</h2>
<table>
    <thead>
        <tr>
            <th>Rôle</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($roles as $role): ?>
        <tr>
            <td><?= htmlspecialchars($role['role']) ?></td>
            <td>
                <button class="edit-btn" data-id="<?= $role['id'] ?>">Modifier</button>
                <button class="delete-btn" data-id="<?= $role['id'] ?>">Supprimer</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button id="add-btn">Ajouter un Rôle</button>

<div id="add-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form method="post" action="gestion_roles.php">
            <h2>Ajouter un Rôle</h2>
            <label for="role">Rôle:</label>
            <input type="text" id="role" name="role" required>
            <button type="submit" name="add">Ajouter</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById("add-modal");
    var btn = document.getElementById("add-btn");
    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
</script>

<?php include 'footer.php'; ?>
