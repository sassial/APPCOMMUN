<?php
// vues/accueil.php
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Accueil – Gusteau’s</title>
    <link rel="stylesheet" href="/APPCOMMUN/vues/style.css">
    <!-- On inclut Chart.js et l'adaptateur de date -->
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
            <img src="/APPCOMMUN/photo.jpg" alt="Intérieur du restaurant Gusteau’s">
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Récupération des données PHP
            const historyData = <?= json_encode($donneesSonDetaillees['history'] ?? []) ?>;
            const liveValue = <?= json_encode($donneesSonDetaillees['live']['valeur'] ?? 0) ?>;
            const rootStyles = getComputedStyle(document.documentElement);
            const navyColor = rootStyles.getPropertyValue('--navy').trim();

            // --- GRAPHIQUE PRINCIPAL (LIGNE) ---
            const mainSoundCanvas = document.getElementById('mainSoundChart');
            if (mainSoundCanvas && historyData.length > 0) {
                new Chart(mainSoundCanvas, {
                    type: 'line',
                    data: {
                        labels: historyData.map(d => new Date(d.temps)),
                        datasets: [{
                            label: 'Niveau Sonore (dB)',
                            data: historyData.map(d => d.valeur),
                            borderColor: navyColor,
                            backgroundColor: navyColor + '20', // Ajoute de la transparence
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0
                        }]
                    },
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
                });
            }

            // --- JAUGE SEMI-CIRCULAIRE (DOUGHNUT) ---
            const liveGaugeCanvas = document.getElementById('liveGaugeChart');
            if (liveGaugeCanvas) {
                const maxGaugeValue = 120; // Valeur max de la jauge (ex: 120 dB)
                new Chart(liveGaugeCanvas, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [liveValue, maxGaugeValue - liveValue],
                            backgroundColor: [navyColor, '#eef2f7'],
                            borderColor: [navyColor, '#eef2f7'],
                            borderWidth: 1,
                            circumference: 180, // Demi-cercle
                            rotation: 270       // Commence en bas
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '80%', // Épaisseur de l'anneau
                        plugins: { tooltip: { enabled: false } }
                    }
                });
            }
        });
    </script>
    
    <!-- Script pour le header qui change de couleur au scroll -->
    <script>
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