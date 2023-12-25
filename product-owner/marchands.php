<?php
session_start();
if (empty($_SESSION['idUser']) || $_SESSION['type'] != 'product-owner') {
    header("Location: ../");
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial- scale=1.0">
        <title>BankLink</title>
        <link rel="shortcut icon" href="../img/favicon.png"><!-- Favicon -->
        <link rel="stylesheet" href="../style/style.css">
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
        <!-- Confirmation de la suppression ou création d'un compte -->
        <section class="po-confirm">
            <h2>Confirmation de création ou supression de compte</h2>
            <div class="creatsuppr">
                <div>
                    <h2>Création</h2>
                    <?php
                    // Inclure votre fichier de configuration de base de données
                    include("../includes/conf.php");

                    // Récupérer la liste des clients temporaires depuis la base de données
                    $query = "SELECT * FROM CLIENT_TEMP";
                    $result = $cnx->query($query);

                    // Parcourir les clients temporaires et afficher les informations de chaque client temporaire
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $siren = $row['siren'];
                        $login = $row['login'];
                        $raisonSociale= $row['raisonSociale'];
                        $devise= $row['devise'];
                        $numCarte= $row['numCarte'];
                        $reseau= $row['reseau'];
                        $mail= $row['mail'];
                        $password=NULL;

                        echo '<div class="compte">';
                        echo "<p>Login : <span style='font-style: italic'>$login</span></p>";
                        echo '<form method="post" action="traitement_clients_temp.php">';

                        // Champs cachés pour le formulaire de création de compte avec les valeurs correspondantes

                        echo "<input type='hidden' name='siren' value='$siren'>";
                        echo "<input type='hidden' name='login' value='$login'>";
                        echo "<input type='hidden' name='raisonSociale' value='$raisonSociale'>";
                        echo "<input type='hidden' name='email' value='$mail'>";
                        echo "<input type='hidden' name='devise' value='$devise'>";
                        echo "<input type='hidden' name='carte' value='$numCarte'>";
                        echo "<input type='hidden' name='reseau' value='$reseau'>";

                        echo '<span class="choix">';
                        echo '<button type="submit" name="yes"><img src="../img/yes.png"></button>';
                        echo '<button type="submit" name="no"><img src="../img/no.png"></button>';
                        echo '</span>';
                        echo '</form>';
                        echo '</div>';
                    }
                    // Si il n'y a aucun client temporaire, afficher un message
                    if ($result->rowCount() == 0) {
                        echo '<p>Aucun client temporaire</p>';
                    } else {
                        echo '<p>Fin de la liste</p>';
                    }
                    ?>

                </div>

                <div>
                    <h2>Suppression</h2>
                    <?php
                    // Récupérer la liste des clients depuis la base de données avec suppr = 1
                    $query = "SELECT U.login, C.idUser, C.raisonSociale, U.mail
                    FROM CLIENT C JOIN UTILISATEUR U 
                    ON C.idUser = U.idUser
                    WHERE C.suppr = 1";

                    $result = $cnx->query($query);

                    // Parcourir les clients et afficher les informations de chaque client
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $idUser = $row['idUser'];
                        $login = $row['login'];
                        $raisonSociale = $row['raisonSociale'];
                        $mail=$row['mail'];
                        // Ajouter d'autres champs si nécessaire

                        echo '<div class="compte">';
                        echo "<p>Login : <span style='font-style: italic'>$login</span></p>";
                        echo '<form method="post" action="traitement_suppression.php">';
                        echo "<input type='hidden' name='idUser' value='$idUser'>";
                        echo "<input type='hidden' name='login' value='$login'>";
                        echo "<input type='hidden' name='mail' value='$mail'>";
                        echo "<input type='hidden' name='raisonSociale' value='$raisonSociale'>";
                        echo '<span class="choix">';
                        echo '<button type="submit" name="yes"><img src="../img/yes.png"></button>';
                        echo '<button type="submit" name="no"><img src="../img/no.png"></button>';
                        echo '</span>';
                        echo '</form>';
                        echo '</div>';
                    }
                    if ($result->rowCount() == 0) {
                        echo '<p>Aucun client</p>';
                    } else {
                        echo '<p>Fin de la liste</p>';
                    }
                    ?>
                </div>
            </div>
        </section>
        <!-- Confirmation de la suppression ou création d'un compte -->
    </body>
</html>