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

// Remplacement des paramètres de la requête pour l'export
$reqString = str_replace(":remise", $remise, $result->queryString);

// Fonction pour générer le tableau HTML
function generateTable($result) {
    $tableHead = "
    <thead>
        <tr>
            <th>N° SIREN</th>
            <th>Date vente</th>
            <th>N° Carte</th>
            <th>Réseau</th>
            <th>N° autorisation</th>
            <th>Devise</th>
            <th>Montant</th>
        </tr>
    </thead>";
    $tableBody = "<tbody>";
    while ($subligne = $result->fetch(PDO::FETCH_OBJ)) {
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
echo "<input type='hidden' name='requete' value='" . $reqString . "'>";
echo "<input type='hidden' name='numRemise' value='" . $remise . "'>";
echo "<label for='format'></label>";
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
echo generateTable($result);