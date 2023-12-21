<?php
// Inclure votre fichier de configuration de base de données
include("../includes/conf.php");

// Récupérer l'identifiant de l'utilisateur à supprimer depuis la variable GET
if (isset($_GET['id'])) {
    $idUserToSuppr = $_GET['id'];
    // Requête SQL pour mettre à jour la valeur de suppr à 1
    $updateQuery = "UPDATE CLIENT SET suppr = 1 WHERE idUser = :idUser";
    $stmt = $cnx->prepare($updateQuery);
    $stmt->bindParam(':idUser', $idUserToSuppr, PDO::PARAM_INT);
    $stmt->execute();

    // Rediriger vers la page précédente tout en affichant un message de succès
//    header("Location: ./?suppression=1");
}
header("Location: ./");
?>