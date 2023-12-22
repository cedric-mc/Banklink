<?php
session_start();
if (empty($_SESSION['idUser']) || $_SESSION['type'] != 'product-owner') {
    header("Location: ../");
    exit;
}

include("../includes/conf.php");
include("../includes/requetes.php");
include("../includes/functions.php");

// Récupération des motifs d'impayés pour le graphique
$reqMotifs =
    "SELECT SUM(montant) AS sommeMontantNegatif, 
        libelle 
FROM REMISE r,
     MOTIFS_IMPAYES m
WHERE r.code = m.code 
    AND montant < 0 
GROUP BY libelle;";
$resultMotif = $cnx->prepare($reqMotifs);
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
        <!-- Scripts HighCharts -->
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/series-label.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>
        <!-- Scripts HighCharts -->
        <style>
            #motifsImpayesChart {
                width: 100%;
                height: auto;
            }
        </style>
    </head>

    <body>
        <!-- Navigation -->
        <nav>
            <input id="nav-toggle" type="checkbox">
            <div class="logo">BankLink</div>
            <ul class="links">
                <li><a href="index.php">Trésorerie</a></li>
                <li><a href="remise.php">Remise</a></li>
                <li><a href="marchands.php">Marchands</a></li>
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
                    <label for="siren">N° SIREN :</label>
                    <select name="siren" id="siren">
                        <option value="">Tous</option>
                        <?php
                        $requete = "SELECT siren FROM CLIENT c, UTILISATEUR u WHERE c.idUser = u.idUser AND password IS NOT NULL";
                        $resultat = $cnx->query($requete);
                        $sirens = $resultat->fetchAll();
                        foreach ($sirens as $siren) {
                            echo "<option value='" . $siren[0] . "'>" . $siren[0] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="inputs">
                    <label for="rs">Raison Sociale :</label>
                    <select name="rs" id="rs">
                        <option value="">Tous</option>
                        <?php
                        $requete = "SELECT raisonSociale FROM CLIENT c, UTILISATEUR u WHERE c.idUser = u.idUser AND password IS NOT NULL";
                        $resultat = $cnx->query($requete);
                        $rs = $resultat->fetchAll();
                        foreach ($rs as $raisonSociale) {
                            echo "<option value='" . $raisonSociale[0] . "'>" . $raisonSociale[0] . "</option>";
                        }
                        ?>
                    </select>
                </div>
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
                        <option value="">Tous</option>
                        <?php
                        $requete = "SELECT numDossierImpaye FROM REMISE WHERE numDossierImpaye IS NOT NULL";
                        $resultat = $cnx->prepare($requete);
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
            if (!empty($_POST['siren'])) {
                if (!empty($_POST['dossier_impaye'])) {
                    $requete = $requetes["select_po_impaye_siren_dossier"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':siren', $_POST['siren']);
                    $resultat->bindParam(':numDossierImpaye', $_POST['dossier_impaye']);
                } elseif (!empty($_POST['debut']) || !empty($_POST['fin'])) {
                    $d_debut = (!empty($_POST['debut'])) ? $_POST['debut'] : $date;
                    $d_fin = (!empty($_POST['fin'])) ? $_POST['fin'] : $d_fin;
                    $requete = $requetes["select_po_impaye_siren_date"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':siren', $_POST['siren']);
                    $resultat->bindParam(':debut', $d_debut);
                    $resultat->bindParam(':fin', $d_fin);
                } else {
                    $requete = $requetes["select_po_impaye_siren"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':siren', $_POST['siren']);
                }
            } elseif (!empty($_POST['rs'])) {
                if (!empty($_POST['dossier_impaye'])) {
                    $requete = $requetes["select_po_impaye_rs_dossier"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':rs', $_POST['rs']);
                    $resultat->bindParam(':numDossierImpaye', $_POST['dossier_impaye']);
                } elseif (!empty($_POST['debut']) || !empty($_POST['fin'])) {
                    $d_debut = (!empty($_POST['debut'])) ? $_POST['debut'] : $date;
                    $d_fin = (!empty($_POST['fin'])) ? $_POST['fin'] : $d_fin;
                    $requete = $requetes["select_po_impaye_rs_date"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':rs', $_POST['rs']);
                    $resultat->bindParam(':debut', $d_debut);
                    $resultat->bindParam(':fin', $d_fin);
                } else {
                    $requete = $requetes["select_po_impaye_rs"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':rs', $_POST['rs']);
                }
            } elseif (!empty($_POST['dossier_impaye'])) {
                $requete = $requetes["select_po_impaye_dossier"];
                $resultat = $cnx->prepare($requete);
                $resultat->bindParam(':numDossierImpaye', $_POST['dossier_impaye']);
            } elseif (!empty($_POST['debut']) || !empty($_POST['fin'])) {
                $d_debut = format_date((!empty($_POST['debut'])) ? $_POST['debut'] : $date);
                $d_fin = format_date((!empty($_POST['fin'])) ? $_POST['fin'] : $d_fin);
                $requete = $requetes["select_po_impaye_date"];
                $resultat = $cnx->prepare($requete);
                $resultat->bindParam(':debut', $d_fin);
                $resultat->bindParam(':fin', $d_debut);
            } else {
                $requete = $requetes["select_po_impaye"];
                $resultat = $cnx->prepare($requete);
            }
            $resultat->execute();
            $nbLignes = $resultat->rowCount();
            $lignes = $resultat->fetchAll(PDO::FETCH_OBJ);
            ?>
        </div>
        <!-- Formulaire de recherche -->
        <!-- Tableau -->
        <div class="resultat">
            <h2 class="title-resultat">Impayés</h2>
            <!-- Formulaire d'exportation -->
            <div class="export">
                <form action="../export/export.php" method="post" id="exportForm">
                    <input type="hidden" name="table" value="impaye">
                    <input type="hidden" name="lignes" value="<?php echo htmlspecialchars(json_encode($lignes)); ?>">
                    <input type="hidden" name="nbLignes" value="<?php echo $nbLignes; ?>">
                    <input type="hidden" name="fichier" value="impaye.php">
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
                        <th>Devise</th>
                        <th>Montant Total</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($lignes as $ligne) {
                        echo "<tr>";
                        echo "<td>$ligne->siren</td>";
                        echo "<td>$ligne->devise</td>";
                        echo "<td class='" . checkNumber($ligne->montantTotal) . "'>$ligne->montantTotal</td>";
                        echo "<td><button class='modal-btn modal-trigger-impaye' data-row='$ligne->siren'>...</button></td>";
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
        <!-- Fenêtre interne / modal -->
        <div class="modal-container">
            <div class="modal" role="dialog" aria-labelledby="modalTitle" aria-describedby="dialogDesc">
                <button id="close-btn" aria-label="close modal" class="close-modal">Fermer</button>
                <div id="modal-table-container"></div>
            </div>
        </div>
        <!-- Fenêtre interne / modal -->
        <!-- Graphique en camembert -->
        <div class="graph-camenbert">
            <div id="chart-container-motifsImpayesChart">
                <div id="motifsImpayesChart"></div>
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
            Highcharts.chart('motifsImpayesChart', {
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
            var sirenHeader = document.querySelector('th:nth-child(1)');
            sirenHeader.classList.add('sortable');
            sirenHeader.addEventListener('click', function() {
                sortTable(0);
            });

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
        <script src="../script/modal.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Scripts -->
    </body>
</html>