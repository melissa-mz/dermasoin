<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../includes/cart.php';
admin_requis();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?? 'Admin' ?> — DermaSoin</title>

<!-- FAVICON - Logo JPG -->
<link rel="icon" type="image/jpeg" href="<?= BASE_URL ?>/assets/img/logo.jpg">
<link rel="shortcut icon" href="<?= BASE_URL ?>/assets/img/logo.jpg">

<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/admin/admin.css?v=5">
<link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400;0,600;0,700;0,900;1,400&family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="admin-shell">
    <nav class="admin-navbar">
        <a href="<?= BASE_URL ?>/admin/index.php" class="logo">
            <img src="<?= BASE_URL ?>/assets/img/logo.jpg" alt="DermaSoin" class="logo-icon" width="90" height="70">
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