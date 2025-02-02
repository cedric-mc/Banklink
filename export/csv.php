<?php
session_start();
if (empty($_SESSION['idUser']) || ($_SESSION['type'] != "client" && $_SESSION['type'] != "product-owner")) {
    header('Location: ../');
    exit();
}

$nbLignes = $_SESSION['export_data']['nbLignes'];
$nomsColonnes = $_SESSION['export_data']['nomsColonnes'];
$lignes = $_SESSION['export_data']['lignes'];
$title = $_SESSION['export_data']['title'];

// Générer le fichier CSV
$csvContent = [];
$csvContent[] = [$title];
$csvContent[] = $nomsColonnes;
foreach ($lignes as $ligne) {
    $csvContent[] = $ligne;
}

// Préparer le téléchargement du fichier CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=export.csv');

// Écrire le contenu CSV dans la sortie
$output = fopen('php://output', 'w');
foreach ($csvContent as $line) {
    fputcsv($output, $line);
}

fclose($output);
exit();
?>
