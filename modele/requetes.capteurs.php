<?php

// Fichier : modele/requetes.capteurs.php
// Version garantie sans erreur de syntaxe.

/**
 * Récupère les données d'une table de capteur spécifique, en adaptant les noms de colonnes.
 * @param PDO $bdd L'objet de connexion à la BDD.
 * @param string $nomTable Le nom exact de la table.
 * @return array Un tableau contenant les valeurs clés et l'historique.
 */
function recupererDonneesCapteur(PDO $bdd, string $nomTable): array {
    
    // On définit les noms de colonnes par défaut
    $colonneValeur = 'valeur';
    $colonneTemps = 'temps';

    // On adapte les noms en fonction de la table interrogée
    if ($nomTable === 'CapteurLumiere') {
        $colonneValeur = 'valeur_luminosite';
        $colonneTemps = 'date_mesure';
    } 
    // Ajoutez d'autres conditions si les autres tables ont des structures différentes
    // else if ($nomTable === 'CapteurGaz') { 
    //     $colonneValeur = 'valeur_ppm'; 
    //     $colonneTemps = 'temps';
    // }

    $nomTableSecurise = "`" . str_replace("`", "", $nomTable) . "`";
    
    // On utilise les variables pour construire la requête
    $query = "SELECT `{$colonneValeur}` AS valeur, `{$colonneTemps}` AS temps 
              FROM {$nomTableSecurise} 
              ORDER BY `{$colonneTemps}` DESC 
              LIMIT 50";
    
    try {
        $historique = $bdd->query($query)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // En cas d'erreur (ex: table ou colonne non trouvée), on renvoie un set vide.
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
 * Récupère un jeu de données détaillé pour le tableau de bord personnel.
 * @param PDO $bdd L'objet de connexion à la BDD commune.
 * @param string $nomTable Le nom de la table du capteur.
 * @return array Données détaillées pour l'affichage.
 */
function recupererDonneesDetaillees(PDO $bdd, string $nomTable): array {
    $nomTableSecurise = "`" . str_replace("`", "", $nomTable) . "`";
    $colonneValeur = 'valeur';
    $colonneTemps = 'temps';

    // S'assurer que les noms de colonnes sont corrects pour la table demandée
    if ($nomTable === 'CapteurLumiere') {
        $colonneValeur = 'valeur_luminosite';
        $colonneTemps = 'date_mesure';
    }

    $maintenant = new DateTime();
    $hier = (new DateTime())->modify('-24 hours');

    $query = "SELECT `{$colonneValeur}` AS valeur, `{$colonneTemps}` AS temps 
              FROM {$nomTableSecurise} 
              WHERE `{$colonneTemps}` >= :hier 
              ORDER BY `{$colonneTemps}` DESC";

    try {
        $statement = $bdd->prepare($query);
        $statement->bindValue(':hier', $hier->format('Y-m-d H:i:s'));
        $statement->execute();
        $data24h = $statement->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [ 'live' => null, 'stats24h' => null, 'alerts' => [], 'history' => [] ];
    }

    if (empty($data24h)) {
        return [ 'live' => null, 'stats24h' => null, 'alerts' => [], 'history' => [] ];
    }
    
    $valeurs24h = array_column($data24h, 'valeur');
    $stats24h = [
        'min' => round(min($valeurs24h), 1),
        'max' => round(max($valeurs24h), 1),
        'avg' => round(array_sum($valeurs24h), 1)
    ];

    // Simuler des alertes (valeurs dépassant un seuil)
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

