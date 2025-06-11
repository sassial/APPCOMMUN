<?php

// on inclut le fichier modèle contenant les appels à la BDD
include('./modele/requetes.capteurs.php');

// si la fonction n'est pas définie, on choisit d'afficher l'accueil
if (!isset($_GET['fonction']) || empty($_GET['fonction'])) {
    $function = "gestion";
} else {
    $function = $_GET['fonction'];
}

switch ($function) {
    
    case 'gestion':
        //affichage de l'accueil
        $vue = "gestion";
        break;
    
    case 'affichage':
        //affichage de l'inscription
        $vue = "affichage";
        break;
        
    default:
        // si aucune fonction ne correspond au paramètre function passé en GET
        $vue = "erreur404";
}

include ('vues/' . $vue . '.php');