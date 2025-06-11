 <?php
// vues/accueil.php
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Accueil – Gusteau’s</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/vues/style.css">
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
      <img src="<?= BASE_PATH ?>/photo.jpg" alt="Gusteau’s">
    </div>
  </section>

  <!-- Section capteur son -->
  <section id="capteur" class="sensor-section">
    <h2>Votre capteur de son</h2>
    <div class="sensor-card">
      <p class="sensor-label">Niveau actuel :</p>
      <p class="decibel-value" id="decibel-value">-- dB</p>
      <!-- placeholder pour un graphique ou historique -->
      <div class="sensor-chart">
        <!-- ici vous pourrez injecter un canvas/chart JS -->
        <p class="sensor-placeholder">Graphique à venir…</p>
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/footer.php'; ?>

  <!--
    Vous pouvez ajouter ici votre script JS qui interroge
    votre API ou WebSocket pour mettre à jour #decibel-value
  -->
      <!-- ... (juste avant la fin du body) ... -->
  <script>
    const header = document.querySelector('.site-header');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 10) { // Si on a scrollé de plus de 10 pixels
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  </script>
</body>
</html>
