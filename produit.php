<?php
require_once __DIR__ . '/includes/header.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM produits WHERE slug = ? AND actif = TRUE");
$stmt->execute([$slug]);
$p = $stmt->fetch();

if (!$p) {
    echo '<div class="empty-state">Produit introuvable. <a href="'.BASE_URL.'/boutique.php">Retour à la boutique</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $p['nom'];
$actifs_list = array_filter(array_map('trim', explode(',', $p['actifs'] ?? '')));
?>

<section class="section">
    <div class="container" style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:start;">
        <!-- Image -->
        <div style="border-radius:var(--radius-lg);background:#EDE5D8;display:flex;align-items:center;justify-content:center;overflow:hidden;padding:0;">
            <?php if (!empty($p['image_principale'])): ?>
                <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($p['image_principale']) ?>" 
                     alt="<?= htmlspecialchars($p['nom']) ?>" 
                     style="width:100%;height:auto;max-height:600px;object-fit:contain;display:block;border-radius:var(--radius-lg);">
            <?php else: ?>
                <span style="font-size:3rem;padding:80px;color:var(--charbon-soft);">Pas d'image</span>
            <?php endif; ?>
        </div>
        
        <!-- Infos produit -->
        <div>
            <div style="font-size:0.85rem;font-weight:600;color:var(--charbon);margin-bottom:8px;text-transform:uppercase;letter-spacing:0.06em;">
                <?= htmlspecialchars($p['description_courte']) ?>
            </div>
            <h1><?= htmlspecialchars($p['nom']) ?></h1>
            
            <div style="margin:16px 0;">
                <?php foreach ($actifs_list as $actif): ?>
                    <span class="badge-actif"><?= htmlspecialchars($actif) ?></span>
                <?php endforeach; ?>
            </div>
            
            <div class="prix" style="font-size:1.6rem;margin-bottom:24px;">
                <?php if ($p['prix_promo']): ?>
                    <span class="prix-barre"><?= prix_format($p['prix']) ?></span>
                <?php endif; ?>
                <?= prix_format($p['prix_promo'] ?? $p['prix']) ?>
            </div>
            
            <p style="color:var(--charbon);margin-bottom:30px;line-height:1.8;"><?= nl2br(htmlspecialchars($p['description'])) ?></p>

            <?php if (!empty($p['necessite_agrement'])): ?>
                <div style="background:#FFF3E0;color:#B26A00;padding:14px 18px;border-radius:var(--radius);font-size:0.88rem;margin-bottom:20px;">
                    Produit réservé aux professionnels de santé. Un numéro d'agrément ou une carte professionnelle sera demandé lors de la commande.
                </div>
            <?php endif; ?>

            <?php if ($p['stock'] > 0): ?>
                <div style="font-weight:700;font-size:1.1rem;color:#1a6b2a;margin-bottom:20px;">
                    En stock (<?= $p['stock'] ?> unités disponibles)
                </div>
                <form method="post" action="<?= BASE_URL ?>/panier.php" style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
                    <input type="hidden" name="produit_id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="action" value="ajouter">
                    <div class="qty-control">
                        <button type="button" onclick="this.nextElementSibling.stepDown()">−</button>
                        <input type="number" name="quantite" value="1" min="1" max="<?= $p['stock'] ?>">
                        <button type="button" onclick="this.previousElementSibling.stepUp()">+</button>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                </form>
            <?php else: ?>
                <div style="font-weight:700;font-size:1.1rem;color:#E53E3E;margin-bottom:20px;">
                    Rupture de stock
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
/* ============================================
   PRODUIT - RESPONSIVE UNIQUEMENT SUR MOBILE
   ============================================ */
@media (max-width: 900px) {
    .container[style*="display:grid"] {
        grid-template-columns: 1fr !important;
        gap: 30px !important;
    }
    .container[style*="display:grid"] > div:first-child {
        max-width: 400px;
        margin: 0 auto;
        width: 100%;
    }
    .container[style*="display:grid"] > div:first-child img {
        max-height: 350px !important;
    }
    .container[style*="display:grid"] > div:last-child {
        text-align: center;
    }
    .container[style*="display:grid"] h1 {
        font-size: 1.6rem !important;
    }
    .container[style*="display:grid"] .prix {
        font-size: 1.4rem !important;
    }
    .container[style*="display:grid"] form {
        justify-content: center !important;
    }
    .container[style*="display:grid"] .badge-actif {
        display: inline-block;
    }
    .container[style*="display:grid"] > div:last-child p {
        text-align: left;
    }
    .container[style*="display:grid"] .alert {
        text-align: left;
    }
    .container[style*="display:grid"] .qty-control {
        justify-content: center;
    }
}

@media (max-width: 600px) {
    .container[style*="display:grid"] {
        gap: 20px !important;
        padding: 0 12px !important;
    }
    .container[style*="display:grid"] > div:first-child {
        max-width: 280px;
    }
    .container[style*="display:grid"] > div:first-child img {
        max-height: 280px !important;
    }
    .container[style*="display:grid"] h1 {
        font-size: 1.3rem !important;
    }
    .container[style*="display:grid"] .prix {
        font-size: 1.2rem !important;
    }
    .container[style*="display:grid"] > div:last-child p {
        font-size: 0.9rem !important;
    }
    .container[style*="display:grid"] form {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .container[style*="display:grid"] form .qty-control {
        align-self: center !important;
    }
    .container[style*="display:grid"] form .btn {
        width: 100% !important;
        justify-content: center !important;
    }
    .container[style*="display:grid"] .produit-detail-stock {
        font-size: 0.95rem !important;
        text-align: center !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>