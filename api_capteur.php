<?php
// Fichier : api_capteur.php (VERSION FINALE AVEC ALERTES MULTIPLES)

// --- INCLUSIONS NÉCESSAIRES ---
require_once(__DIR__ . '/modele/connexion_commune.php');
require_once(__DIR__ . '/controleurs/fonctions.php');

// --- CONFIGURATION CENTRALISÉE DES CAPTEURS ET DES ALERTES ---
$config_capteurs = [
    'son' => [
        'id_capteur' => 1,
        'nom_table'  => '`Capteur Son`', // Nom de la table dans la BDD commune
        'seuil'      => 85, // dB
        'sujet'      => "Alerte Sonore : Niveau de bruit élevé !",
        'message'    => "Le capteur sonore a détecté une valeur de <strong>{valeur} dB</strong>, ce qui dépasse le seuil d'alerte de {seuil} dB."
    ],
    'lumiere' => [
        'id_capteur' => 2,
        'nom_table'  => '`CapteurLumiere`',
        'col_valeur' => 'valeur_luminosite', // Nom de la colonne spécifique
        'col_temps'  => 'date_mesure',       // Nom de la colonne spécifique
        'seuil'      => 2000, // lux
        'sujet'      => "Alerte Luminosité : Niveau de lumière anormal !",
        'message'    => "Le capteur de luminosité a détecté une valeur de <strong>{valeur} lux</strong>, dépassant le seuil de {seuil} lux."
    ],
    'proximite' => [
        'id_capteur' => 3,
        'nom_table'  => '`CapteurProximite`',
        'seuil'      => 5, // cm (alerte si un objet est TROP PROCHE)
        'type_alerte'=> 'inferieur', // On alerte si la valeur est INFÉRIEURE au seuil
        'sujet'      => "Alerte de Proximité : Obstacle détecté !",
        'message'    => "Le capteur de proximité a détecté un obstacle à <strong>{valeur} cm</strong>, ce qui est dans la zone d'alerte (< {seuil} cm)."
    ],
    'gaz' => [
        'id_capteur' => 4,
        'nom_table'  => '`CapteurGaz`',
        'seuil'      => 1000, // ppm
        'sujet'      => "Alerte de Sécurité : Niveau de Gaz Élevé !",
        'message'    => "Le capteur de gaz a détecté une valeur de <strong>{valeur} ppm</strong>, ce qui dépasse le seuil de sécurité de {seuil} ppm."
    ],
    'temperature' => [ // Pour le capteur Temp/Hum
        'id_capteur' => 5,
        'nom_table'  => '`CapteurTempHum`',
        'col_valeur' => 'temperature', // On vérifie la température
        'seuil'      => 30, // °C
        'sujet'      => "Alerte Température : Chaleur excessive détectée !",
        'message'    => "Le capteur de température a détecté une valeur de <strong>{valeur} °C</strong>, dépassant le seuil de {seuil} °C."
    ]
    // Ajoutez d'autres capteurs ici...
];


// --- TRAITEMENT GÉNÉRIQUE DES DONNÉES REÇUES ---

// On vérifie les paramètres communs : le type de capteur et sa valeur.
if (isset($_GET['type'], $_GET['valeur']) && is_numeric($_GET['valeur'])) {
    
    $type = strtolower($_GET['type']);
    $valeur = (float)$_GET['valeur'];
    
    // On vérifie si ce type de capteur est bien configuré
    if (isset($config_capteurs[$type])) {
        
        $config = $config_capteurs[$type];
        
        // -- 1. Enregistrement en base de données --
        // Noms de colonnes par défaut
        $col_valeur = $config['col_valeur'] ?? 'valeur';
        $col_temps = $config['col_temps'] ?? 'temps';
        
        // Pour le capteur Temp/Hum, il faut aussi enregistrer l'humidité
        if ($type === 'temperature' && isset($_GET['humidite'])) {
            $humidite = (float)$_GET['humidite'];
            $query = "INSERT INTO {$config['nom_table']} (temperature, humidite, id_capteur) VALUES (:valeur, :humidite, :id_capteur)";
            $params = ['valeur' => $valeur, 'humidite' => $humidite, 'id_capteur' => $config['id_capteur']];
        } else {
            // Requête standard pour les autres capteurs
            $query = "INSERT INTO {$config['nom_table']} ({$col_valeur}, id_capteur) VALUES (:valeur, :id_capteur)";
            $params = ['valeur' => $valeur, 'id_capteur' => $config['id_capteur']];
        }
        
        $statement = $bdd_commune->prepare($query);
        $statement->execute($params);

        // -- 2. Vérification des alertes --
        $alerte_declenchee = false;
        if (isset($config['seuil'])) {
            // On vérifie si l'alerte est de type "supérieur" (défaut) ou "inférieur"
            $type_alerte = $config['type_alerte'] ?? 'superieur';
            
            if ($type_alerte === 'superieur' && $valeur > $config['seuil']) {
                $alerte_declenchee = true;
            } elseif ($type_alerte === 'inferieur' && $valeur < $config['seuil']) {
                $alerte_declenchee = true;
            }
        }
        
        if ($alerte_declenchee) {
            // On personnalise le message avec les vraies valeurs
            $message_alerte = str_replace(['{valeur}', '{seuil}'], [$valeur, $config['seuil']], $config['message']);
            // On envoie l'email
            envoyerAlerteEmail($config['sujet'], $message_alerte);
        }
        
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Données pour '{$type}' traitées."]);

    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "Type de capteur '{$type}' non reconnu."]);
    }

} else {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "Paramètres 'type' et 'valeur' requis."]);
}