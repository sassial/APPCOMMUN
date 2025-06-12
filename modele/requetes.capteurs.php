<?php
// Fichier : modele/requetes.capteurs.php

/**
 * Récupère un jeu de données détaillé pour le tableau de bord personnel.
 * @param PDO    $bdd      PDO vers la BDD distante (alwaysdata)
 * @param string $nomTable Nom exact de la table distante (ex: 'CapteurSon')
 * @return array           ['live'=>…, 'stats24h'=>…, 'alerts'=>…, 'history'=>…]
 */
function recupererDonneesDetaillees(PDO $bdd, string $nomTable): array {
    $nomTableSecurise = "`" . str_replace("`", "", $nomTable) . "`";
    $colVal  = 'valeur';
    $colTime = 'temps';
    $hier    = (new DateTime())->modify('-24 hours')->format('Y-m-d H:i:s');

    $sql = "
      SELECT `{$colVal}` AS valeur, `{$colTime}` AS temps
        FROM {$nomTableSecurise}
       WHERE `{$colTime}` >= :hier
       ORDER BY `{$colTime}` DESC
    ";
    try {
        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':hier', $hier);
        $stmt->execute();
        $data24h = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur SQL détails {$nomTable}: " . $e->getMessage());
        return ['live'=>null,'stats24h'=>null,'alerts'=>[],'history'=>[]];
    }

    if (empty($data24h)) {
        return ['live'=>null,'stats24h'=>null,'alerts'=>[],'history'=>[]];
    }

    $vals = array_column($data24h, 'valeur');
    $stats24h = [
        'min' => round(min($vals), 1),
        'max' => round(max($vals), 1),
        'avg' => round(array_sum($vals) / count($vals), 1),
    ];
    $alerts = array_filter($data24h, fn($m) => $m['valeur'] > 80);

    return [
      'live'     => $data24h[0],
      'stats24h' => $stats24h,
      'alerts'   => array_slice($alerts, 0, 5),
      'history'  => array_reverse($data24h),
    ];
}


/**
 * Récupère les données d'une table de capteur distante.
 */
function recupererDonneesCapteur(PDO $bdd, string $nomTable): array {
    $colVal  = 'valeur';
    $colTime = 'temps';
    if ($nomTable === 'CapteurLumiere') {
        $colVal  = 'valeur_luminosite';
        $colTime = 'date_mesure';
    }

    $table = "`" . str_replace("`","",$nomTable) . "`";
    $sql   = "
        SELECT `{$colVal}` AS valeur, `{$colTime}` AS temps
          FROM {$table}
         ORDER BY `{$colTime}` DESC
         LIMIT 50
    ";
    try {
        $hist = $bdd->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("SQL {$nomTable}: " . $e->getMessage());
        return ['latest'=>null,'min'=>null,'max'=>null,'average'=>null,'history'=>[]];
    }
    if (empty($hist)) {
        return ['latest'=>null,'min'=>null,'max'=>null,'average'=>null,'history'=>[]];
    }

    $vals = array_column($hist, 'valeur');
    return [
      'latest'  => $hist[0],
      'min'     => round(min($vals), 1),
      'max'     => round(max($vals), 1),
      'average' => round(array_sum($vals) / count($vals), 1),
      'history' => array_reverse($hist),
    ];
}


/**
 * Récupère les données du capteur Temp/Hum.
 */
function recupererDonneesTempHum(PDO $bdd): array {
    $sql = "
      SELECT temperature, humidite, temps
        FROM `CapteurTempHum`
       ORDER BY temps DESC
       LIMIT 50
    ";
    try {
        $hist = $bdd->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("SQL CapteurTempHum: " . $e->getMessage());
        return ['latest'=>null,'min_temp'=>null,'max_temp'=>null,'history'=>[]];
    }
    if (empty($hist)) {
        return ['latest'=>null,'min_temp'=>null,'max_temp'=>null,'history'=>[]];
    }

    $temps = array_column($hist, 'temperature');
    return [
      'latest'   => [
        'temperature'=> round($hist[0]['temperature'], 1),
        'humidite'   => round($hist[0]['humidite'], 1),
      ],
      'min_temp' => round(min($temps), 1),
      'max_temp' => round(max($temps), 1),
      'history'  => array_reverse($hist),
    ];
}


/* ---------------------------------------------------
   === GESTION DES CAPTEURS (ACTIVATION / SEUILS) ===
   --------------------------------------------------- */

/**
 * Liste les capteurs (type='capteur') selon leur état.
 */
function listerCapteurs(PDO $bdd, bool $actif): array {
    $sql = "
      SELECT d.nom, d.nom_table_bdd
        FROM dispositifs d
   LEFT JOIN etats_actionneurs e
          ON e.id_dispositif = d.id
       WHERE d.type = 'capteur'
         AND COALESCE(e.etat,0) = :etat
    ";
    $stm = $bdd->prepare($sql);
    $stm->execute(['etat' => $actif ? 1 : 0]);
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère les seuils (champ 'etat') pour tous les capteurs.
 */
function recupererSeuils(PDO $bdd): array {
    $sql = "
      SELECT d.nom_table_bdd, COALESCE(e.etat,0) AS seuil
        FROM dispositifs d
   LEFT JOIN etats_actionneurs e
          ON e.id_dispositif = d.id
       WHERE d.type = 'capteur'
    ";
    $rows = $bdd->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $out  = [];
    foreach ($rows as $r) {
        $out[$r['nom_table_bdd']] = (int)$r['seuil'];
    }
    return $out;
}

/**
 * Bascule l’état ON/OFF d’un capteur (INSERT si première fois).
 */
function basculerEtatCapteur(PDO $bdd, string $nomTable): void {
    // récupère l’id du dispositif
    $id = $bdd->prepare("SELECT id FROM dispositifs WHERE nom_table_bdd = :n")
              ->execute(['n'=>$nomTable])
              ? $bdd->prepare("SELECT id FROM dispositifs WHERE nom_table_bdd = :n")->fetchColumn()
              : null;
    if (!$id) return;

    // tente l’UPDATE
    $upd = $bdd->prepare("
      UPDATE etats_actionneurs
         SET etat = 1 - etat, derniere_modif = NOW()
       WHERE id_dispositif = :d
    ");
    $upd->execute(['d'=>$id]);

    // si aucun row n’a été touché, on INSERT
    if ($upd->rowCount() === 0) {
        $ins = $bdd->prepare("
          INSERT INTO etats_actionneurs
                    (id_dispositif, etat, derniere_modif)
          VALUES      (:d, 1, NOW())
        ");
        $ins->execute(['d'=>$id]);
    }
}

/**
 * Ajuste le seuil (champ 'etat') d’un capteur de ±$delta.
 */
function ajusterSeuilCapteur(PDO $bdd, string $nomTable, int $delta): void {
    $id = $bdd->prepare("SELECT id FROM dispositifs WHERE nom_table_bdd = :n")
              ->execute(['n'=>$nomTable])
              ? $bdd->prepare("SELECT id FROM dispositifs WHERE nom_table_bdd = :n")->fetchColumn()
              : null;
    if (!$id) return;

    $bdd->prepare("
      UPDATE etats_actionneurs
         SET etat = etat + :delta, derniere_modif = NOW()
       WHERE id_dispositif = :d
    ")->execute(['delta'=>$delta,'d'=>$id]);
}

/**
 * Retourne l’unité d’affichage selon le nom_table_bdd.
 */
function getUnite(string $nomTable): string {
    return match($nomTable) {
      'CapteurTempHum'   => '°C / %',
      'CapteurLumiere'   => 'lux',
      'CapteurProximite' => 'cm',
      'CapteurGaz'       => 'ppm',
      default             => '',
    };
}
