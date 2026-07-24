<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/cart.php';
$base = BASE_URL;

$nav_categories = $pdo->query("SELECT * FROM categories ORDER BY ordre")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' — DermaSoin' : 'DermaSoin — Médecine esthétique & soins premium' ?></title>
    <meta name="description" content="DermaSoin — Produits de médecine esthétique et soins premium, livraison partout en Algérie.">

    <!-- FAVICON - Logo JPG -->
    <link rel="icon" type="image/jpeg" href="<?= $base ?>/assets/img/logo.jpg">
    <link rel="shortcut icon" href="<?= $base ?>/assets/img/logo.jpg">

    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a href="<?= $base ?>/index.php" class="logo">
            <img src="<?= $base ?>/assets/img/logo.jpg" alt="DermaSoin" class="logo-icon" width="48" height="48">
            Derma<span>Soin</span>
        </a>

        <!-- BOUTON HAMBURGER (indispensable pour le menu mobile) -->
        <button class="menu-toggle" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="main-nav">
            <a href="<?= $base ?>/index.php">Accueil</a>
            <a href="<?= $base ?>/boutique.php">Boutique</a>
            <a href="<?= $base ?>/boutique.php" class="nav-highlight">Nos produits</a>

            <div class="nav-dropdown">
                <span class="nav-dropdown-trigger">
                    Catégories
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </span>
                <div class="nav-dropdown-menu">
                    <?php foreach ($nav_categories as $cat): ?>
                        <a href="<?= $base ?>/boutique.php?cat=<?= urlencode($cat['slug']) ?>"><?= htmlspecialchars($cat['nom']) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <a href="<?= $base ?>/index.php#contact">Contact</a>
        </nav>

        <div class="header-actions">
            <a href="<?= $base ?>/panier.php" class="cart-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                    <path d="M3 6h18"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
                <span>Panier</span>
                <?php if (panier_nb_articles() > 0): ?>
                    <span class="cart-badge"><?= panier_nb_articles() ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= $base ?>/admin/login.php" class="lock-icon" title="Accès administrateur">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="11" width="16" height="10" rx="2"/>
                    <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                </svg>
            </a>
        </div>
    </div>
</header>

<script>
// Menu hamburger - ouvre/ferme la navbar
document.querySelector('.menu-toggle')?.addEventListener('click', function() {
    this.classList.toggle('active');
    document.querySelector('.main-nav').classList.toggle('open');
});

// Dropdown des catégories sur mobile
document.querySelector('.nav-dropdown-trigger')?.addEventListener('click', function(e) {
    if (window.innerWidth <= 900) {
        e.preventDefault();
        this.closest('.nav-dropdown').querySelector('.nav-dropdown-menu').classList.toggle('open');
    }
});
</script>