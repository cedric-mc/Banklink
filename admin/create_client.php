<?php
// Inclure votre fichier de configuration de base de données
include("../includes/conf.php");

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $siren = $_POST["siren"];
    $raisonSociale = $_POST["raisonSociale"];
    $devise = $_POST["devise"];
    $numCarte = $_POST["carte"];
    $reseau = $_POST["reseau"];
    $login = $_POST["login"];
    $email = $_POST["email"];

    session_start();
    if (empty($_SESSION["idUser"]) || $_SESSION["type"] != "admin") {
        header("Location: ../");
        exit();
    }

    // Initialiser un tableau pour stocker les messages d'erreur
    $errorMessages = array();

    // Vérifier si les valeurs ne sont pas déjà utilisées
    $checkQuery = 
    "SELECT siren, 
            raisonSociale, 
            numCarte, 
            login, 
            mail 
    FROM CLIENT_TEMP 
    WHERE siren = :siren 
        OR raisonSociale = :raisonSociale 
        OR numCarte = :numCarte 
        OR login = :login 
        OR mail = :email";
    $stmtCheck = $cnx->prepare($checkQuery);
    $stmtCheck->bindParam(":siren", $siren);
    $stmtCheck->bindParam(":raisonSociale", $raisonSociale);
    $stmtCheck->bindParam(":numCarte", $numCarte);
    $stmtCheck->bindParam(":login", $login);
    $stmtCheck->bindParam(":email", $email);
    $stmtCheck->execute();
    // Si les valeurs sont déjà utilisées, ajouter un message d'erreur
    if ($stmtCheck->rowCount() > 0) {
        $existingData = $stmtCheck->fetchAll(PDO::FETCH_ASSOC);
        foreach ($existingData as $existingRow) {
            if ($siren == $existingRow["siren"]) {
                $errorMessages[] = "Le N° de SIREN ($siren) est déjà utilisé.";
            }
            if ($raisonSociale == $existingRow["raisonSociale"]) {
                $errorMessages[] = "La Raison Sociale ($raisonSociale) est déjà utilisée.";
            }
            if ($numCarte == $existingRow["numCarte"]) {
                $errorMessages[] = "Le N° de Carte ($numCarte) est déjà utilisé.";
            }
            if ($login == $existingRow["login"]) {
                $errorMessages[] = "Le Login ($login) est déjà utilisé.";
            }
            if ($email == $existingRow["mail"]) {
                $errorMessages[] = "L'Email ($email) est déjà utilisé.";
            }
        }
        echo 1;
        echo "<script>alert('Erreur lors de la création du compte client')</script>";
        echo "<script>window.location.replace('./');</script>";
        exit();
    } else { // Sinon, insérer les données dans la table CLIENT_TEMP
        // Préparer la requête d'insertion
        $insertQuery = 
        "INSERT INTO CLIENT_TEMP (siren, raisonSociale, devise, numCarte, reseau, login, mail) 
        VALUES (:siren, :raisonSociale, :devise, :numCarte, :reseau, :login, :email)";
        $stmtInsert = $cnx->prepare($insertQuery);
        $stmtInsert->bindParam(":siren", $siren);
        $stmtInsert->bindParam(":raisonSociale", $raisonSociale);
        $stmtInsert->bindParam(":devise", $devise);
        $stmtInsert->bindParam(":numCarte", $numCarte);
        $stmtInsert->bindParam(":reseau", $reseau);
        $stmtInsert->bindParam(":login", $login);
        $stmtInsert->bindParam(":email", $email);
        // Exécuter la requête
        $stmtInsert->execute();
        // Vérifier si l'insertion a réussi
        if ($stmtInsert->rowCount() > 0) {
            echo 0;
            echo "<script>alert('Compte client créé avec succès')</script>";
            echo "<script>window.location.replace('./');</script>";
            exit();
        } else {
            echo 1;
            echo "<script>alert('Erreur lors de la création du compte client')</script>";
            echo "<script>window.location.replace('./');</script>";
            exit();
        }
    }
}
header("Location: ./");
