<?php
// Fichier: controleurs/capteurs.php (Version finale propre)

require_once(__DIR__ . '/../modele/connexion.php');
require_once(__DIR__ . '/../modele/connexion_commune.php');
require_once(__DIR__ . '/../modele/requetes.capteurs.php');
require_once(__DIR__ . '/../modele/requetes.gestion.php');

if (!isset($_SESSION['utilisateur'])) {
    header('Location: index.php?cible=utilisateurs&fonction=login');
    exit();
}

// Traitement des actions POST de la page de gestion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['utilisateur']['role']) && $_SESSION['utilisateur']['role'] === 'admin') {
    $action = $_GET['action'] ?? null;
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'toggle' && $id > 0) {
        basculerEtat($bdd, $id);
    }
    if ($action === 'update_seuil' && $id > 0) {
        $seuil = !empty($_POST['seuil']) ? (float)$_POST['seuil'] : null;
        mettreAJourSeuil($bdd, $id, $seuil);
    }
    
    header('Location: index.php?cible=capteurs&fonction=gestion');
    exit();
}

$function = $_GET['fonction'] ?? 'affichage';
$vue = "erreur404"; // Vue par défaut

switch ($function) {
    case 'gestion':
        if (isset($_SESSION['utilisateur']['role']) && $_SESSION['utilisateur']['role'] === 'admin') {
            $capteursActifs = listerDispositifsParEtat($bdd, 'capteur', true);
            $capteursInactifs = listerDispositifsParEtat($bdd, 'capteur', false);
            $tousLesCapteurs = listerTousLesCapteurs($bdd);
            $vue = "gestion";
        } else {
            header('Location: index.php?cible=utilisateurs&fonction=accueil');
            exit();
        }
        break;
    
    case 'affichage':
    $dispositifs_capteurs = listerDispositifsParEtat($bdd, 'capteur', true);
    $dispositifs_actionneurs = listerDispositifsParEtat($bdd, 'actionneur', true);
    $etats_actionneurs = $bdd->query('SELECT id_dispositif, etat FROM etats_actionneurs')->fetchAll(PDO::FETCH_KEY_PAIR);
    $citation = recupererCitationDuJour();
    
    // On récupère les seuils une seule fois
    $tous_les_capteurs_details = listerTousLesCapteurs($bdd);
    $seuils_graphiques = array_column($tous_les_capteurs_details, 'seuil', 'id');

    // On récupère les données initiales pour les capteurs actifs
    $donnees_capteurs = [];
    foreach ($dispositifs_capteurs as $capteur) {
        $id = $capteur['id'];
        $nom_table = $capteur['nom_table_bdd'];
        
        if ($nom_table === 'capteur_temp_hum') {
            $donnees_capteurs[$id] = recupererDonneesTempHum($bdd_commune);
        } else {
            $donnees_capteurs[$id] = recupererDonneesCapteur($bdd_commune, $nom_table);
        }
    }
    $vue = "affichage";
    break;
}

require_once(__DIR__ . '/../vues/' . $vue . '.php');