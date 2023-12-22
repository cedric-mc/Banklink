<?php
session_start();
$_SESSION['export_data'] = array();
if (empty($_SESSION['idUser']) || ($_SESSION['type'] != "client" && $_SESSION['type'] != "product-owner")) {
    header('Location: ../');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $table = $_POST['table'];
    $resultat = $_POST['lignes'];
    $nbLignes = $_POST['nbLignes'];
    $format = $_POST['format'];
} else {
    header("Location: ../");
    exit();
}

echo $_POST['table'];
echo $_POST['format'];
if (!empty($_POST['numRemise'])) {
    $numRemise = $_POST['numRemise'];
}

include("../includes/functions.php");
// if une information dans $lignes est de longueur 16, c'est un numéro de carte bancaire et on le masque
foreach ($lignes as $key => $value) {
    foreach ($value as $key2 => $value2) {
        if (strlen($value2) == 16) {
            $lignes[$key][$key2] = masquerNumeroCarte($value2);
        }
    }
}
if ($table == 'treso') {
    $title .= "LISTE DES TRÉSORERIES";
    $nomsColonnes = array("N° SIREN", "Raison sociale", "Nombre de Transactions", "Devise", "Montant total");
} elseif ($table == 'remise') {
    $title .= "LISTE DES REMISES";
} elseif ($table == 'impaye') {
    $title .= "LISTE DES IMPAYÉS";
    $nomsColonnes = array("N° SIREN", "Raison sociale", "Date de vente", "Date de remise", "N° Carte", "Réseau", "N° Dossier Impayé", "Devise", "Montant", "Libellé Impayé");
} elseif ($table == 'sub_remise') {
    $title .= "LISTE DES TRANSACTIONS DE LA REMISE N° {$numRemise}";
    $nomsColonnes = array("N° SIREN", "Raison sociale", "N° Remise", "Date de remise", "Nbre de transactions", "Devise", "Montant total");
} else {
    header("Location: ../");
    exit();
}
if ($_SESSION['type'] == "client") {
    $title .= " DE L'ENTREPRISE {$_SESSION['rs']} - N° SIREN {$_SESSION['siren']}.";
} elseif ($_SESSION['type'] == "product-owner") {
    $title .= " DES ENTREPRISES ENREGISTRÉES SUR BANKLINK.";
}

$export_data = [
    'nbLignes' => $nbLignes,
    'nomsColonnes' => $nomsColonnes,
    'lignes' => $lignes,
    'title' => $title
];
header("Location: ".$format.".php?export_data=$export_data");
exit();
?>