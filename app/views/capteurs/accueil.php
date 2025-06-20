<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Accueil – Gusteau’s</title>
    <link rel="stylesheet" href="/APPCOMMUN/public/assets/css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js" defer></script>
</head>
<body>
    <?php include __DIR__ . '/../layouts/header.php'; ?>

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
            <img src="/APPCOMMUN/photo.jpg" alt="Intérieur du restaurant Gusteau’s" />
        </div>
    </section>

    <div class="greenframe-stats card">
        <h3>🌿 Empreinte écologique du site</h3>
        <ul style="list-style: none; padding: 0;">
            <li>
                💡 <strong>Énergie</strong> :
                <?= htmlspecialchars($greenframeData['energy'] ?? '--') ?> mWh
                <span style="margin-left: 0.5rem; font-style: italic; color: #4caf50;">
                    <?= $ecoEnergy['emoji'] ?> <?= $ecoEnergy['label'] ?>
                </span>
            </li>
            <li>
                🌍 <strong>CO₂</strong> :
                <?= htmlspecialchars($greenframeData['carbon'] ?? '--') ?> mg eq. CO₂
                <span style="margin-left: 0.5rem; font-style: italic; color: #4caf50;">
                    <?= $ecoCarbon['emoji'] ?> <?= $ecoCarbon['label'] ?>
                </span>
            </li>
            <li>
                📊 <strong>Incertitude</strong> :
                ± <?= htmlspecialchars($greenframeData['uncertainty'] ?? '0') ?> %
            </li>
        </ul>
    </div>

    <section id="dashboard" class="personal-dashboard">
        <div class="dashboard-header">
            <h2>Votre Centre de Contrôle Sonore</h2>
            <p>Analyse en temps réel et historique de l'ambiance sonore.</p>
        </div>

        <div class="dashboard-grid-perso">
            <div class="main-panel">
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

            <div class="side-panel">
                <div class="card chart-card">
                    <h4>Activité des 24 dernières heures</h4>
                    <div class="chart-container-perso">
                        <canvas id="mainSoundChart"></canvas>
                    </div>
                </div>

                <div class="card alerts-card">
                    <h3>Dernières Alertes (seuil > 80 dB)</h3>
                    <ul>
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

    <?php include __DIR__ . '/../layouts/footer.php'; ?>
    <script id="historyData" type="application/json"><?= json_encode($donneesSonDetaillees['history'] ?? []) ?></script>
    <script id="liveValue" type="application/json"><?= json_encode($donneesSonDetaillees['live']['valeur'] ?? 0) ?></script>
    <script src="/APPCOMMUN/public/assets/js/accueil.js" defer></script>
    

</body>
</html>