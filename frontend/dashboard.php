<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
is_logged_in();
check_inactivity();

// Récupérer la valeur totale du stock des munitions, le stock total des cartouches et le total des armes depuis la session
$valeur_totale_munitions = $_SESSION['valeur_totale_munitions'] ?? 0;
$stock_total_cartouches = $_SESSION['stock_total_cartouches'] ?? 0;
$total_armes = $_SESSION['total_armes'] ?? 0;

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
                Rectangle 5
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
