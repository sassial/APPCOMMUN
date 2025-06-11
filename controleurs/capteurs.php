<?php

// ===================================================================
//              CORRECTION AVEC CHEMINS ABSOLUS ROBUSTES
// ===================================================================

// On inclut les fichiers en partant du dossier de ce fichier (__DIR__)
// C'est la méthode la plus fiable.
include_once(__DIR__ . '/../modele/connexion_commune.php'); 
include_once(__DIR__ . '/../modele/requetes.capteurs.php');

// On détermine la fonction à appeler, par défaut 'affichage'.
$function = $_GET['fonction'] ?? 'affichage';

// On utilise un switch pour gérer les différentes fonctions
switch ($function) {
    
    case 'gestion':
        $vue = "gestion";
        break;
    
    case 'affichage':
        // L'appel à la fonction est maintenant valide car le fichier a été inclus correctement
        $donneesSon = recupererDonneesCapteur($bdd_commune, 'Capteur Son');
        $donneesLumiere = recupererDonneesCapteur($bdd_commune, 'CapteurLumiere');
        $donneesProximite = recupererDonneesCapteur($bdd_commune, 'CapteurProximite');
        $donneesGaz = recupererDonneesCapteur($bdd_commune, 'CapteurGaz'); 

        // On définit la vue à charger
        $vue = "affichage";
        break;
        
    default:
        // Si la fonction demandée n'existe pas, on affiche une erreur 404.
        $vue = "erreur404";
        break;
}

// On inclut la vue correspondante.
include(__DIR__ . '/../vues/' . $vue . '.php');