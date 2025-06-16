<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Tableau de Bord – Gusteau’s</title>
    <link rel="stylesheet" href="/APPCOMMUN/vues/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js"></script>
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    <main class="container-full">
        <h1>Tableau de Bord des Capteurs</h1>
        <div class="dashboard-grid">

            <?php if (isset($citation) && $citation): ?>
                <div class="dashboard-card card">
                    <div class="card-header"><span class="card-icon"><i class="fas fa-lightbulb"></i></span><h2 class="card-title">Inspiration du Jour</h2></div>
                    <div class="card-content-simple"><p class="quote-text">« <?= htmlspecialchars($citation['texte']) ?> »</p><p class="quote-author">— <?= htmlspecialchars($citation['auteur']) ?></p></div>
                </div>
            <?php endif; ?>

            <?php foreach ($dispositifs_capteurs as $capteur):
                $icon = 'fa-microchip';
                $nom_capteur = strtolower($capteur['nom']);
                if (str_contains($nom_capteur, 'son')) $icon = 'fa-volume-up';
                if (str_contains($nom_capteur, 'lumière')) $icon = 'fa-sun';
                if (str_contains($nom_capteur, 'température')) $icon = 'fa-thermometer-half';
                if (str_contains($nom_capteur, 'proximité')) $icon = 'fa-ruler-horizontal';
                if (str_contains($nom_capteur, 'gaz')) $icon = 'fa-smog';
            ?>
                <div class="dashboard-card card" id="card-<?= $capteur['id'] ?>">
                    <div class="card-header"><span class="card-icon"><i class="fas <?= $icon ?>"></i></span><h2 class="card-title"><?= htmlspecialchars($capteur['nom']) ?></h2></div>
                    <div class="card-content">
                        <!-- Ce conteneur sera rempli par le JavaScript -->
                    </div>
                </div>
            <?php endforeach; ?>

            <?php foreach ($dispositifs_actionneurs as $actionneur):
                $etat_actuel = $etats_actionneurs[$actionneur['id']] ?? 0;
            ?>
                <div class="dashboard-card card">
                    <div class="card-header"><span class="card-icon"><i class="fas fa-power-off"></i></span><h2 class="card-title"><?= htmlspecialchars($actionneur['nom']) ?></h2></div>
                    <div class="card-content-simple">
                        <label class="switch"><input type="checkbox" class="actionneur-checkbox" data-id="<?= $actionneur['id'] ?>" <?= $etat_actuel ? 'checked' : '' ?>><span class="slider"></span></label>
                        <p class="stat-label" id="etat-label-<?= $actionneur['id'] ?>"><?= $etat_actuel ? 'ALLUMÉ' : 'ÉTEINT' ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // ------------------- SETUP INITIAL -------------------
        const charts = {};
        const rootStyles = getComputedStyle(document.documentElement);
        const navyColor = rootStyles.getPropertyValue('--navy').trim();
        const seuils = <?= json_encode($seuils_graphiques ?? []) ?>;
        const initialData = <?= json_encode($donnees_capteurs ?? []) ?>;
        const capteursActifs = <?= json_encode($dispositifs_capteurs ?? []) ?>;
        const allSensorDetails = <?= json_encode($tous_les_capteurs_details ?? []) ?>;
        let liveUpdateInterval;

        // ------------------- INITIALISATION DES CARTES -------------------
        capteursActifs.forEach(capteur => {
            const card = document.getElementById(`card-${capteur.id}`);
            if (!card) return;

            const contentContainer = card.querySelector('.card-content');
            const data = initialData[capteur.id];

            if (data && data.history && data.history.length > 0) {
                const isTempHum = (capteur.nom_table_bdd === 'capteur_temp_hum');
                const sensorDetails = allSensorDetails.find(d => d.id == capteur.id);
                
                const values = data.history.map(d => isTempHum ? d.temperature : d.valeur);
                const max = Math.max(...values).toFixed(1);
                const min = Math.min(...values).toFixed(1);
                
                let statsHTML = '';
                if (isTempHum) {
                    statsHTML = `
                        <div class="stat-item"><p class="stat-value"><span id="val-${capteur.id}-temp">${data.latest?.temperature ?? '--'}</span><span class="unit">°C</span></p><p class="stat-label">Actuel</p></div>
                        <div class="stat-item"><p class="stat-value"><span id="val-${capteur.id}-hum">${data.latest?.humidite ?? '--'}</span><span class="unit">%</span></p><p class="stat-label">Humidité</p></div>
                        <div class="stat-item"><p class="stat-value">${max}</p><p class="stat-label">Max</p></div>
                        <div class="stat-item"><p class="stat-value">${min}</p><p class="stat-label">Min</p></div>`;
                } else {
                    statsHTML = `
                        <div class="stat-item"><p class="stat-value"><span id="val-${capteur.id}-latest">${data.latest?.valeur ?? '--'}</span><span class="unit">${sensorDetails?.unite ?? ''}</span></p><p class="stat-label">Actuel</p></div>
                        <div class="stat-item"><p class="stat-value">${max}</p><p class="stat-label">Max</p></div>
                        <div class="stat-item"><p class="stat-value">${min}</p><p class="stat-label">Min</p></div>`;
                }
                
                contentContainer.innerHTML = `
                    <div class="card-stats">${statsHTML}</div>
                    <div class="card-chart-container"><canvas id="chart-${capteur.id}"></canvas></div>`;

                const ctx = document.getElementById(`chart-${capteur.id}`).getContext('2d');
                charts[capteur.id] = {
                    instance: new Chart(ctx, {
                        type: 'line',
                        data: {
                             labels: data.history.map(() => ''),
                            datasets: [{
                                label: 'Valeur', data: data.history.map(d => isTempHum ? d.temperature : d.valeur),
                                borderColor: navyColor, backgroundColor: navyColor + '20', fill: true, tension: 0.4, pointRadius: 0
                            }]
                        },
                       options: {
    responsive: true, maintainAspectRatio: false,
    scales: {
        // ----- MODIFIEZ CE BLOC 'x' -----
        x: {
            // On retire type: 'time'
            ticks: {
                display: false // On cache les étiquettes de l'axe X pour un look plus propre
            },
            grid: {
                display: false // On cache la grille verticale
            }
        },
        // ----- FIN DE LA MODIFICATION -----
        y: { 
            beginAtZero: false, 
            grid: { color: '#eef2f7' } 
        }
    },
                            plugins: {
                                legend: { display: false },
                                annotation: {
                                    annotations: {
                                        ...(seuils[capteur.id] && {
                                            thresholdLine: {
                                                type: 'line', yMin: seuils[capteur.id], yMax: seuils[capteur.id],
                                                borderColor: 'rgba(255, 0, 0, 0.7)', borderWidth: 2, borderDash: [6, 6],
                                                label: { content: 'Seuil', enabled: true, position: 'start', backgroundColor: 'rgba(255, 0, 0, 0.7)', font: { size: 10 } }
                                            }
                                        })
                                    }
                                }
                            }
                        }
                    }),
                    nom_table: capteur.nom_table_bdd
                };
            } else {
                contentContainer.innerHTML = `<div class="no-data-message"><i class="fas fa-satellite-dish"></i><p>En attente de données...</p></div>`;
            }
        });

        // ------------------- FONCTION DE MISE À JOUR LIVE -------------------
        const mettreAJourDonnees = async () => { /* ... (code inchangé) ... */ };

        // ------------------- GESTION DE L'INTERVALLE -------------------
        function startUpdates() { /* ... (code inchangé) ... */ }
        function stopUpdates() { /* ... (code inchangé) ... */ }
        document.addEventListener("visibilitychange", () => document.hidden ? stopUpdates() : startUpdates());
        startUpdates();

        // ------------------- GESTION DES ACTIONNEURS -------------------
        document.querySelectorAll('.actionneur-checkbox').forEach(checkbox => { /* ... (code inchangé) ... */ });
    });
    </script>
</body>
</html>