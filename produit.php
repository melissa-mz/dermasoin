<?php
require_once __DIR__ . '/includes/header.php'; // header inclut déjà db.php + cart.php

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM produits WHERE slug = ? AND actif = TRUE");  // ← Correction ici : actif = 1 → actif = TRUE
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
    <div class="container" style="display:grid;grid-template-columns:1fr 1fr;gap:60px;">
        <div style="border-radius:var(--radius-lg);background:#EDE5D8;display:flex;align-items:center;justify-content:center;overflow:hidden;padding:0;">
            <?php if (!empty($p['image_principale'])): ?>
                <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($p['image_principale']) ?>" 
                     alt="<?= htmlspecialchars($p['nom']) ?>" 
                     style="width:100%;height:auto;max-height:600px;object-fit:contain;display:block;border-radius:var(--radius-lg);">
            <?php else: ?>
                <span style="font-size:3rem;padding:80px;">🧴</span>
            <?php endif; ?>
        </div>
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
            
            <!-- DESCRIPTION LONGUE EN NOIR -->
            <p style="color:var(--charbon);margin-bottom:30px;line-height:1.8;"><?= nl2br(htmlspecialchars($p['description'])) ?></p>

            <?php if (!empty($p['necessite_agrement'])): ?>
            <div class="alert" style="background:#FFF3E0;color:#B26A00;">
                Produit réservé aux professionnels de santé. Un numéro d'agrément ou une carte professionnelle sera demandé lors de la commande.
            </div>
            <?php endif; ?>

            <?php if ($p['stock'] > 0): ?>
                <div style="margin-bottom:20px;font-weight:700;font-size:1.1rem;color:#1a6b2a;">
                    En stock (<?= $p['stock'] ?> unités disponibles)
                </div>
                <form method="post" action="<?= BASE_URL ?>/panier.php" style="display:flex;gap:14px;align-items:center;">
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
                <div style="margin-bottom:20px;font-weight:700;font-size:1.1rem;color:#E53E3E;">
                    Rupture de stock
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>