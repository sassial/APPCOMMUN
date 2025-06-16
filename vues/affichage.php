<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Tableau de Bord – Gusteau’s</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/vues/style.css">
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
<!-- DANS vues/affichage.php -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // ------------------- SETUP INITIAL -------------------
    const charts = {};
    // <<< CLARIFICATION : Constante renommée pour plus de clarté.
    // Vous pouvez changer cette valeur pour ajuster le nombre de points sur les graphiques.
    const MAX_DATA_POINTS = 50; 
    
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
            
            const values = data.history.map(d => isTempHum ? d.temperature : (d.valeur || 0));
            const max = Math.max(...values).toFixed(1);
            const min = Math.min(...values).toFixed(1);
            
            let statsHTML = '';
             if (isTempHum) {
                const temp = data.latest && data.latest.temperature ? data.latest.temperature.toFixed(1) : '--';
                const hum = data.latest && data.latest.humidite ? data.latest.humidite.toFixed(1) : '--';
                statsHTML = `
                    <div class="stat-item"><p class="stat-value"><span id="val-${capteur.id}-temp">${temp}</span><span class="unit">°C</span></p><p class="stat-label">Actuel</p></div>
                    <div class="stat-item"><p class="stat-value"><span id="val-${capteur.id}-hum">${hum}</span><span class="unit">%</span></p><p class="stat-label">Humidité</p></div>
                    <div class="stat-item"><p class="stat-value">${max}</p><p class="stat-label">Max</p></div>
                    <div class="stat-item"><p class="stat-value">${min}</p><p class="stat-label">Min</p></div>`;
            } else {
                const latestVal = data.latest && data.latest.valeur ? parseFloat(data.latest.valeur).toFixed(1) : '--';
                statsHTML = `
                    <div class="stat-item"><p class="stat-value"><span id="val-${capteur.id}-latest">${latestVal}</span><span class="unit">${sensorDetails?.unite ?? ''}</span></p><p class="stat-label">Actuel</p></div>
                    <div class="stat-item"><p class="stat-value">${max}</p><p class="stat-label">Max</p></div>
                    <div class="stat-item"><p class="stat-value">${min}</p><p class="stat-label">Min</p></div>`;
            }
            
            contentContainer.innerHTML = `
                <div class="card-stats">${statsHTML}</div>
                <div class="card-chart-container"><canvas id="chart-${capteur.id}"></canvas></div>`;

            const ctx = document.getElementById(`chart-${capteur.id}`).getContext('2d');
            
                       // Ce bloc remplace l'ancien "charts[capteur.id] = { ... };"
            charts[capteur.id] = {
                instance: new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.history.map(d => new Date(d.temps)),
                        datasets: [{
                            label: 'Valeur',
                            data: data.history.map(d => isTempHum ? d.temperature : d.valeur),
                            borderColor: navyColor,
                            backgroundColor: navyColor + '20',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0
                        }]
                    },
                    // L'OBJET 'OPTIONS' COMPLET QUI MANQUAIT EST CI-DESSOUS
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                type: 'time',
                                time: { unit: 'hour', tooltipFormat: 'HH:mm' },
                                ticks: { display: false },
                                grid: { display: false }
                            },
                            y: { 
                                beginAtZero: false, 
                                grid: { color: '#eef2f7' },
                                ticks: {
                                    callback: function(value) { return value.toFixed(0); }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            annotation: {
                                annotations: {
                                    ...(seuils[capteur.id] != null && {
                                        thresholdLine: {
                                            type: 'line',
                                            yMin: seuils[capteur.id],
                                            yMax: seuils[capteur.id],
                                            borderColor: 'rgba(255, 0, 0, 0.7)',
                                            borderWidth: 2,
                                            borderDash: [6, 6],
                                            label: {
                                                content: 'Seuil', enabled: true, position: 'start',
                                                backgroundColor: 'rgba(255, 0, 0, 0.7)', font: { size: 10 }
                                            }
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
// DANS vues/affichage.php

const mettreAJourDonnees = async () => {
    try {
        const response = await fetch('api_get_latest.php');
        if (!response.ok) return;
        const latestData = await response.json();

        for (const id_capteur in charts) {
            const chartInfo = charts[id_capteur];
            const nom_table = chartInfo.nom_table;
            const data = latestData[nom_table];

            // Si aucune donnée n'est renvoyée pour ce capteur, on passe au suivant.
            if (!data || !data.latest) continue;

            const isTempHum = (nom_table === 'capteur_temp_hum');
            
            // --- CORRECTION PRINCIPALE ICI ---
            // On va chercher la valeur dans l'objet "latest"
            const nouvelleValeur = isTempHum ? data.latest.temperature : data.latest.valeur;

            // On vérifie que la valeur existe avant de l'utiliser
            if (nouvelleValeur === undefined) continue;
            
            // Mise à jour des valeurs numériques
            if (isTempHum) {
                document.getElementById(`val-${id_capteur}-temp`).textContent = nouvelleValeur.toFixed(1);
                // On met aussi à jour l'humidité
                if (data.latest.humidite !== undefined) {
                    document.getElementById(`val-${id_capteur}-hum`).textContent = data.latest.humidite.toFixed(1);
                }
            } else {
                document.getElementById(`val-${id_capteur}-latest`).textContent = nouvelleValeur.toFixed(1);
            }

            // Mise à jour du graphique
            const chartInstance = chartInfo.instance;
            const dataset = chartInstance.data.datasets[0].data;
            const labels = chartInstance.data.labels;
            
            dataset.push(nouvelleValeur);
            labels.push(new Date()); // On ajoute la date actuelle comme étiquette

            if (dataset.length > MAX_DATA_POINTS) {
                dataset.shift();
                labels.shift();
            }
            chartInstance.update('none'); 
        }
    } catch (error) {
        console.error("Erreur lors de la mise à jour live:", error);
    }
};



    function startUpdates() {
        if (liveUpdateInterval) clearInterval(liveUpdateInterval);
        liveUpdateInterval = setInterval(mettreAJourDonnees, 5000);
    }

    function stopUpdates() {
        clearInterval(liveUpdateInterval);
    }

    document.addEventListener("visibilitychange", () => {
        document.hidden ? stopUpdates() : startUpdates();
    });

    startUpdates();

    document.querySelectorAll('.actionneur-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', async function() {
            const id = this.dataset.id;
            const etat = this.checked ? 1 : 0;
            const label = document.getElementById(`etat-label-${id}`);
            const formData = new FormData();
            formData.append('id', id);
            formData.append('etat', etat);

            try {
                const response = await fetch('api_actionneur.php', { method: 'POST', body: formData });
                const result = await response.json();
                if (result.success) {
                    label.textContent = etat ? 'ALLUMÉ' : 'ÉTEINT';
                } else {
                    this.checked = !this.checked;
                }
            } catch (error) {
                console.error('Erreur:', error);
                this.checked = !this.checked;
            }
        });
    });
});
</script>
</body>
</html>