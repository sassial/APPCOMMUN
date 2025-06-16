<?php
// Fichier : api_capteur.php (VERSION FINALE AVEC SEUILS DYNAMIQUES)

// --- INCLUSIONS NÉCESSAIRES ---
require_once(__DIR__ . '/modele/connexion_commune.php');
require_once(__DIR__ . '/modele/connexion.php'); // Connexion à la BDD locale !
require_once(__DIR__ . '/controleurs/fonctions.php');

// --- CONFIGURATION CENTRALISÉE DES CAPTEURS ---
// Ce tableau sert de "carte" pour lier un type d'URL à un ID de dispositif et un message
$config_capteurs = [
    'son' => [
        'id_dispositif' => 1,
        'nom_table'  => '`Capteur_Son`',
        'sujet'      => "Alerte Sonore : Niveau de bruit élevé !",
        'message'    => "Le capteur sonore a détecté une valeur de <strong>{valeur} dB</strong>, ce qui dépasse le seuil d'alerte de {seuil} dB."
    ],
    'lumiere' => [
        'id_dispositif' => 2,
        'nom_table'  => '`CapteurLumiere`',
        'col_valeur' => 'valeur_luminosite',
        'sujet'      => "Alerte Luminosité : Niveau de lumière anormal !",
        'message'    => "Le capteur de luminosité a détecté une valeur de <strong>{valeur} lux</strong>, dépassant le seuil de {seuil} lux."
    ],
    'proximite' => [
        'id_dispositif' => 3,
        'nom_table'  => '`CapteurProximite`',
        'col_valeur' => 'Value',
        'sujet'      => "Alerte de Proximité : Obstacle détecté !",
        'message'    => "Le capteur de proximité a détecté un obstacle à <strong>{valeur} cm</strong>, ce qui est dans la zone d'alerte (< {seuil} cm)."
    ],
    'gaz' => [
        'id_dispositif' => 4,
        'nom_table'  => '`CapteurGaz`',
        'sujet'      => "Alerte de Sécurité : Niveau de Gaz Élevé !",
        'message'    => "Le capteur de gaz a détecté une valeur de <strong>{valeur} ppm</strong>, ce qui dépasse le seuil de sécurité de {seuil} ppm."
    ],
    'temperature' => [
        'id_dispositif' => 5,
        'nom_table'  => '`capteur_temp_hum`',
        'col_valeur' => 'temperature',
        'sujet'      => "Alerte Température : Chaleur excessive détectée !",
        'message'    => "Le capteur de température a détecté une valeur de <strong>{valeur} °C</strong>, dépassant le seuil de {seuil} °C."
    ]
];
// DANS : api_capteur.php (version simplifiée)

// ... (gardez les inclusions et le tableau $config_capteurs) ...

if (isset($_GET['type'], $_GET['valeur']) && is_numeric($_GET['valeur'])) {
    
    $type = strtolower($_GET['type']);
    $valeur = (float)$_GET['valeur'];
    
    if (isset($config_capteurs[$type])) {
        
        $config = $config_capteurs[$type];
        
        // --- 1. Enregistrement en base de données distante ---
        $col_valeur = $config['col_valeur'] ?? 'valeur';
        
        if ($type === 'temperature' && isset($_GET['humidite'])) {
            $humidite = (float)$_GET['humidite'];
            $query = "INSERT INTO {$config['nom_table']} (temperature, humidite) VALUES (:valeur, :humidite)";
            $params = ['valeur' => $valeur, 'humidite' => $humidite];
        } else {
            $query = "INSERT INTO {$config['nom_table']} ({$col_valeur}) VALUES (:valeur)";
            $params = ['valeur' => $valeur];
        }
        
        $statement = $bdd_commune->prepare($query);
        $statement->execute($params);

        // --- LA SECTION D'ALERTE A ÉTÉ SUPPRIMÉE D'ICI ---
        
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Données enregistrées."]);

    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Type de capteur non reconnu."]);
    }

} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Paramètres requis."]);
}