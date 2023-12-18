<?php
session_start();
$_SESSION['error'] = ""; // Variable contenant l'erreur à afficher
if (isset($_SESSION['lockout_time']) && $_SESSION['lockout_time'] > time()) { // Si la temps de lockout n'est pas dépassé
    $_SESSION['error'] = "Trop de tentatives. Veuillez réessayer plus tard. Il vous reste " . ($_SESSION['lockout_time'] - time()) . " secondes.";
    header('Location: ./');
    exit;
} elseif (isset($_SESSION['lockout_time']) && $_SESSION['lockout_time'] <= time()) { // Si le temps de lockout est dépassé
    // Réinitialiser lockout_time et le nombre d'essais
    unset($_SESSION['lockout_time']);
    $_SESSION['login_attempts'] = 0;
}

// Initialisation du nombre d'essais
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// S'il manque un champ dans le formulaire de connexion, on affiche une erreur
if (empty($_POST['login']) || empty($_POST['password'])) {
    $_SESSION['error'] = "Veuillez saisir un identifiant et un mot de passe.";
    header('Location: ./');
    exit();
}
$login = $_POST['login'];
$password = $_POST['password'];

include('includes/conf.php');
include('includes/requetes.php');

// Si ce n'est pas un try catch, alors le script s'arrête à la première erreur
try {
    // Vérification du login
    $requete = $cnx->prepare($requetes["select_user"]);
    $requete->bindParam(':login', $login, PDO::PARAM_STR);
    $requete->execute();
    $resultat = $requete->fetch(PDO::FETCH_ASSOC);

    if ($resultat) {
        // Vérification du mot de passe
        if (password_verify($password, $resultat['password'])) {
            $_SESSION['idUser'] = $resultat['idUser'];
            $_SESSION['login'] = $login;
            $_SESSION['type'] = $resultat['type'];
            $_SESSION['mail'] = $resultat['mail'];
            $requete->closeCursor();

            // Récupération du siren du client
            if ($_SESSION['type'] == 'client') {
                $req = $cnx->prepare("SELECT siren FROM CLIENT WHERE idUser = :idUser");
                $req->bindParam(':idUser', $_SESSION['idUser'], PDO::PARAM_INT);
                $req->execute();
                $result = $req->fetch(PDO::FETCH_ASSOC);
                $_SESSION['siren'] = $result['siren'];
                $req->closeCursor();
            }
            unset($_SESSION['login_attempts']); // Réinitialisation du nombre d'essais
            header("Location: " . $_SESSION['type'] . "/");
            exit;
        } else { // Si le mot de passe est incorrect
            $_SESSION['login_attempts']++; // Incrémentation du nombre d'essais
            if ($_SESSION['login_attempts'] < 3) { // Si le nombre d'essais est inférieur à 3
                $_SESSION['error'] = "Mot de passe incorrect. Tentative " . $_SESSION['login_attempts'] . " sur 3."; // Nouvelle erreur
            } else { // Si le nombre d'essais est supérieur ou égal à 3
                $_SESSION['lockout_time'] = time() + (5 * 60); // On définit le temps de lockout à 5 minutes
                $_SESSION['error'] = "Trop de tentatives. Veuillez réessayer plus tard. Il vous reste " . ($_SESSION['lockout_time'] - time()) . " secondes."; // Nouvelle erreur avec le temps restant
            }
            header('Location: ./');
            exit();
        }
    } else { // Si le login est incorrect
        $_SESSION['error'] = "Identifiant incorrect."; // Nouvelle erreur
        header('Location: ./');
        exit;
    }
} catch (PDOException $e) {
    // En cas d'erreur, on affiche le message sur la page d'accueil
    $_SESSION['error'] = "Erreur : " . $e->getMessage();
    header('Location: ./');
    exit;
}
?>