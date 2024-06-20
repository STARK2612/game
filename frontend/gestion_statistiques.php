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

    if (empty($errors) && $categorie == 'armes') {
        $params = [];
        $query = "
            SELECT 
                armes.marque, 
                armes.model, 
                armes.prix AS prix_arme,
                SUM(CASE WHEN seance_tir.stock = 'reglementaire' THEN seance_tir.nombre_munitions_tirees ELSE 0 END) AS cartouches_reglementaire,
                SUM(CASE WHEN seance_tir.stock = 'achete' THEN seance_tir.nombre_munitions_tirees ELSE 0 END) AS cartouches_achete,
                SUM(reparations.prix) AS prix_reparations,
                articles.prix_unite AS prix_unitaire_reglementaire
                ";

        if ($periode == 'mois') {
            $query .= ", MONTH(seance_tir.date_seance) AS mois, YEAR(seance_tir.date_seance) AS annee";
        } elseif ($periode == 'semaine') {
            $query .= ", seance_tir.date_seance";
        } else {
            $query .= ", YEAR(seance_tir.date_seance) AS annee";
        }

        $query .= "
            FROM armes
            LEFT JOIN seance_tir ON armes.id = seance_tir.arme
            LEFT JOIN reparations ON armes.id = reparations.arme_id
            LEFT JOIN articles ON seance_tir.arme = articles.id AND articles.type = 'munition'
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
            $query .= " GROUP BY mois, annee, armes.id, articles.prix_unite";
        } elseif ($periode == 'annee') {
            $query .= " GROUP BY annee, armes.id, articles.prix_unite";
        } else {
            $query .= " GROUP BY seance_tir.date_seance, armes.id, articles.prix_unite";
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
            <option value="munitions">Munitions</option>
            <option value="seances">Séances</option>
            <option value="articles">Articles</option>
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
    <div class="form-group" id="date_debut_group" style="display:none;">
        <label for="date_debut">Date de début:</label>
        <input type="date" id="date_debut" name="date_debut" class="form-control">
    </div>
    <div class="form-group" id="date_fin_group" style="display:none;">
        <label for="date_fin">Date de fin:</label>
        <input type="date" id="date_fin" name="date_fin" class="form-control">
    </div>
    <button type="submit" class="btn btn-primary">Afficher les statistiques</button>
</form>

<?php if (isset($seances)): ?>
    <h3>Résultats</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <?php if ($periode == 'mois'): ?>
                    <th>Mois</th>
                <?php elseif ($periode == 'annee'): ?>
                    <th>Année</th>
                <?php endif; ?>
                <th>Nom de l'Arme</th>
                <th>Prix de l'Arme (€)</th>
                <th>Cartouches Réglementaire Tirées</th>
                <th>Prix Total Cartouches Réglementaire (€)</th>
                <th>Cartouches Achetées Tirées</th>
                <th>Prix Total Cartouches Achetées (€)</th>
                <th>Prix de Réparation (€)</th>
                <th>Total des Prix (€)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $currentArme = null;
            $armeTotals = [
                'prix_arme' => 0.0,
                'cartouches_reglementaire' => 0,
                'prix_cartouches_reglementaire' => 0.0,
                'cartouches_achete' => 0,
                'prix_cartouches_achete' => 0.0,
                'prix_reparations' => 0.0
            ];

            foreach ($seances as $seance):
                $armeName = $seance['marque'] . ' ' . $seance['model'];
                $prix_total_reglementaire = $seance['cartouches_reglementaire'] * $seance['prix_unitaire_reglementaire'];
                $prix_total_achete = $seance['cartouches_achete'] * ($seance['prix_total_achete_boite'] / 50); // Assumed price per 50
                $totalPrix = $seance['prix_arme'] + $prix_total_reglementaire + $prix_total_achete + $seance['prix_reparations'];

                if ($currentArme !== $armeName) {
                    $currentArme = $armeName;
                }

                $armeTotals['prix_arme'] += $seance['prix_arme'];
                $armeTotals['cartouches_reglementaire'] += $seance['cartouches_reglementaire'];
                $armeTotals['prix_cartouches_reglementaire'] += $prix_total_reglementaire;
                $armeTotals['cartouches_achete'] += $seance['cartouches_achete'];
                $armeTotals['prix_cartouches_achete'] += $prix_total_achete;
                $armeTotals['prix_reparations'] += $seance['prix_reparations'];
            ?>
                <tr>
                    <?php if ($periode == 'mois'): ?>
                        <td><?= htmlspecialchars(getMonthName($seance['mois'])) ?></td>
                    <?php elseif ($periode == 'annee'): ?>
                        <td><?= htmlspecialchars($seance['annee']) ?></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($armeName) ?></td>
                    <td><?= number_format($seance['prix_arme'], 2) ?> €</td>
                    <td><?= htmlspecialchars($seance['cartouches_reglementaire']) ?></td>
                    <td><?= number_format($prix_total_reglementaire, 2) ?> €</td>
                    <td><?= htmlspecialchars($seance['cartouches_achete']) ?></td>
                    <td><?= number_format($prix_total_achete, 2) ?> €</td>
                    <td><?= number_format($seance['prix_reparations'], 2) ?> €</td>
                    <td><?= number_format($totalPrix, 2) ?> €</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="8">Total des Prix:</td>
                <td><?= number_format($armeTotals['prix_arme'] + $armeTotals['prix_cartouches_reglementaire'] + $armeTotals['prix_cartouches_achete'] + $armeTotals['prix_reparations'], 2) ?> €</td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var categorieSelect = document.getElementById('categorie');
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
