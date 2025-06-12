<header class="site-header">
  <div class="header-container">
    <a href="<?= BASE_PATH ?>/index.php?cible=utilisateurs&fonction=accueil" class="logo-link">
      <img src="<?= BASE_PATH ?>/logo.jpg" alt="Logo Gusteau’s" class="logo"/>
    </a>
    <nav class="main-nav">
      <ul>
        <?php if (isset($_SESSION['utilisateur'])): ?>
            <!-- Menu pour utilisateur CONNECTÉ -->
            <li><a href="#">Bienvenue, <?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?></a></li>
            <li><a href="<?= BASE_PATH ?>/index.php?cible=utilisateurs&fonction=accueil#capteur">Capteur son</a></li>
            <li><a href="<?= BASE_PATH ?>/index.php?cible=capteurs&fonction=affichage">Autres capteurs</a></li>
            <li><a href="<?= BASE_PATH ?>/index.php?cible=utilisateurs&fonction=logout">Déconnexion</a></li>
        <?php else: ?>
            <!-- Menu pour VISITEUR -->
            <li><a href="<?= BASE_PATH ?>/index.php?cible=utilisateurs&fonction=login">Connexion</a></li>
            <li><a href="<?= BASE_PATH ?>/index.php?cible=utilisateurs&fonction=inscription">Inscription</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>