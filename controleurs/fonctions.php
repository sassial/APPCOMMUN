<?php
/**
 * Fonctions liées aux contrôleurs
 */

function crypterMdp($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function nettoyerDonnees($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>