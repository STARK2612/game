<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

// Récupérer le stock en fonction du type (acheté ou réglementaire)
$type_stock = isset($_GET['type']) ? $_GET['type'] : 'achete';
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$selected_month = isset($_GET['month']) ? $_GET['month'] : 'all';  // Définir par défaut à 'all' pour tous les mois

// Récupérer les données du stock
$stmt = $conn->prepare("
    SELECT 
        articles.type, 
        articles.marque, 
        articles.model, 
        articles.prix, 
        articles.quantite, 
        articles.date_achat, 
        fournisseurs.nom AS fournisseur
    FROM articles 
    JOIN fournisseurs ON articles.fournisseur = fournisseurs.id
    WHERE articles.type = :type
");
$stmt->bindParam(':type', $type_stock);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculer le total en stock des articles
$total_stock_articles = 0;
$total_value_articles = 0;
foreach ($articles as $article) {
    $total_stock_articles += $article['quantite'];
    $total_value_articles += $article['prix'] * $article['quantite'];
}

// Récupérer les années disponibles pour les statistiques
$stmt = $conn->prepare("SELECT DISTINCT EXTRACT(YEAR FROM date_seance) AS year FROM seance_tir ORDER BY year");
$stmt->execute();
$years = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Calculer le total de munitions utilisées par mois et par an
if ($selected_month == 'all') {
    $stmt = $conn->prepare("
        SELECT 
            EXTRACT(YEAR FROM date_seance) AS year, 
            EXTRACT(MONTH FROM date_seance) AS month, 
            SUM(nombre_munitions_tirees) AS total_munitions_tirees,
            SUM(nombre_munitions) AS total_munitions
        FROM seance_tir
        WHERE stock = :type AND EXTRACT(YEAR FROM date_seance) = :year
        GROUP BY year, month
        ORDER BY year, month
    ");
    $stmt->bindParam(':type', $type_stock);
    $stmt->bindParam(':year', $selected_year);
} else {
    $stmt = $conn->prepare("
        SELECT 
            EXTRACT(YEAR FROM date_seance) AS year, 
            EXTRACT(MONTH FROM date_seance) AS month, 
            SUM(nombre_munitions_tirees) AS total_munitions_tirees,
            SUM(nombre_munitions) AS total_munitions
        FROM seance_tir
        WHERE stock = :type AND EXTRACT(YEAR FROM date_seance) = :year AND EXTRACT(MONTH FROM date_seance) = :month
        GROUP BY year, month
        ORDER BY year, month
    ");
    $stmt->bindParam(':type', $type_stock);
    $stmt->bindParam(':year', $selected_year);
    $stmt->bindParam(':month', $selected_month);
}
$stmt->execute();
$usage_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculer le total en stock y compris les séances de tir
$stmt = $conn->prepare("
    SELECT 
        SUM(nombre_munitions_tirees) AS total_munitions_tirees
    FROM seance_tir
    WHERE stock = :type
");
$stmt->bindParam(':type', $type_stock);
$stmt->execute();
$seances_totals = $stmt->fetch(PDO::FETCH_ASSOC);

$total_stock = 0;
if ($type_stock == 'reglementaire') {
    $stmt = $conn->prepare("
        SELECT 
            SUM(quantite) AS total_quantite
        FROM articles
        WHERE type = 'reglementaire'
    ");
    $stmt->execute();
    $articles_totals = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_stock = $articles_totals['total_quantite'];
} else {
    $total_stock = $total_stock_articles - $seances_totals['total_munitions_tirees'];
}

// Calculer la valeur totale du stock y compris les séances de tir
$average_price_per_munition = $total_stock_articles > 0 ? $total_value_articles / $total_stock_articles : 0;
$total_value = $average_price_per_munition * $total_stock;
?>

<?php include 'header.php'; ?>

<h2>Gestion du Stock</h2>

<div class="form-group">
    <label for="type-stock">Sélectionner le type de stock:</label>
    <select id="type-stock" class="form-control" onchange="updateStockType(this.value);">
        <option value="achete" <?= $type_stock == 'achete' ? 'selected' : '' ?>>Acheté</option>
        <option value="reglementaire" <?= $type_stock == 'reglementaire' ? 'selected' : '' ?>>Réglementaire</option>
    </select>
</div>

<h3>Articles en Stock (<?= $type_stock == 'achete' ? 'Acheté' : 'Réglementaire' ?>)</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Type</th>
            <th>Marque</th>
            <th>Modèle</th>
            <th>Prix</th>
            <th>Quantité</th>
            <th>Date d'Achat</th>
            <th>Fournisseur</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $article): ?>
        <tr>
            <td><?= htmlspecialchars($article['type']) ?></td>
            <td><?= htmlspecialchars($article['marque']) ?></td>
            <td><?= htmlspecialchars($article['model']) ?></td>
            <td><?= htmlspecialchars($article['prix']) ?> €</td>
            <td><?= htmlspecialchars($article['quantite']) ?></td>
            <td><?= htmlspecialchars($article['date_achat']) ?></td>
            <td><?= htmlspecialchars($article['fournisseur']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h4>Total en Stock: <?= $total_stock ?> unités</h4>
<h4>Valeur Totale du Stock: <?= $total_value ?> €</h4>

<h3>Statistiques d'Utilisation des Munitions</h3>

<div class="form-group">
    <label for="select-year">Sélectionner l'année:</label>
    <select id="select-year" class="form-control" onchange="updateStats();">
        <?php foreach ($years as $year): ?>
            <option value="<?= $year ?>" <?= $year == $selected_year ? 'selected' : '' ?>><?= $year ?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="form-group">
    <label for="select-month">Sélectionner le mois:</label>
    <select id="select-month" class="form-control" onchange="updateStats();">
        <option value="all" <?= $selected_month == 'all' ? 'selected' : '' ?>>Tous les mois</option>
        <?php 
        $months = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril', 5 => 'Mai', 6 => 'Juin',
            7 => 'Juillet', 8 => 'Août', 9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        foreach ($months as $num => $name): ?>
            <option value="<?= $num ?>" <?= $num == $selected_month ? 'selected' : '' ?>><?= $name ?></option>
        <?php endforeach; ?>
    </select>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Année</th>
            <th>Mois</th>
            <th>Total Munitions Tirées</th>
            <th>Total Munitions Utilisées</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usage_stats as $stats): ?>
        <tr>
            <td><?= htmlspecialchars($stats['year']) ?></td>
            <td><?= htmlspecialchars($months[$stats['month']]) ?></td>
            <td><?= htmlspecialchars($stats['total_munitions_tirees']) ?></td>
            <td><?= htmlspecialchars($stats['total_munitions']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
function updateStockType(type) {
    const year = document.getElementById('select-year').value;
    const month = document.getElementById('select-month').value;
    window.location.href = `gestion_stock.php?type=${type}&year=${year}&month=${month}`;
}

function updateStats() {
    const type = document.getElementById('type-stock').value;
    const year = document.getElementById('select-year').value;
    const month = document.getElementById('select-month').value;
    window.location.href = `gestion_stock.php?type=${type}&year=${year}&month=${month}`;
}
</script>

<?php include 'footer.php'; ?>
