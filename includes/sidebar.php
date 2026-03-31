<nav class="sidebar">
    <img src="../assets/img/logov1.png" alt="Ydate" class="brand-logo">
    <ul class="nav-links">
        <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
        <li><a href="feed.php" class="<?= ($currentPage == 'feed.php') ? 'active' : '' ?>">📰 Fil d'actualité</a></li>
        <li><a href="messagerie.php" class="<?= ($currentPage == 'messagerie.php') ? 'active' : '' ?>">💬 Messagerie</a></li>
        <li><a href="profil.php" class="<?= ($currentPage == 'profil.php') ? 'active' : '' ?>">👤 Mon Profil</a></li>
        <li><a href="ydate.php" class="<?= ($currentPage == 'ydate.php') ? 'active' : '' ?>">🔥 Rencontrer</a></li>
        <li><a href="matches.php" class="<?= ($currentPage == 'matches.php') ? 'active' : '' ?>">❤️ Mes Matches</a></li>
        <li><a href="logout.php" class="logout-btn">🚪 Déconnexion</a></li>
    </ul>
</nav>