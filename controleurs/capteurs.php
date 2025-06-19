<?php
// Fichier: controleurs/capteurs.php (Version finale et propre)

// --- 1. Inclusions des modèles nécessaires ---
require_once(__DIR__ . '/../modele/requetes.capteurs.php');  // Fonctions pour lire les données des capteurs
require_once(__DIR__ . '/../modele/requetes.gestion.php');   // Fonctions pour la page de gestion

// --- 2. Vérification de la session utilisateur ---
// Personne non connectée ne peut accéder à cette section.
if (!isset($_SESSION['utilisateur'])) {
    header('Location: index.php?cible=utilisateurs&fonction=login');
    exit();
}

// --- 3. Traitement des actions POST (depuis la page de gestion) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Seul un admin peut effectuer des modifications.
    if (isset($_SESSION['utilisateur']['role']) && $_SESSION['utilisateur']['role'] === 'admin' && isset($_POST['id'])) {
        $action = $_GET['action'] ?? null;
        $id = (int)$_POST['id'];

        if ($action === 'toggle') {
            basculerEtat($bdd, $id);
        }
    }
    // Après une action POST, on redirige pour éviter le re-soumission du formulaire.
    header('Location: index.php?cible=capteurs&fonction=gestion');
    exit();
}

// --- 4. Routage GET pour afficher la bonne page ---
$function = $_GET['fonction'] ?? 'affichage';

switch ($function) {
    case 'gestion':        
        // Préparation des données pour la vue de gestion
        $capteursActifs = listerDispositifsParEtat($bdd, 'capteur', true);
        $capteursInactifs = listerDispositifsParEtat($bdd, 'capteur', false);
        $actionneursActifs = listerDispositifsParEtat($bdd, 'actionneur', true);
        $actionneursInactifs = listerDispositifsParEtat($bdd, 'actionneur', false);
        
        $vue = "gestion";
        break;
    
    case 'affichage':
        // Préparation des données pour la vue du tableau de bord
        
        // a) On récupère la liste des dispositifs actifs
        $dispositifs_capteurs = listerDispositifsParEtat($bdd, 'capteur', true);
        $dispositifs_actionneurs = listerDispositifsParEtat($bdd, 'actionneur', true);
        
        // b) On récupère les données pour chaque capteur actif
        $donnees_capteurs = [];
        foreach ($dispositifs_capteurs as &$capteur) {
            $capteur['nom_table_bdd'] = getNomTable($capteur['nom']);
            $nom_table = $capteur['nom_table_bdd'];
            if ($nom_table === 'capteur_temp_hum') {
                $donnees_capteurs[$capteur['id']] = recupererDonneesTempHum($bdd_commune);
            } else {
                $donnees_capteurs[$capteur['id']] = recupererDonneesCapteur($bdd_commune, $nom_table);
            }
        }
        
        // c) On récupère l'état des actionneurs
        $etats_actionneurs = $bdd->query('SELECT id_dispositif, etat FROM etats_actionneurs')->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // d) On récupère les données du service web externe
        $temperatureExterne = recupererTemperatureExterne();
        
        $vue = "affichage";
        break;
        
    default:
        $vue = "erreur404";
        break;
}

require_once(__DIR__ . '/../vues/' . $vue . '.php');