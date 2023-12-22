<?php
session_start();

if (isset($_SESSION['lockout_time']) && $_SESSION['lockout_time'] > time()) {
    $remainingTime = $_SESSION['lockout_time'] - time();
    echo $remainingTime;
} else {
    echo 0; // Le temps est écoulé ou n'est pas défini
}
?>
