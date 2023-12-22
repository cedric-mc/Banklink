<?php
session_start();
if (empty($_SESSION['idUser']) || ($_SESSION['type'] != "client" && $_SESSION['type'] != "product-owner")) {
    header('Location: ../');
    exit();
}

if (empty($_SESSION['export_data'])) {
    header('Location: export.php');
    exit();
}

$nbLignes = $_SESSION['export_data']['nbLignes'];
$nomsColonnes = $_SESSION['export_data']['nomsColonnes'];
$lignes = json_decode($_SESSION['export_data']['lignes']);
$title = $_SESSION['export_data']['title'];

// Définir un répertoire temporaire accessible en écriture
$tmpDir = 'tmp/';

// Vérifier si le répertoire existe, sinon le créer
if (!is_dir($tmpDir)) {
    mkdir($tmpDir, 0777, true);
}

require_once("../vendor/autoload.php");
include("../includes/functions.php");

$mpdf = new Mpdf\Mpdf(['tempDir' => $tmpDir, 'orientation' => 'P']);
$stylesheet = file_get_contents('../style/tableau.css'); // Path to your CSS file
$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

$mpdf->SetTitle($title);
$mpdf->WriteHTML('<h1>' . $title . '</h1>');

// Table creation
$html = '<table>';
$html .= '<tr>';
foreach ($nomsColonnes as $nomColonne) {
    $html .= '<th style="padding: 5px;">' . $nomColonne . '</th>';
}
$html .= '</tr>';

foreach ($lignes as $ligne) {
    $html .= '<tr>';
    foreach ($ligne as $valeur) {
        // Si la valeur est une datetime, on la formate
        if (preg_match("/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/", $valeur)) {
            $valeur = format_date($valeur);
        }
        $html .= '<td style="padding: 5px;">' . $valeur . '</td>';
    }
    $html .= '</tr>';
}
$html .= '</table>';
$mpdf->WriteHTML($html);

// Adding extraction date
$extrac_timecode = "EXTRAIT DU " . date("d/m/Y") . " À " . date("H:i");
$mpdf->WriteHTML('<p>' . $extrac_timecode . '</p>');

// Output the PDF as download
$mpdf->Output('export.pdf', 'D');

unset($_SESSION['export_data']);
header('Location: export.php');
?>
