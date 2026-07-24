<?php
$page_title = 'Commande confirmée';
require_once __DIR__ . '/includes/header.php';

$num = $_GET['num'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE numero_commande = ?");
$stmt->execute([$num]);
$commande = $stmt->fetch();

if (!$commande) {
    header('Location: '.BASE_URL.'/index.php');
    exit;
}

$stmt2 = $pdo->prepare("SELECT * FROM commande_articles WHERE commande_id = ?");
$stmt2->execute([$commande['id']]);
$articles = $stmt2->fetchAll();

$labels_paiement = [
    'cod' => 'Paiement à la livraison (espèces)',
    'baridimob' => 'BaridiMob / CCP',
];
?>

<section class="section">
    <div class="container" style="max-width:640px;">

        <div style="text-align:center;margin-bottom:36px;">
            <div style="width:78px;height:78px;border-radius:50%;background:var(--petrole);display:flex;align-items:center;justify-content:center;margin:0 auto 22px;box-shadow:0 12px 30px rgba(4,151,167,0.28);">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <h2 style="margin-bottom:8px;color:#1C1F1F;">Merci, <?= htmlspecialchars($commande['nom_client']) ?> !</h2>
            <p style="color:var(--charbon-soft);font-size:0.95rem;">
                Votre commande a bien été enregistrée.<br>
                <strong>Vérifiez votre WhatsApp</strong> — notre équipe va vous y contacter très vite pour confirmer votre livraison.
            </p>
        </div>

        <div style="background:#FFFFFF;border-radius:var(--radius-lg);box-shadow:0 10px 34px rgba(4,151,167,0.10);overflow:hidden;">

           <!-- Fond vert pétrole avec texte BLANC PUR pour tout -->
<div style="background:var(--petrole);padding:22px 30px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #025C66;">
    <div>
        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:#FFFFFF;font-weight:700;opacity:0.8;">N° de commande</div>
        <div style="font-family:'Inter',sans-serif;font-weight:700;font-size:1.2rem;color:#FFFFFF;letter-spacing:0.03em;">
            <?= htmlspecialchars($commande['numero_commande']) ?>
        </div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;color:#FFFFFF;font-weight:700;opacity:0.8;">Total</div>
        <div style="font-family:'Inter',sans-serif;font-weight:700;font-size:1.4rem;color:#FFFFFF;font-variant-numeric:tabular-nums;letter-spacing:0.03em;">
            <?= prix_format($commande['total']) ?>
        </div>
    </div>
</div>

            <div style="padding:28px 30px;">
                <?php foreach ($articles as $a): $ss_total = $a['prix_unitaire'] * $a['quantite']; ?>
                <div style="display:flex;justify-content:space-between;padding:11px 0;border-bottom:1px dashed var(--sable);font-size:0.92rem;color:#1C1F1F;">
                    <span><?= htmlspecialchars($a['nom_produit']) ?> <span style="color:#5C605F;">× <?= $a['quantite'] ?></span></span>
                    <span style="font-weight:600;color:#1C1F1F;"><?= prix_format($ss_total) ?></span>
                </div>
                <?php endforeach; ?>

                <div style="display:flex;justify-content:space-between;padding:14px 0 4px;font-size:0.88rem;color:#1C1F1F;">
                    <span style="color:#5C605F;">Sous-total</span><span style="color:#1C1F1F;"><?= prix_format($commande['sous_total']) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:4px 0 14px;font-size:0.88rem;color:#1C1F1F;">
                    <span style="color:#5C605F;">Livraison</span><span style="color:#1C1F1F;"><?= prix_format($commande['frais_livraison']) ?></span>
                </div>

                <div style="border-top:1px solid var(--sable);padding-top:16px;margin-top:6px;">
                    <div style="display:flex;justify-content:space-between;font-size:0.88rem;margin-bottom:6px;color:#1C1F1F;">
                        <span style="color:#5C605F;">Livraison à</span>
                        <span style="font-weight:600;text-align:right;color:#1C1F1F;"><?= htmlspecialchars($commande['wilaya']) ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:0.88rem;color:#1C1F1F;">
                        <span style="color:#5C605F;">Paiement</span>
                        <span style="font-weight:600;color:#1C1F1F;"><?= $labels_paiement[$commande['mode_paiement']] ?? $commande['mode_paiement'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($commande['mode_paiement'] === 'baridimob'): ?>
        <div class="alert alert-success" style="text-align:left;margin-top:22px;color:#1C1F1F;">
            Notre équipe vous enverra les coordonnées BaridiMob/CCP par WhatsApp dans quelques instants.
        </div>
        <?php else: ?>
        <div class="alert alert-success" style="text-align:left;margin-top:22px;color:#1C1F1F;">
            Préparez le montant exact si possible — paiement en espèces, main à main, à la réception du colis.
        </div>
        <?php endif; ?>

        <div style="display:flex;align-items:center;justify-content:center;gap:10px;background:var(--petrole-transparent);color:var(--petrole-fonce);font-weight:600;font-size:0.9rem;padding:16px;border-radius:100px;margin-top:22px;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.6 6.32A7.85 7.85 0 0 0 12.03 4a7.94 7.94 0 0 0-6.87 11.9L4 20l4.2-1.1a7.9 7.9 0 0 0 3.83.98h.01a7.94 7.94 0 0 0 5.56-13.56zM12.04 18.4a6.6 6.6 0 0 1-3.36-.92l-.24-.14-2.5.65.67-2.44-.16-.25a6.6 6.6 0 1 1 12.26-3.5 6.56 6.56 0 0 1-6.67 6.6z"/>
            </svg>
            DermaSoin va vous contacter sur WhatsApp
        </div>

        <div style="text-align:center;margin-top:28px;">
            <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-outline">Continuer mes achats</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>