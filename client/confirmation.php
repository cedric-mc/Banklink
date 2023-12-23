<?php
session_start();
if (isset($_SESSION['type']) && $_SESSION['type'] == 'client') {
    header('Location: ../');
    exit;
}

include('../includes/conf.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Vérifier si le mot de passe et la confirmation du mot de passe sont identiques
    if ($newPassword !== $confirmPassword) {
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }

    // Hasher le nouveau mot de passe
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Mettre à jour la table utilisateurs
    $updateQuery = "UPDATE UTILISATEUR SET password = :hashedPassword WHERE login = :login";
    $stmt = $cnx->prepare($updateQuery);
    $stmt->bindParam(':hashedPassword', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':login', $login, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $resultat = $cnx->query("SELECT u.idUser, mail, type, siren, raisonSociale, devise, numCarte, reseau FROM UTILISATEUR u, CLIENT c WHERE login = '$login' AND u.idUser = c.idUser;");
        $resultat2 = $resultat->fetch(PDO::FETCH_ASSOC);
        $_SESSION['idUser'] = $resultat2['idUser'];
        $_SESSION['login'] = $login;
        $_SESSION['type'] = $resultat2['type'];
        $_SESSION['mail'] = $resultat2['mail'];
        $_SESSION['siren'] = $result['siren'];
        $_SESSION['raisonSociale'] = $result['raisonSociale'];
        $_SESSION['devise'] = $result['devise'];
        echo "<script>alert('Le mot de passe a été mis à jour avec succès. Votre compte $login a été créer avec succès.');</script>";
        echo "<script>window.setTimeout(function() { window.location.href = 'profil.php'; }, 1000);</script>";
    } else {
        echo "Erreur lors de la mise à jour du mot de passe : " . $stmt->errorInfo()[2];
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BankLink</title>
        <link rel="shortcut icon" href="../img/favicon.png"><!-- Favicon -->
        <link rel="stylesheet" href="../style/style.css">
    </head>

    <body>
        <!-- Navigation -->
        <nav>
            <input id="nav-toggle" type="checkbox">
            <div class="logo">BankLink</div>
        </nav>
        <!-- Navigation -->
        <!-- Formulaire de confirmation de la création du compte client -->
        <div class="container">
            <div class="login form">
                <header>Confirmation de compte</header>
                <form action="confirmation.php" method="post" id="loginForm">
                    <input type="text" id="login" name="login" placeholder="Identifiant">
                    <div class="input-container">
                        <input type="password" id="password" name="password" placeholder="Mot de passe">
                        <span id="eyeIcon" class="eyeIcon material-symbols-outlined"></span>
                    </div>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirmation de mot de passe">
                    <input type="submit" class="button" value="Connexion">
                </form>
            </div>
        </div>
        <!-- Formulaire de confirmation de la création du compte client -->
        <!-- Script -->
        <script>
            // -- Affichage du mot de passe -- //
            var passwordInput = document.getElementById('password');
            var confirmPassInput = document.getElementById('confirmPassword');
            var eyeIcon = document.getElementById('eyeIcon');
            // Fonction pour afficher ou masquer le mot de passe lors du clic sur l'icône de l'oeil
            eyeIcon.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    // Si le type de l'input est un mot de passe, change le type de l'input en texte et affiche l'icône de l'oeil barré
                    passwordInput.type = 'text';
                    confirmPassInput.type = 'text';
                    eyeIcon.classList.add('hide-password');
                } else {
                    // Sinon, change le type de l'input en password et affiche l'icône de l'oeil
                    passwordInput.type = 'password';
                    confirmPassInput.type = 'password';
                    eyeIcon.classList.remove('hide-password');
                }
            });
        </script>
        <!-- Script -->
    </body>
</html>