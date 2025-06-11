<?php

// 1. On inclut la connexion à la BDD commune. La variable $bdd_commune est maintenant disponible.
include_once('./modele/connexion_commune.php'); 

// 2. On inclut le fichier avec les fonctions de requêtes. PHP connaît maintenant recupererDerniereMesure().
include_once('./modele/requetes.capteurs.php');

// si la fonction n'est pas définie, on choisit d'afficher l'accueil
if (!isset($_GET['fonction']) || empty($_GET['fonction'])) {
    $function = "gestion";
} else {
    $function = $_GET['fonction'];
}

switch ($function) {
    
    case 'gestion':
        $vue = "gestion";
        break;
    
    case 'affichage':
        // 3. On appelle la fonction. Cela fonctionne car elle a été chargée à l'étape 2.
        //    La variable $bdd_commune a été chargée à l'étape 1.
        $derniereMesure = recupererDerniereMesure($bdd_commune);

        $vue = "affichage";
        break;
        
    default:
        $vue = "erreur404";
}

include ('vues/' . $vue . '.php');