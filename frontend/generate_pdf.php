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
$pdf->Cell(0, 15, mb_convert_encoding('Fiche de Vente', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C');
$pdf->Ln(10);

// Section d'informations sur l'arme
$pdf->SetFont('DejaVuSans', '', 12);
$pdf->Cell(0, 4, mb_convert_encoding('Informations sur l\'arme:', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
$pdf->Ln(2);

// Cadre pour les informations sur l'arme
$pdf->Cell(0, 80, '', 1, 1); // Cadre extérieur

$pdf->SetY(40);
$pdf->SetX(10);
$pdf->Cell(60, 10, mb_convert_encoding('Marque: ', 'ISO-8859-1', 'UTF-8'), 0, 0);
$pdf->Cell(60, 10, mb_convert_encoding($arme['marque'], 'ISO-8859-1', 'UTF-8'), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, mb_convert_encoding('Modèle: ', 'ISO-8859-1', 'UTF-8'), 0, 0);
$pdf->Cell(60, 10, mb_convert_encoding($arme['model'], 'ISO-8859-1', 'UTF-8'), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, mb_convert_encoding('Prix d\'achat: ', 'ISO-8859-1', 'UTF-8'), 0, 0);
$pdf->Cell(60, 10, mb_convert_encoding($arme['prix'] . ' Euros', 'ISO-8859-1', 'UTF-8'), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, mb_convert_encoding('Calibre: ', 'ISO-8859-1', 'UTF-8'), 0, 0);
$pdf->Cell(60, 10, mb_convert_encoding($arme['calibre'], 'ISO-8859-1', 'UTF-8'), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, mb_convert_encoding('Fournisseur: ', 'ISO-8859-1', 'UTF-8'), 0, 0);
$pdf->Cell(60, 10, mb_convert_encoding($arme['fournisseur_nom'], 'ISO-8859-1', 'UTF-8'), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, mb_convert_encoding('État à l\'achat: ', 'ISO-8859-1', 'UTF-8'), 0, 0);
$pdf->Cell(60, 10, mb_convert_encoding($arme['etat_achat'], 'ISO-8859-1', 'UTF-8'), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, mb_convert_encoding('Date d\'achat: ', 'ISO-8859-1', 'UTF-8'), 0, 0);
$pdf->Cell(60, 10, mb_convert_encoding(date('d/m/Y', strtotime($arme['date_achat'])), 'ISO-8859-1', 'UTF-8'), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, mb_convert_encoding('Numéro de série: ', 'ISO-8859-1', 'UTF-8'), 0, 0);
$pdf->Cell(60, 10, mb_convert_encoding($arme['num_serie'], 'ISO-8859-1', 'UTF-8'), 0, 1);

// Cadre pour les détails d'utilisation et réparations
$pdf->Cell(0, 60, '', 1, 1); // Cadre extérieur

$pdf->SetY(125);
$pdf->SetX(10);
$pdf->Cell(60, 10, mb_convert_encoding('Total des cartouches tirées: ', 'ISO-8859-1', 'UTF-8'), 0, 0);
$pdf->Cell(60, 10, mb_convert_encoding($total_cartouches, 'ISO-8859-1', 'UTF-8'), 0, 1);

$pdf->SetX(10);
$pdf->Cell(60, 10, mb_convert_encoding('Total des réparations: ', 'ISO-8859-1', 'UTF-8'), 0, 0);
$pdf->Cell(60, 10, mb_convert_encoding($total_reparations . ' Euros', 'ISO-8859-1', 'UTF-8'), 0, 1);

if (!empty($arme['date_revente'])) {
    $pdf->SetX(10);
    $pdf->Cell(60, 10, mb_convert_encoding('Date de revente: ', 'ISO-8859-1', 'UTF-8'), 0, 0);
    $pdf->Cell(60, 10, mb_convert_encoding(date('d/m/Y', strtotime($arme['date_revente'])), 'ISO-8859-1', 'UTF-8'), 0, 1);
}

if (!empty($arme['prix_revente'])) {
    $pdf->SetFont('DejaVuSans', 'B', 16);
    $pdf->SetX(45);
    $pdf->Cell(60, 15, mb_convert_encoding('Prix de revente: ', 'ISO-8859-1', 'UTF-8'), 1, 0);
    $pdf->Cell(60, 15, mb_convert_encoding($arme['prix_revente'] . ' Euros', 'ISO-8859-1', 'UTF-8'), 1, 1);
}

// Section de signatures
$pdf->Ln(15);
$pdf->Cell(0, 10, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');
$pdf->Ln(2);

// Cadre pour les signatures
$pdf->Cell(0, 60, '', 1, 1); // Cadre extérieur

$pdf->SetY(200);
$pdf->SetX(15);
$pdf->Cell(90, 40, '', 1, 0); // Cadre pour signature vendeur
$pdf->Cell(90, 40, '', 1, 1); // Cadre pour signature acheteur

$pdf->SetY(200);
$pdf->SetX(15);
$pdf->Cell(90, 10, mb_convert_encoding('Signature vendeur', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C');
$pdf->Cell(90, 10, mb_convert_encoding('Signature acheteur', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

// En-têtes pour afficher le PDF dans un nouvel onglet
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="fiche_vente.pdf"');

$pdf->Output();
?>
