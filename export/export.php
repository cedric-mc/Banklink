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

    include("../includes/functions.php");

    $title = "";
    if ($table == 'treso') {
        $title .= "LISTE DES TRÉSORERIES";
        $nomsColonnes = array("N° SIREN", "Raison Sociale", "Nombre de Transactions", "Devise", "Montant Total");
    } elseif ($table == 'remise') {
        $title .= "LISTE DES REMISES";
        $nomsColonnes = array("N° SIREN", "Raison Sociale", "N° Remise", "Date Traitement", "Nbre Transactions", "Devise", "Montant Total");
    } elseif ($table == 'impaye') {
        $title .= "LISTE DES IMPAYÉS";
        $nomsColonnes = array("N° SIREN", "Raison Sociale", "Date Vente", "Date Remise", "N° Carte", "Réseau", "N° Dossier Impayé", "Devise", "Montant", "Libellé Impayé");
    } elseif ($table == 'sub_remise') {
        $title .= "LISTE DES TRANSACTIONS DE LA REMISE N° $_POST[numRemise]";
        $nomsColonnes = array("N° SIREN", "Date Vente", "N° Carte", "Réseau", "N° Autorisation", "Devise", "Montant");
    } elseif ($table == 'sub_impaye') {
        $title .= "LISTE DES TRANSACTIONS D'IMPAYÉS du client $_POST[siren]";
        $nomsColonnes = array("N° SIREN", "Raison Sociale", "Date Vente", "Date Remise", "N° Carte", "Réseau", "N° Dossier Impayé", "Devise", "Montant", "Libellé Impayé");
    } else {
        header("Location: ../");
        exit();
    }

    if ($_SESSION['type'] == "client") {
        $title .= " DE L'ENTREPRISE $_SESSION[raisonSociale] - N° SIREN $_SESSION[siren].";
    } elseif ($_SESSION['type'] == "product-owner" && $table != 'sub_impaye') {
        $title .= " DES ENTREPRISES ENREGISTRÉES SUR BANKLINK.";
    }

    // Si l'il y a des données de 16 de longueurs, alors on masque les 4 derniers chiffres
    $lignes = (json_decode($_POST['lignes'], true));
    foreach ($lignes as $key => $value) {
        foreach ($value as $key2 => $value2) {
            if (strlen($value2) == 16) {
                $lignes[$key][$key2] = masquerNumeroCarte($value2);
            }
        }
    }

    $_SESSION['export_data'] = [
        'nbLignes' => $nbLignes,
        'nomsColonnes' => $nomsColonnes,
        'lignes' => json_encode($lignes),
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