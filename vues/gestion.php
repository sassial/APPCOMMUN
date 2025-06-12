<?php
/**
 * Vue : gestion des capteurs
 */
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Gestion des Capteurs – Gusteau’s</title>
  <link rel="stylesheet" href="/APPCOMMUN/vues/style.css">
</head>
<body>

  <?php include __DIR__ . '/header.php'; ?>

  <main>
    <div class="gestion-container">

      <!-- Colonne de gauche -->
      <div class="gestion-left">
        <div class="gestion-group">
          <h3>Capteurs actifs</h3>
          <ul class="capteur-list">
            <?php foreach ($capteursActifs as $c): ?>
              <li>
                <span><?= htmlspecialchars($c['nom']) ?></span>
                <form
                  method="post"
                  action="index.php?cible=capteurs&fonction=gestion&action=toggle"
                >
                  <input type="hidden" name="capteur" value="<?= $c['nom_table_bdd'] ?>">
                  <button type="submit" title="Désactiver">–</button>
                </form>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="gestion-group">
          <h3>Capteurs inactifs</h3>
          <ul class="capteur-list">
            <?php foreach ($capteursInactifs as $c): ?>
              <li>
                <span><?= htmlspecialchars($c['nom']) ?></span>
                <form
                  method="post"
                  action="index.php?cible=capteurs&fonction=gestion&action=toggle"
                >
                  <input type="hidden" name="capteur" value="<?= $c['nom_table_bdd'] ?>">
                  <button type="submit" title="Activer">+</button>
                </form>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

      <!-- Colonne de droite -->
      <div class="gestion-right">
        <h3>Seuils des capteurs</h3>
        <?php foreach ($seuils as $cap => $val): ?>
          <div class="seuil-item">
            <label for="seuil-<?= $cap ?>">
              <?= htmlspecialchars($cap) ?> (<?= htmlspecialchars(getUnite($cap)) ?>)
            </label>
            <div class="seuil-controls">
              <form
                method="post"
                action="index.php?cible=capteurs&fonction=gestion&action=updateSeuil"
              >
                <input type="hidden" name="capteur" value="<?= $cap ?>">
                <button type="submit" name="delta" value="-1">–</button>
                <div id="seuil-<?= $cap ?>" class="seuil-value"><?= $val ?></div>
                <button type="submit" name="delta" value="1">+</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>

</body>
</html>
