<?php
/**
* Vue : affichage des capteurs
*/
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Affichage des Capteurs – Gusteau’s</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/vues/style.css">
</head>
<body>

  <?php include __DIR__ . '/header.php'; ?>

  <main class="container">
    <section class="signup-card">
        <h1>Données du Capteur Sonore</h1>
        
        <?php if (isset($derniereMesure) && $derniereMesure): ?>
            <p>Dernière mesure enregistrée :</p>
            <div class="sensor-card" style="margin-top: 1rem;">
                <p class="sensor-label">Niveau sonore :</p>
                <p class="decibel-value"><?= htmlspecialchars($derniereMesure['valeur_db']) ?> dB</p>
                <p class="sensor-placeholder">
                    Enregistré le : <?= date('d/m/Y à H:i:s', strtotime($derniereMesure['horodatage'])) ?>
                </p>
            </div>
        <?php else: ?>
            <p>Aucune mesure n'a encore été enregistrée dans la base de données commune.</p>
        <?php endif; ?>

    </section>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>

</body>
</html>