<?php
// controleurs/capteurs.php
session_start();

// 1) Chargement des connexions
require_once __DIR__ . '/../modele/connexion_commune.php'; // $bdd_commune
require_once __DIR__ . '/../modele/connexion.php';         // $bdd
require_once __DIR__ . '/../modele/requetes.capteurs.php';

// 2) Traitement des POST (toggle / updateSeuil)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_GET['action']  ?? null;
    $capteur = $_POST['capteur'] ?? null;

    switch ($action) {
        case 'toggle':
            basculerEtatCapteur($bdd, $capteur);
            break;

        case 'updateSeuil':
            $delta = (int) ($_POST['delta'] ?? 0);
            ajusterSeuilCapteur($bdd, $capteur, $delta);
            break;
    }

    // PRG : on recharge la page de gestion
    header('Location: index.php?cible=capteurs&fonction=gestion');
    exit;
}

// 3) Routage GET pour afficher gestion ou affichage
$function = $_GET['fonction'] ?? 'affichage';
switch ($function) {
    case 'gestion':
        // récupère la config locale
        $capteursActifs   = listerCapteurs($bdd, true);
        $capteursInactifs = listerCapteurs($bdd, false);
        $seuils           = recupererSeuils($bdd);
        $vue = 'gestion';
        break;

    case 'affichage':
        // récupère les mesures distantes
        $donneesTempHum   = recupererDonneesTempHum($bdd_commune);
        $donneesLumiere   = recupererDonneesCapteur($bdd_commune, 'CapteurLumiere');
        $donneesProximite = recupererDonneesCapteur($bdd_commune, 'CapteurProximite');
        $donneesGaz       = recupererDonneesCapteur($bdd_commune, 'CapteurGaz');
        $vue = 'affichage';
        break;

    default:
        $vue = 'erreur404';
        break;
}

require_once __DIR__ . '/../vues/' . $vue . '.php';
