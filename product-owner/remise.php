<?php
session_start();
if (empty($_SESSION['idUser']) || $_SESSION['type'] != 'product-owner') {
    header("Location: ../redirect.php");
    exit;
}

include("../includes/conf.php");
include("../includes/functions.php");
include("../includes/requetes.php");
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BankLink - <?php echo $_SESSION['login'] ?></title>
        <link rel="shortcut icon" href="../img/favicon.png"><!-- Favicon -->
        <link rel="stylesheet" href="../style/style.css"><!-- Style -->
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
            <h3>Recherche des Remises</h3>
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
                        $requete = "SELECT DISTINCT numRemise FROM REMISE r, CLIENT c WHERE r.siren = c.siren AND suppr != 2;";
                        $resultat = $cnx->prepare($requete);
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
            if (!empty($_POST['siren'])) {
                if (!empty($_POST['n_remise'])) {
                    $requete = $requetes["select_po_remise_siren_num"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':siren', $_POST['siren']);
                    $resultat->bindParam(':numRemise', $_POST['n_remise']);
                } elseif (!empty($_POST['debut']) || !empty($_POST['fin'])) {
                    $d_debut = (!empty($_POST['debut'])) ? $_POST['debut'] : $date;
                    $d_fin = (!empty($_POST['fin'])) ? $_POST['fin'] : $d_fin;
                    $requete = $requetes["select_po_remise_siren_date"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':siren', $_POST['siren']);
                    $resultat->bindParam(':debut', $d_debut);
                    $resultat->bindParam(':fin', $d_fin);
                } else {
                    $requete = $requetes["select_po_remise_siren"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':siren', $_POST['siren']);
                }
            } elseif (!empty($_POST['rs'])) {
                if (!empty($_POST['n_remise'])) {
                    $requete = $requetes["select_po_remise_rs_num"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':rs', $_POST['rs']);
                    $resultat->bindParam(':numRemise', $_POST['n_remise']);
                } elseif (!empty($_POST['debut']) || !empty($_POST['fin'])) {
                    $d_debut = (!empty($_POST['debut'])) ? $_POST['debut'] : $date;
                    $d_fin = (!empty($_POST['fin'])) ? $_POST['fin'] : $d_fin;
                    $requete = $requetes["select_po_remise_rs_date"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':rs', $_POST['rs']);
                    $resultat->bindParam(':debut', $d_debut);
                    $resultat->bindParam(':fin', $d_fin);
                } else {
                    $requete = $requetes["select_po_remise_rs"];
                    $resultat = $cnx->prepare($requete);
                    $resultat->bindParam(':rs', $_POST['rs']);
                }
            } elseif (!empty($_POST['n_remise'])) {
                $requete = $requetes["select_po_remise_num"];
                $resultat = $cnx->prepare($requete);
                $resultat->bindParam(':numRemise', $_POST['n_remise']);
            } elseif (!empty($_POST['debut']) || !empty($_POST['fin'])) {
                $d_debut = (!empty($_POST['debut'])) ? $_POST['debut'] : $date;
                $d_fin = (!empty($_POST['fin'])) ? $_POST['fin'] : $d_fin;
                $requete = $requetes["select_po_remise_date"];
                $resultat = $cnx->prepare($requete);
                $resultat->bindParam(':debut', $d_debut);
                $resultat->bindParam(':fin', $d_fin);
            } else {
                $requete = $requetes["select_po_remise"];
                $resultat = $cnx->prepare($requete);
            }
            $resultat->execute();
            $nbLignes = $resultat->rowCount();
            $requeteString = $resultat->queryString;
            if (strpos($requeteString, ":siren") !== false) {
                $requeteString = str_replace(":siren", $_POST['siren'], $requeteString);
            }
            if (strpos($requeteString, ":rs") !== false) {
                $requeteString = str_replace(":rs", $_POST['rs'], $requeteString);
            }
            if (strpos($requeteString, ":numRemise") !== false) {
                $requeteString = str_replace(":numRemise", $_POST['n_remise'], $requeteString);
            }
            if (strpos($requeteString, ":debut") !== false) {
                $requeteString = str_replace(":debut", $d_debut, $requeteString);
                $requeteString = str_replace(":fin", $d_fin, $requeteString);
            }
            ?>
        </div>
        <!-- Formulaire de recherche -->
        <!-- Tableau -->
        <div class="tableau-remise">
            <h2 class="title-resultat">Remises</h2>
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
                    while ($ligne = $resultat->fetch(PDO::FETCH_OBJ)) {
                        echo "<tr>";
                        echo "<td>$ligne->siren</td>";
                        echo "<td>$ligne->raisonSociale</td>";
                        echo "<td>$ligne->numRemise</td>";
                        echo "<td>" . format_date($ligne->dateRemise) . "</td>";
                        echo "<td>$ligne->transactions</td>";
                        echo "<td>$ligne->devise</td>";
                        echo "<td class='" . checkNumber($ligne->montant) . "'>$ligne->montant</td>";
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
            // Permet d'exporter le tableau dans le format choisi sans bouton d'envoi
            document.getElementById('format').addEventListener('change', function() {
                var selectedFormat = this.value;
                if (selectedFormat) {
                    // Modfie l'attribut 'action' du formulaire vers le fichier approprié
                    document.getElementById('exportForm').action = '../export/export.php';
                    // Envoi les données vers le fichier approprié
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