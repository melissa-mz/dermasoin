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
            <h2 style="margin-bottom:8px;">Merci, <?= htmlspecialchars($commande['nom_client']) ?> !</h2>
            <p style="color:var(--charbon-soft);font-size:0.95rem;">
                Votre commande a bien été enregistrée.<br>
                <strong>Vérifiez votre WhatsApp</strong> — notre équipe va vous y contacter très vite pour confirmer votre livraison.
            </p>
        </div>

        <div style="background:#FFFFFF;border-radius:var(--radius-lg);box-shadow:0 10px 34px rgba(4,151,167,0.10);overflow:hidden;">

            <div style="background:var(--petrole);padding:22px 30px;display:flex;justify-content:space-between;align-items:center;color:#FFFFFF;">
                <div>
                    <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;opacity:0.75;">N° de commande</div>
                    <div style="font-family:var(--font-display);font-weight:700;font-size:1.15rem;"><?= htmlspecialchars($commande['numero_commande']) ?></div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.08em;opacity:0.75;">Total</div>
                    <div style="font-family:var(--font-display);font-weight:700;font-size:1.3rem;"><?= prix_format($commande['total']) ?></div>
                </div>
            </div>

            <div style="padding:28px 30px;">
                <?php foreach ($articles as $a): $ss_total = $a['prix_unitaire'] * $a['quantite']; ?>
                <div style="display:flex;justify-content:space-between;padding:11px 0;border-bottom:1px dashed var(--sable);font-size:0.92rem;">
                    <span><?= htmlspecialchars($a['nom_produit']) ?> <span style="color:var(--charbon-soft);">× <?= $a['quantite'] ?></span></span>
                    <span style="font-weight:600;"><?= prix_format($ss_total) ?></span>
                </div>
                <?php endforeach; ?>

                <div style="display:flex;justify-content:space-between;padding:14px 0 4px;font-size:0.88rem;color:var(--charbon-soft);">
                    <span>Sous-total</span><span><?= prix_format($commande['sous_total']) ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:4px 0 14px;font-size:0.88rem;color:var(--charbon-soft);">
                    <span>Livraison</span><span><?= prix_format($commande['frais_livraison']) ?></span>
                </div>

                <div style="border-top:1px solid var(--sable);padding-top:16px;margin-top:6px;">
                    <div style="display:flex;justify-content:space-between;font-size:0.88rem;margin-bottom:6px;">
                        <span style="color:var(--charbon-soft);">Livraison à</span>
                        <span style="font-weight:600;text-align:right;"><?= htmlspecialchars($commande['wilaya']) ?></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;font-size:0.88rem;">
                        <span style="color:var(--charbon-soft);">Paiement</span>
                        <span style="font-weight:600;"><?= $labels_paiement[$commande['mode_paiement']] ?? $commande['mode_paiement'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($commande['mode_paiement'] === 'baridimob'): ?>
        <div class="alert alert-success" style="text-align:left;margin-top:22px;">
            Notre équipe vous enverra les coordonnées BaridiMob/CCP par WhatsApp dans quelques instants.
        </div>
        <?php else: ?>
        <div class="alert alert-success" style="text-align:left;margin-top:22px;">
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