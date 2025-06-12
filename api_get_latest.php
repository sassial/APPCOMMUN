<?php
// Fichier : APPCOMMUN/api_get_latest.php

// On indique que la réponse sera du JSON
header('Content-Type: application/json');

// On inclut les fichiers nécessaires pour accéder à la BDD et aux fonctions
require_once(__DIR__ . '/modele/connexion_commune.php'); 
require_once(__DIR__ . '/modele/requetes.capteurs.php');

// On récupère les données fraîches pour chaque capteur
// Note: la fonction pour Temp/Hum est nouvelle (voir étape 3)
$donnees = [
    'tempHum'   => recupererDonneesTempHum($bdd_commune),
    'lumiere'   => recupererDonneesCapteur($bdd_commune, 'CapteurLumiere'),
    'proximite' => recupererDonneesCapteur($bdd_commune, 'CapteurProximite'),
    'gaz'       => recupererDonneesCapteur($bdd_commune, 'CapteurGaz')
];

// On encode le tableau en JSON et on l'affiche. C'est ce que le JavaScript recevra.
echo json_encode($donnees);