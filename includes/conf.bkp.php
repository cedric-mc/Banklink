<?php
// Fichier de configuration
$utilisateur = "";
$motdepasse = "";
$serveur = "";
$bdd = "";
try {
    $cnx = new PDO("mysql:host=$serveur;dbname=$bdd;charset=utf8mb4", $utilisateur, $motdepasse);
    // charset=utf8mb4 : pour gérer les emojis, les caractères spéciaux et les accents dans la BDD
    $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// Variables pour l'envoi de mail
$mailHost = "";
$mailPort = "";
$email = "";
$mdp = "";
// Lien vers la page de confirmation
$lien = "";

// Lien vers la page d'Accueil du site
$lienAccueil = "";
?>