<?php
/**
 * Fonctions liées aux contrôleurs
 */

function crypterMdp($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}
?>