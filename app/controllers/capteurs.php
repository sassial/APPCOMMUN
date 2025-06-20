<?php

require_once __DIR__ . '/../models/requetes.capteurs.php';
require_once __DIR__ . '/../models/requetes.gestion.php';

// Personne non connectée ne peut accéder à cette section.
if (!isset($_SESSION['utilisateur'])) {
    header('Location: index.php?cible=utilisateurs&fonction=login');
    exit();
}

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

$function = $_GET['fonction'] ?? 'affichage';

switch ($function) {
    case 'accueil':
        // Protège la page : si personne n'est connecté, on renvoie au login.
        if (!isset($_SESSION['utilisateur'])) {
            header('Location: index.php?cible=utilisateurs&fonction=login');
            exit();
        }
        // Récupère les données pour le dashboard personnel sur la page d'accueil.
        $donneesSonDetaillees = recupererDonneesDetaillees($bdd_commune, 'CapteurSon');

        $greenframePath = realpath(__DIR__ . '/../../public/assets/data/donnees-greenframe.json');
        $greenframeData = file_exists($greenframePath) ? json_decode(file_get_contents($greenframePath), true) : null;
        $ecoEnergy = getEcoStatus($greenframeData['energy'] ?? 0, 'energy');
        $ecoCarbon = getEcoStatus($greenframeData['carbon'] ?? 0, 'carbon');

        $vue = "capteurs/accueil";
        break;

    case 'gestion':
        if (isset($_GET['action']) && $_GET['action'] === 'toggle' && isset($_POST['id'])) {
            $id = (int) $_POST['id'];
            basculerEtat($bdd, $id);
        }

        // Always reload the updated lists
        $capteursActifs = listerDispositifsParEtat($bdd, 'capteur', true);
        $capteursInactifs = listerDispositifsParEtat($bdd, 'capteur', false);
        $actionneursActifs = listerDispositifsParEtat($bdd, 'actionneur', true);
        $actionneursInactifs = listerDispositifsParEtat($bdd, 'actionneur', false);

        $vue = "capteurs/gestion";
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
        
        $vue = "capteurs/affichage";
        break;
        
    default:
        $vue = "erreur404";
        break;
}

require_once(__DIR__ . '/../views/' . $vue . '.php');