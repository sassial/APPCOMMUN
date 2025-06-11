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

      <!-- graphique barres statique d’exemple -->
      <div class="sensor-chart">
        <div class="bar-chart">
          <div class="bar-item" style="--height:80%;" title="80 dB"></div>
          <div class="bar-item" style="--height:65%;" title="65 dB"></div>
          <div class="bar-item" style="--height:90%;" title="90 dB"></div>
          <div class="bar-item" style="--height:50%;" title="50 dB"></div>
          <div class="bar-item" style="--height:70%;" title="70 dB"></div>
        </div>
        <div class="chart-labels">
          <span>–4h</span><span>–3h</span><span>–2h</span><span>–1h</span><span>Maint.</span>
        </div>
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
