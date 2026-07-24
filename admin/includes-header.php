<?php
require_once __DIR__ . '/auth.php';
admin_requis();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?? 'Admin' ?> — DermaSoin</title>

<!-- FAVICON - Cercle avec le signe DermaSoin -->
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 80'%3E%3Ccircle cx='40' cy='40' r='36' fill='%23FFFFFF'/%3E%3Ccircle cx='32' cy='40' r='18' stroke='%23003D42' stroke-width='3' fill='none'/%3E%3Ccircle cx='48' cy='40' r='18' stroke='%23003D42' stroke-width='3' fill='none'/%3E%3Cpath d='M40 20 C40 20 52 30 52 40 C52 50 40 60 40 60 C40 60 28 50 28 40 C28 30 40 20 40 20Z' stroke='%23003D42' stroke-width='2.5' fill='none'/%3E%3Cline x1='40' y1='20' x2='40' y2='60' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='30' x2='46' y2='36' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='30' x2='34' y2='36' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='38' x2='47' y2='44' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='38' x2='33' y2='44' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='46' x2='45' y2='51' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='46' x2='35' y2='51' stroke='%23003D42' stroke-width='2'/%3E%3C/svg%3E">
<link rel="shortcut icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 80'%3E%3Ccircle cx='40' cy='40' r='36' fill='%23FFFFFF'/%3E%3Ccircle cx='32' cy='40' r='18' stroke='%23003D42' stroke-width='3' fill='none'/%3E%3Ccircle cx='48' cy='40' r='18' stroke='%23003D42' stroke-width='3' fill='none'/%3E%3Cpath d='M40 20 C40 20 52 30 52 40 C52 50 40 60 40 60 C40 60 28 50 28 40 C28 30 40 20 40 20Z' stroke='%23003D42' stroke-width='2.5' fill='none'/%3E%3Cline x1='40' y1='20' x2='40' y2='60' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='30' x2='46' y2='36' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='30' x2='34' y2='36' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='38' x2='47' y2='44' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='38' x2='33' y2='44' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='46' x2='45' y2='51' stroke='%23003D42' stroke-width='2'/%3E%3Cline x1='40' y1='46' x2='35' y2='51' stroke='%23003D42' stroke-width='2'/%3E%3C/svg%3E">

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/admin/admin.css?v=5">
<link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400;0,600;0,700;0,900;1,400&family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="admin-shell">
    <nav class="admin-navbar">
        <a href="<?= BASE_URL ?>/admin/index.php" class="logo">
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
        <div class="admin-nav-links">
            <a href="<?= BASE_URL ?>/admin/index.php" class="<?= $current==='index.php'?'active':'' ?>">Tableau de bord</a>
            <a href="<?= BASE_URL ?>/admin/produits.php" class="<?= $current==='produits.php'?'active':'' ?>">Produits</a>
            <a href="<?= BASE_URL ?>/admin/commandes.php" class="<?= $current==='commandes.php'?'active':'' ?>">Commandes</a>
        </div>
        <a href="<?= BASE_URL ?>/admin/logout.php" class="admin-logout">Déconnexion</a>
    </nav>
    <main class="admin-main">