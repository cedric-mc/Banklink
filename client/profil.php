<?php
session_start();
if (empty($_SESSION['idUser']) || $_SESSION['type'] != "client") {
    header('Location: ../');
    exit();
}
include("../includes/functions.php");
include("../includes/conf.php");
include("../includes/requetes.php");

// Je récupère les informations du client
$req = $cnx->prepare($requetes["select_client"]);
$req->bindParam(':idUser', $_SESSION['idUser'], PDO::PARAM_INT);
$req->execute();
$resultat = $req->fetch(PDO::FETCH_ASSOC);
// Je récupère le résultat avec une boucle sans utiliser la session
foreach ($resultat as $key => $value) {
    ${$key} = $value;
}
$req->closeCursor();
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
        <!-- Profil de l'utilisateur -->
        <div class="mesinformations">
            <h2>Mes informations</h2>
            <div class="profil">
                <div class="profil-img">
                    <img src="../img/profil.jpg">
                </div>
                <div class="profil-text">
                    <div class="mini-info">
                        <span class="info">Identifiant : &nbsp;</span>
                        <span class="perso-data"><?php echo $_SESSION['login']; ?></span>
                    </div>
                    <div class="mini-info">
                        <span class="info">Raison Social : &nbsp;</span>
                        <span class="perso-data"><?php echo $raisonSociale; ?></span>
                    </div>
                    <div class="mini-info">
                        <span class="info">N° de SIREN : &nbsp;</span>
                        <span class="perso-data"><?php echo spacedText($_SESSION['siren']); ?></span>
                    </div>
                    <div class="mini-info">
                        <span class="info">E-mail : &nbsp;</span>
                        <span class="perso-data"><?php echo $_SESSION['mail']; ?></span>
                    </div>
                    <div class="mini-info">
                        <span class="info">N° et Réseau de la Carte : &nbsp;</span>
                        <span class="perso-data cache">
                            <?php echo masquerNumeroCarte($numCarte) . " - " . $reseau ?>
                        </span>
                    </div>
                    <div class="mini-info">
                        <span class="info">Devise : &nbsp;</span>
                        <span class="perso-data"><?php echo $devise; ?></span>
                    </div>
                </div>
            </div>
            <button id="disconnect-btn">Se Déconnecter</button>
        </div>
        <!-- Profil de l'utilisateur -->
        <!-- Script -->
        <script>
            document.getElementById('disconnect-btn').addEventListener('click', function() {
                if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                    window.location.href = '../logout.php?logout=true'; // Redirigez vers la page de déconnexion
                }
            });
        </script>
        <!-- Script -->
    </body>
</html>