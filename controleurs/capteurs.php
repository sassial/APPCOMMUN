<?php
// Fichier: controleurs/capteurs.php

require_once(__DIR__ . '/../modele/connexion.php'); // Pour les dispositifs locaux
require_once(__DIR__ . '/../modele/connexion_commune.php'); 
require_once(__DIR__ . '/../modele/requetes.capteurs.php');
require_once(__DIR__ . '/../modele/requetes.gestion.php'); 

if (!isset($_SESSION['utilisateur'])) {
    header('Location: index.php?cible=utilisateurs&fonction=login');
    exit();
}

$function = $_GET['fonction'] ?? 'affichage';

switch ($function) {
    case 'gestion':
        // ... (votre code de gestion reste ici, il est parfait)
        // Au début de 'case gestion:'
        if ($_SESSION['utilisateur']['role'] !== 'admin') {
          header('Location: index.php?cible=utilisateurs&fonction=accueil'); // Ou afficher une erreur
          exit();
        }
        if (isset($_POST['action']) && $_POST['action'] === 'supprimer') { /* ... */ }
        if (isset($_POST['action']) && $_POST['action'] === 'ajouter') { /* ... */ }
        $dispositifs = listerDispositifs($bdd);
        $vue = "gestion";
        break;
    
    case 'affichage':
        // --- NOUVELLE LOGIQUE DYNAMIQUE ---
        // 1. Récupérer tous les dispositifs de notre BDD locale
        $dispositifs = listerDispositifs($bdd);
        
        // 2. Récupérer les données pour chaque capteur
        $donnees_capteurs = [];
        foreach ($dispositifs as $d) {
            if ($d['type'] === 'capteur') {
                if ($d['nom_table_bdd'] === 'CapteurTempHum') {
                    $donnees_capteurs[$d['id']] = recupererDonneesTempHum($bdd_commune);
                } else {
                    $donnees_capteurs[$d['id']] = recupererDonneesCapteur($bdd_commune, $d['nom_table_bdd']);
                }
            }
        }
        
        // 3. Récupérer l'état de tous les actionneurs
       $etats_actionneurs = $bdd->query('SELECT id_dispositif, etat FROM etats_actionneurs')->fetchAll(PDO::FETCH_KEY_PAIR);

    // 4. Récupérer la météo externe
    $temperatureExterne = recupererTemperatureExterne();
    
    $vue = "affichage";
    break;
        
    default:
        $vue = "erreur404";
        break;
}

require_once(__DIR__ . '/../vues/' . $vue . '.php');