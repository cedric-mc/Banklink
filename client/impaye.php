<?php
session_start();
if (empty($_SESSION['idUser']) || $_SESSION['type'] != "client") {
    header('Location: ../');
    exit();
}
include("../includes/conf.php");
include("../includes/requetes.php");
include("../includes/functions.php");

// Récupération des impayés du client pour le graphique
$reqMotifs =
    "SELECT SUM(montant) AS sommeMontantNegatif, 
        libelle 
FROM REMISE r,
     MOTIFS_IMPAYES m
WHERE r.code = m.code 
    AND montant < 0 
    AND siren = :siren 
GROUP BY libelle;";
$resultMotif = $cnx->prepare($reqMotifs);
$resultMotif->bindParam(':siren', $_SESSION['siren']);
$resultMotif->execute();

$rows = $resultMotif->fetchAll(PDO::FETCH_ASSOC);
$dataMotifs = array();
foreach ($rows as $row) {
    $dataMotifs[] = $row;
}
$dataMotifs = json_encode($dataMotifs);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BankLink - <?php echo $_SESSION['login'] ?></title>
        <link rel="shortcut icon" href="../img/favicon.png"><!-- Favicon -->
        <link rel="stylesheet" href="../style/style.css">
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/series-label.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    </head>

    <body>
        <!-- Navigation -->
        <nav>
            <input id="nav-toggle" type="checkbox">
            <div class="logo">BankLink</div>
            <ul class="links">
                <li><a href="./">Trésorerie</a></li>
                <li><a href="remise.php">Remise</a></li>
                <li><a href="impaye.php">Impayé</a></li>
                <li><a href="profil.php">Mon Profil</a></li>
            </ul>
            <label for="nav-toggle" class="icon-burger">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </label>
        </nav>
        <!-- Navigation -->
        <!-- Formulaire de recherche -->
        <div class="recherche">
            <h3>Recherche des Impayés</h3>
            <form action="" method="post">
                <div class="inputs">
                    <label for="debut">Date de début :</label>
                    <input type="date" id="debut" name="debut">
                </div>
                <div class="inputs">
                    <label for="fin">Date de fin :</label>
                    <input type="date" id="fin" name="fin">
                </div>
                <div class="inputs">
                    <label for="dossier_impaye">N° dossier impayé :</label>
                    <select name="dossier_impaye" id="dossier_impaye">
                        <option value="" disabled selected>Tous</option>
                        <?php
                        $requete = "SELECT numDossierImpaye FROM REMISE WHERE siren = :siren AND numDossierImpaye IS NOT NULL";
                        $resultat = $cnx->prepare($requete);
                        $resultat->bindParam(':siren', $_SESSION['siren']);
                        $resultat->execute();
                        $dossiers = $resultat->fetchAll();
                        foreach ($dossiers as $dossier) {
                            echo "<option value='" . $dossier[0] . "'>" . $dossier[0] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit">Valider</button>
            </form>
            <?php
            if (!empty($_POST['dossier_impaye'])) {
                $requete = $requetes["select_client_impaye_dossier"];
                $resultat = $cnx->prepare($requete);
                $resultat->bindParam(':siren', $_SESSION['siren']);
                $resultat->bindParam(':numDossierImpaye', $_POST['dossier_impaye']);
            } elseif (!empty($_POST['debut']) || !empty($_POST['fin'])) {
                $d_debut = (!empty($_POST['debut'])) ? $_POST['debut'] : $date;
                $d_fin = (!empty($_POST['fin'])) ? $_POST['fin'] : $d_fin;
                $requete = $requetes["select_client_impaye_date"];
                $resultat = $cnx->prepare($requete);
                $resultat->bindParam(':siren', $_SESSION['siren']);
                $resultat->bindParam(':debut', $d_debut);
                $resultat->bindParam(':fin', $d_fin);
            } else {
                $requete = $requetes["select_client_impaye"];
                $resultat = $cnx->prepare($requete);
                $resultat->bindParam(':siren', $_SESSION['siren']);
            }
            $resultat->execute();
            $nbLignes = $resultat->rowCount();
            $requeteString = $resultat->queryString;
            $requeteString = str_replace(":siren", $_SESSION['siren'], $requeteString);
            if (strpos($requeteString, ":numDossierImpaye") !== false) {
                $requeteString = str_replace(":numDossierImpaye", $_POST['dossier_impaye'], $requeteString);
            }
            if (strpos($requeteString, ":debut") !== false) {
                $requeteString = str_replace(":debut", $d_debut, $requeteString);
                $requeteString = str_replace(":fin", $d_fin, $requeteString);
            }
            ?>
        </div>
        <!-- Formulaire de recherche -->
        <!-- Tableau -->
        <div class="resultat">
            <h2 class="title-resultat">Impayés</h2>
            <!-- Formulaire d'exportation -->
            <div class="export">
                <form action="../export/export.php" method="post" id="exportForm">
                    <input type="hidden" name="table" value="treso">
                    <input type="hidden" name="requete" value="<?php echo $requeteString; ?>">
                    <label for="format"></label>
                    <select name="format" id="format">
                        <option value="" disabled selected>Exporter en</option>
                        <option value="csv">CSV</option>
                        <option value="xls">Excel</option>
                        <option value="pdf">PDF</option>
                    </select>
                </form>
            </div>
            <!-- Formulaire d'exportation -->
            <span class="total">
                <?php echo $nbLignes; ?> résultat(s) (Affichant <span class="visible-rows" id="visibleRows"></span> ligne(s))
            </span>
            <table>
                <thead>
                    <tr>
                        <th>N° SIREN</th>
                        <th>Raison Sociale</th>
                        <th>Date vente</th>
                        <th>Date remise</th>
                        <th>N° Carte</th>
                        <th>Réseau</th>
                        <th>N° Dossier Impayé</th>
                        <th>Devise</th>
                        <th>Montant</th>
                        <th>Libellé impayé</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($ligne = $resultat->fetch(PDO::FETCH_OBJ)) {
                        echo "<tr>";
                        echo "<td>$ligne->siren</td>";
                        echo "<td>$ligne->raisonSociale</td>";
                        echo "<td>" . date_format(date_create($ligne->dateTransaction), 'd/m/Y') . "</td>";
                        echo "<td>" . date_format(date_create($ligne->dateRemise), 'd/m/Y') . "</td>";
                        echo "<td>" . masquerNumeroCarte($ligne->numCarte) . "</td>";
                        echo "<td>$ligne->reseau</td>";
                        echo "<td>$ligne->numDossierImpaye</td>";
                        echo "<td>$ligne->devise</td>";

                        // Récupérer la couleur pour le montant actuel
                        $color = getColorByAmountRange($ligne->montant);

                        // Appliquer la couleur à la cellule correspondante
                        echo "<td style='color: $color;'>$ligne->montant</td>";

                        if (empty($ligne->libelle)) {
                            echo "<td class='form-details'>raison non communiquée, contactez la banque du client</td>";
                        } else {
                            echo "<td>$ligne->libelle</td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <!-- Paramètres de Pagination du tableau -->
            <div class="on-pagination">
                <div class="pagination">
                    <button id="previous" class="element left-arrow">
                        <span class="left-arrow-icon arrow" aria-hidden="true"></span>
                    </button>
                    <button id="next" class="element right-arrow">
                        <span class="right-arrow-icon arrow" aria-hidden="true"></span>
                    </button>
                    <div class="element">
                        <label for="rowsPerPage">Lignes par page:</label>
                        <input type="number" id="rowsPerPage" min="1" value="10">
                    </div>
                    <div id="currentPage" class="pages">
                        Page <span id="currentPageNumber">1</span> / <span id="totalPages">1</span>
                    </div>
                </div>
            </div>
            <!-- Paramètres de Pagination du tableau -->
        </div>
        <!-- Tableau -->
        <!-- Graphique en camembert -->
        <div class="graph-camenbert">
            <div id="chart-container-motifs">
                <div id="myChart-motifs"></div>
            </div>
        </div>
        <!-- Graphique en camembert -->
        <!-- Scripts -->
        <script>
            Highcharts.setOptions({
                colors: Highcharts.map(Highcharts.getOptions().colors, function(color) {
                    return {
                        radialGradient: {
                            cx: 0.5,
                            cy: 0.3,
                            r: 0.7
                        },
                        stops: [
                            [0, color],
                            [1, Highcharts.color(color).brighten(-0.3).get('rgb')] // darken
                        ]
                    };
                })
            });

            var dataMotifs = <?php echo $dataMotifs; ?>;
            Highcharts.chart('myChart-motifs', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Répartition des montants négatifs par motif'
                },
                subtitle: {
                    text: 'Source: <a href="https://cedric-mc.github.io/Banklink/" target="_blank">BankLink</a>'
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<span style="font-size: 1.2em"><b>{point.name}</b></span><br>' +
                                '<span style="opacity: 0.6">{point.percentage:.1f} %</span>',
                            connectorColor: 'rgba(128,128,128,0.5)'
                        }
                    }
                },
                series: [{
                    name: 'Montant',
                    colorByPoint: true,
                    data: dataMotifs.map(function(m) {
                        return {
                            name: m.libelle,
                            y: Math.abs(m.sommeMontantNegatif), // Utilisez la valeur absolue pour les montants négatifs
                        }
                    })
                }],
                legend: {
                    enabled: false
                },
                exporting: {
                    buttons: {
                        contextButton: {
                            menuItems: ['viewFullscreen', 'separator', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'separator', 'downloadCSV', 'downloadXLS']
                        }
                    }
                }
            });

            // Pour trier les montants
            var montantHeader = document.querySelector('th:nth-child(9)');
            montantHeader.classList.add('sortable');
            montantHeader.addEventListener('click', function() {
                sortTable(8);
            });

            // Pour l'exportation des données du tableau
            document.getElementById('format').addEventListener('change', function() {
                var selectedFormat = this.value;
                if (selectedFormat) {
                    document.getElementById('exportForm').action = '../export/export.php';
                    document.getElementById('exportForm').submit();
                }
            });
        </script>
        <script src="../script/sort.js"></script>
        <script src="../script/tableau.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Scripts -->
    </body>
</html>