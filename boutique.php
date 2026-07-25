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

<style>
/* ============================================
   PRODUIT - RESPONSIVE SANS ICÔNE
   ============================================ */
.produit-detail-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: start;
}

.produit-detail-image {
    border-radius: var(--radius-lg);
    background: #EDE5D8;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 0;
}
.produit-detail-image img {
    width: 100%;
    height: auto;
    max-height: 600px;
    object-fit: contain;
    display: block;
    border-radius: var(--radius-lg);
}

.produit-detail-cat {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--charbon);
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.produit-detail-info h1 {
    margin-bottom: 16px;
}
.produit-detail-actifs {
    margin: 16px 0;
}
.produit-detail-prix {
    font-size: 1.6rem;
    font-weight: 700;
    font-family: var(--font-display);
    color: var(--petrole);
    margin-bottom: 24px;
}
.produit-detail-desc {
    color: var(--charbon);
    margin-bottom: 30px;
    line-height: 1.8;
}
.alert-pro {
    background: #FFF3E0;
    color: #B26A00;
    padding: 14px 18px;
    border-radius: var(--radius);
    font-size: 0.88rem;
    margin-bottom: 20px;
}
.produit-detail-stock {
    font-weight: 700;
    font-size: 1.1rem;
    color: #1a6b2a;
    margin-bottom: 20px;
}
.produit-detail-stock.rupture {
    color: #E53E3E;
}
.produit-detail-form {
    display: flex;
    gap: 14px;
    align-items: center;
    flex-wrap: wrap;
}

/* ============================================
   RESPONSIVE PRODUIT
   ============================================ */
@media (max-width: 1024px) {
    .produit-detail-container {
        gap: 40px;
    }
    .produit-detail-image img {
        max-height: 450px;
    }
}

@media (max-width: 900px) {
    .produit-detail-container {
        grid-template-columns: 1fr !important;
        gap: 30px !important;
    }
    .produit-detail-image {
        max-width: 400px;
        margin: 0 auto;
        width: 100%;
    }
    .produit-detail-image img {
        max-height: 350px;
    }
    .produit-detail-info {
        text-align: center;
    }
    .produit-detail-info h1 {
        font-size: 1.6rem;
    }
    .produit-detail-prix {
        font-size: 1.4rem;
    }
    .produit-detail-form {
        justify-content: center;
    }
    .produit-detail-actifs {
        justify-content: center;
        display: flex;
        flex-wrap: wrap;
    }
    .alert-pro {
        text-align: left;
    }
    .produit-detail-desc {
        text-align: left;
    }
}

@media (max-width: 600px) {
    .produit-detail-container {
        gap: 20px !important;
        padding: 0 12px;
    }
    .produit-detail-image {
        max-width: 280px;
    }
    .produit-detail-image img {
        max-height: 280px;
    }
    .produit-detail-info h1 {
        font-size: 1.3rem;
    }
    .produit-detail-prix {
        font-size: 1.2rem;
    }
    .produit-detail-desc {
        font-size: 0.9rem;
    }
    .produit-detail-form {
        flex-direction: column;
        align-items: stretch;
    }
    .produit-detail-form .qty-control {
        align-self: center;
    }
    .produit-detail-form .btn {
        width: 100%;
        justify-content: center;
    }
    .produit-detail-stock {
        font-size: 0.95rem;
        text-align: center;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>