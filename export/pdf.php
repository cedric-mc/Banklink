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
$lignes = $_SESSION['export_data']['lignes'];
$title = $_SESSION['export_data']['title'];

$tmpDir = '../tmp/mpdf';
if (!is_dir($tmpDir)) { // Créer le répertoire s'il n'existe pas déjà
    mkdir($tmpDir, 777, true);
}

require_once("../vendor/autoload.php");
$mpdf = new \Mpdf\Mpdf(['tempDir' => $tmpDir]);
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
    foreach ($ligne as $value) {
        $html .= '<td style="padding: 5px;">' . $value . '</td>';
    }
    $html .= '</tr>';
}
$html .= '</table>';

$mpdf->WriteHTML($html);

// Adding extraction date
$extrac_timecode = "EXTRAIT DU " . date("d/m/Y") . " À " . date("H:i");
$mpdf->WriteHTML('<p>' . $extrac_timecode . '</p>');

// Output the PDF as download
$mpdf->Output('export.pdf', \Mpdf\Output\Destination::DOWNLOAD);

unset($_SESSION['export_data']);
header('Location: export.php');
?>
