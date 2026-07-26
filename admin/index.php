<?php
$page_title = 'Tableau de bord';
require_once __DIR__ . '/includes-header.php';

// Correction PostgreSQL : TRUE → 1
$nb_commandes = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$ca_total = $pdo->query("SELECT COALESCE(SUM(total),0) FROM commandes WHERE statut_commande != 'annulee'")->fetchColumn();
$nb_produits = $pdo->query("SELECT COUNT(*) FROM produits WHERE actif = 1")->fetchColumn();
$nb_nouvelles = $pdo->query("SELECT COUNT(*) FROM commandes WHERE statut_commande = 'nouvelle'")->fetchColumn();
$dernieres = $pdo->query("SELECT * FROM commandes ORDER BY created_at DESC LIMIT 8")->fetchAll();
?>

<h2>Tableau de bord</h2>
<div class="stat-cards" style="margin-top:24px;">
    <div class="stat-card"><div class="val"><?= $nb_commandes ?></div><div class="lbl">Commandes totales</div></div>
    <div class="stat-card"><div class="val"><?= prix_format($ca_total) ?></div><div class="lbl">Chiffre d'affaires</div></div>
    <div class="stat-card"><div class="val"><?= $nb_produits ?></div><div class="lbl">Produits actifs</div></div>
    <div class="stat-card"><div class="val"><?= $nb_nouvelles ?></div><div class="lbl">Nouvelles commandes</div></div>
</div>

</main></div>
</body>
</html>