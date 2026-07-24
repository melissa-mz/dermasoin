<?php
$page_title = 'Tableau de bord';
require_once __DIR__ . '/includes-header.php';

// Correction PostgreSQL : actif = 1 → actif = TRUE
$nb_commandes = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$ca_total = $pdo->query("SELECT COALESCE(SUM(total),0) FROM commandes WHERE statut_commande != 'annulee'")->fetchColumn();
$nb_produits = $pdo->query("SELECT COUNT(*) FROM produits WHERE actif = TRUE")->fetchColumn();  // ← Correction ici
$nb_nouvelles = $pdo->query("SELECT COUNT(*) FROM commandes WHERE statut_commande = 'nouvelle'")->fetchColumn();

// Calcul des évolutions (exemple avec le mois dernier)
$mois_dernier = date('Y-m-d', strtotime('-1 month'));
$nb_commandes_mois = $pdo->query("SELECT COUNT(*) FROM commandes WHERE created_at >= '$mois_dernier'")->fetchColumn();
$nb_commandes_precedent = $pdo->query("SELECT COUNT(*) FROM commandes WHERE created_at < '$mois_dernier' AND created_at >= DATE_SUB('$mois_dernier', INTERVAL 1 MONTH)")->fetchColumn();
$evolution = $nb_commandes_precedent > 0 ? round((($nb_commandes_mois - $nb_commandes_precedent) / $nb_commandes_precedent) * 100) : 0;
?>

<!-- HEADER -->
<div class="page-header">
    <h1>👋 Bonjour, Admin</h1>
    <p>Voici l'aperçu de votre boutique DermaSoin</p>
</div>

<!-- STATISTIQUES -->
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-content">
            <div class="val"><?= $nb_commandes ?></div>
            <div class="lbl">Commandes totales</div>
            <div class="sub <?= $evolution >= 0 ? 'up' : 'down' ?>">
                <?= $evolution >= 0 ? '⬆' : '⬇' ?> <?= abs($evolution) ?>% ce mois
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
            <div class="val"><?= prix_format($ca_total) ?></div>
            <div class="lbl">Chiffre d'affaires</div>
            <div class="sub up">⬆ +8% ce mois</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">🧴</div>
        <div class="stat-content">
            <div class="val"><?= $nb_produits ?></div>
            <div class="lbl">Produits actifs</div>
            <div class="sub"><?= $nb_produits > 0 ? '✅ En ligne' : '⚠️ Aucun produit' ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">🆕</div>
        <div class="stat-content">
            <div class="val"><?= $nb_nouvelles ?></div>
            <div class="lbl">Nouvelles commandes</div>
            <div class="sub">
                <?php if ($nb_nouvelles > 0): ?>
                    <span class="badge-dot orange"></span> À traiter
                <?php else: ?>
                    ✅ Tout est traité
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
// Récupération des dernières commandes (optionnel, si tu veux les afficher)
$dernieres = $pdo->query("SELECT * FROM commandes ORDER BY created_at DESC LIMIT 5")->fetchAll();
if (!empty($dernieres)):
?>
<!-- DERNIÈRES COMMANDES -->
<div class="admin-table-wrapper" style="margin-top: 32px;">
    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px 0;">
        <h3 style="font-family:'Playfair Display',serif;font-size:1.1rem;color:var(--admin-text);">📋 Dernières commandes</h3>
        <a href="<?= BASE_URL ?>/admin/commandes.php" class="btn-sm">Voir toutes</a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Commande</th>
                <th>Client</th>
                <th>Total</th>
                <th>Statut</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dernieres as $c): ?>
            <tr>
                <td><strong>#<?= str_pad($c['id'], 4, '0', STR_PAD_LEFT) ?></strong></td>
                <td><?= htmlspecialchars($c['email'] ?? 'Client') ?></td>
                <td><?= prix_format($c['total']) ?></td>
                <td><span class="status-pill status-<?= $c['statut_commande'] ?>"><?= ucfirst($c['statut_commande']) ?></span></td>
                <td><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes-footer.php'; ?>