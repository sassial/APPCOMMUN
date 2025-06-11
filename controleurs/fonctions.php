<?php
/**
 * Fonctions liées aux contrôleurs
 */

function crypterMdp($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function nettoyerDonnees($data) {
    // S'assurer que la donnée n'est pas un tableau ou un objet avant de la traiter
    if (is_array($data) || is_object($data)) {
        return $data;
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}