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
    <div class="container produit-detail-container">
        <!-- Image -->
        <div class="produit-detail-image">
            <?php if (!empty($p['image_principale'])): ?>
                <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($p['image_principale']) ?>" 
                     alt="<?= htmlspecialchars($p['nom']) ?>">
            <?php else: ?>
                <span style="font-size:3rem;padding:80px;color:var(--charbon-soft);">Pas d'image</span>
            <?php endif; ?>
        </div>
        
        <!-- Infos produit -->
        <div class="produit-detail-info">
            <div class="produit-detail-cat">
                <?= htmlspecialchars($p['description_courte']) ?>
            </div>
            <h1><?= htmlspecialchars($p['nom']) ?></h1>
            
            <div class="produit-detail-actifs">
                <?php foreach ($actifs_list as $actif): ?>
                    <span class="badge-actif"><?= htmlspecialchars($actif) ?></span>
                <?php endforeach; ?>
            </div>
            
            <div class="produit-detail-prix">
                <?php if ($p['prix_promo']): ?>
                    <span class="prix-barre"><?= prix_format($p['prix']) ?></span>
                <?php endif; ?>
                <?= prix_format($p['prix_promo'] ?? $p['prix']) ?>
            </div>
            
            <p class="produit-detail-desc"><?= nl2br(htmlspecialchars($p['description'])) ?></p>

            <?php if (!empty($p['necessite_agrement'])): ?>
                <div class="alert alert-pro">
                    Produit réservé aux professionnels de santé. Un numéro d'agrément ou une carte professionnelle sera demandé lors de la commande.
                </div>
            <?php endif; ?>

            <?php if ($p['stock'] > 0): ?>
                <div class="produit-detail-stock">
                    En stock (<?= $p['stock'] ?> unités disponibles)
                </div>
                <form method="post" action="<?= BASE_URL ?>/panier.php" class="produit-detail-form">
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
                <div class="produit-detail-stock rupture">
                    Rupture de stock
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
