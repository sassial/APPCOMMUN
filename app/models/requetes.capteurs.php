<?php

/**
 * "Carte de traduction" pour les noms de colonnes incohérents de la BDD distante.
 */
function get_table_schema(string $nomTable): ?array {
    $schemas = [
        'CapteurSon' => [
            'valeur' => 'valeur',
            'temps'  => 'temps'
        ],
        'CapteurLumiere' => [
            'valeur' => 'valeur_luminosite',
            'temps'  => 'date_mesure'
        ],
        'CapteurProximite' => [
            'valeur' => 'Value',
            'temps'  => 'Times'
        ],
        'CapteurGaz' => [
            'valeur' => 'value',
            'temps'  => 'timestamp'
        ],
        'capteur_temp_hum' => [
            'temperature' => 'temperature',
            'humidite'    => 'humidite',
            'temps'       => 'horodatage'
        ]
    ];
    return $schemas[$nomTable] ?? null;
}

/**
 * Valide le nom de table par rapport à une liste blanche.
 */
function is_valid_table(string $nomTable): bool {
    $allowedTables = ['CapteurSon', 'CapteurLumiere', 'CapteurProximite', 'CapteurGaz', 'capteur_temp_hum'];
    return in_array($nomTable, $allowedTables, true);
}

/**
 * Récupère les données d'une table de capteur, en s'adaptant à sa structure.
 */
function recupererDonneesCapteur(PDO $bdd, string $nomTable): array {
    if (!is_valid_table($nomTable)) {
        trigger_error("Nom de table non autorisé : {$nomTable}");
        return ['latest' => null, 'min' => null, 'max' => null, 'average' => null, 'history' => []];
    }

    $schema = get_table_schema($nomTable);
    if (!$schema) {
        trigger_error("Schéma inconnu pour la table : {$nomTable}");
        return ['latest' => null, 'min' => null, 'max' => null, 'average' => null, 'history' => []];
    }

    // Cas spécial pour capteur_temp_hum
    if ($nomTable === 'capteur_temp_hum') {
        return recupererDonneesTempHum($bdd);
    }

    $colonneValeur = $schema['valeur'];
    $colonneTemps = $schema['temps'];
    $nomTableSecurise = "`" . str_replace("`", "", $nomTable) . "`";

    $query = "SELECT `{$colonneValeur}` AS valeur, `{$colonneTemps}` AS temps 
              FROM {$nomTableSecurise} 
              ORDER BY `{$colonneTemps}` DESC 
              LIMIT 200";
    
    try {
        $stmt = $bdd->prepare($query);
        $stmt->execute();
        $historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        trigger_error("Erreur SQL pour {$nomTable}: " . $e->getMessage());
        return ['latest' => null, 'min' => null, 'max' => null, 'average' => null, 'history' => []];
    }

    if (empty($historique)) {
        return ['latest' => null, 'min' => null, 'max' => null, 'average' => null, 'history' => []];
    }

    $valeurs = array_column($historique, 'valeur');

    return [
        'latest'  => $historique[0],
        'min'     => round(min($valeurs), 1),
        'max'     => round(max($valeurs), 1),
        'average' => round(array_sum($valeurs) / count($valeurs), 1),
        'history' => array_reverse($historique)
    ];
}

/**
 * Récupère les données du capteur Temp/Hum (cas spécial).
 */
function recupererDonneesTempHum(PDO $bdd): array {
    $nomTable = '`capteur_temp_hum`'; 
    $query = "SELECT `temperature`, `humidite`, `horodatage` AS temps FROM {$nomTable} ORDER BY `horodatage` DESC LIMIT 200";
    
    try {
        $stmt = $bdd->prepare($query);
        $stmt->execute();
        $historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        trigger_error("Erreur SQL pour capteur_temp_hum: " . $e->getMessage());
        return ['latest' => null, 'min_temp' => null, 'max_temp' => null, 'min_humidite' => null, 'max_humidite' => null, 'history' => []];
    }

    if (empty($historique)) {
        return ['latest' => null, 'min_temp' => null, 'max_temp' => null, 'min_humidite' => null, 'max_humidite' => null, 'history' => []];
    }

    $temperatures = array_column($historique, 'temperature');
    $humidites = array_column($historique, 'humidite');

    return [
        'latest'      => [
            'temperature' => round($historique[0]['temperature'], 1),
            'humidite'    => round($historique[0]['humidite'], 1)
        ],
        'min_temp'    => round(min($temperatures), 1),
        'max_temp'    => round(max($temperatures), 1),
        'min_humidite'=> round(min($humidites), 1),
        'max_humidite'=> round(max($humidites), 1),
        'history'     => array_reverse($historique)
    ];
}

/**
 * Récupère la température externe via une API en utilisant cURL (plus robuste).
 */
function recupererTemperatureExterne(): ?float {
    $url = "https://api.open-meteo.com/v1/forecast?latitude=48.85&longitude=2.35&current_weather=true";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        trigger_error('Erreur cURL : ' . curl_error($ch));
        curl_close($ch);
        return null;
    }
    curl_close($ch);

    $weather_data = json_decode($response, true);

    if (json_last_error() === JSON_ERROR_NONE && isset($weather_data['current_weather']['temperature'])) {
        return $weather_data['current_weather']['temperature'];
    }
    return null;
}

/**
 * Récupère un jeu de données détaillé pour le tableau de bord personnel (accueil.php).
 */
function recupererDonneesDetaillees(PDO $bdd, string $nomTable): array {
    if (!is_valid_table($nomTable)) {
        trigger_error("Nom de table non autorisé pour données détaillées : {$nomTable}");
        return ['live' => null, 'stats24h' => null, 'alerts' => [], 'history' => []];
    }

    $schema = get_table_schema($nomTable);
    if (!$schema) {
        trigger_error("Schéma inconnu pour la table lors de la récupération des données détaillées : {$nomTable}");
        return ['live' => null, 'stats24h' => null, 'alerts' => [], 'history' => []];
    }

    if ($nomTable === 'capteur_temp_hum') {
        return ['live' => null, 'stats24h' => null, 'alerts' => [], 'history' => []];
    }

    $colonneValeur = $schema['valeur'];
    $colonneTemps = $schema['temps'];
    $nomTableSecurise = "`" . str_replace("`", "", $nomTable) . "`";
    $hier = (new DateTime())->modify('-24 hours')->format('Y-m-d H:i:s');

    $query = "SELECT `{$colonneValeur}` AS valeur, `{$colonneTemps}` AS temps 
              FROM {$nomTableSecurise} 
              WHERE `{$colonneTemps}` >= :hier 
              ORDER BY `{$colonneTemps}` DESC";

    try {
        $statement = $bdd->prepare($query);
        $statement->bindValue(':hier', $hier);
        $statement->execute();
        $data24h = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        trigger_error("Erreur SQL pour données détaillées de {$nomTable}: " . $e->getMessage());
        return ['live' => null, 'stats24h' => null, 'alerts' => [], 'history' => []];
    }

    if (empty($data24h)) {
        return ['live' => null, 'stats24h' => null, 'alerts' => [], 'history' => []];
    }

    $valeurs24h = array_column($data24h, 'valeur');
    $stats24h = [
        'min' => round(min($valeurs24h), 1),
        'max' => round(max($valeurs24h), 1),
        'avg' => round(array_sum($valeurs24h) / count($valeurs24h), 1)
    ];

    $alertes = [];
    $seuilAlerte = 80;
    foreach ($data24h as $mesure) {
        if ($mesure['valeur'] > $seuilAlerte) {
            $alertes[] = $mesure;
        }
    }

    return [
        'live'      => $data24h[0],
        'stats24h'  => $stats24h,
        'alerts'    => array_slice($alertes, 0, 5),
        'history'   => array_reverse($data24h)
    ];
}

