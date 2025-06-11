<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Tableau de Bord – Gusteau’s</title>
  
  <!-- On ne charge QUE la feuille de style unique -->
  <link rel="stylesheet" href="<?= BASE_PATH ?>/vues/style.css">
  
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

  <?php include __DIR__ . '/header.php'; ?>

  <main class="dashboard-container">
    <h1>Tableau de Bord des Capteurs</h1>
    <div class="dashboard-grid">

      <!-- Carte 1 : Capteur Sonore -->
      <div class="dashboard-card">
        <div class="card-header"><span class="card-icon">🔊</span><h2 class="card-title">Capteur Sonore</h2></div>
        <div class="card-content">
          <div class="card-stats">
            <div class="stat-item"><p class="stat-value"><?= $donneesSon['latest']['valeur'] ?? '--' ?><span style="font-size: 1rem; font-weight: normal;"> dB</span></p><p class="stat-label">Actuel</p></div>
            <div class="stat-item"><p class="stat-value"><?= $donneesSon['max'] ?? '--' ?></p><p class="stat-label">Max</p></div>
            <div class="stat-item"><p class="stat-value"><?= $donneesSon['average'] ?? '--' ?></p><p class="stat-label">Moyenne</p></div>
          </div>
          <div class="card-chart-container"><canvas id="chartSon"></canvas></div>
        </div>
      </div>

      <!-- Carte 2 : Capteur Lumière -->
      <div class="dashboard-card">
        <div class="card-header"><span class="card-icon">💡</span><h2 class="card-title">Capteur Lumière</h2></div>
        <div class="card-content">
          <div class="card-stats">
            <div class="stat-item"><p class="stat-value"><?= $donneesLumiere['latest']['valeur'] ?? '--' ?><span style="font-size: 1rem; font-weight: normal;"> lux</span></p><p class="stat-label">Actuel</p></div>
            <div class="stat-item"><p class="stat-value"><?= $donneesLumiere['max'] ?? '--' ?></p><p class="stat-label">Max</p></div>
            <div class="stat-item"><p class="stat-value"><?= $donneesLumiere['min'] ?? '--' ?></p><p class="stat-label">Min</p></div>
          </div>
          <div class="card-chart-container"><canvas id="chartLumiere"></canvas></div>
        </div>
      </div>

      <!-- Carte 3 : Capteur Proximité -->
      <div class="dashboard-card">
        <div class="card-header"><span class="card-icon">📏</span><h2 class="card-title">Capteur Proximité</h2></div>
        <div class="card-content">
          <div class="card-stats">
            <div class="stat-item"><p class="stat-value"><?= $donneesProximite['latest']['valeur'] ?? '--' ?><span style="font-size: 1rem; font-weight: normal;"> cm</span></p><p class="stat-label">Actuel</p></div>
            <div class="stat-item"><p class="stat-value"><?= $donneesProximite['min'] ?? '--' ?></p><p class="stat-label">Min</p></div>
            <div class="stat-item"><p class="stat-value"><?= $donneesProximite['average'] ?? '--' ?></p><p class="stat-label">Moyenne</p></div>
          </div>
          <div class="card-chart-container"><canvas id="chartProximite"></canvas></div>
        </div>
      </div>

      <!-- Carte 4 : Capteur de Gaz -->
      <div class="dashboard-card">
        <div class="card-header"><span class="card-icon">💨</span><h2 class="card-title">Capteur de Gaz</h2></div>
        <div class="card-content">
          <div class="card-stats">
            <div class="stat-item"><p class="stat-value"><?= $donneesGaz['latest']['valeur'] ?? '--' ?><span style="font-size: 1rem; font-weight: normal;"> ppm</span></p><p class="stat-label">Actuel</p></div>
            <div class="stat-item"><p class="stat-value"><?= $donneesGaz['max'] ?? '--' ?></p><p class="stat-label">Max</p></div>
            <div class="stat-item"><p class="stat-value"><?= $donneesGaz['min'] ?? '--' ?></p><p class="stat-label">Min</p></div>
          </div>
          <div class="card-chart-container"><canvas id="chartGaz"></canvas></div>
        </div>
      </div>

    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>

  
    <script>
    // --- NOUVELLE PARTIE : On récupère les vraies valeurs des couleurs CSS ---
    const rootStyles = getComputedStyle(document.documentElement);
    const redColor = rootStyles.getPropertyValue('--red').trim();
    const goldColor = rootStyles.getPropertyValue('--gold').trim();
    const navyColor = rootStyles.getPropertyValue('--navy').trim();
    const tealColor = rootStyles.getPropertyValue('--teal').trim();
    // ----------------------------------------------------------------------

    function createChart(canvasId, historyData, color, tension = 0.3) {
      if (!historyData || historyData.length === 0) return;
      const ctx = document.getElementById(canvasId).getContext('2d');
      new Chart(ctx, {
        type: 'line',
        data: {
          labels: historyData.map(d => new Date(d.temps).toLocaleTimeString()),
          datasets: [{
            data: historyData.map(d => d.valeur),
            borderColor: color,
            backgroundColor: color + '33', // Ajoute de la transparence
            fill: true, 
            pointRadius: 0, 
            tension: tension
          }]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: { x: { display: false }, y: { display: false } }
        }
      });
    }

    // --- MODIFICATION : On utilise les variables de couleur au lieu des chaînes de caractères ---
    createChart('chartSon', <?= json_encode($donneesSon['history'] ?? []) ?>, redColor, 0.1);
    createChart('chartLumiere', <?= json_encode($donneesLumiere['history'] ?? []) ?>, goldColor, 0.4);
    createChart('chartProximite', <?= json_encode($donneesProximite['history'] ?? []) ?>, navyColor, 0.2);
    createChart('chartGaz', <?= json_encode($donneesGaz['history'] ?? []) ?>, tealColor, 0.3);
  </script>

</body>
</html>