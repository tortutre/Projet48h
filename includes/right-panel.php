<?php
global $pdo;

// 1. On va chercher les 4 dernières actualités (type 'news')
$stmtNews = $pdo->query("SELECT * FROM news_offers WHERE type = 'news' ORDER BY created_at DESC LIMIT 4");
$newsList = $stmtNews->fetchAll();

// 2. On va chercher les 4 dernières offres (type 'offer')
$stmtOffers = $pdo->query("SELECT * FROM news_offers WHERE type = 'offer' ORDER BY created_at DESC LIMIT 4");
$offersList = $stmtOffers->fetchAll();
?>

<aside class="right-sidebar">
    <div class="widget">
        <h3>📰 News Ynov</h3>
        <ul style="list-style: none; padding: 0;">
            <?php if (empty($newsList)): ?>
                <li><small style="color: var(--text-muted);">Aucune actualité pour le moment.</small></li>
            <?php else: ?>
                <?php foreach ($newsList as $news): ?>
                    <li style="margin-bottom: 12px;">
                        <strong style="color: var(--text-main); display: block; margin-bottom: 3px;">
                            <?= htmlspecialchars($news['title']) ?>
                        </strong> 
                        <span style="color: var(--text-muted); font-size: 13px;">
                            <?= htmlspecialchars($news['content']) ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div class="widget">
        <h3>💼 Ymatch - Offres</h3>
        <ul style="list-style: none; padding: 0;">
            <?php if (empty($offersList)): ?>
                <li><small style="color: var(--text-muted);">Aucune offre pour le moment.</small></li>
            <?php else: ?>
                <?php foreach ($offersList as $offer): ?>
                    <li style="margin-bottom: 12px;">
                        <strong style="color: var(--text-main); display: block; margin-bottom: 3px;">
                            <?= htmlspecialchars($offer['title']) ?>
                        </strong> 
                        <span style="color: var(--text-muted); font-size: 13px;">
                            <?= htmlspecialchars($offer['content']) ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div class="widget" style="text-align: center; border-top: 1px solid var(--border-color); padding-top: 20px;">
        <h3 style="margin-bottom: 15px;">🚀 Plateforme Ynov</h3>
        <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 15px;">Retrouvez toutes vos opportunités sur l'app officielle.</p>
        <a href="https://ymatch.ynov.com" target="_blank" class="btn btn-send" style="display: block; text-decoration: none; line-height: 48px;">
            Ouvrir Ymatch
        </a>
    </div>
</aside>