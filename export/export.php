<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $table = $_POST['table'];
    $nbLignes = $_POST['nbLignes'];
    $format = $_POST['format'];

    session_start();
    $_SESSION['export_data'] = array();
    if (empty($_SESSION['idUser']) || ($_SESSION['type'] != "client" && $_SESSION['type'] != "product-owner")) {
        header('Location: ../');
        exit();
    }

    if (!empty($_POST['numRemise'])) {
        $numRemise = $_POST['numRemise'];
    }

    include("../includes/functions.php");

    $title = "";
    if ($table == 'treso') {
        $title .= "LISTE DES TRÉSORERIES";
        $nomsColonnes = array("N° SIREN", "Raison Sociale", "Nombre de Transactions", "Devise", "Montant total");
    } elseif ($table == 'remise') {
        $title .= "LISTE DES REMISES";
    } elseif ($table == 'impaye') {
        $title .= "LISTE DES IMPAYÉS";
        $nomsColonnes = array("N° SIREN", "Raison Sociale", "Date de vente", "Date de remise", "N° Carte", "Réseau", "N° Dossier Impayé", "Devise", "Montant", "Libellé Impayé");
    } elseif ($table == 'sub_remise') {
        $title .= "LISTE DES TRANSACTIONS DE LA REMISE N° $numRemise";
        $nomsColonnes = array("N° SIREN", "Raison Sociale", "N° Remise", "Date de remise", "Nbre de transactions", "Devise", "Montant total");
    } else {
        header("Location: ../");
        exit();
    }

    if ($_SESSION['type'] == "client") {
        $title .= " DE L'ENTREPRISE \n$_SESSION[raisonSociale] - N° SIREN $_SESSION[siren].";
    } elseif ($_SESSION['type'] == "product-owner") {
        $title .= " DES ENTREPRISES ENREGISTRÉES SUR BANKLINK.";
    }

    $_SESSION['export_data'] = [
        'nbLignes' => $nbLignes,
        'nomsColonnes' => $nomsColonnes,
        'lignes' => $_POST['lignes'],
        'title' => $title,
        'fichier' => $_POST['fichier']
    ];
    header("Location: $format.php");
    exit();
} else {
    header("Location: ../");
    exit();
}
?>