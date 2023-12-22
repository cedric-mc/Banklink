<?php
session_start();
if (empty($_SESSION['idUser']) || $_SESSION['type'] != "client") {
    header('Location: ../');
    exit();
}

include '../includes/conf.php';
include('../includes/requetes.php');
include("../includes/functions.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BankLink - <?php echo $_SESSION['login'];?></title>
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
            <h3>Recherche des Remises</h3>
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
                    <label for="n_remise">N° Remise :</label>
                    <select name="n_remise" id="n_remise">
                        <option value="">Tous</option>
                        <?php
                        $requete = "SELECT DISTINCT numRemise FROM REMISE WHERE siren = :siren";
                        $resultat = $cnx->prepare($requete);
                        $resultat->bindParam(':siren', $_SESSION['siren']);
                        $resultat->execute();
                        $remises = $resultat->fetchAll();
                        foreach ($remises as $remise) {
                            echo "<option value='" . $remise[0] . "'>" . $remise[0] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit">Valider</button>
            </form>
            <?php
            if (!empty($_POST['n_remise'])) {
                $requete = $requetes["select_client_remise_num"];
                $resultat = $cnx->prepare($requete);
                $resultat->bindParam(':siren', $_SESSION['siren']);
                $resultat->bindParam(':remise', $_POST['n_remise']);
            } elseif (!empty($_POST['debut']) || !empty($_POST['fin'])) {
                $d_debut = (!empty($_POST['debut'])) ? $_POST['debut'] : $date;
                $d_fin = (!empty($_POST['fin'])) ? $_POST['fin'] : $d_fin;
                $requete = $requetes["select_client_remise_date"];
                $resultat = $cnx->prepare($requete);
                $resultat->bindParam(':siren', $_SESSION['siren']);
                $resultat->bindParam(':debut', $d_debut);
                $resultat->bindParam(':fin', $d_fin);
            } else {
                $requete = $requetes["select_client_remise"];
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
        <div class="tableau-remise">
            <h2 class="title-resultat">Remises</h2>
            <!-- Formulaire d'exportation -->
            <div class="export">
                <form action="../export/export.php" method="post" id="exportForm">
                    <input type="hidden" name="table" value="remise">
                    <input type="hidden" name="lignes" value="<?php echo htmlspecialchars(json_encode($lignes)); ?>">
                    <input type="hidden" name="nbLignes" value="<?php echo $nbLignes; ?>">
                    <input type="hidden" name="fichier" value="remise.php">
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
                <?php echo $nbLignes;?> résultat(s) (Affichant <span class="visible-rows" id="visibleRows"></span> ligne(s))
            </span>
            <table>
                <thead>
                    <tr>
                        <th>N° SIREN</th>
                        <th>Raison sociale</th>
                        <th>N° Remise</th>
                        <th>Date traitement</th>
                        <th>Nbre transactions</th>
                        <th>Devise</th>
                        <th>Montant total</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ($lignes as $ligne) {
                    echo "<tr>";
                    echo "<td>$ligne->siren</td>";
                    echo "<td>$ligne->raisonSociale</td>";
                    echo "<td>$ligne->numRemise</td>";
                    echo "<td>".date_format(date_create($ligne->dateRemise), 'd/m/Y')."</td>";
                    echo "<td>$ligne->transactions</td>";
                    echo "<td>$ligne->devise</td>";
                    echo "<td class='".checkNumber($ligne->montant)."'>$ligne->montant</td>";
                    echo "<td><button class='modal-btn modal-trigger-remise' data-row='$ligne->numRemise'>...</button></td>";
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
        <!-- Scripts -->
        <script>
            document.getElementById('format').addEventListener('change', function () {
                var selectedFormat = this.value;
                if (selectedFormat) {
                    // Modify the 'action' attribute of the form to the appropriate file
                    document.getElementById('exportForm').action = '../export/export.php';
                    // Submit the form to redirect to the selected file
                    document.getElementById('exportForm').submit();
                }
            });
        </script>
        <script src="../script/tableau.js"></script>
        <script src="../script/modal.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Scripts -->
    </body>
</html>