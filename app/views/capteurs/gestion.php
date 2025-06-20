<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Dispositifs</title>
    <link rel="stylesheet" href="/APPCOMMUN/public/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/../layouts/header.php'; ?>
    <main class="container-full">
        <h1>Gestion des Dispositifs</h1>

        <!-- Section pour activer/désactiver les capteurs -->
        <div class="gestion-container">
            <div class="gestion-group">
                <h3>Capteurs Actifs sur le Dashboard</h3>
                <ul class="capteur-list">
                    <?php foreach ($capteursActifs as $c): ?>
                        <li><span><?= htmlspecialchars($c['nom']) ?></span>
                            <form method="post" action="index.php?cible=capteurs&fonction=gestion&action=toggle"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button type="submit" title="Désactiver">–</button></form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="gestion-group">
                <h3>Capteurs Inactifs</h3>
                <ul class="capteur-list">
                    <?php foreach ($capteursInactifs as $c): ?>
                        <li><span><?= htmlspecialchars($c['nom']) ?></span>
                            <form method="post" action="index.php?cible=capteurs&fonction=gestion&action=toggle"><input type="hidden" name="id" value="<?= $c['id'] ?>"><button type="submit" title="Activer">+</button></form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>