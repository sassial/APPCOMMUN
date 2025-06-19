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

    <main class="container-full">
        <h1>Tableau de Bord des Capteurs</h1>
        <div class="dashboard-grid">

            <!-- Carte Météo Externe -->
            <div class="dashboard-card card">
                <div class="card-header">
                    <span class="card-icon">🌍</span>
                    <h2 class="card-title">Météo Externe (Paris)</h2>
                </div>
                <div class="card-content-simple">
                    <p class="big-value"><?= isset($temperatureExterne) ? number_format($temperatureExterne, 1) : 'N/A' ?> °C</p>
                    <p class="stat-label">Température actuelle</p>
                </div>
            </div>
            
            <!-- Boucle dynamique pour les CAPTEURS ACTIFS -->
            <?php foreach ($dispositifs_capteurs as &$capteur):
                $data = $donnees_capteurs[$capteur['id']] ?? null;
                if (!$data) continue; // Ne pas afficher la carte si pas de données
            ?>
                <div class="dashboard-card card">
                    <div class="card-header">
                        <span class="card-icon">#️⃣</span>
                        <h2 class="card-title"><?= htmlspecialchars($capteur['nom']) ?></h2>
                    </div>
                    <div class="card-content">
                        <div class="card-stats">
                            <?php if ($capteur['nom_table_bdd'] === 'capteur_temp_hum'): ?>
                                <div class="stat-item"><p class="stat-value"><span id="val-<?= $capteur['id'] ?>-temp"><?= $data['latest']['temperature'] ?? '--' ?></span><span class="unit">°C</span></p><p class="stat-label">Temp. Actuelle</p></div>
                                <div class="stat-item"><p class="stat-value"><span id="val-<?= $capteur['id'] ?>-hum"><?= $data['latest']['humidite'] ?? '--' ?></span><span class="unit">%</span></p><p class="stat-label">Humidité</p></div>
                                <div class="stat-item"><p class="stat-value"><span id="val-<?= $capteur['id'] ?>-max"><?= $data['max_temp'] ?? '--' ?></span><span class="unit">°C</span></p><p class="stat-label">Temp. Max</p></div>
                                <div class="stat-item"><p class="stat-value"><span id="val-<?= $capteur['id'] ?>-min"><?= $data['min_temp'] ?? '--' ?></span><span class="unit">°C</span></p><p class="stat-label">Temp. Min</p></div>
                            <?php else: ?>
                                <div class="stat-item"><p class="stat-value"><span id="val-<?= $capteur['id'] ?>-actuel"><?= $data['latest']['valeur'] ?? '--' ?></span><span class="unit"><?= htmlspecialchars(getUnite($capteur['nom_table_bdd'])) ?></span></p><p class="stat-label">Actuel</p></div>
                                <div class="stat-item"><p class="stat-value"><span id="val-<?= $capteur['id'] ?>-max"><?= $data['max'] ?? '--' ?></span></p><p class="stat-label">Max</p></div>
                                <div class="stat-item"><p class="stat-value"><span id="val-<?= $capteur['id'] ?>-min"><?= $data['min'] ?? '--' ?></span></p><p class="stat-label">Min</p></div>
                            <?php endif; ?>
                        </div>
                        <div class="card-chart-container"><canvas id="chart-<?= $capteur['id'] ?>"></canvas></div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Boucle dynamique pour les ACTIONNEURS ACTIFS -->
            <?php foreach ($dispositifs_actionneurs as $actionneur):
                $etat_actuel = $etats_actionneurs[$actionneur['id']] ?? 0;
            ?>
                <div class="dashboard-card card">
                    <div class="card-header">
                        <span class="card-icon">⚡</span>
                        <h2 class="card-title"><?= htmlspecialchars($actionneur['nom']) ?></h2>
                    </div>
                    <div class="card-content-simple">
                        <label class="switch">
                            <input type="checkbox" class="actionneur-checkbox" data-id="<?= $actionneur['id'] ?>" <?= $etat_actuel ? 'checked' : '' ?>>
                            <span class="slider"></span>
                        </label>
                        <p class="stat-label" id="etat-label-<?= $actionneur['id'] ?>"><?= $etat_actuel ? 'ALLUMÉ' : 'ÉTEINT' ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </main>

    <?php include __DIR__ . '/footer.php'; ?>

    <script>
        // Le JavaScript complet pour les graphiques et les actionneurs va ici
        // (Il est identique à la version que je vous ai fournie précédemment,
        // vous pouvez la copier-coller ici si elle n'y est plus).
    </script>
</body>
</html>