<?php

define('BASE_PATH', '/APPDEUX/APPCOMMUN');

ini_set('display_errors', 1);
error_reporting(E_ALL); // Affiche toutes les erreurs pour un meilleur débogage

include_once("controleurs/fonctions.php");

// On identifie le contrôleur à appeler
if (isset($_GET['cible']) && !empty($_GET['cible'])) {
    $url = $_GET['cible'];
} else {
    $url = 'utilisateurs'; // Page par défaut
}

// On appelle le contrôleur
$controller_file = 'controleurs/' . $url . '.php';

if (file_exists($controller_file)) {
    include($controller_file);
} else {
    // Si le contrôleur n'existe pas, on affiche une erreur 404
    include('vues/erreur404.php');
}