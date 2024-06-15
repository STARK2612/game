<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

// Calcul du total des cartouches achetées pour une séance de tir
$stmt = $conn->prepare("SELECT SUM(nombre_munitions_tirees) AS total_cartouches_achetees FROM seance_tir WHERE stock = 'achete'");
$stmt->execute();
$total_cartouches_achetees = $stmt->fetch(PDO::FETCH_ASSOC)['total_cartouches_achetees'];

// Calcul du stock total des cartouches et de la valeur totale du stock des cartouches
$stmt = $conn->prepare("
    SELECT 
        SUM(IFNULL(achats.quantite * articles.cartouches_par_boite, 0)) - COALESCE(SUM(seance_tir.nombre_munitions_tirees), 0) AS stock_total_cartouches,
        SUM(IFNULL(articles.prix_unite * achats.quantite, 0)) AS valeur_totale_munitions
    FROM articles
    LEFT JOIN achats ON articles.id = achats.article_id
    LEFT JOIN seance_tir ON articles.id = seance_tir.arme
    WHERE articles.type = 'munition'
");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$stock_total_cartouches = $result['stock_total_cartouches'];
$valeur_totale_munitions = $result['valeur_totale_munitions'];

// Calcul du total des armes
$stmt = $conn->prepare("SELECT COUNT(*) AS total_armes FROM armes");
$stmt->execute();
$total_armes = $stmt->fetch(PDO::FETCH_ASSOC)['total_armes'];
?>

<?php include 'header.php'; ?>

<style>
.rectangle {
    height: 150px;
    margin: 10px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #fff;
    border-radius: 5px;
    text-align: center;
}
.rectangle1 { background-color: #e74c3c; }
.rectangle2 { background-color: #3498db; }
.rectangle3 { background-color: #2ecc71; }
.rectangle4 { background-color: #f39c12; }
.rectangle5 { background-color: #9b59b6; }
.rectangle6 { background-color: #e67e22; }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="rectangle rectangle1">
                Valeur Totale du Stock de cartouches: <?= number_format($valeur_totale_munitions, 2) ?> €
            </div>
        </div>
        <div class="col-md-4">
            <div class="rectangle rectangle2">
                Stock total des cartouches: <?= htmlspecialchars($stock_total_cartouches) ?> cartouche(s)
            </div>
        </div>
        <div class="col-md-4">
            <div class="rectangle rectangle3">
                Stock total d'armes: <?= htmlspecialchars($total_armes) ?> arme(s)
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="rectangle rectangle4">
                Rectangle 4
            </div>
        </div>
        <div class="col-md-4">
            <div class="rectangle rectangle5">
                Total des cartouches achetées pour une séance de tir: <?= htmlspecialchars($total_cartouches_achetees) ?> cartouche(s)
            </div>
        </div>
        <div class="col-md-4">
            <div class="rectangle rectangle6">
                Rectangle 6
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
