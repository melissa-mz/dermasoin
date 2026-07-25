<?php
$page_title = 'Boutique';
require_once __DIR__ . '/includes/header.php';

$categories = $pdo->query("SELECT * FROM categories ORDER BY ordre")->fetchAll();

// Récupérer la catégorie depuis l'URL
$cat_slug = $_GET['cat'] ?? '';

// ============================================
// PAGINATION
// ============================================
$produits_par_page = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $produits_par_page;

// Construire la requête SQL avec filtre si catégorie est présente
$sql_count = "SELECT COUNT(*) FROM produits WHERE actif = TRUE";
$sql = "SELECT * FROM produits WHERE actif = TRUE";
$params = [];

if (!empty($cat_slug)) {
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
    $stmt->execute([$cat_slug]);
    $categorie = $stmt->fetch();
    
    if ($categorie) {
        $sql_count .= " AND categorie_id = " . (int)$categorie['id'];
        $sql .= " AND categorie_id = " . (int)$categorie['id'];
    }
}

// Compter le nombre total de produits
$total_produits = $pdo->query($sql_count)->fetchColumn();
$total_pages = ceil($total_produits / $produits_par_page);

// Récupérer les produits de la page avec LIMIT et OFFSET
$sql .= " ORDER BY created_at DESC LIMIT " . (int)$produits_par_page . " OFFSET " . (int)$offset;
$produits = $pdo->query($sql)->fetchAll();

// Récupérer le nom de la catégorie pour l'affichage
$categorie_nom = 'Tous nos produits';
if (!empty($cat_slug)) {
    $stmt = $pdo->prepare("SELECT nom FROM categories WHERE slug = ?");
    $stmt->execute([$cat_slug]);
    $cat = $stmt->fetch();
    if ($cat) {
        $categorie_nom = $cat['nom'];
    }
}
?>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Boutique</span>
                <h2><?= htmlspecialchars($categorie_nom) ?></h2>
                <p style="color:var(--charbon-soft);font-size:0.85rem;margin-top:4px;">
                    <?= $total_produits ?> produit<?= $total_produits > 1 ? 's' : '' ?> disponibles
                </p>
            </div>
            <?php if (!empty($cat_slug)): ?>
                <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-outline" style="padding:8px 20px;font-size:0.75rem;">Voir tous les produits</a>
            <?php endif; ?>
        </div>

        <div style="display:flex;justify-content:flex-end;align-items:center;flex-wrap:wrap;gap:16px;margin-bottom:36px;">
            <div style="position:relative;flex-shrink:0;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:var(--charbon-soft);pointer-events:none;">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" id="recherche-live" placeholder="Rechercher un produit..." autocomplete="off"
                       style="padding:12px 18px 12px 42px;border:1px solid var(--sable);border-radius:100px;font-family:var(--font-body);font-size:0.9rem;width:320px;max-width:70vw;background:var(--blanc);">
            </div>
        </div>

        <div id="produits-grid" class="produits-grid">
            <?php if (count($produits) > 0): ?>
                <?php foreach ($produits as $p): ?>
                <?php include __DIR__ . '/includes/produit-card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state" style="grid-column:1/-1;">
                    Aucun produit dans cette catégorie.
                </div>
            <?php endif; ?>
        </div>

        <div id="aucun-resultat" class="empty-state" style="display:none;">
            Aucun produit ne correspond à votre recherche.
        </div>

        <!-- ============================================ -->
        <!-- PAGINATION -->
        <!-- ============================================ -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?><?= $cat_slug ? '&cat=' . urlencode($cat_slug) : '' ?>" 
               class="btn btn-outline">
                ‹ Précédent
            </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php if ($i === $page): ?>
                    <span class="btn btn-primary" style="cursor:default;">
                        <?= $i ?>
                    </span>
                <?php else: ?>
                    <a href="?page=<?= $i ?><?= $cat_slug ? '&cat=' . urlencode($cat_slug) : '' ?>" 
                       class="btn btn-outline">
                        <?= $i ?>
                    </a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?><?= $cat_slug ? '&cat=' . urlencode($cat_slug) : '' ?>" 
               class="btn btn-outline">
                Suivant ›
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
(function() {
    const input = document.getElementById('recherche-live');
    const cards = Array.from(document.querySelectorAll('#produits-grid .produit-card'));
    const grid = document.getElementById('produits-grid');
    const aucunResultat = document.getElementById('aucun-resultat');

    function normaliser(str) {
        return str.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    }

    input.addEventListener('input', function() {
        const q = normaliser(input.value.trim());
        let visibles = 0;

        cards.forEach(function(card) {
            const nom = normaliser(card.getAttribute('data-nom') || '');
            const correspond = q === '' || nom.startsWith(q);
            card.style.display = correspond ? '' : 'none';
            if (correspond) visibles++;
        });

        aucunResultat.style.display = visibles === 0 ? 'block' : 'none';
        grid.style.display = visibles === 0 ? 'none' : 'grid';
    });
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>