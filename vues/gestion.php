<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Dispositifs – Gusteau’s</title>
    <link rel="stylesheet" href="<?= BASE_PATH ?>/vues/style.css">
</head>
<body>
    <?php include __DIR__ . '/header.php'; ?>
    <main class="container">
        <div class="management-page">
            <h1>Gestion des Capteurs et Actionneurs</h1>

            <!-- Formulaire d'ajout -->
            <section class="card-management">
                <h2>Ajouter un dispositif</h2>
                <form action="index.php?cible=capteurs&fonction=gestion" method="post" class="management-form">
                    <input type="hidden" name="action" value="ajouter">
                    <div class="form-row">
                        <input type="text" name="nom" placeholder="Nom d'affichage (ex: Lumière Salon)" required>
                        <select name="type" required>
                            <option value="capteur">Capteur</option>
                            <option value="actionneur">Actionneur</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <input type="text" name="nom_table_bdd" placeholder="Nom de la table BDD (ex: CapteurLumiere)" required>
                        <input type="text" name="unite" placeholder="Unité (ex: °C, lux, On/Off)">
                    </div>
                    <button type="submit" class="btn-submit">Ajouter</button>
                </form>
            </section>

            <!-- Liste des dispositifs -->
            <section class="card-management">
                <h2>Dispositifs existants</h2>
                <table class="management-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Table BDD</th>
                            <th>Unité</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dispositifs as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['nom']) ?></td>
                            <td><?= htmlspecialchars($d['type']) ?></td>
                            <td><?= htmlspecialchars($d['nom_table_bdd']) ?></td>
                            <td><?= htmlspecialchars($d['unite']) ?></td>
                            <td>
                                <form action="index.php?cible=capteurs&fonction=gestion" method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer ce dispositif ?');">
                                    <input type="hidden" name="action" value="supprimer">
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    <button type="submit" class="btn-delete">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </main>
    <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>