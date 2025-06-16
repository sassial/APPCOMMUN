<?php
// vues/accueil.php
?><!DOCTYPE html>
<html lang="fr">
<!-- DANS vues/accueil.php, dans <head> -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Accueil – Gusteau’s</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/vues/style.css">
    
    <!-- VÉRIFIEZ QUE CES DEUX LIGNES SONT PRÉSENTES -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
</head>
<body>

   <body>

    <?php include __DIR__ . '/header.php'; ?>

    <!-- Section Hero (Présentation) -->
    <section class="hero">
        <div class="hero-content">
            <p class="hero-subtitle">Bienvenue chez</p>
            <h1 class="hero-title">Gusteau’s</h1>
            <p class="hero-text">
                Découvrez l’ambiance unique de notre restaurant et surveillez le niveau sonore en temps réel.
            </p>
            <a href="#dashboard" class="btn btn-hero">Voir mon tableau de bord</a>
        </div>
        <div class="hero-image">
            <img src="<?= BASE_PATH ?>/photo.jpg" alt="Intérieur du restaurant Gusteau’s">
        </div>
    </section>

    <!-- Section du Dashboard Personnel -->
    <section id="dashboard" class="personal-dashboard">
        <div class="dashboard-header">
            <h2>Votre Centre de Contrôle Sonore</h2>
            <p>Analyse en temps réel et historique de l'ambiance sonore.</p>
        </div>

        <div class="dashboard-grid-perso">
            <!-- Colonne de gauche : Stats & Live -->
            <div class="main-panel">
                <!-- Valeur en direct avec une jauge -->
                <div class="card live-card">
                    <div class="live-indicator-wrapper">
                        <div class="live-indicator"></div>
                        <span>En direct</span>
                    </div>
                    <div class="live-value-container">
                        <span class="live-value"><?= htmlspecialchars(number_format($donneesSonDetaillees['live']['valeur'] ?? 0, 1)) ?></span>
                        <span class="live-unit">dB</span>
                    </div>
                    <div class="live-gauge-container">
                        <canvas id="liveGaugeChart"></canvas>
                    </div>
                </div>
                <!-- Statistiques sur 24h -->
                <div class="card stats-card">
                    <h3>Statistiques (24h)</h3>
                    <div class="stats-container">
                        <div class="stat-item-perso">
                            <span class="stat-label">MIN</span>
                            <span class="stat-value"><?= htmlspecialchars($donneesSonDetaillees['stats24h']['min'] ?? '--') ?> dB</span>
                        </div>
                        <div class="stat-item-perso">
                            <span class="stat-label">MOYENNE</span>
                            <span class="stat-value"><?= htmlspecialchars($donneesSonDetaillees['stats24h']['avg'] ?? '--') ?> dB</span>
                        </div>
                        <div class="stat-item-perso">
                            <span class="stat-label">MAX</span>
                            <span class="stat-value"><?= htmlspecialchars($donneesSonDetaillees['stats24h']['max'] ?? '--') ?> dB</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne de droite : Graphique & Alertes -->
            <div class="side-panel">
                <!-- Graphique principal de l'historique -->
                <div class="card chart-card">
                    <h4>Activité des 24 dernières heures</h4>
                    <div class="chart-container-perso">
                        <canvas id="mainSoundChart"></canvas>
                    </div>
                </div>
                <!-- Dernières alertes (seuil > 80 dB) -->
                <div class="card alerts-card">
                    <h3>Dernières Alertes (seuil > 80 dB)</h3>
                    <ul class="alerts-list">
                        <?php if (!empty($donneesSonDetaillees['alerts'])): ?>
                            <?php foreach ($donneesSonDetaillees['alerts'] as $alerte): ?>
                                <li>
                                    <span class="alert-icon">⚠️</span>
                                    <span class="alert-value"><?= round($alerte['valeur']) ?> dB</span>
                                    <span class="alert-time"><?= date('H:i', strtotime($alerte['temps'])) ?></span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="no-alerts">
                                <span class="alert-icon">✅</span>
                                Aucune alerte récente.
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/footer.php'; ?>
    
    <!-- Le script JavaScript que nous avons corrigé précédemment reste ici -->
    <script>
        // ... (collez ici le script JS complet de ma réponse précédente)
    </script>
</body>

    <!-- ============================================= -->
    <!--     SCRIPT JAVASCRIPT POUR LES GRAPHIQUES     -->
    <!-- ============================================= -->
    <!-- DANS vues/accueil.php -->

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- SETUP INITIAL (EXISTING CODE) ---
    const historyData = <?= json_encode($donneesSonDetaillees['history'] ?? []) ?>;
    const initialLiveValue = <?= json_encode($donneesSonDetaillees['live']['valeur'] ?? 0) ?>;
    const rootStyles = getComputedStyle(document.documentElement);
    const navyColor = rootStyles.getPropertyValue('--navy').trim();

    let mainChartInstance;
    let gaugeChartInstance;

    // --- GRAPHIQUE PRINCIPAL (LIGNE) ---
    const mainSoundCanvas = document.getElementById('mainSoundChart');
    if (mainSoundCanvas && historyData.length > 0) {
        mainChartInstance = new Chart(mainSoundCanvas, {
            type: 'line',
            data: {
                labels: historyData.map(d => new Date(d.temps)),
                datasets: [{
                    label: 'Niveau Sonore (dB)',
                    data: historyData.map(d => d.valeur),
                    borderColor: navyColor,
                    backgroundColor: navyColor + '20',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                }]
            },
            // ... inside new Chart(mainSoundCanvas, { ...
options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: {
            type: 'time',
            time: { unit: 'hour', tooltipFormat: 'HH:mm' },
            grid: { display: false },
            ticks: { color: '#888' }
        },
        y: {
            beginAtZero: true,
            grid: { color: '#eef2f7' },
            ticks: { color: '#888' }
        }
    }
}
// ...
        });
    }

    // --- JAUGE SEMI-CIRCULAIRE (DOUGHNUT) ---
    const liveGaugeCanvas = document.getElementById('liveGaugeChart');
    if (liveGaugeCanvas) {
        const maxGaugeValue = 120;
        gaugeChartInstance = new Chart(liveGaugeCanvas, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [initialLiveValue, maxGaugeValue - initialLiveValue],
                    backgroundColor: [navyColor, '#eef2f7'],
                    borderColor: [navyColor, '#eef2f7'],
                    borderWidth: 1,
                    circumference: 180,
                    rotation: 270
                }]
            },
            options: { /* ... options inchangées ... */ }
        });
    }

    // ================================================================
    //     NOUVELLE SECTION : MISE À JOUR LIVE POUR LA PAGE D'ACCUEIL
    // ================================================================
    let liveUpdateInterval;

    const updateHomePageData = async () => {
        try {
            const response = await fetch('api_get_latest.php');
            if (!response.ok) return;
            const latestData = await response.json();

            // On cherche spécifiquement les données du capteur sonore
            const soundData = latestData['Capteur_Son'];
            if (!soundData || !soundData.valeur) return;

            const newLiveValue = parseFloat(soundData.valeur);

            // 1. Mettre à jour la valeur numérique
            document.querySelector('.live-value').textContent = newLiveValue.toFixed(1);

            // 2. Mettre à jour la jauge
            if (gaugeChartInstance) {
                const maxGaugeValue = 120;
                gaugeChartInstance.data.datasets[0].data[0] = newLiveValue;
                gaugeChartInstance.data.datasets[0].data[1] = maxGaugeValue - newLiveValue;
                gaugeChartInstance.update('none');
            }

            // 3. Mettre à jour le graphique principal (optionnel mais recommandé)
            // VERSION CORRIGÉE de la fonction updateHomePageData

const updateHomePageData = async () => {
    try {
        const response = await fetch('api_get_latest.php');
        if (!response.ok) return;
        const latestData = await response.json();

        const soundData = latestData['Capteur_Son'];
        if (!soundData || !soundData.valeur) return;

        const newLiveValue = parseFloat(soundData.valeur);

        // 1. Mettre à jour la valeur numérique (ON GARDE)
        document.querySelector('.live-value').textContent = newLiveValue.toFixed(1);

        // 2. Mettre à jour la jauge (ON GARDE)
        if (gaugeChartInstance) {
            const maxGaugeValue = 120;
            gaugeChartInstance.data.datasets[0].data[0] = newLiveValue;
            gaugeChartInstance.data.datasets[0].data[1] = maxGaugeValue - newLiveValue;
            gaugeChartInstance.update('none');
        }

        // Le bloc pour mettre à jour le graphique principal a été supprimé d'ici.

    } catch (error) {
        console.error("Erreur de mise à jour (accueil):", error);
    }
};

        } catch (error) {
            console.error("Erreur de mise à jour (accueil):", error);
        }
    };

    function startHomeUpdates() {
        if (liveUpdateInterval) clearInterval(liveUpdateInterval);
        liveUpdateInterval = setInterval(updateHomePageData, 5000);
        console.log("Mises à jour (accueil) démarrées.");
    }

    function stopHomeUpdates() {
        clearInterval(liveUpdateInterval);
        console.log("Mises à jour (accueil) arrêtées.");
    }

    document.addEventListener("visibilitychange", () => {
        document.hidden ? stopHomeUpdates() : startHomeUpdates();
    });

    startHomeUpdates();

});

// Script pour le header (inchangé)
const header = document.querySelector('.site-header');
window.addEventListener('scroll', () => {
  if (window.scrollY > 10) {
    header.classList.add('scrolled');
  } else {
    header.classList.remove('scrolled');
  }
});
</script>
</body>
</html>