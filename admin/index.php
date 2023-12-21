<?php
session_start();
// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (empty($_SESSION["idUser"]) || $_SESSION["type"] != "admin") {
    header("Location: ../");
    exit();
}
// Vérifier si le formulaire a été soumis
if (isset($_GET["etat"]) && $_GET["success"] == 2) {
    echo "<script>alert('Le compte a bien été créé !');</script>";
} elseif ($_GET["etat"] == 1) {
    echo "<script>alert('Le compte a bien été supprimé !');</script>";
}
?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"><!-- Responsive -->
        <title>BankLink - <?php echo $_SESSION['login'] ?> </title>
        <link rel="shortcut icon" href="../img/favicon.png"><!-- Favicon -->
        <link rel="stylesheet" href="../style/style.css"><!-- Style -->
    </head>

    <body>
        <!-- Navigation -->
        <nav>
            <input id="nav-toggle" type="checkbox">
            <div class="logo">BankLink</div>
            <ul class="links">
                <li><a href="./">Gestion de compte</a></li>
                <li><a href="profil.php">Mon Profil</a></li>
            </ul>
            <label for="nav-toggle" class="icon-burger">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </label>
        </nav>
        <!-- Navigation -->
        <header>
            <h1 class="jump">Création ou Supression de compte client</h1>
        </header>
        <div>
            <ul class="tab">
                <li><a class="tablinks clickable" onclick="onglets_switch(event, 'create')">Création</a></li>
                <li><a class="tablinks clickable" onclick="onglets_switch(event, 'suppr')">Supression</a></li>
            </ul>
            <br>
            <!-- Formulaire de création de compte client -->
            <div id="create" class="tabcontent create-suppr-bloc" style="display: flow;">
                <form action="create_client.php" method="post">
                    <h2>Création de compte client</h2>
                    <div class="inputBox">
                        <input type="text" name="siren" id="siren" required>
                        <label for="siren">N° de SIREN</label>
                        <hr>
                    </div>
                    <div class="inputBox">
                        <input type="text" name="raisonSociale" id="raisonSociale" required>
                        <label for="raisonSociale">Raison Sociale</label>
                        <hr>
                    </div>
                    <div class="inputBox">
                        <input type="text" name="login" id="login" required>
                        <label for="login">Login</label>
                        <hr>
                    </div>
                    <div class="inputBox">
                        <input type="email" name="email" id="email" required>
                        <label for="email">Email</label>
                        <hr>
                    </div>
                    <div class="inputBox">
                        <input type="text" name="devise" id="devise" required>
                        <label for="devise">Devise</label>
                        <hr>
                    </div>
                    <div class="inputBox">
                        <input type="text" name="carte" id="carte" required>
                        <label for="carte">N° de Carte</label>
                        <hr>
                    </div>
                    <div class="inputBox">
                        <input type="text" name="reseau" id="reseau" required>
                        <label for="reseau">Réseau</label>
                        <hr>
                    </div>
                    <br>
                    <input type="submit" value="Créer le compte" style="align-items: center; justify-items: center; width: 100%">
                </form>
            </div>
            <!-- Formulaire de création de compte client -->
            <!-- Formulaire de suppression de compte client -->
            <div id="suppr" class="tabcontent create-suppr-bloc" style="display: none;">
                <h2>Suppression d'un client</h2>
                <ul>
                    <?php
                    // Inclure votre fichier de configuration de base de données
                    include("../includes/conf.php");

                    // Requête pour récupérer les données depuis la base de données
                    $query = "SELECT siren, raisonSociale, U.idUser, login
                        FROM CLIENT AS C
                        JOIN UTILISATEUR AS U ON C.idUser = U.idUser
                        WHERE C.suppr = 0 AND password IS NOT NULL";

                    $result = $cnx->query($query);

                    // Parcourir les résultats et afficher les informations de chaque client
                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        $siren = $row['siren'];
                        $raisonSociale = $row['raisonSociale'];
                        $idUser = $row['idUser'];
                        $login = $row['login'];
                        echo '<li>';
                        echo '<details class="suppr-client clickable">';
                        echo "<summary>$raisonSociale</summary>";
                        echo "Login : $login<br>";
                        echo "N° de SIREN : $siren<br>";
                        echo "idUser : $idUser<br>";
                        echo '<br>';
                        // Bouton pour supprimer le client sélectionné
                        echo "<a class='btn-suppr' href='suppr_client.php?id='" . $idUser . "'>";
                        echo 'Supprimer';
                        echo "<svg class='trash' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'>";
                        echo "<path d='M 28 6 C 25.791 6 24 7.791 24 10 L 24 12 L 23.599609 12 L 10 14 L 10 17 L 54 17 L 54 14 L 40.400391 12 L 40 12 L 40 10 C 40 7.791 38.209 6 36 6 L 28 6 z M 28 10 L 36 10 L 36 12 L 28 12 L 28 10 z M 12 19 L 14.701172 52.322266 C 14.869172 54.399266 16.605453 56 18.689453 56 L 45.3125 56 C 47.3965 56 49.129828 54.401219 49.298828 52.324219 L 51.923828 20 L 12 19 z M 20 26 C 21.105 26 22 26.895 22 28 L 22 51 L 19 51 L 18 28 C 18 26.895 18.895 26 20 26 z M 32 26 C 33.657 26 35 27.343 35 29 L 35 51 L 29 51 L 29 29 C 29 27.343 30.343 26 32 26 z M 44 26 C 45.105 26 46 26.895 46 28 L 45 51 L 42 51 L 42 28 C 42 26.895 42.895 26 44 26 z'></path>";
                        echo '</svg>';
                        echo '</a>';
                        echo '<br>';
                        echo '</details>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>
            <!-- Formulaire de suppression de compte client -->
        </div>
        <!-- Script -->
        <script src="../script/onglets.js"></script>
        <!-- Script -->
    </body>
</html>