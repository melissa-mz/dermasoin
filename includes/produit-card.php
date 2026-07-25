<?php
if (empty($p) || !is_array($p)) return;

$actifs_list = [];$categorie_nom = '';
if (!empty($p['categorie_id'])) {
    static $cat_cache = [];
    if (!isset($cat_cache[$p['categorie_id']])) {
        $stmt = $pdo->prepare("SELECT nom FROM categories WHERE id = ?");
        $stmt->execute([$p['categorie_id']]);
        $cat_cache[$p['categorie_id']] = $stmt->fetchColumn();
    }
    $categorie_nom = $cat_cache[$p['categorie_id']];
}

$stock = isset($p['stock']) ? (int)$p['stock'] : null;
$en_rupture = ($stock !== null && $stock <= 0);
// PostgreSQL: necessite_agrement est BOOLEAN (TRUE/FALSE)
$necessite_agrement = isset($p['necessite_agrement']) ? (bool)$p['necessite_agrement'] : false;
?>
<div class="produit-card<?= $en_rupture ? ' produit-rupture' : '' ?>" data-nom="<?= htmlspecialchars(mb_strtolower($p['nom'])) ?>">
    <a href="<?= BASE_URL ?>/produit.php?slug=<?= urlencode($p['slug']) ?>" class="produit-img">
        <?php if ($p['prix_promo']): ?><span class="produit-badge">Promo</span><?php endif; ?>
        <?php if ($necessite_agrement): ?><span class="produit-badge" style="left:auto;right:12px;background:var(--charbon-soft);">Pro</span><?php endif; ?>
        <?php if (!empty($p['image_principale'])): ?>
            <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($p['image_principale']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" style="width:100%;height:100%;object-fit:cover;">
        <?php else: ?>
            <span style="font-size:2rem;">🧴</span>
        <?php endif; ?>
        
        <!-- BADGE STOCK SUR L'IMAGE (en bas à droite) -->
        <?php if ($stock !== null): ?>
            <span class="stock-badge <?= $en_rupture ? 'stock-badge--rupture' : 'stock-badge--dispo' ?>" style="color: #000000 !important;">
                <?= $en_rupture ? 'Rupture' : 'En stock' ?>
            </span>
        <?php endif; ?>
    </a>
    <div class="produit-body">
        <!-- 🔥 NOM DU PRODUIT EN PREMIER -->
        <a href="<?= BASE_URL ?>/produit.php?slug=<?= urlencode($p['slug']) ?>"><h3 class="produit-nom"><?= htmlspecialchars($p['nom']) ?></h3></a>
        
        <!-- 🔥 CATÉGORIE EN DESSOUS -->
        <?php if ($categorie_nom): ?><div class="produit-cat"><?= htmlspecialchars($categorie_nom) ?></div><?php endif; ?>
        
        <div>
            <?php foreach (array_slice($actifs_list, 0, 2) as $actif): ?>
                <span class="badge-actif"><?= htmlspecialchars($actif) ?></span>
            <?php endforeach; ?>
        </div>
        <div class="produit-footer">
            <div class="prix-bloc">
                <div class="prix">
                    <?php if ($p['prix_promo']): ?>
                        <span class="prix-barre"><?= prix_format($p['prix']) ?></span>
                    <?php endif; ?>
                    <?= prix_format($p['prix_promo'] ?? $p['prix']) ?>
                </div>
            </div>
            <form method="post" action="<?= BASE_URL ?>/panier.php">
                <input type="hidden" name="produit_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="action" value="ajouter">
                <button type="submit" class="btn-add" title="Ajouter au panier"
                    <?= $en_rupture ? 'disabled style="opacity:0.4;cursor:not-allowed;"' : '' ?>>+</button>
            </form>
        </div>
    </div>
</div>