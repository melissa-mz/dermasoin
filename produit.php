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
$actifs_list = []; // La colonne actifs a été supprimée
$prix_unitaire = $p['prix_promo'] ?? $p['prix'];
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
            <!-- STOCK / RUPTURE EN HAUT -->
            <?php if ($p['stock'] > 0): ?>
                <div style="font-weight:700;font-size:0.9rem;color:#1a6b2a;margin-bottom:12px;background:rgba(26,107,42,0.08);padding:6px 14px;border-radius:20px;display:inline-block;">
                    En stock (<?= $p['stock'] ?> unités)
                </div>
            <?php else: ?>
                <div style="font-weight:700;font-size:0.9rem;color:#E53E3E;margin-bottom:12px;background:rgba(229,62,62,0.08);padding:6px 14px;border-radius:20px;display:inline-block;">
                    Rupture de stock
                </div>
            <?php endif; ?>

            <div style="font-size:0.85rem;font-weight:600;color:var(--charbon);margin-bottom:8px;text-transform:uppercase;letter-spacing:0.06em;">
                <?= htmlspecialchars($p['description_courte']) ?>
            </div>
            <h1><?= htmlspecialchars($p['nom']) ?></h1>
            
            <div style="margin:16px 0;">
                <?php foreach ($actifs_list as $actif): ?>
                    <span class="badge-actif"><?= htmlspecialchars($actif) ?></span>
                <?php endforeach; ?>
            </div>
            
            <!-- PRIX UNITAIRE EN CADRE (NOIR) -->
            <div style="font-size:1.8rem;font-weight:700;font-family:'Inter','Arial',sans-serif;color:#1C1F1F;margin-bottom:16px;background:var(--creme-moyen);padding:12px 20px;border-radius:var(--radius-lg);display:inline-block;">
                <?php if ($p['prix_promo']): ?>
                    <span style="text-decoration:line-through;color:var(--charbon-soft);font-weight:400;font-size:1.2rem;margin-right:10px;"><?= prix_format($p['prix']) ?></span>
                <?php endif; ?>
                <span id="prix-unitaire" data-prix="<?= $prix_unitaire ?>"><?= prix_format($prix_unitaire) ?></span>
            </div>

            <p style="color:var(--charbon);margin-bottom:30px;line-height:1.8;"><?= nl2br(htmlspecialchars($p['description'])) ?></p>

            <?php if (!empty($p['necessite_agrement'])): ?>
                <div style="background:#FFF3E0;color:#B26A00;padding:14px 18px;border-radius:var(--radius);font-size:0.88rem;margin-bottom:20px;">
                    Produit réservé aux professionnels de santé. Un numéro d'agrément ou une carte professionnelle sera demandé lors de la commande.
                </div>
            <?php endif; ?>

            <?php if ($p['stock'] > 0): ?>
                <form method="post" action="<?= BASE_URL ?>/panier.php" style="display:flex;gap:14px;align-items:center;flex-wrap:wrap;">
                    <input type="hidden" name="produit_id" value="<?= $p['id'] ?>">
                    <input type="hidden" name="action" value="ajouter">
                    
                    <!-- QUANTITÉ AVEC + ET - -->
                    <div style="display:flex;align-items:center;gap:10px;border:1px solid var(--sable);border-radius:var(--radius-lg);padding:4px 8px;background:#FFFFFF;">
                        <button type="button" onclick="updateQuantite(-1)" style="background:none;border:none;font-size:1.2rem;font-weight:700;color:var(--charbon);cursor:pointer;padding:4px 10px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:var(--radius);transition:background 0.2s ease;">
                            −
                        </button>
                        <input type="number" name="quantite" id="quantite-input" value="1" min="1" max="<?= $p['stock'] ?>" style="width:44px;text-align:center;border:none;font-size:1.1rem;font-weight:600;color:var(--charbon);background:transparent;padding:4px 0;">
                        <button type="button" onclick="updateQuantite(1)" style="background:none;border:none;font-size:1.2rem;font-weight:700;color:var(--charbon);cursor:pointer;padding:4px 10px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:var(--radius);transition:background 0.2s ease;">
                            +
                        </button>
                    </div>

                    <!-- TOTAL QUI CHANGE DYNAMIQUEMENT (NOIR) -->
                    <div style="font-size:1.3rem;font-weight:700;color:#1C1F1F;font-family:'Inter','Arial',sans-serif;">
                        <span id="prix-total"><?= prix_format($prix_unitaire) ?></span>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-left:auto;">Ajouter au panier</button>
                </form>
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
    .container[style*="display:grid"] > div:last-child p {
        text-align: left;
    }
    .container[style*="display:grid"] .alert {
        text-align: left;
    }
    .container[style*="display:grid"] form {
        justify-content: center !important;
        flex-direction: row !important;
        flex-wrap: wrap !important;
        gap: 10px !important;
    }
    .container[style*="display:grid"] form .btn {
        margin-left: 0 !important;
        width: 100% !important;
        justify-content: center !important;
    }
    .container[style*="display:grid"] .badge-actif {
        display: inline-block;
    }
    .container[style*="display:grid"] .qty-control {
        justify-content: center;
    }
    .container[style*="display:grid"] .btn {
        width: 100% !important;
        justify-content: center !important;
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
    .container[style*="display:grid"] > div:last-child p {
        font-size: 0.9rem !important;
    }
    .container[style*="display:grid"] form {
        flex-direction: column !important;
        align-items: stretch !important;
        gap: 10px !important;
    }
    .container[style*="display:grid"] form .qty-control {
        align-self: center !important;
        display: inline-flex !important;
    }
    .container[style*="display:grid"] form .btn {
        width: 100% !important;
        justify-content: center !important;
        margin-left: 0 !important;
    }
    .container[style*="display:grid"] form .prix-total {
        text-align: center !important;
        font-size: 1.2rem !important;
    }
    .qty-control {
        display: inline-flex !important;
    }
}
</style>

<script>
// Calcul automatique du prix selon la quantité
function updateQuantite(delta) {
    const input = document.getElementById('quantite-input');
    if (!input) return;
    let nouvelleValeur = parseInt(input.value) + delta;
    const max = parseInt(input.getAttribute('max'));
    
    if (nouvelleValeur < 1) nouvelleValeur = 1;
    if (nouvelleValeur > max) nouvelleValeur = max;
    
    input.value = nouvelleValeur;
    calculerTotal();
}

function calculerTotal() {
    const input = document.getElementById('quantite-input');
    const prixElement = document.getElementById('prix-unitaire');
    const totalElement = document.getElementById('prix-total');
    
    if (!input || !prixElement || !totalElement) return;
    
    const prixUnitaire = parseFloat(prixElement.getAttribute('data-prix'));
    const quantite = parseInt(input.value) || 1;
    const total = prixUnitaire * quantite;
    
    totalElement.textContent = total.toLocaleString('fr-FR') + ' DA';
}

// Calculer le total au chargement et à chaque changement
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('quantite-input');
    if (input) {
        input.addEventListener('change', calculerTotal);
        input.addEventListener('input', calculerTotal);
        calculerTotal();
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>