<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $categorie = $_POST['categorie'];
    $periode = $_POST['periode'];
    $date_debut = $_POST['date_debut'] ?? null;
    $date_fin = $_POST['date_fin'] ?? null;

    if ($periode == 'semaine' && (!$date_debut || !$date_fin)) {
        $errors[] = "Veuillez saisir une date de début et une date de fin.";
    }

    if (empty($errors)) {
        $params = [];
        $query = "
            SELECT 
                armes.marque, 
                armes.model, 
                armes.prix AS prix,
                SUM(seance_tir.nombre_munitions_tirees) AS total_tirs, 
                SUM(CASE WHEN seance_tir.stock = 'reglementaire' THEN seance_tir.nombre_munitions_tirees ELSE 0 END) AS cartouches_reglementaire,
                SUM(CASE WHEN seance_tir.stock = 'achete' THEN seance_tir.nombre_munitions_tirees ELSE 0 END) AS cartouches_achetees,
                SUM(CASE WHEN seance_tir.stock = 'achete' THEN seance_tir.prix_boite ELSE 0 END) AS prix_total_achete_boite
        ";

        if ($periode == 'mois') {
            $query .= ", MONTH(seance_tir.date_seance) AS mois, YEAR(seance_tir.date_seance) AS annee";
        } elseif ($periode == 'semaine') {
            $query .= ", seance_tir.date_seance";
        } else {
            $query .= ", YEAR(seance_tir.date_seance) AS annee";
        }

        $query .= "
            FROM seance_tir 
            JOIN armes ON seance_tir.arme = armes.id 
            WHERE 1=1
        ";

        if ($periode == 'semaine' && $date_debut && $date_fin) {
            $query .= " AND seance_tir.date_seance BETWEEN :date_debut AND :date_fin";
            $params[':date_debut'] = $date_debut;
            $params[':date_fin'] = $date_fin;
        } elseif ($periode == 'mois') {
            $query .= " AND YEAR(seance_tir.date_seance) = YEAR(CURDATE())";
        } elseif ($periode == 'annee') {
            $query .= " AND YEAR(seance_tir.date_seance) = YEAR(CURDATE())";
        }

        if ($periode == 'mois') {
            $query .= " GROUP BY mois, annee, armes.id";
        } elseif ($periode == 'annee') {
            $query .= " GROUP BY annee, armes.id";
        } else {
            $query .= " GROUP BY seance_tir.date_seance, armes.id";
        }

        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $seances = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

function getMonthName($monthNumber) {
    $months = [
        1 => 'Janvier',
        2 => 'Février',
        3 => 'Mars',
        4 => 'Avril',
        5 => 'Mai',
        6 => 'Juin',
        7 => 'Juillet',
        8 => 'Août',
        9 => 'Septembre',
        10 => 'Octobre',
        11 => 'Novembre',
        12 => 'Décembre'
    ];
    return $months[$monthNumber] ?? '';
}
?>

<?php include 'header.php'; ?>

<h2>Statistiques</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" action="gestion_statistiques.php">
    <div class="form-group">
        <label for="categorie">Catégorie:</label>
        <select id="categorie" name="categorie" class="form-control" required>
            <option value="armes">Armes</option>
            <option value="invites">Invités</option>
        </select>
    </div>
    <div class="form-group">
        <label for="periode">Période:</label>
        <select id="periode" name="periode" class="form-control" required>
            <option value="semaine">Semaine</option>
            <option value="mois">Mois</option>
            <option value="annee">Année</option>
        </select>
    </div>
    <div class="form-group" id="date_debut_group">
        <label for="date_debut">Date de début:</label>
        <input type="date" id="date_debut" name="date_debut" class="form-control">
    </div>
    <div class="form-group" id="date_fin_group">
        <label for="date_fin">Date de fin:</label>
        <input type="date" id="date_fin" name="date_fin" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Afficher les statistiques</button>
</form>

<?php if (isset($seances) && $categorie == 'armes'): ?>
    <h3>Résultats</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Période</th>
                <th>Nom de l'Arme</th>
                <th>Prix de l'Arme</th>
                <th>Cartouches Réglementaire Tirées</th>
                <th>Prix Total Cartouches Réglementaire (€)</th>
                <th>Cartouches Achetées Tirées</th>
                <th>Prix Total Cartouches Achetées (€)</th>
                <th>Prix de Réparation</th>
                <th>Total des Prix (€)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($seances as $seance):
                if (!isset($seance['prix_total_achete_boite'])) {
                    $seance['prix_total_achete_boite'] = 0;
                }

                $prixTotalReglementaire = 0.0;
                $prixTotalAchetees = 0.0;

                if ($seance['cartouches_reglementaire'] > 0) {
                    $stmt = $conn->prepare("SELECT prix_unite FROM articles WHERE type = 'munition'");
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $prixTotalReglementaire = $seance['cartouches_reglementaire'] * $result['prix_unite'];
                }

                if ($seance['cartouches_achetees'] > 0 && $seance['prix_total_achete_boite'] > 0) {
                    $prixTotalAchetees = $seance['cartouches_achetees'] * $seance['prix_total_achete_boite'] / 50;
                }

                $totalPrix = $seance['prix'] + $prixTotalReglementaire + $prixTotalAchetees;
            ?>
                <tr>
                    <?php if ($periode == 'mois'): ?>
                        <td><?= htmlspecialchars(getMonthName($seance['mois'])) ?></td>
                    <?php elseif ($periode == 'annee'): ?>
                        <td><?= htmlspecialchars($seance['annee']) ?></td>
                    <?php else: ?>
                        <td><?= htmlspecialchars($seance['date_seance']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($seance['marque'] . ' ' . $seance['model']) ?></td>
                    <td><?= number_format($seance['prix'], 2) ?> €</td>
                    <td><?= htmlspecialchars($seance['cartouches_reglementaire']) ?></td>
                    <td><?= number_format($prixTotalReglementaire, 2) ?> €</td>
                    <td><?= htmlspecialchars($seance['cartouches_achetees']) ?></td>
                    <td><?= number_format($prixTotalAchetees, 2) ?> €</td>
                    <td><?= number_format(isset($seance['prix_reparation']) ? $seance['prix_reparation'] : 0, 2) ?> €</td>
                    <td><?= number_format($totalPrix, 2) ?> €</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var periodeSelect = document.getElementById('periode');
    var dateDebutGroup = document.getElementById('date_debut_group');
    var dateFinGroup = document.getElementById('date_fin_group');

    function toggleDateFields() {
        if (periodeSelect.value === 'semaine') {
            dateDebutGroup.style.display = 'block';
            dateFinGroup.style.display = 'block';
        } else {
            dateDebutGroup.style.display = 'none';
            dateFinGroup.style.display = 'none';
        }
    }

    periodeSelect.addEventListener('change', toggleDateFields);
    toggleDateFields(); // initial call to set the correct visibility on page load

    var form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        if (periodeSelect.value === 'semaine') {
            var dateDebut = document.getElementById('date_debut').value;
            var dateFin = document.getElementById('date_fin').value;
            if (!dateDebut || !dateFin) {
                event.preventDefault();
                alert('Veuillez saisir une date de début et une date de fin.');
            }
        }
    });
});
</script>

<?php include 'footer.php'; ?>
