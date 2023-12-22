<?php
// Inclure le chargeur automatique de classes
require_once("../vendor/autoload.php");

// Utiliser les classes PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fonction pour envoyer un email de confirmation pour l'inscription au client
function sendConfirmationEmail($to, $username)
{
    include("../includes/conf.php"); // Inclure le fichier de configuration de la base de données contenant la configuration pour le Serveur SMTP et le lien vers la page de confirmation

    // Créer un nouvel objet PHPMailer
    $mail = new PHPMailer(true);
    // Configurer PHPMailer pour utiliser SMTP
    $mail->isSMTP();
    $mail->Host = $mailHost;
    $mail->Port = $mailPort;
    $mail->SMTPSecure = 'ssl'; // Utiliser une connexion sécurisée SSL
    // Activer l'authentification SMTP
    $mail->SMTPAuth = true; // Activer l'authentification SMTP
    $mail->Username = $email;
    $mail->Password = $mdp;
    // Configurer l'expéditeur et le destinataire du mail
    $mail->setFrom($email, 'Banklink');
    $mail->addAddress($to, $username);
    // Préparer le contenu HTML du mail
    $htmlContent = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Email Professionnel</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .email-container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            border: 1px solid #dddddd;
        }
        .header {
            background-color: #0056b3;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            text-align: center;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <div class='header'>Confirmation de Compte</div>
        <div class='content'>
            <p>Bonjour $username,</p>
            <p>Nous sommes heureux de vous informer que votre compte a été activé avec succès sur notre plateforme. Votre identifiant unique est : <strong>$username</strong>.</p>
            <p>Pour accéder à votre compte, veuillez confirmer votre inscription en cliquant sur le lien ci-dessous :</p>
            <a href='$lien' class='button'>Activer Mon Compte</a>
            <p>Si vous rencontrez des difficultés pour accéder à votre compte, n'hésitez pas à contacter notre équipe de support client.</p>
        </div>
        <div class='footer'>
            Merci de choisir BankLink<br>
            Pour plus d'informations ou d'assistance, veuillez visiter notre <a href='$lienAccueil'>site</a>.
        </div>
    </div>
</body>
</html>";

    // Affecter le contenu HTML au corps du mail
    $mail->isHTML(true);
    $mail->Subject = 'Confirmation d\'inscription';
    $mail->Body = $htmlContent;
    $mail->CharSet = 'UTF-8';

    try { // Tenter d'envoyer le mail, si le mail est envoyé avec succès, afficher un message
        $mail->send();
        echo "<script>alert('Le mail de confirmation a été envoyé avec succès.')</script>";
        echo "<script>window.setTimeout(function() { window.location.href = './'; }, 1000);</script>";
    } catch (Exception $e) { // Si une erreur se produit lors de l'envoi du mail, afficher un message d'erreur
        echo "<script>alert('Erreur lors de l\'envoi du mail : ' . $mail->ErrorInfo)</script>";
        echo "<script>window.setTimeout(function() { window.location.href = './'; }, 1000);</script>";
    }
}

// Fonction pour accepter le client temporaire et créer un compte client
function accepterClientTemp($siren, $raisonSociale, $login, $devise, $numCarte, $reseau, $mail)
{
    include("../includes/conf.php"); // Inclure le fichier de configuration de la base de données

    // Insérer les données dans les tables UTILISATEUR et CLIENT
    $insertUserQuery = "INSERT INTO UTILISATEUR (login, password, type, mail) VALUES (?, ?, ?, ?)";
    $stmtInsertUser = $cnx->prepare($insertUserQuery);
    $stmtInsertUser->execute([$login, null, 'client', $mail]);
    $stmtInsertUser->closeCursor();

    // Obtenir l'idUser de l'utilisateur créé
    $selectIdUserQuery = "SELECT idUser FROM UTILISATEUR WHERE login = ?";
    $stmtSelectIdUser = $cnx->prepare($selectIdUserQuery);
    $stmtSelectIdUser->execute([$login]);
    $result = $stmtSelectIdUser->fetch(PDO::FETCH_ASSOC);
    $nextUserId = $result['idUser'];
    $stmtSelectIdUser->closeCursor();

    // Insérer les données dans la table CLIENT
    $insertClientQuery = "INSERT INTO CLIENT (siren, raisonSociale, devise, numCarte, reseau, suppr, idUser) VALUES (?, ?, ?, ?, ?, 0, ?)";
    $stmtInsertClient = $cnx->prepare($insertClientQuery);
    $stmtInsertClient->execute([$siren, $raisonSociale, $devise, $numCarte, $reseau, $nextUserId]);
    $stmtInsertClient->closeCursor();

    // Supprimer le client temporaire
    $deleteQuery = "DELETE FROM CLIENT_TEMP WHERE siren = ?";
    $stmtDelete = $cnx->prepare($deleteQuery);
    $stmtDelete->execute([$siren]);
    $stmtDelete->closeCursor();

    // Envoyer un mail au client
    sendConfirmationEmail($mail, $login);
}


// Fonction pour refuser le client temporaire
function refuserClientTemp($siren)
{
    include("../includes/conf.php"); // Inclure le fichier de configuration de la base de données

    // Supprimer le client temporaire
    $deleteQuery = "DELETE FROM CLIENT_TEMP WHERE siren = ?";
    $stmtDelete = $cnx->prepare($deleteQuery);
    $stmtDelete->execute([$siren]);
    $stmtDelete->closeCursor();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['yes'])) {
        // Récupérer les données du formulaire
        $siren = $_POST['siren'];
        $login = $_POST['login'];
        $raisonSociale = $_POST['raisonSociale'];
        $mail = $_POST['email'];
        $devise = $_POST['devise'];
        $carte = $_POST['carte'];
        $reseau = $_POST['reseau'];

        // Traiter l'action d'acceptation du client temporaire
        accepterClientTemp($siren, $raisonSociale, $login, $devise, $carte, $reseau, $mail);
        echo "<script>alert('Le client temporaire $siren a été traité avec succès.')</script>";
    } elseif (isset($_POST['no'])) {
        $siren = $_POST['siren'];
        // Traiter l'action de refus du client temporaire
        refuserClientTemp($siren);
        echo "<script>alert('Le client temporaire $siren n'a pas été traité.')</script>";
    }
    echo "<script>window.setTimeout(function() { window.location.href = './'; }, 1000);</script>"; // Rediriger vers la page d'accueil
    header("Location: marchands.php");
}
