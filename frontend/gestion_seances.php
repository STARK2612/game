<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $arme = $_POST['arme'];
        $stock = $_POST['stock'];
        $nombre_munitions_tirees = $_POST['nombre_munitions_tirees'];
        $stand_de_tir = $_POST['stand_de_tir'];
        $date_seance = $_POST['date_seance'];
        $heure_debut = $_POST['heure_debut'];
        $heure_fin = $_POST['heure_fin'];
        $nom_invite = $_POST['nom_invite'];
        $commentaire = $_POST['commentaire'];
        
        if ($stock == 'reglementaire') {
            $stmt = $conn->prepare("INSERT INTO seance_tir (arme, stock, nombre_munitions_tirees, stand_de_tir, date_seance, heure_debut, heure_fin, nom_invite, commentaire) VALUES (:arme, :stock, :nombre_munitions_tirees, :stand_de_tir, :date_seance, :heure_debut, :heure_fin, :nom_invite, :commentaire)");
        } else {
            $prix_boite = $_POST['prix_boite'];
            $tarif = $_POST['tarif'];
            $stmt = $conn->prepare("INSERT INTO seance_tir (arme, stock, nombre_munitions_tirees, stand_de_tir, date_seance, heure_debut, heure_fin, prix_boite, tarif, nom_invite, commentaire) VALUES (:arme, :stock, :nombre_munitions_tirees, :stand_de_tir, :date_seance, :heure_debut, :heure_fin, :prix_boite, :tarif, :nom_invite, :commentaire)");
            $stmt->bindParam(':prix_boite', $prix_boite);
            $stmt->bindParam(':tarif', $tarif);
        }
        
        $stmt->bindParam(':arme', $arme);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':nombre_munitions_tirees', $nombre_munitions_tirees);
        $stmt->bindParam(':stand_de_tir', $stand_de_tir);
        $stmt->bindParam(':date_seance', $date_seance);
        $stmt->bindParam(':heure_debut', $heure_debut);
        $stmt->bindParam(':heure_fin', $heure_fin);
        $stmt->bindParam(':nom_invite', $nom_invite);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->execute();

        // Mettre à jour le stock réglementaire
        if ($stock == 'reglementaire') {
            $_SESSION['stock_total_cartouches'] -= $nombre_munitions_tirees;

            // Mettre à jour la base de données pour refléter les changements de stock
            $stmt = $conn->prepare("
                SELECT id, cartouches_par_boite, (SELECT COALESCE(SUM(quantite), 0) FROM achats WHERE article_id = articles.id) AS total_boites
                FROM articles
                WHERE type = 'munition'
            ");
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($articles as $article) {
                $total_cartouches = $article['total_boites'] * $article['cartouches_par_boite'];
                if ($total_cartouches >= $nombre_munitions_tirees) {
                    $nouveau_total_boites = ceil(($total_cartouches - $nombre_munitions_tirees) / $article['cartouches_par_boite']);
                    $stmt = $conn->prepare("
                        UPDATE achats
                        SET quantite = :nouveau_total_boites
                        WHERE article_id = :id
                    ");
                    $stmt->bindParam(':nouveau_total_boites', $nouveau_total_boites);
                    $stmt->bindParam(':id', $article['id']);
                    $stmt->execute();
                    break;
                }
            }
        }

        header("Location: gestion_seances.php");
        exit;
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];

        // Récupérer les informations de la séance de tir avant suppression
        $stmt = $conn->prepare("SELECT stock, nombre_munitions_tirees FROM seance_tir WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $seance = $stmt->fetch(PDO::FETCH_ASSOC);

        // Supprimer la séance de tir
        $stmt = $conn->prepare("DELETE FROM seance_tir WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Mettre à jour le stock réglementaire si nécessaire
        if ($seance['stock'] == 'reglementaire') {
            $_SESSION['stock_total_cartouches'] += $seance['nombre_munitions_tirees'];

            // Mettre à jour la base de données pour refléter les changements de stock
            $stmt = $conn->prepare("
                SELECT id, cartouches_par_boite, (SELECT COALESCE(SUM(quantite), 0) FROM achats WHERE article_id = articles.id) AS total_boites
                FROM articles
                WHERE type = 'munition'
            ");
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($articles as $article) {
                $total_cartouches = $article['total_boites'] * $article['cartouches_par_boite'];
                $nouveau_total_boites = ceil(($total_cartouches + $seance['nombre_munitions_tirees']) / $article['cartouches_par_boite']);
                $stmt = $conn->prepare("
                    UPDATE achats
                    SET quantite = :nouveau_total_boites
                    WHERE article_id = :id
                ");
                $stmt->bindParam(':nouveau_total_boites', $nouveau_total_boites);
                $stmt->bindParam(':id', $article['id']);
                $stmt->execute();
                break;
            }
        }

        header("Location: gestion_seances.php");
        exit;
    } elseif (isset($_POST['update'])) {
        $id = $_POST['id'];
        $arme = $_POST['arme'];
        $stock = $_POST['stock'];
        $nombre_munitions_tirees = $_POST['nombre_munitions_tirees'];
        $stand_de_tir = $_POST['stand_de_tir'];
        $date_seance = $_POST['date_seance'];
        $heure_debut = $_POST['heure_debut'];
        $heure_fin = $_POST['heure_fin'];
        $nom_invite = $_POST['nom_invite'];
        $commentaire = $_POST['commentaire'];
        
        if ($stock == 'reglementaire') {
            $stmt = $conn->prepare("UPDATE seance_tir SET arme = :arme, stock = :stock, nombre_munitions_tirees = :nombre_munitions_tirees, stand_de_tir = :stand_de_tir, date_seance = :date_seance, heure_debut = :heure_debut, heure_fin = :heure_fin, nom_invite = :nom_invite, commentaire = :commentaire WHERE id = :id");
        } else {
            $prix_boite = $_POST['prix_boite'];
            $tarif = $_POST['tarif'];
            $stmt = $conn->prepare("UPDATE seance_tir SET arme = :arme, stock = :stock, nombre_munitions_tirees = :nombre_munitions_tirees, stand_de_tir = :stand_de_tir, date_seance = :date_seance, heure_debut = :heure_debut, heure_fin = :heure_fin, prix_boite = :prix_boite, tarif = :tarif, nom_invite = :nom_invite, commentaire = :commentaire WHERE id = :id");
            $stmt->bindParam(':prix_boite', $prix_boite);
            $stmt->bindParam(':tarif', $tarif);
        }

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':arme', $arme);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':nombre_munitions_tirees', $nombre_munitions_tirees);
        $stmt->bindParam(':stand_de_tir', $stand_de_tir);
        $stmt->bindParam(':date_seance', $date_seance);
        $stmt->bindParam(':heure_debut', $heure_debut);
        $stmt->bindParam(':heure_fin', $heure_fin);
        $stmt->bindParam(':nom_invite', $nom_invite);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->execute();

        // Mettre à jour le stock réglementaire si nécessaire
        if ($seance['stock'] != $stock) {
            if ($stock == 'reglementaire') {
                $_SESSION['stock_total_cartouches'] -= $nombre_munitions_tirees;
            } else {
                $_SESSION['stock_total_cartouches'] += $nombre_munitions_tirees;
            }

            // Mettre à jour la base de données pour refléter les changements de stock
            $stmt = $conn->prepare("
                SELECT id, cartouches_par_boite, (SELECT COALESCE(SUM(quantite), 0) FROM achats WHERE article_id = articles.id) AS total_boites
                FROM articles
                WHERE type = 'munition'
            ");
            $stmt->execute();
            $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($articles as $article) {
                $total_cartouches = $article['total_boites'] * $article['cartouches_par_boite'];
                if ($seance['stock'] == 'reglementaire' && $stock == 'achete') {
                    $nouveau_total_boites = ceil(($total_cartouches + $seance['nombre_munitions_tirees']) / $article['cartouches_par_boite']);
                } else {
                    $nouveau_total_boites = ceil(($total_cartouches - $seance['nombre_munitions_tirees']) / $article['cartouches_par_boite']);
                }
                $stmt = $conn->prepare("
                    UPDATE achats
                    SET quantite = :nouveau_total_boites
                    WHERE article_id = :id
                ");
                $stmt->bindParam(':nouveau_total_boites', $nouveau_total_boites);
                $stmt->bindParam(':id', $article['id']);
                $stmt->execute();
                break;
            }
        }

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
            <th>n°ordre</th>
            <th>Date</th>
            <th>Heure de début</th>
            <th>Heure de fin</th>
            <th>Arme</th>
            <th>Nombre de Munitions Tirées</th>
            <th>Stock</th>
            <th>Stand de Tir</th>
            <th>Nom de l'Invité</th>
            <th>Commentaire</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($seances as $seance): ?>
        <tr>
            <td><?= 'SEA-' . str_pad($seance['id'], 5, '0', STR_PAD_LEFT) ?></td>
            <td><?= date('d/m/Y', strtotime($seance['date_seance'])) ?></td>
            <td><?= date('H:i', strtotime($seance['heure_debut'])) ?></td>
            <td><?= date('H:i', strtotime($seance['heure_fin'])) ?></td>
            <td><?= htmlspecialchars($seance['marque'] . ' ' . $seance['model']) ?></td>
            <td><?= htmlspecialchars($seance['nombre_munitions_tirees']) ?></td>
            <td><?= htmlspecialchars($seance['stock']) ?></td>
            <td><?= htmlspecialchars($seance['nom_stand']) ?></td>
            <td><?= htmlspecialchars($seance['nom_invite']) ?></td>
            <td><?= htmlspecialchars($seance['commentaire']) ?></td>
            <td>
                <button class="btn btn-sm btn-warning edit-btn" data-id="<?= $seance['id'] ?>" data-arme="<?= $seance['arme'] ?>" data-stock="<?= $seance['stock'] ?>" data-nombre_munitions_tirees="<?= $seance['nombre_munitions_tirees'] ?>" data-stand_de_tir="<?= $seance['stand_de_tir'] ?>" data-date_seance="<?= $seance['date_seance'] ?>" data-heure_debut="<?= $seance['heure_debut'] ?>" data-heure_fin="<?= $seance['heure_fin'] ?>" data-prix_boite="<?= $seance['prix_boite'] ?>" data-tarif="<?= $seance['tarif'] ?>" data-nom_invite="<?= $seance['nom_invite'] ?>" data-commentaire="<?= $seance['commentaire'] ?>">Modifier</button>
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
                        <label for="stock">Stock:</label>
                        <select id="stock" name="stock" class="form-control" required>
                            <option value="achete">Acheté</option>
                            <option value="reglementaire">Réglementaire</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nombre_munitions_tirees">Nombre de Munitions Tirées:</label>
                        <input type="number" id="nombre_munitions_tirees" name="nombre_munitions_tirees" class="form-control" required>
                    </div>
                    <div class="form-group" id="prix_boite_group">
                        <label for="prix_boite">Prix de la boîte de munitions:</label>
                        <input type="number" id="prix_boite" name="prix_boite" class="form-control" step="0.01">
                    </div>
                    <div class="form-group" id="tarif_group">
                        <label for="tarif">Tarif:</label>
                        <select id="tarif" name="tarif" class="form-control">
                            <option value="club">Tarif Club</option>
                            <option value="exterieur">Tarif Extérieur</option>
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
                        <label for="heure_debut">Heure de début:</label>
                        <input type="time" id="heure_debut" name="heure_debut" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="heure_fin">Heure de fin:</label>
                        <input type="time" id="heure_fin" name="heure_fin" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="nom_invite">Nom de l'Invité:</label>
                        <input type="text" id="nom_invite" name="nom_invite" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="commentaire">Commentaire:</label>
                        <textarea id="commentaire" name="commentaire" class="form-control"></textarea>
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
                        <label for="edit-stock">Stock:</label>
                        <select id="edit-stock" name="stock" class="form-control" required>
                            <option value="achete">Acheté</option>
                            <option value="reglementaire">Réglementaire</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-nombre_munitions_tirees">Nombre de Munitions Tirées:</label>
                        <input type="number" id="edit-nombre_munitions_tirees" name="nombre_munitions_tirees" class="form-control" required>
                    </div>
                    <div class="form-group" id="edit-prix_boite_group">
                        <label for="edit-prix_boite">Prix de la boîte de munitions:</label>
                        <input type="number" id="edit-prix_boite" name="prix_boite" class="form-control" step="0.01">
                    </div>
                    <div class="form-group" id="edit-tarif_group">
                        <label for="edit-tarif">Tarif:</label>
                        <select id="edit-tarif" name="tarif" class="form-control">
                            <option value="club">Tarif Club</option>
                            <option value="exterieur">Tarif Extérieur</option>
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
                        <label for="edit-heure_debut">Heure de début:</label>
                        <input type="time" id="edit-heure_debut" name="heure_debut" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-heure_fin">Heure de fin:</label>
                        <input type="time" id="edit-heure_fin" name="heure_fin" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-nom_invite">Nom de l'Invité:</label>
                        <input type="text" id="edit-nom_invite" name="nom_invite" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="edit-commentaire">Commentaire:</label>
                        <textarea id="edit-commentaire" name="commentaire" class="form-control"></textarea>
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
            var stock = btn.getAttribute('data-stock');
            var nombre_munitions_tirees = btn.getAttribute('data-nombre_munitions_tirees');
            var stand_de_tir = btn.getAttribute('data-stand_de_tir');
            var date_seance = btn.getAttribute('data-date_seance');
            var heure_debut = btn.getAttribute('data-heure_debut');
            var heure_fin = btn.getAttribute('data-heure_fin');
            var prix_boite = btn.getAttribute('data-prix_boite');
            var tarif = btn.getAttribute('data-tarif');
            var nom_invite = btn.getAttribute('data-nom_invite');
            var commentaire = btn.getAttribute('data-commentaire');

            document.getElementById('edit-id').value = id;
            document.getElementById('edit-arme').value = arme;
            document.getElementById('edit-stock').value = stock;
            document.getElementById('edit-nombre_munitions_tirees').value = nombre_munitions_tirees;
            document.getElementById('edit-stand_de_tir').value = stand_de_tir;
            document.getElementById('edit-date_seance').value = date_seance;
            document.getElementById('edit-heure_debut').value = heure_debut;
            document.getElementById('edit-heure_fin').value = heure_fin;
            document.getElementById('edit-prix_boite').value = prix_boite;
            document.getElementById('edit-tarif').value = tarif;
            document.getElementById('edit-nom_invite').value = nom_invite;
            document.getElementById('edit-commentaire').value = commentaire;

            if (stock === 'reglementaire') {
                document.getElementById('edit-prix_boite_group').style.display = 'none';
                document.getElementById('edit-tarif_group').style.display = 'none';
            } else {
                document.getElementById('edit-prix_boite_group').style.display = 'block';
                document.getElementById('edit-tarif_group').style.display = 'block';
            }

            editModal.show();
        }
    });

    var stockSelect = document.getElementById('stock');
    stockSelect.onchange = function() {
        var prixBoiteGroup = document.getElementById('prix_boite_group');
        var tarifGroup = document.getElementById('tarif_group');
        if (stockSelect.value === 'reglementaire') {
            prixBoiteGroup.style.display = 'none';
            tarifGroup.style.display = 'none';
        } else {
            prixBoiteGroup.style.display = 'block';
            tarifGroup.style.display = 'block';
        }
    };

    var editStockSelect = document.getElementById('edit-stock');
    editStockSelect.onchange = function() {
        var editPrixBoiteGroup = document.getElementById('edit-prix_boite_group');
        var editTarifGroup = document.getElementById('edit-tarif_group');
        if (editStockSelect.value === 'reglementaire') {
            editPrixBoiteGroup.style.display = 'none';
            editTarifGroup.style.display = 'none';
        } else {
            editPrixBoiteGroup.style.display = 'block';
            editTarifGroup.style.display = 'block';
        }
    };
});
</script>

<?php include 'footer.php'; ?>
