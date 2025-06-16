<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Dispositifs</title>
    <link rel="stylesheet" href="/APPCOMMUN/vues/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
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

        <!-- NOUVELLE SECTION pour les seuils d'alerte -->
        <div class="gestion-group" style="margin-top: 2rem;">
            <h3>Seuils d'Alerte par E-mail</h3>
      
<ul class="seuil-list">
    <?php foreach ($tousLesCapteurs as $c): ?>
        <?php
            // Logique pour choisir la bonne icône
            $icon = 'fa-microchip';
            $nom_capteur = strtolower($c['nom']);
            if (str_contains($nom_capteur, 'son')) $icon = 'fa-volume-up';
            if (str_contains($nom_capteur, 'lumière')) $icon = 'fa-sun';
            if (str_contains($nom_capteur, 'température')) $icon = 'fa-thermometer-half';
            if (str_contains($nom_capteur, 'proximité')) $icon = 'fa-ruler-horizontal';
            if (str_contains($nom_capteur, 'gaz')) $icon = 'fa-smog';
        ?>
        <li>
            <!-- PARTIE 1 : Le nom du capteur avec son icône -->
            <span class="seuil-nom"><i class="fas <?= $icon ?>"></i> <?= htmlspecialchars($c['nom']) ?></span>
            
            <!-- PARTIE 2 : Le formulaire qui avait disparu -->
            <form method="post" action="index.php?cible=capteurs&fonction=gestion&action=update_seuil" class="seuil-form">
                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                <input type="number" name="seuil" step="0.1" value="<?= htmlspecialchars($c['seuil'] ?? '') ?>" placeholder="Aucun seuil">
                <button type="submit" class="btn-save" title="Sauvegarder">✓</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>
        </div>
    </main>
    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>