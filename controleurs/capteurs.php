<?php
// Fichier: controleurs/capteurs.php

require_once(__DIR__ . '/../modele/requetes.capteurs.php');

$function = $_GET['fonction'] ?? 'affichage';

switch ($function) {
    case 'gestion':
        $vue = "gestion";
        break;
    
    case 'affichage':
        // On récupère les données pour chaque carte
        $donneesTempHum = recupererDonneesTempHum($bdd_commune); 
        $donneesLumiere = recupererDonneesCapteur($bdd_commune, 'CapteurLumiere');
        $donneesProximite = recupererDonneesCapteur($bdd_commune, 'CapteurProximite');
        $donneesGaz = recupererDonneesCapteur($bdd_commune, 'CapteurGaz'); 

        $vue = "affichage";
        break;
    
    
        
    default:
        $vue = "erreur404";
        break;
}

require_once(__DIR__ . '/../vues/' . $vue . '.php');