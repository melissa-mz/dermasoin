<?php
$page_title = 'Commandes';
require_once __DIR__ . '/includes-header.php';
require_once __DIR__ . '/../config/paiement.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['statut_commande'])) {
    $pdo->prepare("UPDATE commandes SET statut_commande = ? WHERE id = ?")
        ->execute([$_POST['statut_commande'], (int)$_POST['id']]);
    header('Location: '.BASE_URL.'/admin/commandes.php?id=' . (int)$_POST['id']);
    exit;
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM commandes WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $commande = $stmt->fetch();
    $articles = $pdo->prepare("SELECT * FROM commande_articles WHERE commande_id = ?");
    $articles->execute([(int)$_GET['id']]);
    $articles = $articles->fetchAll();

    // ============================================
    // MESSAGE WHATSAPP - Version finale
    // ============================================
    $lignes = [];
    $lignes[] = "Bonjour *{$commande['nom_client']}*,";
    $lignes[] = "";
    $lignes[] = "Merci pour votre commande chez DermaSoin !";
    $lignes[] = "";
    $lignes[] = "*Récapitulatif de votre commande :*";
    foreach ($articles as $a) {
        $ss_total = $a['prix_unitaire'] * $a['quantite'];
        $lignes[] = "• {$a['nom_produit']} × {$a['quantite']} — " . prix_format($ss_total);
    }
    $lignes[] = "";
    $lignes[] = "Sous-total : " . prix_format($commande['sous_total']);
    $lignes[] = "Livraison : " . prix_format($commande['frais_livraison']);
    $lignes[] = "*Total :* " . prix_format($commande['total']);
    $lignes[] = "";
    $lignes[] = "Adresse de livraison :";
    $lignes[] = "{$commande['adresse']}, {$commande['wilaya']}";

    if ($commande['mode_paiement'] === 'baridimob') {
    $lignes[] = "";
    $lignes[] = "Paiement BaridiMob :";
    $lignes[] = "RIP : *" . BARIDIMOB_RIP . "*";
    $lignes[] = "Titulaire : " . BARIDIMOB_TITULAIRE;
    $lignes[] = "";
    $lignes[] = "Merci de nous transmettre votre *reçu de paiement* par ce même canal afin de finaliser les procédures administratives.";
    $lignes[] = "Dès validation, votre commande sera préparée et expédiée dans les meilleurs délais.";
} else {
    $lignes[] = "";
    $lignes[] = "Paiement à la livraison : règlement en espèces à la réception de votre colis.";
    $lignes[] = "";
    $lignes[] = "Nous vous contacterons pour la livraison.";
}
$lignes[] = "";
$lignes[] = "Merci de votre confiance.";

    $message_client = implode("\n", $lignes);
    $tel_client = preg_replace('/[^0-9]/', '', $commande['telephone']);
    if (substr($tel_client, 0, 1) === '0') $tel_client = '213' . substr($tel_client, 1);
    $lien_whatsapp_client = "https://wa.me/{$tel_client}?text=" . rawurlencode($message_client);
    ?>
    <a href="<?= BASE_URL ?>/admin/commandes.php" class="admin-back-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Retour aux commandes
    </a>
    <h2>Commande <?= htmlspecialchars($commande['numero_commande']) ?></h2>

    <a href="<?= htmlspecialchars($lien_whatsapp_client) ?>" target="_blank" rel="noopener"
       style="display:inline-flex;align-items:center;gap:8px;background:#25D366;color:#FFFFFF;font-weight:600;font-size:0.88rem;padding:12px 22px;border-radius:100px;margin:14px 0;text-decoration:none;box-shadow:0 4px 16px rgba(37,211,102,0.25);transition:transform 0.2s ease;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.6 6.32A7.85 7.85 0 0 0 12.03 4a7.94 7.94 0 0 0-6.87 11.9L4 20l4.2-1.1a7.9 7.9 0 0 0 3.83.98h.01a7.94 7.94 0 0 0 5.56-13.56zM12.04 18.4a6.6 6.6 0 0 1-3.36-.92l-.24-.14-2.5.65.67-2.44-.16-.25a6.6 6.6 0 1 1 12.26-3.5 6.56 6.56 0 0 1-6.67 6.6z"/>
        </svg>
        Envoyer confirmation WhatsApp au client
    </a>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:30px;margin-top:20px;">
        <div style="background:var(--admin-white);padding:24px;border-radius:var(--admin-radius);box-shadow:var(--admin-shadow);">
            <h3>Client</h3>
            <p><?= htmlspecialchars($commande['nom_client']) ?><br>
            📞 <?= htmlspecialchars($commande['telephone']) ?><br>
            <?= $commande['email'] ? '✉ ' . htmlspecialchars($commande['email']) . '<br>' : '' ?>
            📍 <?= htmlspecialchars($commande['adresse']) ?>, <?= htmlspecialchars($commande['commune']) ?>, <?= htmlspecialchars($commande['wilaya']) ?></p>

            <h3 style="margin-top:20px;">Statut</h3>
            <form method="post">
                <input type="hidden" name="id" value="<?= $commande['id'] ?>">
                <div class="status-select-wrap">
                    <select name="statut_commande" class="status-select" onchange="this.form.submit()">
                        <?php foreach (['nouvelle','confirmee','preparee','expediee','livree','annulee'] as $s): ?>
                        <option value="<?= $s ?>" <?= $commande['statut_commande']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <div style="background:var(--admin-white);padding:24px;border-radius:var(--admin-radius);box-shadow:var(--admin-shadow);">
            <h3>Articles</h3>
            <?php foreach ($articles as $a): ?>
                <div class="summary-row"><span><?= htmlspecialchars($a['nom_produit']) ?> × <?= $a['quantite'] ?></span><span><?= prix_format($a['prix_unitaire'] * $a['quantite']) ?></span></div>
            <?php endforeach; ?>
            <div class="summary-row"><span>Livraison</span><span><?= prix_format($commande['frais_livraison']) ?></span></div>
            <div class="summary-row total"><span>Total</span><span><?= prix_format($commande['total']) ?></span></div>
            <p style="margin-top:14px;font-size:0.85rem;color:var(--charbon-soft);">Paiement : <?= $commande['mode_paiement'] ?> — <?= $commande['statut_paiement'] ?></p>
        </div>
    </div>
    <?php
} else {
    $filtre = $_GET['statut'] ?? '';
    if ($filtre) {
        $stmt = $pdo->prepare("SELECT * FROM commandes WHERE statut_commande = ? ORDER BY created_at DESC");
        $stmt->execute([$filtre]);
    } else {
        $stmt = $pdo->query("SELECT * FROM commandes ORDER BY created_at DESC");
    }
    $commandes = $stmt->fetchAll();
    ?>
    <h2>Commandes</h2>
    <div style="display:flex;gap:8px;margin:20px 0;flex-wrap:wrap;">
        <a href="<?= BASE_URL ?>/admin/commandes.php" class="btn <?= !$filtre?'btn-primary':'btn-outline' ?>" style="padding:6px 14px;font-size:0.8rem;">Toutes</a>
        <?php foreach (['nouvelle','confirmee','preparee','expediee','livree','annulee'] as $s): ?>
        <a href="<?= BASE_URL ?>/admin/commandes.php?statut=<?= $s ?>" class="btn <?= $filtre===$s?'btn-primary':'btn-outline' ?>" style="padding:6px 14px;font-size:0.8rem;"><?= ucfirst($s) ?></a>
        <?php endforeach; ?>
    </div>
    
    <table class="admin-table">
        <thead><tr><th>N°</th><th>Client</th><th>Tél.</th><th>Wilaya</th><th>Paiement</th><th>Statut</th><th>Total</th><th>Bon</th></tr></thead>
        <tbody>
        <?php foreach ($commandes as $c): ?>
            <tr>
                <td><?= $c['numero_commande'] ?></td>
                <td><?= htmlspecialchars($c['nom_client']) ?></td>
                <td><?= htmlspecialchars($c['telephone']) ?></td>
                <td><?= htmlspecialchars($c['wilaya']) ?></td>
                <td><?= $c['mode_paiement'] ?></td>
                <td><span class="status-pill status-<?= $c['statut_commande'] ?>"><?= $c['statut_commande'] ?></span></td>
                <td><?= prix_format($c['total']) ?></td>
                <td><a href="<?= BASE_URL ?>/admin/commandes.php?id=<?= $c['id'] ?>">Voir</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}
?>

</main></div>
</body>
</html>