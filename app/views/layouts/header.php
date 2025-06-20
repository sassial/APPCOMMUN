<header class="site-header">
  <div class="header-container">
    <a href="<?= BASE_PATH ?>index.php?cible=capteurs&fonction=accueil" class="logo-link">
      <img src="<?= BASE_PATH ?>/assets/img/logo.jpg" alt="Logo Gusteau’s" class="logo"/>
    </a>
    <nav class="main-nav">
      <ul>
        <?php if (isset($_SESSION['utilisateur'])): ?>
            <!--   MENU POUR UTILISATEUR CONNECTÉ -->
            <li><strong><span style="color: gold;">Bienvenue, <?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?></span></strong></li>
            <li><a href="<?= BASE_PATH ?>index.php?cible=capteurs&fonction=accueil#capteur">Capteur son</a></li>
            <li><a href="<?= BASE_PATH ?>index.php?cible=capteurs&fonction=affichage">Autres capteurs</a></li>
            <li><a href="<?= BASE_PATH ?>index.php?cible=capteurs&fonction=gestion">Gestion</a></li>
            <li><a href="<?= BASE_PATH ?>index.php?cible=utilisateurs&fonction=logout">Déconnexion</a></li>

        <?php else: ?>
            <!--      MENU POUR VISITEUR NON CONNECTÉ     -->
            <li><a href="<?= BASE_PATH ?>index.php?cible=utilisateurs&fonction=login">Connexion</a></li>
            <li><a href="<?= BASE_PATH ?>index.php?cible=utilisateurs&fonction=inscription">Inscription</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>