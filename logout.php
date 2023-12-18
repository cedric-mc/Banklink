<?php
// logout.php : Déconnexion de l'utilisateur ou redirection vers la page de profil

session_start();
// Si l'utilisateur n'est pas connecté, on le redirige vers la page d'accueil
if (empty($_SESSION['idUser'])) {
    header("Location: index.php");
    exit();
}
// Si l'utilisateur est connecté, on le redirige vers la page de profil correspondante (selon le type du compte)
if (empty($_GET['logout'])) {
    if ($_SESSION['type'] == "admin") {
        header("Location: admin/profil.php");
        exit();
    } else if ($_SESSION['type'] == "client") {
        header("Location: marchand/profil.php");
        exit();
    } else if ($_SESSION['type'] == "po") {
        header("Location: product-owner/profil.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}

// Si l'utilisateur demande explicitement la déconnexion, on détruit la session
if ($_GET['logout'] == "true") {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
