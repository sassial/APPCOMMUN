<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Tableau de Bord – Gusteau’s</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/vues/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
</head>
<body>

  <?php include __DIR__ . '/header.php'; ?>

  <main class="dashboard-container">
    <h1>Tableau de Bord des Capteurs</h1>
    <div class="dashboard-grid">

      <!-- Carte 1 : Capteur Température & Humidité -->
      <div class="dashboard-card">
        <div class="card-header"><span class="card-icon">🌡️</span><h2 class="card-title">Température & Humidité</h2></div>
        <div class="card-content">
          <div class="card-stats">
            <div class="stat-item"><p class="stat-value"><span id="val-temp-actuelle"><?= $donneesTempHum['latest']['temperature'] ?? '--' ?></span><span class="unit">°C</span></p><p class="stat-label">Temp. Actuelle</p></div>
            <div class="stat-item"><p class="stat-value"><span id="val-hum-actuelle"><?= $donneesTempHum['latest']['humidite'] ?? '--' ?></span><span class="unit">%</span></p><p class="stat-label">Humidité</p></div>
            <div class="stat-item"><p class="stat-value"><span id="val-temp-max"><?= $donneesTempHum['max_temp'] ?? '--' ?></span><span class="unit">°C</span></p><p class="stat-label">Temp. Max</p></div>
            <div class="stat-item"><p class="stat-value"><span id="val-temp-min"><?= $donneesTempHum['min_temp'] ?? '--' ?></span><span class="unit">°C</span></p><p class="stat-label">Temp. Min</p></div>
          </div>
          <div class="card-chart-container"><canvas id="chartTempHum"></canvas></div>
        </div>
      </div>

      <!-- Carte 2 : Capteur Lumière -->
      <div class="dashboard-card">
        <div class="card-header"><span class="card-icon">💡</span><h2 class="card-title">Capteur Lumière</h2></div>
        <div class="card-content">
          <div class="card-stats">
            <div class="stat-item"><p class="stat-value"><span id="val-lum-actuelle"><?= $donneesLumiere['latest']['valeur'] ?? '--' ?></span><span class="unit">lux</span></p><p class="stat-label">Actuel</p></div>
            <div class="stat-item"><p class="stat-value"><span id="val-lum-max"><?= $donneesLumiere['max'] ?? '--' ?></span></p><p class="stat-label">Max</p></div>
            <div class="stat-item"><p class="stat-value"><span id="val-lum-min"><?= $donneesLumiere['min'] ?? '--' ?></span></p><p class="stat-label">Min</p></div>
          </div>
          <div class="card-chart-container"><canvas id="chartLumiere"></canvas></div>
        </div>
      </div>

      <!-- Carte 3 : Capteur Proximité -->
      <div class="dashboard-card">
        <div class="card-header"><span class="card-icon">📏</span><h2 class="card-title">Capteur Proximité</h2></div>
        <div class="card-content">
          <div class="card-stats">
            <div class="stat-item"><p class="stat-value"><span id="val-prox-actuelle"><?= $donneesProximite['latest']['valeur'] ?? '--' ?></span><span class="unit">cm</span></p><p class="stat-label">Actuel</p></div>
            <div class="stat-item"><p class="stat-value"><span id="val-prox-min"><?= $donneesProximite['min'] ?? '--' ?></span></p><p class="stat-label">Min</p></div>
            <div class="stat-item"><p class="stat-value"><span id="val-prox-moyenne"><?= $donneesProximite['average'] ?? '--' ?></span></p><p class="stat-label">Moyenne</p></div>
          </div>
          <div class="card-chart-container"><canvas id="chartProximite"></canvas></div>
        </div>
      </div>

      <!-- Carte 4 : Capteur de Gaz -->
      <div class="dashboard-card">
        <div class="card-header"><span class="card-icon">💨</span><h2 class="card-title">Capteur de Gaz</h2></div>
        <div class="card-content">
          <div class="card-stats">
            <div class="stat-item"><p class="stat-value"><span id="val-gaz-actuelle"><?= $donneesGaz['latest']['valeur'] ?? '--' ?></span><span class="unit">ppm</span></p><p class="stat-label">Actuel</p></div>
            <div class="stat-item"><p class="stat-value"><span id="val-gaz-max"><?= $donneesGaz['max'] ?? '--' ?></span></p><p class="stat-label">Max</p></div>
            <div class="stat-item"><p class="stat-value"><span id="val-gaz-min"><?= $donneesGaz['min'] ?? '--' ?></span></p><p class="stat-label">Min</p></div>
          </div>
          <div class="card-chart-container"><canvas id="chartGaz"></canvas></div>
        </div>
      </div>

    </div>
  </main>

  <?php include __DIR__ . '/footer.php'; ?>

  <script>
    const rootStyles = getComputedStyle(document.documentElement);
    const redColor = rootStyles.getPropertyValue('--red').trim();
    const goldColor = rootStyles.getPropertyValue('--gold').trim();
    const navyColor = rootStyles.getPropertyValue('--navy').trim();
    const tealColor = rootStyles.getPropertyValue('--teal').trim();

    const charts = {};

    function createChart(canvasId, historyData, color, dataKey = 'valeur', tension = 0.3) {
      if (!document.getElementById(canvasId) || !historyData || historyData.length === 0) return null;
      const ctx = document.getElementById(canvasId).getContext('2d');
      return new Chart(ctx, {
        type: 'line',
        data: {
          labels: historyData.map(d => new Date(d.temps).toLocaleTimeString()),
          datasets: [{ data: historyData.map(d => d[dataKey]), borderColor: color, backgroundColor: color + '33', fill: true, pointRadius: 0, tension: tension }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } } }
      });
    }

    charts.tempHum = createChart('chartTempHum', <?= json_encode($donneesTempHum['history'] ?? []) ?>, redColor, 'temperature');
    charts.lumiere = createChart('chartLumiere', <?= json_encode($donneesLumiere['history'] ?? []) ?>, goldColor);
    charts.proximite = createChart('chartProximite', <?= json_encode($donneesProximite['history'] ?? []) ?>, navyColor);
    charts.gaz = createChart('chartGaz', <?= json_encode($donneesGaz['history'] ?? []) ?>, tealColor);

    function updateChart(chartInstance, historyData, dataKey = 'valeur') {
        if (!chartInstance || !historyData || historyData.length === 0) return;
        chartInstance.data.labels = historyData.map(d => new Date(d.temps).toLocaleTimeString());
        chartInstance.data.datasets[0].data = historyData.map(d => d[dataKey]);
        chartInstance.update('none'); // 'none' for no animation
    }

    function updateElement(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value ?? '--';
    }

    function mettreAJourLesValeurs(donnees) {
        if (donnees.tempHum) {
            updateElement('val-temp-actuelle', donnees.tempHum.latest?.temperature);
            updateElement('val-hum-actuelle', donnees.tempHum.latest?.humidite);
            updateElement('val-temp-max', donnees.tempHum.max_temp);
            updateElement('val-temp-min', donnees.tempHum.min_temp);
            updateChart(charts.tempHum, donnees.tempHum.history, 'temperature');
        }
        if (donnees.lumiere) {
            updateElement('val-lum-actuelle', donnees.lumiere.latest?.valeur);
            updateElement('val-lum-max', donnees.lumiere.max);
            updateElement('val-lum-min', donnees.lumiere.min);
            updateChart(charts.lumiere, donnees.lumiere.history);
        }
        if (donnees.proximite) {
            updateElement('val-prox-actuelle', donnees.proximite.latest?.valeur);
            updateElement('val-prox-min', donnees.proximite.min);
            updateElement('val-prox-moyenne', donnees.proximite.average);
            updateChart(charts.proximite, donnees.proximite.history);
        }
        if (donnees.gaz) {
            updateElement('val-gaz-actuelle', donnees.gaz.latest?.valeur);
            updateElement('val-gaz-max', donnees.gaz.max);
            updateElement('val-gaz-min', donnees.gaz.min);
            updateChart(charts.gaz, donnees.gaz.history);
        }
    }

    async function fetchDonneesLive() {
        try {
            const reponse = await fetch('<?= BASE_PATH ?>/api_get_latest.php');
            if (!reponse.ok) throw new Error(`HTTP error! status: ${reponse.status}`);
            const donnees = await reponse.json();
            mettreAJourLesValeurs(donnees);
        } catch (error) {
            console.error('Erreur lors de la récupération des données live:', error);
        }
    }

    setInterval(fetchDonneesLive, 5000); // Update every 5 seconds
  </script>
</body>
</html>