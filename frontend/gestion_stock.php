<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

$type_stock = isset($_POST['type_stock']) ? $_POST['type_stock'] : '';

if ($type_stock == 'reglementaire' || $type_stock == 'achete') {
    $stmt = $conn->prepare("
        SELECT 
            articles.*, 
            fournisseurs.nom AS fournisseur_nom,
            COALESCE(SUM(commandes.quantite), 0) AS total_commande,
            COALESCE(SUM(seance_tir.nombre_munitions_tirees), 0) AS total_tire,
            (COALESCE(SUM(commandes.quantite), 0) - COALESCE(SUM(seance_tir.nombre_munitions_tirees), 0)) AS stock
        FROM articles 
        LEFT JOIN fournisseurs ON articles.fournisseur = fournisseurs.id 
        LEFT JOIN commandes ON articles.id = commandes.article_id 
        LEFT JOIN seance_tir ON articles.id = seance_tir.arme 
        WHERE articles.type = 'munition' AND articles.stock = :type_stock
        GROUP BY articles.id
    ");
    $stmt->bindParam(':type_stock', $type_stock);
} else {
    $stmt = $conn->prepare("
        SELECT 
            articles.*, 
            fournisseurs.nom AS fournisseur_nom,
            COALESCE(SUM(commandes.quantite), 0) AS total_commande,
            COALESCE(SUM(seance_tir.nombre_munitions_tirees), 0) AS total_tire,
            (COALESCE(SUM(commandes.quantite), 0) - COALESCE(SUM(seance_tir.nombre_munitions_tirees), 0)) AS stock
        FROM articles 
        LEFT JOIN fournisseurs ON articles.fournisseur = fournisseurs.id 
        LEFT JOIN commandes ON articles.id = commandes.article_id 
        LEFT JOIN seance_tir ON articles.id = seance_tir.arme 
        WHERE articles.type = 'munition'
        GROUP BY articles.id
    ");
}

$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'header.php'; ?>

<h2>Gestion du Stock</h2>

<form method="post" action="gestion_stock.php">
    <div class="form-group">
        <label for="type_stock">Sélectionner le type de stock:</label>
        <select id="type_stock" name="type_stock" class="form-control" required>
            <option value="reglementaire" <?= $type_stock == 'reglementaire' ? 'selected' : '' ?>>Réglementaire</option>
            <option value="achete" <?= $type_stock == 'achete' ? 'selected' : '' ?>>Acheté</option>
            <option value="" <?= $type_stock == '' ? 'selected' : '' ?>>Tous</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Filtrer</button>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Type</th>
            <th>Marque</th>
            <th>Modèle</th>
            <th>Prix Unité</th>
            <th>Total Commandé</th>
            <th>Total Tiré</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $article): ?>
        <tr>
            <td><?= htmlspecialchars($article['type']) ?></td>
            <td><?= htmlspecialchars($article['marque']) ?></td>
            <td><?= htmlspecialchars($article['model']) ?></td>
            <td><?= htmlspecialchars($article['prix_unite'] ?? '') ?> €</td>
            <td><?= htmlspecialchars($article['total_commande']) ?></td>
            <td><?= htmlspecialchars($article['total_tire']) ?></td>
            <td><?= htmlspecialchars($article['stock']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>
