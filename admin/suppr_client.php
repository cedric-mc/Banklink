<?php
// Inclure votre fichier de configuration de base de données
include("../includes/conf.php");

// Récupérer l'identifiant de l'utilisateur à supprimer depuis la variable GET
if (!empty($_GET['id'])) {
    // Requête SQL pour mettre à jour la valeur de suppr à 1
    $updateQuery = "UPDATE CLIENT SET suppr = 1 WHERE idUser = :idUser";
    $stmt = $cnx->prepare($updateQuery);
    $stmt->bindParam(':idUser', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();

    // Rediriger vers la page d'accueil
    header("Location: ./?etat=suppr");
    exit();
}
header("Location: ./");
?>