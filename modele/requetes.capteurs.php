<?php

function recupererDonneesCapteur(PDO $bdd, string $nomTable): array {
    $colonneValeur = 'valeur';
    $colonneTemps = 'temps';

    // On adapte les noms de colonnes en fonction de la table
    if ($nomTable === 'CapteurLumiere') {
        $colonneValeur = 'valeur_luminosite';
        $colonneTemps = 'date_mesure';
    } 
    // On ajoute une condition pour le futur capteur de gaz
    else if ($nomTable === 'CapteurGaz') { 
        // Hypothèses pour le futur nom des colonnes, à adapter si besoin
        $colonneValeur = 'valeur_ppm'; 
        $colonneTemps = 'temps';
    }

    $nomTableSecurise = "`" . str_replace("`", "", $nomTable) . "`";
    $query = "SELECT `$colonneValeur` AS valeur, `$colonneTemps` AS temps 
              FROM $nomTableSecurise 
              ORDER BY `$colonneTemps` DESC 
              LIMIT 50";
    
    try {
        $historique = $bdd->query($query)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
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