<?php
include("conf.php");
function checkNumber($number) { // Vérifie si le nombre est négatif ou positif, et renvoie la classe CSS correspondante
    if ($number < 0) {
        return "montant-neg";
    } else {
        return "montant-pos";
    }
}

function spacedText($input) { // Ajoute des espaces dans un numéro de carte bancaire ou n° siren
    $length = strlen($input);
    switch ($length) {
        case 9:
            return preg_replace("/^(\d{3})(\d{3})(\d{3})$/", "$1 $2 $3", $input);
        case 16:
            return preg_replace("/^(\d{4})(\d{4})(\d{4})(\d{4})$/", "$1 $2 $3 $4", $input);
        default:
            return $input;
    }
}

function getDevise($devise) { // Renvoie le symbole de la devise en fonction de la valeur de la variable
    if ($devise == "USD") {
        return "$";
    } elseif ($devise == "EUR") {
        return "€";
    }
}

function masquerNumeroCarte($numeroCarte) {
    $longueur = strlen($numeroCarte);
    $etoiles = str_repeat('*', $longueur - 4);
    $debut = substr($numeroCarte, 0, 2);
    $fin = substr($numeroCarte, -2);
    return $debut . $etoiles . $fin;
}

function format_date($date) {
    return date_format(date_create($date), 'd/m/Y');
}

function getColorByAmountRange($montant) {
    // Assurer que le montant est dans la plage de 0 à -2000
    $montant = max(-2000, min(0, $montant));

    // Diviser la plage de montants en tranches de 100
    $tranche = 100;

    // Calculer le numéro de tranche pour le montant donné
    $tranche_number = ceil(abs($montant) / $tranche);

    // Calculer la composante verte en fonction du numéro de tranche
    $green = max(0, min(255, 255 - ($tranche_number * 15)));

    // Formatage des valeurs RGB en une chaîne hexadécimale
    $color = sprintf("#ff%02x00", $green);

    return $color;
}

$req = $cnx->prepare("SELECT MIN(dateRemise) FROM REMISE;");
$req->execute();
$date = $req->fetchColumn();
$d_fin = date("Y-m-d");
$req->closeCursor();