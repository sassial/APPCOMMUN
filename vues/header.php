<header class="site-header">
  <div class="header-container">
    <a href="/APPCOMMUN/index.php?cible=utilisateurs&fonction=accueil" class="logo-link">
      <img src="/APPCOMMUN/logo.jpg" alt="Logo Gusteau’s" class="logo"/>
    </a>
    <nav class="main-nav">
      <ul>
        <?php if (isset($_SESSION['utilisateur'])): ?>
            <!-- =============================== -->
            <!--   MENU POUR UTILISATEUR CONNECTÉ -->
            <!-- =============================== -->
            <li><span class="user-welcome">Bienvenue, <?= htmlspecialchars($_SESSION['utilisateur']['prenom']) ?></span></li>
            <li><a href="/APPCOMMUN/index.php?cible=utilisateurs&fonction=accueil#capteur" class="<?= ($_GET['fonction'] === 'accueil') ? 'active' : '' ?>">Capteur son</a></li>
            <?php if (!empty($_SESSION['utilisateur']['role']) && $_SESSION['utilisateur']['role'] == 'admin'): ?>
                <li><a href="index.php?cible=capteurs&fonction=affichage" class="<?= ($_GET['fonction'] === 'affichage') ? 'active' : '' ?>">Autres capteurs</a></li>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['utilisateur']['role']) && $_SESSION['utilisateur']['role'] === 'admin'): ?>
    <li><a href="/APPCOMMUN/index.php?cible=capteurs&fonction=gestion" class="<?= ($_GET['fonction'] === 'gestion') ? 'active' : '' ?>">Gestion</a></li>
<?php endif; ?>

            <li><a href="/APPCOMMUN/index.php?cible=utilisateurs&fonction=logout">Déconnexion</a></li>

        <?php else: ?>
            <!-- =============================== -->
            <!--      MENU POUR VISITEUR NON CONNECTÉ     -->
            <!-- =============================== -->
            <li><a href="index.php?cible=utilisateurs&fonction=login" class="<?= ($_GET['fonction'] === 'login') ? 'active' : '' ?>">Connexion</a></li>
            <li><a href="index.php?cible=utilisateurs&fonction=inscription" class="<?= ($_GET['fonction'] === 'inscription') ? 'active' : '' ?>">Inscription</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>