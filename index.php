<?php
session_start();
// Si l'utilisateur est déjà connecté, on le redirige vers la page correspondant à son type
if (isset($_SESSION['type'])) {
    header("Location: " . $_SESSION['type'] . "/");
}

$errorMessages = [
    0 => "",
    1 => "Veuillez saisir un identifiant et un mot de passe.",
    2 => "Identifiant incorrect.",
    3 => "Mot de passe incorrect. Tentative n°1 sur 3.",
    4 => "Mot de passe incorrect. Tentative n°2 sur 3. Attention, il ne vous reste qu'un essai.",
    5 => "Trop de tentatives. Veuillez réessayer dans "
];

if (isset($_SESSION['error'])) {
    $errorCode = $_SESSION['error'];
    if (isset($errorMessages[$errorCode])) {
        $errorMessage = $errorMessages[$errorCode];
    } else {
        $errorMessage = "Une erreur inconnue est survenue.";
    }
} else {
    $errorMessage = "";
}
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
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
        <script>
            var errorBlock = $('.error-block');
            var errorMessage = $('#error-message');

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

            function updateErrorMessage(remainingTime, specificErrorMessage) {
                if (remainingTime > 0) {
                    // Affichage du message d'erreur et du temps restant
                    remainingSeconds = remainingTime % 60;
                    remainingMinutes = Math.floor(remainingTime / 60);
                    if (remainingMinutes > 0 && remainingSeconds === 0) {
                        errorMessage.text(specificErrorMessage + remainingMinutes + " minutes.");
                    } else if (remainingMinutes > 0) {
                        errorMessage.text(specificErrorMessage + remainingMinutes + " minutes et " + remainingSeconds + " secondes.");
                    } else {
                        errorMessage.text(specificErrorMessage + remainingSeconds + " secondes.");
                    }
                    errorBlock.addClass('show-warning').show();
                } else if (specificErrorMessage !== "") {
                    if (specificErrorMessage === "Trop de tentatives. Veuillez réessayer dans ") {
                        errorBlock.hide().removeClass('show-warning');
                        return;
                    }
                    // Affichage du message d'erreur spécifique si disponible
                    errorMessage.text(specificErrorMessage);
                    errorBlock.addClass('show-warning').show();
                } else {
                    // Si le temps est écoulé, on cache le bloc d'erreur
                    errorBlock.hide().removeClass('show-warning');
                }
            }

            function fetchRemainingTime() {
                $.ajax({
                    url: 'time_left.php',
                    success: function(remainingTime) {
                        // Récupération du message d'erreur spécifique à la situation
                        var specificErrorMessage = "<?php echo $errorMessage; ?>";
                        updateErrorMessage(parseInt(remainingTime), specificErrorMessage);
                    }
                });
            }

            $(document).ready(function() {
                fetchRemainingTime(); // Appel initial
                setInterval(fetchRemainingTime, 1000); // Appel périodique
            });
        </script>
    </body>
</html>