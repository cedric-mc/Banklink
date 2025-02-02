<?php
// Inclure votre fichier de configuration de base de données
include("../includes/conf.php");

// Inclure le chargeur automatique de classes
require_once("../vendor/autoload.php");

// Utiliser les classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer l'ID de l'utilisateur
    $idUser = $_POST['idUser'];

    // Vérifier si l'utilisateur a choisi de supprimer le compte
    if (isset($_POST['yes'])) {
        // Récupérer les informations de l'utilisateur
        $login = $_POST['login'];
        $raisonSociale = $_POST['raisonSociale'];
        $mail = $_POST['mail'];

        // Supprimer le client et changer le mot de passe
        supprimerClient($idUser, $login, $mail);
        echo "<script>alert('Compte client supprimé.')</script>";
        // Le message d'alert ne s'affiche pas avec $login, comment faire ? Il affiche plutôt 'Compte client supprimé.' sans le nom du client
        // J'ai essayé avec $raisonSociale, $mail, $idUser, mais ça ne fonctionne pas non plus
        // Comment convertir le $login en string ?
    } elseif (isset($_POST['no'])) {
        // Annuler la suppression
        annulerSuppression($idUser);
        echo "<script>alert('Compte client non supprimé.')</script>";
    }
    echo "<script>window.location.replace('./');</script>";
}

// Fonction pour supprimer le client avec changement de mot de passe
function supprimerClient($idUser, $login, $mail) {
    updatePassword($idUser);
    updateClient($idUser, 2);
    sendConfirmationEmail($mail, $login);
}

// Fonction pour annuler la suppression
function annulerSuppression($idUser) {
    // Rétablir le statut du client
    updateClient($idUser, 0);
}

// Fonction pour mettre à jour le mot de passe
function updatePassword($idUser) {
    global $cnx;
    // Préparer la requête SQL
    $updatePasswordQuery = "UPDATE UTILISATEUR SET password = 'product' WHERE idUser = ?";
    $stmtUpdatePassword = $cnx->prepare($updatePasswordQuery);
    // Exécuter la requête avec l'ID de l'utilisateur
    $stmtUpdatePassword->execute([$idUser]);
    $stmtUpdatePassword->closeCursor(); // Fermer le curseur
}

// Fonction pour mettre à jour le statut du client
function updateClient($idUser, $suppr) {
    // Utiliser la connexion à la base de données
    global $cnx;
    // Préparer la requête SQL pour mettre à jour le statut de suppression
    $updateSupprQuery = "UPDATE CLIENT SET suppr = ? WHERE idUser = ?";
    // Préparer la requête
    $stmtUpdateSuppr = $cnx->prepare($updateSupprQuery);
    // Exécuter la requête avec le nouveau statut et l'ID de l'utilisateur
    $stmtUpdateSuppr->execute([$suppr, $idUser]);
    $stmtUpdateSuppr->closeCursor(); // Fermer le curseur
}

// Fonction pour envoyer un email de confirmation de suppression
function sendConfirmationEmail($to, $username) {
    // Utiliser les variables globales pour la configuration du mail
    global $mailHost, $mailPort, $email, $mdp;

    // Créer un nouvel objet PHPMailer et configuration de PHPMailer pour utiliser SMTP
    $mail = new PHPMailer(true);
    $mail->isSMTP();

    // Configurer le serveur SMTP
    $mail->Host = $mailHost;
    $mail->Port = $mailPort;
    $mail->SMTPSecure = 'ssl';

    // Activer l'authentification SMTP
    $mail->SMTPAuth = true;
    $mail->Username = $email;
    $mail->Password = $mdp;

    // Configurer l'expéditeur et le destinataire du mail
    $mail->setFrom($email, 'Banklink');
    $mail->addAddress($to, $username);

    // Configurer le contenu du mail
    $mail->isHTML(true);
    $mail->Subject = 'Confirmation de Suppression';
    $mail->Body = "Bonjour,<br>
    J'ai le regret de vous annoncé que votre compte Bankink a été supprimé. De ce fait vous n'aurez plus accès à votre compte.<br>
    Cordialement,<br>Banklink";
    $mail->CharSet = 'UTF-8';

    try { // Tenter d'envoyer le mail, si le mail est envoyé avec succès, afficher un message
        $mail->send();
        echo "<script>alert('Confirmation de Suppression envoyé avec succès.')</script>";
        echo "<script>window.location.replace('./');</script>";
    } catch (Exception $e) { // Si une erreur se produit lors de l'envoi du mail, afficher un message d'erreur
        echo "<script>alert('Erreur lors de l\'envoi du mail : ' . $mail->ErrorInfo)</script>";
        echo "<script>window.location.replace('./');</script>";
    }
}