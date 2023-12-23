<?php
session_start();
if (empty($_SESSION['idUser']) || $_SESSION['type'] != "client") {
    header('Location: ../');
    exit();
}

include("../includes/conf.php");
include("../includes/requetes.php");
include("../includes/functions.php");
// Préparation de la requête avec des paramètres de liaison
$req =
    "SELECT dateTransaction,
        (SELECT SUM(r2.montant) 
            FROM REMISE r2 
            WHERE r2.siren = :siren
                AND r2.dateTransaction <= r.dateTransaction) AS CA, 
        (SELECT SUM(r3.montant) 
            FROM REMISE r3 
            WHERE r3.siren = :siren 
            AND r3.dateTransaction <= r.dateTransaction 
            AND r3.montant < 0) AS IM
FROM REMISE r
WHERE r.siren = :siren
ORDER BY dateTransaction;";

$result = $cnx->prepare($req);
$result->bindParam(':siren', $_SESSION['siren']);
$result->execute();

$rows = $result->fetchAll(PDO::FETCH_ASSOC);
$data = array();
foreach ($rows as $row) {
    $data[] = $row;
}
$data = json_encode($data);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BankLink - <?php echo $_SESSION['login'] ?></title>
    <link rel="shortcut icon" href="../img/favicon.png"> <!-- Favicon -->
    <link rel="stylesheet" href="../style/style.css">
    <!-- Scripts HighCharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <!-- Scripts HighCharts -->
    <style>
        .global-treso .global-treso-element .element .tresoglobal .element button {
            display: block;
        }

        /* Style du bouton de changement de type de graphique */
        #graph-type {
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-right: 1rem;
        }

        /* Au survol */
        #graph-type:hover {
            background-color: #2980b9;
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
    <!-- Graphique de l'évolution de la trésorerie global du client -->
    <div class="resultat global-treso">
        <h2>Trésorerie Global</h2>
        <div class="global-treso-element">
            <div class="element">
                <div class="tresoglobal">
                    <?php
                    $requete = $requetes["select_client_global_treso"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':siren', $_SESSION['siren']);
                    $resultat->execute();
                    $ligne = $resultat->fetch(PDO::FETCH_OBJ);
                    echo "<h3 class='" . checkNumber($ligne->tresoglobal) . "'>$ligne->tresoglobal " . getDevise($ligne->devise) . "</h3>";
                    ?>
                    <div class="element">
                        <button id="graph-type">Changer le Type du Graphique</button>
                    </div>
                </div>
            </div>
            <div id="chart-container" class="element graph">
                <div id="myChart"></div>
            </div>
        </div>
    </div>
    <!-- Graphique de l'évolution de la trésorerie global du client -->
    <!-- Formulaire de recherche -->
    <div class="recherche">
        <h3>Recherche des Trésoreries</h3>
        <form action="" method="post">
            <div class="inputs">
                <label for="treso">Date :</label>
                <input type="date" id="treso" name="treso">
            </div>
            <?php
            if (!empty($_POST['treso'])) {
                echo "<button type='submit'>Réinitialiser</button>";
            } else {
                echo "<button type='submit'>Rechercher</button>";
            }
            ?>
        </form>
        <?php
        if (!empty($_POST['treso'])) {
            $requete = $requetes["select_client_treso_date"];
            $resultat = $cnx->prepare($requete);
            $resultat->bindParam(':siren', $_SESSION['siren']);
            $resultat->bindParam(':treso', $_POST['treso']);
        } else {
            $requete = $requetes["select_client_treso"];
            $resultat = $cnx->prepare($requete);
            $resultat->bindParam(':siren', $_SESSION['siren']);
        }
        $resultat->execute();
        $nbLignes = $resultat->rowCount();
        $lignes = $resultat->fetchAll(PDO::FETCH_OBJ);
        ?>
    </div>
    <!-- Formulaire de recherche -->
    <!-- Tableau -->
    <div class="resultat">
        <h2 class="title-resultat">Trésoreries</h2>
        <!-- Formulaire d'exportation -->
        <div class="export">
            <form action="../export/export.php" method="post" id="exportForm">
                <!-- Champs du formulaire -->
                <input type="hidden" name="table" value="treso">
                <input type="hidden" name="lignes" value="<?php echo htmlspecialchars(json_encode($lignes)); ?>">
                <input type="hidden" name="nbLignes" value="<?php echo $nbLignes; ?>">
                <input type="hidden" name="fichier" value="./">
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
                    <th>Nombre de Transactions</th>
                    <th>Devise</th>
                    <th>Montant Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($lignes as $ligne) {
                    echo "<tr>";
                    echo "<td>$ligne->siren</td>";
                    echo "<td>$ligne->raisonSociale</td>";
                    echo "<td>$ligne->transactions</td>";
                    echo "<td>$ligne->devise</td>";
                    echo "<td class='" . checkNumber($ligne->montant) . "'>$ligne->montant</td>";
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
    <!-- Scripts -->
    <script>
        function formatDate(date) {
            var day = date.getDate();
            var month = date.getMonth() + 1;
            var year = date.getFullYear();
            var date = day + '/' + month + '/' + year;
            return date;
        }

        Highcharts.setOptions({
            lang: {
                months: [
                    'Janvier', 'Février', 'Mars', 'Avril',
                    'Mai', 'Juin', 'Juillet', 'Août',
                    'Septembre', 'Octobre', 'Novembre', 'Décembre'
                ],
                weekdays: [
                    'Dimanche', 'Lundi', 'Mardi', 'Mercredi',
                    'Jeudi', 'Vendredi', 'Samedi'
                ]
            }
        });

        var chartInstance; // Stocke l'instance du graphique
        var data = <?php echo $data; ?>;
        chartInstance = Highcharts.chart('myChart', {
            title: {
                text: 'Évolution de la Trésorerie Global<br>en fonction des impayés'
            },
            subtitle: {
                text: 'Source: <a href="https://cedric-mc.github.io/Banklink/" target="_blank">BankLink</a>'
            },
            credits: {
                enabled: false
            },
            xAxis: {
                title: {
                    text: 'Date'
                },
                type: 'datetime',
                dateFormat: 'dd/MM/yyyy',
                categories: data.map(function(e) {
                    return formatDate(new Date(e.dateTransaction));
                })
            },
            yAxis: {
                title: {
                    text: 'Montant'
                },
            },
            plotOptions: {
                label: {
                    connectorAllowed: true
                },
                line: {
                    enableMouseTracking: true,
                    color: 'black', // Couleur de la ligne
                    dataLabels: {
                        enabled: false
                    }
                },
                series: {
                    marker: {
                        fillColor: null, // Permet de ne pas remplir le marqueur par défaut
                        lineWidth: 2, // Épaisseur de la bordure du marqueur
                        lineColor: null, // Permet de ne pas définir la couleur de la bordure par défaut
                        states: {
                            hover: {
                                fillColor: null, // Permet de ne pas remplir le marqueur en hover
                                lineColor: null // Permet de ne pas définir la couleur de la bordure en hover
                            }
                        }
                    },
                    point: {
                        events: {
                            update: function() {
                                var color = this.y >= 0 ? 'green' : 'red'; // Couleur conditionnelle
                                this.update({
                                    color: color
                                }, false);
                            }
                        }
                    }
                }
            },
            series: [{
                name: 'Trésorerie',
                data: data.map(function(e) {
                    return {
                        y: parseFloat(e.CA),
                        color: parseFloat(e.CA) >= 0 ? 'green' : 'red' // Couleur conditionnelle
                    };
                }),
                zindex: 1,
                color: 'green',
                negativeColor: 'red'
            }, {
                name: 'Impayés',
                data: data.map(function(e) {
                    return {
                        y: parseFloat(e.IM),
                        color: 'purple'
                    };
                }),
                itemStyle: {
                    color: 'purple'
                },
                color: 'purple',
                zindex: -1
            }],
            exporting: {
                buttons: {
                    contextButton: {
                        menuItems: ['viewFullscreen', 'separator', 'downloadPNG', 'downloadJPEG', 'downloadPDF', 'separator', 'downloadCSV', 'downloadXLS']
                    }
                }
            }
        });
        var currentTypeChart = 'line'; // Type de graphique actuel
        function updateChartType(chart, newType) {
            chart.update({
                chart: {
                    type: newType
                }
            });
        }

        document.getElementById('graph-type').addEventListener('click', function() {
            var newChartType = currentTypeChart === 'line' ? 'column' : 'line';
            currentTypeChart = newChartType;

            // Mettre à jour le type du graphique existant
            updateChartType(chartInstance, newChartType);
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
    <script src="../script/tableau.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Scripts -->
</body>

</html>