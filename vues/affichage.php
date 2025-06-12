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

            <!-- Carte Météo Externe (Fonctionnalité Bonus) -->
            <div class="dashboard-card">
                <div class="card-header"><span class="card-icon">🌍</span><h2 class="card-title">Météo Externe (Paris)</h2></div>
                <div class="card-content-simple">
                    <p class="big-value"><?= isset($temperatureExterne) ? number_format($temperatureExterne, 1) : 'N/A' ?> °C</p>
                    <p class="stat-label">Température actuelle</p>
                </div>
            </div>
            
            <!-- Boucle dynamique pour générer les cartes des dispositifs -->
            <?php foreach ($dispositifs as $dispositif): ?>
                <?php if ($dispositif['type'] === 'capteur'): 
                    $data = $donnees_capteurs[$dispositif['id']] ?? null;
                ?>
                    <!-- CARTE TYPE CAPTEUR -->
                    <div class="dashboard-card">
                        <div class="card-header"><span class="card-icon">#</span><h2 class="card-title"><?= htmlspecialchars($dispositif['nom']) ?></h2></div>
                        <div class="card-content">
                            <div class="card-stats">
                                <?php if ($dispositif['nom_table_bdd'] === 'CapteurTempHum'): ?>
                                    <div class="stat-item"><p class="stat-value"><span id="val-<?= $dispositif['id'] ?>-temp"><?= $data['latest']['temperature'] ?? '--' ?></span><span class="unit">°C</span></p><p class="stat-label">Temp. Actuelle</p></div>
                                    <div class="stat-item"><p class="stat-value"><span id="val-<?= $dispositif['id'] ?>-hum"><?= $data['latest']['humidite'] ?? '--' ?></span><span class="unit">%</span></p><p class="stat-label">Humidité</p></div>
                                    <div class="stat-item"><p class="stat-value"><span id="val-<?= $dispositif['id'] ?>-max"><?= $data['max_temp'] ?? '--' ?></span><span class="unit">°C</span></p><p class="stat-label">Temp. Max</p></div>
                                    <div class="stat-item"><p class="stat-value"><span id="val-<?= $dispositif['id'] ?>-min"><?= $data['min_temp'] ?? '--' ?></span><span class="unit">°C</span></p><p class="stat-label">Temp. Min</p></div>
                                <?php else: ?>
                                    <div class="stat-item"><p class="stat-value"><span id="val-<?= $dispositif['id'] ?>-actuel"><?= $data['latest']['valeur'] ?? '--' ?></span><span class="unit"><?= htmlspecialchars($dispositif['unite']) ?></span></p><p class="stat-label">Actuel</p></div>
                                    <div class="stat-item"><p class="stat-value"><span id="val-<?= $dispositif['id'] ?>-max"><?= $data['max'] ?? '--' ?></span></p><p class="stat-label">Max</p></div>
                                    <div class="stat-item"><p class="stat-value"><span id="val-<?= $dispositif['id'] ?>-min"><?= $data['min'] ?? '--' ?></span></p><p class="stat-label">Min</p></div>
                                <?php endif; ?>
                            </div>
                            <div class="card-chart-container"><canvas id="chart-<?= $dispositif['id'] ?>"></canvas></div>
                        </div>
                    </div>
                <?php elseif ($dispositif['type'] === 'actionneur'): 
                    $etat_actuel = $etats_actionneurs[$dispositif['id']] ?? 0;
                ?>
                    <!-- CARTE TYPE ACTIONNEUR -->
                    <div class="dashboard-card">
                        <div class="card-header"><span class="card-icon">⚡</span><h2 class="card-title"><?= htmlspecialchars($dispositif['nom']) ?></h2></div>
                        <div class="card-content-simple">
                            <label class="switch">
                                <input type="checkbox" class="actionneur-checkbox" data-id="<?= $dispositif['id'] ?>" <?= $etat_actuel ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                            <p class="stat-label" id="etat-label-<?= $dispositif['id'] ?>"><?= $etat_actuel ? 'ALLUMÉ' : 'ÉTEINT' ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rootStyles = getComputedStyle(document.documentElement);
            const colors = {
                red: rootStyles.getPropertyValue('--red').trim(),
                gold: rootStyles.getPropertyValue('--gold').trim(),
                navy: rootStyles.getPropertyValue('--navy').trim(),
                teal: rootStyles.getPropertyValue('--teal').trim()
            };

            const charts = {};

            // Initial chart creation
            <?php foreach ($dispositifs as $dispositif): ?>
                <?php if ($dispositif['type'] === 'capteur'): 
                    $data = $donnees_capteurs[$dispositif['id']] ?? null;
                    $dataKey = ($dispositif['nom_table_bdd'] === 'CapteurTempHum') ? 'temperature' : 'valeur';
                ?>
                    charts[<?= $dispositif['id'] ?>] = createChart(
                        'chart-<?= $dispositif['id'] ?>',
                        <?= json_encode($data['history'] ?? []) ?>,
                        colors.navy, // You can make this dynamic later
                        '<?= $dataKey ?>'
                    );
                <?php endif; ?>
            <?php endforeach; ?>
            
            function createChart(canvasId, historyData, color, dataKey = 'valeur', tension = 0.3) {
                const canvas = document.getElementById(canvasId);
                if (!canvas || !historyData || historyData.length === 0) return null;
                return new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: historyData.map(d => new Date(d.temps).toLocaleTimeString()),
                        datasets: [{ data: historyData.map(d => d[dataKey]), borderColor: color, backgroundColor: color + '33', fill: true, pointRadius: 0, tension: tension }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { display: false }, y: { display: false } } }
                });
            }
            
            // --- GESTION DES ACTIONNEURS ---
            document.querySelector('.dashboard-grid').addEventListener('change', function(e) {
                if (e.target.matches('.actionneur-checkbox')) {
                    const id = e.target.dataset.id;
                    const etat = e.target.checked ? 1 : 0;
                    const label = document.getElementById(`etat-label-${id}`);
                    if (label) label.textContent = etat ? 'ALLUMÉ' : 'ÉTEINT';

                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('etat', etat);

                    fetch('<?= BASE_PATH ?>/api_actionneur.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            console.error('Erreur lors du changement d\'état de l\'actionneur.');
                            // Optional: revert the switch state on failure
                            e.target.checked = !etat;
                            if (label) label.textContent = !etat ? 'ALLUMÉ' : 'ÉTEINT';
                        }
                    })
                    .catch(error => console.error('Fetch error:', error));
                }
            });
            
            // --- LIVE UPDATE (currently not implemented for dynamic cards, would need API update) ---
            // The previous live update script would need to be adapted to this new dynamic structure.
            // For now, the page is dynamic on load.
        });
    </script>
</body>
</html>