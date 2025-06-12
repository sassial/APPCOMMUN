<?php
// Fichier : modele/requetes.capteurs.php (VERSION FINALE, PROPRE ET CORRIGÉE)

/**
 * "Carte de traduction" pour les noms de colonnes incohérents de la BDD distante.
 */
function get_table_schema(string $nomTable): ?array {
    $schemas = [
        'Capteur_Son' => [
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
            'valeur' => 'valeur',
            'temps'  => 'temps'
        ]
    ];
    return $schemas[$nomTable] ?? null;
}

/**
 * Récupère les données d'une table de capteur, en s'adaptant à sa structure.
 */
function recupererDonneesCapteur(PDO $bdd, string $nomTable): array {
    $schema = get_table_schema($nomTable);
    if (!$schema) {
        error_log("Schéma inconnu pour la table : {$nomTable}");
        return ['latest' => null, 'min' => null, 'max' => null, 'average' => null, 'history' => []];
    }

    $colonneValeur = $schema['valeur'];
    $colonneTemps = $schema['temps'];
    $nomTableSecurise = "`" . str_replace("`", "", $nomTable) . "`";
    
    $query = "SELECT `{$colonneValeur}` AS valeur, `{$colonneTemps}` AS temps 
              FROM {$nomTableSecurise} 
              ORDER BY `{$colonneTemps}` DESC 
              LIMIT 50";
    
    try {
        $historique = $bdd->query($query)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur SQL pour {$nomTable}: " . $e->getMessage());
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
    $nomTable = '`CapteurTempHum`'; // Assurez-vous que cette table existe sur la BDD distante
    $query = "SELECT `temperature`, `humidite`, `temps` FROM {$nomTable} ORDER BY `temps` DESC LIMIT 50";
    
    try {
        $historique = $bdd->query($query)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur SQL pour CapteurTempHum: " . $e->getMessage());
        return ['latest' => null, 'min_temp' => null, 'max_temp' => null, 'history' => []];
    }

    if (empty($historique)) {
        return ['latest' => null, 'min_temp' => null, 'max_temp' => null, 'history' => []];
    }

    $temperatures = array_column($historique, 'temperature');
    return [
        'latest'  => ['temperature' => round($historique[0]['temperature'], 1), 'humidite' => round($historique[0]['humidite'], 1)],
        'min_temp' => round(min($temperatures), 1),
        'max_temp' => round(max($temperatures), 1),
        'history' => array_reverse($historique) 
    ];
}

/**
 * Récupère la température externe via une API en utilisant cURL (plus robuste).
 */
function recupererTemperatureExterne(): ?float {
    // URL CORRIGÉE avec & et non ¤
    $url = "https://api.open-meteo.com/v1/forecast?latitude=48.85&longitude=2.35¤t_weather=true";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        error_log('Erreur cURL : ' . curl_error($ch));
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
    $schema = get_table_schema($nomTable);
    if (!$schema) {
        error_log("Schéma inconnu pour la table lors de la récupération des données détaillées : {$nomTable}");
        return [ 'live' => null, 'stats24h' => null, 'alerts' => [], 'history' => [] ];
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
        error_log("Erreur SQL pour données détaillées de {$nomTable}: " . $e->getMessage());
        return [ 'live' => null, 'stats24h' => null, 'alerts' => [], 'history' => [] ];
    }

    if (empty($data24h)) {
        return [ 'live' => null, 'stats24h' => null, 'alerts' => [], 'history' => [] ];
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