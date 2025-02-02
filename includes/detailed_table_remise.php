<?php
if (!isset($_GET['rowIndex'])) { // Vérifier si le N° de Remise est défini
    header('Location: remise.php');
    exit;
}
$remise = $_GET['rowIndex'];

// Fichiers pour la connexion à la base de données, pour les requêtes SQL et pour les fonctions
include("conf.php");
include("requetes.php");
include("functions.php");

// Exécuter la requête pour obtenir les transactions de la remise d'un client
$req = $requetes["select_client_transactions"]; // :remise est un paramètre
$result = $cnx->prepare($req);
$result->bindParam(':remise', $remise, PDO::PARAM_INT);
$result->execute();
$nbLignes = $result->rowCount();
$lignes = $result->fetchAll(PDO::FETCH_OBJ);

// Fonction pour générer le tableau HTML
function generateTable($lignes) {
    $tableHead = "
    <thead>
        <tr>
            <th>N° SIREN</th>
            <th>Date Vente</th>
            <th>N° Carte</th>
            <th>Réseau</th>
            <th>N° Autorisation</th>
            <th>Devise</th>
            <th>Montant</th>
        </tr>
    </thead>";
    $tableBody = "<tbody>";
    foreach ($lignes as $subligne) {
        $tableBody .= "<tr>";
        $tableBody .= "<td>$subligne->siren</td>";
        $tableBody .= "<td>" . format_date($subligne->dateTransaction) . "</td>";
        $tableBody .= "<td>" . masquerNumeroCarte($subligne->numCarte) . "</td>";
        $tableBody .= "<td>$subligne->reseau</td>";
        $tableBody .= "<td>$subligne->n_autorisation</td>";
        $tableBody .= "<td>$subligne->devise</td>";
        $tableBody .= "<td class='" . checkNumber($subligne->montant) . "'>$subligne->montant</td>";
        $tableBody .= "</tr>";
    }
    $tableBody .= "</tbody>";
    return "<table>" . $tableHead . $tableBody . "</table>";
}

// Structure de la page
echo "<h1>Détails de la Remise n° $remise</h1>";
echo "<div class='export'>";
echo "<form action='../export/export.php' method='post' id='exportForm'>";
echo "<input type='hidden' name='table' value='sub_remise'>";
echo "<input type='hidden' name='lignes' value='" . htmlspecialchars(json_encode($lignes)) . "'/>";
echo "<input type='hidden' name='nbLignes' value='$nbLignes'>";
echo "<input type='hidden' name='fichier' value='detailed_table_remise.php'>";
echo "<input type='hidden' name='numRemise' value='" . $remise . "'>";
echo "<select name='format' id='format'>";
echo "<option value='' disabled selected>Exporter en</option>";
echo "<option value='csv'>CSV</option>";
echo "<option value='xls'>Excel</option>";
echo "<option value='pdf'>PDF</option>";
echo "</select>";
echo "<button type='submit' id='exportButton'>Exporter</button>";
echo "</form>";
echo "</div>";
echo "<span class='total'>$nbLignes résultat(s)</span>";
echo generateTable($lignes);