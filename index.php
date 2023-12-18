<?php
session_start();
// Si l'utilisateur est déjà connecté, on le redirige vers la page correspondant à son type
if (isset($_SESSION['type'])) {
    header("Location: " . $_SESSION['type'] . "/");
}
// Si la variable de session lockout_time existe et que le temps de lockout n'est pas dépassé
if (isset($_SESSION['lockout_time']) && $_SESSION['lockout_time'] > time()) {
    $_SESSION['error'] = "Trop de tentatives. Veuillez réessayer plus tard. Il vous reste " . ($_SESSION['lockout_time'] - time()) . " secondes.";
} elseif (isset($_SESSION['lockout_time']) && $_SESSION['lockout_time'] <= time()) { // Si le temps de lockout est dépassé
    // Réinitialiser lockout_time et le nombre d'essais
    unset($_SESSION['lockout_time']);
    $_SESSION['login_attempts'] = 0;
    $_SESSION['error'] = "";
}
// Récupération de l'erreur depuis la session
$error = isset($_SESSION['error']) ? $_SESSION['error'] : "";
// Suppression de l'erreur de la session pour ne pas l'afficher plusieurs fois
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Banklink</title>
        <link rel="shortcut icon" href="img/favicon.png"><!-- Favicon -->
        <link rel="stylesheet" href="style/style.css"><!-- Style -->
    </head>

    <body>
        <nav>
            <input id="nav-toggle" type="checkbox">
            <div class="logo">BankLink</div>
        </nav>

        <div class="container">
            <div class="login form">
                <header>Se connecter</header>
                <form id="loginForm" action="auth.php" method="post">
                    <input type="text" id="login" name="login" placeholder="Identifiant">
                    <div class="input-container">
                        <input type="password" id="password" name="password" placeholder="Mot de passe">
                        <span id="eyeIcon" class="eyeIcon material-symbols-outlined"></span>
                    </div>
                    <input type="submit" class="button" value="Connexion">
                </form>
                <div class="error-block">
                    <p id="error-message"></p>
                </div>
            </div>
        </div>
        <script>
            // -- Affichage du mot de passe -- //
            var passwordInput = document.getElementById('password');
            var eyeIcon = document.getElementById('eyeIcon');
            // Fonction pour afficher ou masquer le mot de passe lors du clic sur l'icône de l'oeil
            eyeIcon.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    // Si le type de l'input est un mot de passe, change le type de l'input en texte et affiche l'icône de l'oeil barré
                    passwordInput.type = 'text';
                    eyeIcon.classList.add('hide-password');
                } else {
                    // Sinon, change le type de l'input en password et affiche l'icône de l'oeil
                    passwordInput.type = 'password';
                    eyeIcon.classList.remove('hide-password');
                }
            });

            // -- Affichage de l'erreur -- //
            var error = "<?php echo $error; ?>";
            var errorBlock = document.querySelector('.error-block');
            var errorMessage = document.querySelector('#error-message');
            if (error == "") {
                // Si le message est vide, on cache le bloc d'erreur
                errorBlock.style.display = "none";
                errorBlock.classList.remove('show-warning');
            } else {
                // Sinon on affiche le bloc d'erreur avec le message
                errorBlock.classList.add('show-warning');
                errorMessage.innerHTML = error;
            }
        </script>
    </body>
</html>