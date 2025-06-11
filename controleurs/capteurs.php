<?php

// 1. On inclut la connexion à la BDD commune.
include_once('./modele/connexion_commune.php'); 

// 2. On inclut le fichier avec les fonctions de requêtes.
include_once('./modele/requetes.capteurs.php');

// On détermine la fonction à appeler, par défaut 'affichage'.
$function = $_GET['fonction'] ?? 'affichage';

// On utilise un switch pour gérer les différentes fonctions
switch ($function) {
    
    case 'gestion':
        $vue = "gestion";
        break;
    
  
   case 'affichage':
        // On récupère les données pour chaque capteur
        $donneesSon = recupererDonneesCapteur($bdd_commune, 'Capteur Son');
        $donneesLumiere = recupererDonneesCapteur($bdd_commune, 'CapteurLumiere');
        $donneesProximite = recupererDonneesCapteur($bdd_commune, 'CapteurProximite');
        // ON AJOUTE L'APPEL POUR LE CAPTEUR DE GAZ
        $donneesGaz = recupererDonneesCapteur($bdd_commune, 'CapteurGaz'); 

        $vue = "affichage";
        break;

        
    default:
        // Si la fonction demandée n'existe pas, on affiche une erreur 404.
        $vue = "erreur404";
        break;
}

// On inclut la vue correspondante.
include ('vues/' . $vue . '.php');