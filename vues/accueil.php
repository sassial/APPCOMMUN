


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

<!-- =================================================================== -->
<!--      NOUVEAU TABLEAU DE BORD PERSONNEL POUR LE CAPTEUR DE SON       -->
<!-- =================================================================== -->
<section id="capteur" class="personal-dashboard">
    <div class="dashboard-header-perso">
        <h2>Votre Centre de Contrôle Sonore</h2>
        <p>Analyse en temps réel et historique de l'ambiance sonore.</p>
    </div>

    <div class="dashboard-grid-perso">
        <!-- Colonne de gauche : Stats & Live -->
        <div class="main-panel">
            <!-- Valeur en direct -->
            <div class="card-perso live-card">
                <div class="live-indicator"></div>
                <div class="live-value-container">
                    <div class="live-value"><?= $donneesSonDetaillees['live']['valeur'] ?? '--' ?></div>
                    <div class="live-unit">dB</div>
                </div>
                <div id="live-gauge"></div>
            </div>
            <!-- Statistiques sur 24h -->
            <div class="card-perso stats-card">
                <h3>Statistiques (24h)</h3>
                <div class="stats-container">
                    <div class="stat-item-perso">
                        <span class="stat-label">MIN</span>
                        <span class="stat-value"><?= $donneesSonDetaillees['stats24h']['min'] ?? '--' ?> dB</span>
                    </div>
                    <div class="stat-item-perso">
                        <span class="stat-label">MOY</span>
                        <span class="stat-value"><?= $donneesSonDetaillees['stats24h']['avg'] ?? '--' ?> dB</span>
                    </div>
                    <div class="stat-item-perso">
                        <span class="stat-label">MAX</span>
                        <span class="stat-value"><?= $donneesSonDetaillees['stats24h']['max'] ?? '--' ?> dB</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne de droite : Graphique & Alertes -->
        <div class="side-panel">
            <!-- Graphique principal -->
            <div class="card-perso chart-card">
                <div class="chart-header">
                    <h4>Activité des 24 dernières heures</h4>
                </div>
                <div class="chart-container-perso">
                    <canvas id="mainSoundChart"></canvas>
                </div>
            </div>
            <!-- Dernières alertes -->
            <div class="card-perso alerts-card">
                <h3>Dernières Alertes (seuil > 80 dB)</h3>
                <ul class="alerts-list">
                    <?php if (!empty($donneesSonDetaillees['alerts'])): ?>
                        <?php foreach ($donneesSonDetaillees['alerts'] as $alerte): ?>
                            <li>
                                <span class="alert-value"><?= round($alerte['valeur']) ?> dB</span>
                                <span class="alert-time"><?= date('H:i', strtotime($alerte['temps'])) ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="no-alerts">Aucune alerte récente.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Le CSS et le JS pour ce composant -->
<style>
    .personal-dashboard {
        background-color: #f0f4f8; padding: 3rem 2rem;
    }
    .dashboard-header-perso { text-align: center; margin-bottom: 2.5rem; }
    .dashboard-header-perso h2 { font-size: 2.25rem; color: var(--navy); }
    .dashboard-header-perso p { font-size: 1.1rem; color: #6a7c92; }
    
    .dashboard-grid-perso {
        display: grid; grid-template-columns: 1fr 2fr;
        gap: 1.5rem; max-width: 1200px; margin: auto;
    }
    .card-perso {
        background-color: #fff; border-radius: 12px; padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.07);
    }
    .main-panel, .side-panel { display: flex; flex-direction: column; gap: 1.5rem; }

    /* Carte Live */
    .live-card { text-align: center; }
    .live-indicator {
        width: 12px; height: 12px; background-color: #48bb78;
        border-radius: 50%; margin: 0 auto 1rem;
        box-shadow: 0 0 10px #48bb78;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
    .live-value-container { display: flex; justify-content: center; align-items: baseline; }
    .live-value { font-size: 4rem; font-weight: 700; color: var(--navy); }
    .live-unit { font-size: 1.5rem; font-weight: 600; color: #a0aec0; margin-left: 0.5rem; }
    #live-gauge { height: 100px; }

    /* Carte Stats */
    .stats-card h3 { font-size: 1.1rem; margin-bottom: 1rem; color: var(--navy); }
    .stats-container { display: flex; justify-content: space-around; text-align: center; }
    .stat-label { display: block; font-size: 0.8rem; color: #a0aec0; }
    .stat-value { font-size: 1.5rem; font-weight: 600; }

    /* Carte Graphique */
    .chart-card h4 { font-size: 1.1rem; color: var(--navy); }
    .chart-container-perso { height: 250px; }
    
    /* Carte Alertes */
    .alerts-card h3 { font-size: 1.1rem; margin-bottom: 1rem; color: var(--navy); }
    .alerts-list { list-style: none; padding: 0; }
    .alerts-list li {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.75rem 0; border-bottom: 1px solid #edf2f7;
    }
    .alerts-list li:last-child { border-bottom: none; }
    .alert-value { font-weight: 600; color: var(--red); }
    .alert-time { font-size: 0.9rem; color: #718096; }
    .no-alerts { color: #a0aec0; text-align: center; padding: 1rem 0; }
</style>

<script>
    // On attend que Chart.js soit chargé
    document.addEventListener('DOMContentLoaded', () => {
        const historyData = <?= json_encode($donneesSonDetaillees['history'] ?? []) ?>;
        
        // --- GRAPHIQUE PRINCIPAL (24H) ---
        if (historyData.length > 0) {
            new Chart(document.getElementById('mainSoundChart'), {
                type: 'line',
                data: {
                    labels: historyData.map(d => new Date(d.temps)),
                    datasets: [{
                        label: 'Niveau Sonore (dB)',
                        data: historyData.map(d => d.valeur),
                        borderColor: 'rgba(44, 82, 130, 0.8)',
                        backgroundColor: 'rgba(44, 82, 130, 0.1)',
                        fill: true, tension: 0.4, pointRadius: 0
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        x: { type: 'time', time: { unit: 'hour' }, grid: { display: false } },
                        y: { beginAtZero: true, grid: { color: '#e2e8f0' } }
                    }
                }
            });
        }

        // --- JAUGE (GAUGE) LIVE ---
        // Chart.js ne fait pas de jauges nativement. On va simuler avec un doughnut.
        const liveValue = <?= $donneesSonDetaillees['live']['valeur'] ?? 0 ?>;
        const gaugeData = {
            datasets: [{
                data: [liveValue, 120 - liveValue], // 120dB = max de la jauge
                backgroundColor: ['var(--navy)', '#e2e8f0'],
                borderWidth: 0,
                circumference: 180, // Demi-cercle
                rotation: 270 // Commence en bas
            }]
        };
        new Chart(document.getElementById('live-gauge'), {
            type: 'doughnut',
            data: gaugeData,
            options: { responsive: true, maintainAspectRatio: true, cutout: '80%' }
        });
    });
</script>

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
