<?php
session_start();
$_SESSION['export_data'] = array();
if (empty($_SESSION['idUser']) || ($_SESSION['type'] != "client" && $_SESSION['type'] != "product-owner")) {
    header('Location: ../');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $table = $_POST['table'];
    $lignes = $_POST['lignes'];
    $nbLignes = $_POST['nbLignes'];
    $fichier = $_POST['fichier'];
    $format = $_POST['format'];

    echo $_POST['table'];
    echo $_POST['format'];
    if (empty($_POST['numRemise'])) {
        $numRemise = $_POST['numRemise'];
    }

    include("../includes/functions.php");

    $title = "";
    if ($table == 'treso') {
        $title .= "LISTE DES TRÉSORERIES";
        $nomsColonnes = array("N° SIREN", "Raison sociale", "Nombre de Transactions", "Devise", "Montant total");
    } elseif ($table == 'remise') {
        $title .= "LISTE DES REMISES";
    } elseif ($table == 'impaye') {
        $title .= "LISTE DES IMPAYÉS";
        $nomsColonnes = array("N° SIREN", "Raison sociale", "Date de vente", "Date de remise", "N° Carte", "Réseau", "N° Dossier Impayé", "Devise", "Montant", "Libellé Impayé");
    } elseif ($table == 'sub_remise') {
        $title .= "LISTE DES TRANSACTIONS DE LA REMISE N° $numRemise";
        $nomsColonnes = array("N° SIREN", "Raison sociale", "N° Remise", "Date de remise", "Nbre de transactions", "Devise", "Montant total");
    } else {
        header("Location: ../");
        exit();
    }

    if ($_SESSION['type'] == "client") {
        $title .= " DE L'ENTREPRISE $_SESSION[raisonSociale] - N° SIREN $_SESSION[siren].";
    } elseif ($_SESSION['type'] == "product-owner") {
        $title .= " DES ENTREPRISES ENREGISTRÉES SUR BANKLINK.";
    }

    $export_data = [
        'nbLignes' => $nbLignes,
        'nomsColonnes' => $nomsColonnes,
        'lignes' => $lignes,
        'title' => $title
    ];
    header("Location: ".$format.".php?export_data=&export_data");
} else {
    // header("Location: ../");
}
echo $_POST['table'];
exit();
?>