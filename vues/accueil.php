 <?php
// vues/accueil.php
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Accueil – Gusteau’s</title>
  <link rel="stylesheet" href="/APPCOMMUN/vues/style.css">
</head>
<body>

  <?php include __DIR__ . '/header.php'; ?>

  <!-- Hero / Présentation -->
  <section class="hero">
    <div class="hero-content">
      <p class="hero-subtitle">Bienvenue chez</p>
      <h1 class="hero-title">Gusteau’s</h1>
      <p class="hero-text">
        Découvrez l’ambiance unique de notre restaurant et surveillez le niveau sonore en temps réel.
      </p>
      <a href="#capteur" class="btn-hero">Voir mes données</a>
    </div>
    <div class="hero-image">
      <!-- remplacez par une belle photo d’ambiance -->
      <img src="/APPCOMMUN/photo.jpg" alt="Gusteau’s">
    </div>
  </section>

  <!-- Section capteur son -->
  <section id="capteur" class="sensor-section">
    <h2>Votre capteur de son</h2>
    <div class="sensor-card">
      <p class="sensor-label">Niveau actuel :</p>
      <p class="decibel-value" id="decibel-value">-- dB</p>
      <div class="sensor-chart">
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/footer.php'; ?>

  <!--
    Vous pouvez ajouter ici votre script JS qui interroge
    votre API ou WebSocket pour mettre à jour #decibel-value
  -->
</body>
</html>
