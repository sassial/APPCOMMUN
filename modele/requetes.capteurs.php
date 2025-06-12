<?php
// Fichier : modele/requetes.capteurs.php

/**
 * Récupère les données d'une table de capteur spécifique.
 * @param PDO $bdd L'objet de connexion à la BDD.
 * @param string $nomTable Le nom exact de la table.
 * @return array Un tableau contenant les valeurs clés et l'historique.
 */
function recupererDonneesCapteur(PDO $bdd, string $nomTable): array {
    $colonneValeur = 'valeur';
    $colonneTemps = 'temps';

    // Adapte les noms de colonnes si nécessaire
    if ($nomTable === 'CapteurLumiere') {
        $colonneValeur = 'valeur_luminosite';
        $colonneTemps = 'date_mesure';
    }

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
 * Récupère les données du capteur Temp/Hum avec stats min/max.
 * @param PDO $bdd L'objet de connexion à la BDD.
 * @return array Un tableau contenant les valeurs clés et l'historique.
 */
function recupererDonneesTempHum(PDO $bdd): array {
    $nomTable = '`CapteurTempHum`'; // IMPORTANT: VÉRIFIEZ CE NOM DE TABLE

    $query = "SELECT `temperature`, `humidite`, `temps` 
              FROM {$nomTable} 
              ORDER BY `temps` DESC 
              LIMIT 50";
    
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
 * **FONCTION AJOUTÉE QUI CORRIGE L'ERREUR FATALE**
 * Récupère un jeu de données détaillé pour le tableau de bord personnel.
 * @param PDO $bdd L'objet de connexion à la BDD commune.
 * @param string $nomTable Le nom de la table du capteur.
 * @return array Données détaillées pour l'affichage.
 */
function recupererDonneesDetaillees(PDO $bdd, string $nomTable): array {
    $nomTableSecurise = "`" . str_replace("`", "", $nomTable) . "`";
    $colonneValeur = 'valeur';
    $colonneTemps = 'temps';
    
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
    $seuilAlerte = 80; // dB
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
// À la fin de modele/requetes.capteurs.php
// ...
function recupererTemperatureExterne(string $ville = 'Paris'): ?float {
    $lat = 48.85;
    $lon = 2.35;
    
    // CORRECTION ICI : Remplacer `¤t_weather` par `¤t_weather`
    $url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}¤t_weather=true";
    
    $json_data = @file_get_contents($url);
    if ($json_data === false) {
        return null;
    }
    
    $weather_data = json_decode($json_data, true);
    
    return $weather_data['current_weather']['temperature'] ?? null;
}