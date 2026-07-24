<?php
ob_start();
$page_title = 'Mon panier';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $produit_id = (int)($_POST['produit_id'] ?? 0);

    if ($action === 'ajouter') {
        panier_ajouter($produit_id, (int)($_POST['quantite'] ?? 1));
    } elseif ($action === 'modifier') {
        panier_modifier($produit_id, (int)($_POST['quantite'] ?? 1));
    } elseif ($action === 'supprimer') {
        panier_supprimer($produit_id);
    }
    header('Location: '.BASE_URL.'/panier.php');
    exit;
}

$items = panier_details($pdo);
$total = panier_total($pdo);
?>

<section class="section">
    <div class="container">
        <h2 style="margin-bottom:36px;">Mon panier</h2>

        <?php if (empty($items)): ?>
            <div class="empty-state">
                Votre panier est vide.<br><br>
                <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-primary">Découvrir la boutique</a>
            </div>
        <?php else: ?>
        <div class="cart-layout">
            <table class="cart-table">
                <thead>
                    <tr><th>Produit</th><th>Prix</th><th>Quantité</th><th>Sous-total</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): $pr = $item['produit']; ?>
                    <tr>
                        <td data-label="Produit">
                            <div class="cart-row-product">
                                <div class="cart-thumb">
                                    <?php if (!empty($pr['image_principale'])): ?>
                                        <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($pr['image_principale']) ?>" alt="<?= htmlspecialchars($pr['nom']) ?>" style="width:64px;height:64px;object-fit:cover;border-radius:4px;">
                                    <?php else: ?>
                                        <div style="width:64px;height:64px;background:#EDE5D8;border-radius:4px;display:flex;align-items:center;justify-content:center;color:#999;font-size:10px;">Image</div>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= BASE_URL ?>/produit.php?slug=<?= $pr['slug'] ?>"><?= htmlspecialchars($pr['nom']) ?></a>
                            </div>
                        </td>
                        <td data-label="Prix"><?= prix_format($item['prix_unitaire']) ?></td>
                        <td data-label="Quantité">
                            <form method="post" style="display:flex;align-items:center;gap:8px;">
                                <input type="hidden" name="produit_id" value="<?= $pr['id'] ?>">
                                <input type="hidden" name="action" value="modifier">
                                <div class="qty-control">
                                    <button type="button" onclick="this.parentElement.querySelector('input').stepDown(); this.form.submit();">−</button>
                                    <input type="number" name="quantite" value="<?= $item['quantite'] ?>" min="1" max="<?= $pr['stock'] ?>" onchange="this.form.submit()">
                                    <button type="button" onclick="this.parentElement.querySelector('input').stepUp(); this.form.submit();">+</button>
                                </div>
                            </form>
                        </td>
                        <td data-label="Sous-total"><?= prix_format($item['sous_total']) ?></td>
                        <td data-label="">
                            <form method="post">
                                <input type="hidden" name="produit_id" value="<?= $pr['id'] ?>">
                                <input type="hidden" name="action" value="supprimer">
                                <button type="submit" class="cart-remove-btn">✕ Retirer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="summary-box cart-summary">
                <h3 style="margin-bottom:20px;">Résumé</h3>
                <div class="summary-row"><span>Sous-total</span><span><?= prix_format($total) ?></span></div>
                <div class="summary-row"><span>Livraison</span><span>Calculée à l'étape suivante</span></div>
                <div class="summary-row total"><span>Total</span><span><?= prix_format($total) ?></span></div>
                <a href="<?= BASE_URL ?>/commande.php" class="btn btn-primary btn-block" style="margin-top:20px;">Passer la commande</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php ob_end_flush(); ?>