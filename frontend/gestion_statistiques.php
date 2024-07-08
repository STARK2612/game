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
        if ($categorie == 'armes') {
            $query = "
                SELECT 
                    armes.marque, 
                    armes.model, 
                    armes.prix AS prix_arme, 
                    SUM(CASE WHEN seance_tir.stock = 'reglementaire' THEN seance_tir.nombre_munitions_tirees ELSE 0 END) AS cartouches_reglementaire,
                    SUM(CASE WHEN seance_tir.stock = 'achete' THEN seance_tir.nombre_munitions_tirees ELSE 0 END) AS cartouches_achetees,
                    SUM(CASE WHEN seance_tir.stock = 'achete' THEN seance_tir.prix_boite ELSE 0 END) AS prix_total_cartouches_achetees,
                    armes.prix_reparation,
                    ";

            if ($periode == 'mois') {
                $query .= "MONTH(seance_tir.date_seance) AS mois, YEAR(seance_tir.date_seance) AS annee";
            } elseif ($periode == 'semaine') {
                $query .= "seance_tir.date_seance AS date_seance";
            } else {
                $query .= "YEAR(seance_tir.date_seance) AS annee";
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
        } elseif ($categorie == 'invites') {
            $query = "
                SELECT 
                    stands.nom AS nom_stand,
                    SUM(CASE WHEN seance_tir.nom_invite != '' THEN 1 ELSE 0 END) AS nombre_invites,
                    COUNT(seance_tir.id) AS nombre_seances,
                    SUM(CASE WHEN seance_tir.nom_invite != '' THEN 1 ELSE 0 END) * 20 AS prix_total_invite
                ";

            if ($periode == 'mois') {
                $query .= ", MONTH(seance_tir.date_seance) AS mois, YEAR(seance_tir.date_seance) AS annee";
            } elseif ($periode == 'semaine') {
                $query .= ", seance_tir.date_seance AS date_seance";
            } else {
                $query .= ", YEAR(seance_tir.date_seance) AS annee";
            }

            $query .= "
                FROM seance_tir 
                JOIN stands ON seance_tir.stand_de_tir = stands.id 
                WHERE seance_tir.nom_invite != ''
            ";

            if ($periode == 'semaine' && $date_debut && $date_fin) {
                $query .= " AND seance_tir.date_seance BETWEEN :date_debut AND :date_fin";
                $params[':date_debut'] = $date_debut;
                $params[':date_fin'] = $date_fin;
            }

            if ($periode == 'mois') {
                $query .= " GROUP BY mois, annee, stands.id";
            } elseif ($periode == 'annee') {
                $query .= " GROUP BY annee, stands.id";
            } else {
                $query .= " GROUP BY seance_tir.date_seance, stands.id";
            }

            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $seances = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
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

<div class="container">
    <h2>Statistiques 
        <button id="generate-pdf" class="btn btn-outline-primary btn-sm">Générer PDF</button>
        <button type="submit" form="stats-form" class="btn btn-primary">Afficher les statistiques</button>
    </h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form id="stats-form" method="post" action="gestion_statistiques.php">
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
    </form>

    <?php if (isset($seances) && $categorie == 'armes'): ?>
        <h3>Résultats pour les Armes</h3>
        <div id="stat-table" class="table-responsive">
            <table class="table table-bordered" id="statistiquesTable">
                <thead>
                    <tr>
                        <?php if ($periode == 'mois'): ?>
                            <th>Mois</th>
                            <th>Année</th>
                        <?php elseif ($periode == 'annee'): ?>
                            <th>Année</th>
                        <?php elseif ($periode == 'semaine'): ?>
                            <th>Date</th>
                        <?php endif; ?>
                        <th>Nom de l'Arme</th>
                        <th>Prix de l'Arme</th>
                        <th>Cartouches Réglementaire Tirées</th>
                        <th>Cartouches Achetées Tirées</th>
                        <th>Prix Total Cartouches Achetées (€)</th>
                        <th>Prix de Réparation</th>
                        <th>Total cartouches tirées</th>
                        <th>Total des Prix (€)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($seances as $seance):
                        $periodName = ($periode == 'mois') ? getMonthName($seance['mois']) : ($periode == 'annee' ? $seance['annee'] : $seance['date_seance']);
                        $totalCartouchesTirees = $seance['cartouches_reglementaire'] + $seance['cartouches_achetees'];
                        $totalDesPrix = ($seance['prix_arme'] ?? 0) + ($seance['prix_total_cartouches_achetees'] ?? 0) + ($seance['prix_reparation'] ?? 0);
                    ?>
                        <tr>
                            <?php if ($periode == 'mois'): ?>
                                <td><?= htmlspecialchars($periodName) ?></td>
                                <td><?= htmlspecialchars($seance['annee']) ?></td>
                            <?php elseif ($periode == 'annee'): ?>
                                <td><?= htmlspecialchars($periodName) ?></td>
                            <?php elseif ($periode == 'semaine'): ?>
                                <td><?= htmlspecialchars($periodName) ?></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($seance['marque'] . ' ' . $seance['model']) ?></td>
                            <td><?= number_format($seance['prix_arme'] ?? 0, 2) ?> €</td>
                            <td><?= htmlspecialchars($seance['cartouches_reglementaire']) ?></td>
                            <td><?= htmlspecialchars($seance['cartouches_achetees']) ?></td>
                            <td><?= number_format($seance['prix_total_cartouches_achetees'] ?? 0, 2) ?> €</td>
                            <td><?= number_format($seance['prix_reparation'] ?? 0, 2) ?> €</td>
                            <td><?= htmlspecialchars($totalCartouchesTirees) ?></td>
                            <td><?= number_format($totalDesPrix, 2) ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (isset($seances) && $categorie == 'invites'): ?>
        <h3>Résultats pour les Invités</h3>
        <div id="stat-table" class="table-responsive">
            <table class="table table-bordered" id="statistiquesTable">
                <thead>
                    <tr>
                        <?php if ($periode == 'mois'): ?>
                            <th>Mois</th>
                            <th>Année</th>
                        <?php elseif ($periode == 'annee'): ?>
                            <th>Année</th>
                        <?php elseif ($periode == 'semaine'): ?>
                            <th>Date</th>
                        <?php endif; ?>
                        <th>Stand de Tir</th>
                        <th>Nombre d'Invités</th>
                        <th>Nombre de Séances</th>
                        <th>Prix Total par Invité (€)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalInvites = 0;
                    $totalSeances = 0;
                    $totalPrixInvite = 0;

                    foreach ($seances as $seance):
                        $periodName = ($periode == 'mois') ? getMonthName($seance['mois']) : ($periode == 'annee' ? $seance['annee'] : $seance['date_seance']);
                        $totalInvites += $seance['nombre_invites'];
                        $totalSeances += $seance['nombre_seances'];
                        $totalPrixInvite += $seance['prix_total_invite'];
                    ?>
                        <tr>
                            <?php if ($periode == 'mois'): ?>
                                <td><?= htmlspecialchars($periodName) ?></td>
                                <td><?= htmlspecialchars($seance['annee']) ?></td>
                            <?php elseif ($periode == 'annee'): ?>
                                <td><?= htmlspecialchars($periodName) ?></td>
                            <?php elseif ($periode == 'semaine'): ?>
                                <td><?= htmlspecialchars($periodName) ?></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($seance['nom_stand']) ?></td>
                            <td><?= htmlspecialchars($seance['nombre_invites']) ?></td>
                            <td><?= htmlspecialchars($seance['nombre_seances']) ?></td>
                            <td><?= number_format($seance['prix_total_invite'] ?? 0, 2) ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                    <!-- Ligne de total -->
                    <tr>
                        <td colspan="<?php if ($periode == 'mois' || $periode == 'annee') { echo '3'; } else { echo '2'; } ?>"><strong>Total</strong></td>
                        <td><strong><?= htmlspecialchars($totalInvites) ?></strong></td>
                        <td><strong><?= htmlspecialchars($totalSeances) ?></strong></td>
                        <td><strong><?= number_format($totalPrixInvite ?? 0, 2) ?> €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
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
    toggleDateFields(); // appel initial pour définir la visibilité correcte au chargement de la page

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

document.getElementById('generate-pdf').addEventListener('click', function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('landscape');

    // Add title
    doc.text("Statistiques", 14, 10);

    // Get data from table
    const table = document.getElementById('statistiquesTable');
    const headers = [];
    const data = [];
    const excludedColumns = []; // Add indexes of columns you want to exclude

    // Get headers
    table.querySelectorAll('thead th').forEach((th, index) => {
        if (!excludedColumns.includes(index)) {
            headers.push(th.innerText);
        }
    });

    // Get rows
    table.querySelectorAll('tbody tr').forEach(row => {
        const rowData = [];
        row.querySelectorAll('td').forEach((td, index) => {
            if (!excludedColumns.includes(index)) {
                rowData.push(td.innerText);
            }
        });
        data.push(rowData);
    });

    // Add table to PDF
    doc.autoTable({
        head: [headers],
        body: data,
        startY: 20,
        styles: { fontSize: 8 }
    });

    // Open in new tab
    window.open(doc.output('bloburl'), '_blank');
});
</script>

<?php include 'footer.php'; ?>
