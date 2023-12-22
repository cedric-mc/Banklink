<?php
session_start();
if (empty($_SESSION['idUser']) || $_SESSION['type'] != 'product-owner') {
    header("Location: ../");
    exit;
}

include("../includes/conf.php");
include("../includes/requetes.php");
include("../includes/functions.php");
?>
<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BankLink - <?php echo $_SESSION['login'] ?></title>
        <link rel="shortcut icon" href="../img/favicon.png"><!-- Favicon -->
        <link rel="stylesheet" href="../style/style.css">
    </head>

    <body>
        <!-- Navigation -->
        <nav>
            <input id="nav-toggle" type="checkbox">
            <div class="logo">BankLink</div>
            <ul class="links">
                <li><a href="./">Trésorerie</a></li>
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
            <h3>Recherche des Trésoreries</h3>
            <form action="" method="post">
                <div class="inputs">
                    <label for="siren">N° SIREN :</label>
                    <select name="siren" id="siren">
                        <option value="">Tous</option>
                        <?php
                        $requete = "SELECT siren FROM CLIENT c, UTILISATEUR u WHERE c.idUser = u.idUser AND password IS NOT NULL AND suppr != 2";
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
                        $requete = "SELECT raisonSociale FROM CLIENT c, UTILISATEUR u WHERE c.idUser = u.idUser AND password IS NOT NULL AND suppr != 2";
                        $resultat = $cnx->query($requete);
                        $rs = $resultat->fetchAll();
                        foreach ($rs as $raisonSociale) {
                            echo "<option value='" . $raisonSociale[0] . "'>" . $raisonSociale[0] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="inputs">
                    <label for="treso">Date :</label>
                    <input type="date" id="treso" name="treso">
                </div>
                <button type="submit">Valider</button>
            </form>
            <?php
            if (!empty($_POST['siren'])) {
                if (!empty($_POST['treso'])) {
                    $requete = $requetes['select_po_treso_siren_date'];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':siren', $_POST['siren']);
                    $resultat->bindParam(':treso', $_POST['treso']);
                } else {
                    $requete = $requetes["select_client_treso"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':siren', $_POST['siren']);
                }
            } elseif (!empty($_POST['rs'])) {
                if (!empty($_POST['treso'])) {
                    $requete = $requetes['select_po_treso_rs_date'];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':rs', $_POST['rs']);
                    $resultat->bindParam(':treso', $_POST['treso']);
                } else {
                    $requete = $requetes["select_po_treso_rs"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':rs', $_POST['rs']);
                }
            } elseif (!empty($_POST['treso'])) {
                $requete = $requetes["select_po_treso_date"];
                $resultat = $cnx->prepare($requete);
                $resultat->bindParam(':treso', $_POST['treso']);
            } else {
                $requete = $requetes["select_po_treso"];
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
            <h2 class="title-resultat">Trésoreries</h2>
            <!-- Formulaire d'exportation -->
            <div class="export">
                <form action="../export/export.php" method="post" id="exportForm">
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
            <br>
            <br>
            <!-- Liste de la trésorerie global de tous les clients -->
            <div class="treso-global">
                <?php
                $requete = $requetes["select_po_global_treso"];
                $resultat = $cnx->prepare($requete);
                $resultat->execute();
                while ($row = $resultat->fetch(PDO::FETCH_OBJ)) {
                    echo "<details class='resultat-treso-global clickable'>";
                    echo "<summary class='" . checkNumber($row->tresoglobal) . "'>$row->raisonSociale</summary>";
                    echo "<span>$row->siren</span>";
                    echo "<span>$row->login</span>";
                    echo "<span class='" . checkNumber($row->tresoglobal) . "'>$row->tresoglobal " . getDevise($row->devise) . "</span>";
                    echo "</details>";
                }
                ?>
            </div>
            <!-- Liste de la trésorerie global de tous les clients -->
        </div>
        <!-- Tableau -->
        <!-- Scripts -->
        <script>
            var sirenHeader = document.querySelector('th:nth-child(1)');
            var montantHeader = document.querySelector('th:nth-child(5)');

            sirenHeader.classList.add('sortable');
            montantHeader.classList.add('sortable');

            sirenHeader.addEventListener('click', function() {
                sortTable(0);
            });
            montantHeader.addEventListener('click', function() {
                sortTable(4);
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