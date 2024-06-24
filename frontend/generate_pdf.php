<?php
require_once '../backend/session.php';
require_once '../backend/config.php';
require_once '../backend/fpdf/fpdf.php';

is_logged_in();
check_inactivity();

if (!isset($_GET['id'])) {
    die("ID de l'arme non spécifié.");
}

$id = $_GET['id'];

// Récupérer les informations de l'arme
$stmt = $conn->prepare("SELECT armes.*, fournisseurs.nom AS fournisseur_nom FROM armes LEFT JOIN fournisseurs ON armes.fournisseur = fournisseurs.id WHERE armes.id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$arme = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$arme) {
    die("Arme non trouvée.");
}

// Récupérer le total des cartouches tirées
$stmt = $conn->prepare("SELECT SUM(nombre_munitions_tirees) AS total_cartouches FROM seance_tir WHERE arme = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$total_cartouches = $stmt->fetch(PDO::FETCH_ASSOC)['total_cartouches'] ?? 0;

// Récupérer le total des réparations
$total_reparations = $arme['prix_reparation'];

// Générer le PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->AddFont('DejaVuSans', '', 'DejaVuSans.php');
$pdf->AddFont('DejaVuSans', 'B', 'DejaVuSans-Bold.php');
$pdf->AddFont('DejaVuSans', 'I', 'DejaVuSans-Oblique.php');
$pdf->AddFont('DejaVuSans', 'BI', 'DejaVuSans-BoldOblique.php');

// Titre
$pdf->SetFont('DejaVuSans', 'B', 32);
$pdf->Cell(0, 15, utf8_decode('Fiche de Vente'), 1, 1, 'C');
$pdf->Ln(10);

// Section d'informations sur l'arme
$pdf->SetFont('DejaVuSans', '', 12);
$pdf->Cell(0, 4, utf8_decode('Informations sur l\'arme:'), 0, 1, 'L');
$pdf->Ln(2);

// Cadre pour les informations sur l'arme
$pdf->Cell(0, 80, '', 1, 1); // Cadre extérieur

$pdf->SetY(40);
$pdf->SetX(10);
$pdf->Cell(60, 10, utf8_decode('Marque: '), 0, 0);
$pdf->Cell(60, 10, utf8_decode($arme['marque']), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, utf8_decode('Modèle: '), 0, 0);
$pdf->Cell(60, 10, utf8_decode($arme['model']), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, utf8_decode('Prix d\'achat: '), 0, 0);
$pdf->Cell(60, 10, utf8_decode($arme['prix'] . ' Euros'), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, utf8_decode('Calibre: '), 0, 0);
$pdf->Cell(60, 10, utf8_decode($arme['calibre']), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, utf8_decode('Fournisseur: '), 0, 0);
$pdf->Cell(60, 10, utf8_decode($arme['fournisseur_nom']), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, utf8_decode('État à l\'achat: '), 0, 0);
$pdf->Cell(60, 10, utf8_decode($arme['etat_achat']), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, utf8_decode('Date d\'achat: '), 0, 0);
$pdf->Cell(60, 10, utf8_decode(date('d/m/Y', strtotime($arme['date_achat']))), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, utf8_decode('Numéro de série: '), 0, 0);
$pdf->Cell(60, 10, utf8_decode($arme['num_serie']), 0, 1);

// Cadre pour les détails d'utilisation et réparations
$pdf->Cell(0, 60, '', 1, 1); // Cadre extérieur

$pdf->SetY(125);
$pdf->SetX(10);
$pdf->Cell(60, 10, utf8_decode('Total des cartouches tirées: '), 0, 0);
$pdf->Cell(60, 10, utf8_decode($total_cartouches), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, utf8_decode('Total des réparations: '), 0, 0);
$pdf->Cell(60, 10, utf8_decode($total_reparations . ' Euros'), 0, 1);

if (!empty($arme['date_revente'])) {
    $pdf->SetX(10);
    $pdf->Cell(60, 10, utf8_decode('Date de revente: '), 0, 0);
    $pdf->Cell(60, 10, utf8_decode(date('d/m/Y', strtotime($arme['date_revente']))), 0, 1);
}

if (!empty($arme['prix_revente'])) {
    $pdf->SetFont('DejaVuSans', 'B', 16);
    $pdf->SetX(45);
    $pdf->Cell(60, 15, utf8_decode('Prix de revente: '), 1, 0);
    $pdf->Cell(60, 15, utf8_decode($arme['prix_revente'] . ' Euros'), 1, 1);
}

// Section de signatures
$pdf->Ln(15);
$pdf->Cell(0, 10, utf8_decode(''), 0, 1, 'L');
$pdf->Ln(2);

// Cadre pour les signatures
$pdf->Cell(0, 60, '', 1, 1); // Cadre extérieur

$pdf->SetY(200);
$pdf->SetX(15);
$pdf->Cell(90, 40, '', 1, 0); // Cadre pour signature vendeur
$pdf->Cell(90, 40, '', 1, 1); // Cadre pour signature acheteur

$pdf->SetY(200);
$pdf->SetX(15);
$pdf->Cell(90, 10, utf8_decode('Signature vendeur'), 0, 0, 'C');
$pdf->Cell(90, 10, utf8_decode('Signature acheteur'), 0, 1, 'C');

// En-têtes pour afficher le PDF dans un nouvel onglet
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="fiche_vente.pdf"');

$pdf->Output();
?>
