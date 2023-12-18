<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $table = $_POST['table'];
    $requete = $_POST['requete'];
    $format = $_POST['format'];
} else {
    header("Location: ../");
    exit();
}

session_start();
$_SESSION['export_data'] = array();
if (empty($_SESSION['idUser']) || ($_SESSION['type'] != "client" && $_SESSION['type'] != "product-owner")) {
    header('Location: ../');
    exit();
}

echo $_POST['table'];
echo $_POST['format'];
if (!empty($_POST['numRemise'])) {
    $numRemise = $_POST['numRemise'];
}

include("../includes/conf.php");
include("../includes/functions.php");
$resultat = $cnx->prepare($requete);
$resultat->execute();
$nbLignes = $resultat->rowCount(); // Le nombre de lignes
$colonnes = $resultat->columnCount(); // Le nombre de colonnes
$nomsColonnes = array();
for ($i = 0; $i < $colonnes; $i++) {
    $nomsColonnes[] = $resultat->getColumnMeta($i)['name'];
}
$lignes = $resultat->fetchAll(PDO::FETCH_ASSOC);
$resultat->closeCursor();
$resultat = null;
$cnx = null;
// if une information dans $lignes est de longueur 16, c'est un numéro de carte bancaire et on le masque
foreach ($lignes as $key => $value) {
    foreach ($value as $key2 => $value2) {
        if (strlen($value2) == 16) {
            $lignes[$key][$key2] = masquerNumeroCarte($value2);
        }
    }
}
$title = '';
if ($table == 'treso') {
    $title .= "LISTE DES TRÉSORERIES";
} elseif ($table == 'remise') {
    $title .= "LISTE DES REMISES";
} elseif ($table == 'impaye') {
    $title .= "LISTE DES IMPAYÉS";
} elseif ($table == 'sub_remise') {
    $title .= "LISTE DES TRANSACTIONS DE LA REMISE N° {$numRemise}";
}
if ($_SESSION['type'] == "client") {
    $title .= " DE L'ENTREPRISE {$_SESSION['rs']} - N° SIREN {$_SESSION['siren']}.";
} elseif ($_SESSION['type'] == "product-owner") {
    $title .= " DES ENTREPRISES ENREGISTRÉES SUR BANKLINK.";
}

$_SESSION['export_data'] = [
    'nbLignes' => $nbLignes,
    'nomsColonnes' => $nomsColonnes,
    'lignes' => $lignes,
    'title' => $title
];
header("Location: ".$format.".php");
exit();
?>