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

    <!-- FAVICON - Grand cercle blanc avec le symbole DermaSoin -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 80'%3E%3Ccircle cx='40' cy='40' r='36' fill='%23FFFFFF'/%3E%3Ccircle cx='32' cy='40' r='18' stroke='%23003D42' stroke-width='3' fill='none'/%3E%3Ccircle cx='48' cy='40' r='18' stroke='%23003D42' stroke-width='3' fill='none'/%3E%3Cpath d='M40 20 C40 20 52 30 52 40 C52 50 40 60 40 60 C40 60 28 50 28 40 C28 30 40 20 40 20Z' stroke='%23003D42' stroke-width='2.5' fill='none'/%3E%3Cline x1='40' y1='20' x2='40' y2='60' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='30' x2='46' y2='36' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='30' x2='34' y2='36' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='38' x2='47' y2='44' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='38' x2='33' y2='44' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='46' x2='45' y2='51' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='46' x2='35' y2='51' stroke='%23003D42' stroke-width='2'/%3E%3C/svg%3E">
    <link rel="shortcut icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 80'%3E%3Ccircle cx='40' cy='40' r='36' fill='%23FFFFFF'/%3E%3Ccircle cx='32' cy='40' r='18' stroke='%23003D42' stroke-width='3' fill='none'/%3E%3Ccircle cx='48' cy='40' r='18' stroke='%23003D42' stroke-width='3' fill='none'/%3E%3Cpath d='M40 20 C40 20 52 30 52 40 C52 50 40 60 40 60 C40 60 28 50 28 40 C28 30 40 20 40 20Z' stroke='%23003D42' stroke-width='2.5' fill='none'/%3E%3Cline x1='40' y1='20' x2='40' y2='60' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='30' x2='46' y2='36' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='30' x2='34' y2='36' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='38' x2='47' y2='44' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='38' x2='33' y2='44' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='46' x2='45' y2='51' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='46' x2='35' y2='51' stroke='%23003D42' stroke-width='2'/%3E%3C/svg%3E">

    <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a href="<?= $base ?>/index.php" class="logo">
            <svg class="logo-icon" width="48" height="48" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="32" cy="40" r="22" stroke="currentColor" stroke-width="2.2" fill="none"/>
                <circle cx="48" cy="40" r="22" stroke="currentColor" stroke-width="2.2" fill="none"/>
                <path d="M40 18 C40 18 52 28 52 40 C52 52 40 62 40 62 C40 62 28 52 28 40 C28 28 40 18 40 18Z" stroke="currentColor" stroke-width="2" fill="none"/>
                <line x1="40" y1="18" x2="40" y2="62" stroke="currentColor" stroke-width="1.8"/>
                <line x1="40" y1="30" x2="46" y2="36" stroke="currentColor" stroke-width="1.5"/>
                <line x1="40" y1="30" x2="34" y2="36" stroke="currentColor" stroke-width="1.5"/>
                <line x1="40" y1="38" x2="47" y2="44" stroke="currentColor" stroke-width="1.5"/>
                <line x1="40" y1="38" x2="33" y2="44" stroke="currentColor" stroke-width="1.5"/>
                <line x1="40" y1="46" x2="45" y2="51" stroke="currentColor" stroke-width="1.5"/>
                <line x1="40" y1="46" x2="35" y2="51" stroke="currentColor" stroke-width="1.5"/>
            </svg>
            Derma<span>Soin</span>
        </a>
        <nav class="main-nav">
            <a href="<?= $base ?>/index.php">Accueil</a>
            <a href="<?= $base ?>/boutique.php">Boutique</a>

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