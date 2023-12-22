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

require_once("../vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Création d'une nouvelle instance de Spreadsheet (classe principale de PhpSpreadsheet)
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Ajout du titre du fichier Excel
$title = "EXTRAIT DU " . date("d/m/Y") . " À " . date("H:i") . " - " . $title;

$sheet->setCellValue('A1', $title);

// Ajout des données dans le fichier Excel
$row = 3; // Ligne à partir de laquelle commencer à écrire les données

// Entête des colonnes
$col = 'A';
foreach ($nomsColonnes as $nomColonne) {
    $sheet->setCellValue($col . $row, $nomColonne);
    $col++;
}
$row++;

// Ajout des lignes de données
if (is_array($lignes)) {
    $lignes = array_map(function ($ligne) {
        return (array) $ligne;
    }, $lignes);
}
// foreach ($lignes as $ligne) {
//     $col = 'A';
//     foreach ($ligne as $key => $value) {
//         if (is_null($value)) {
//             $value = "Pas de données";
//         }
//         $sheet->setCellValue($col . $row, $value);
//         $col++;
//     }
//     $row++;
// }

$filename = "export.xlsx";
$writer = new Xlsx($spreadsheet);
$writer->save($filename);

// Envoi du fichier Excel au navigateur pour téléchargement
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Content-Length: ' . filesize($filename)); // Ajoutez cette ligne pour la taille du contenu

readfile($filename); // Lecture et envoi du fichier

// Suppression du fichier Excel du serveur
unlink($filename);

// Fin de votre code pour la création du fichier Excel..., je voudrais aller au fichier export.php
header('Location: export.php');
exit();
