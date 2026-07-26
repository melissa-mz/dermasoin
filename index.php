<?php
$page_title = 'Accueil';
require_once __DIR__ . '/includes/header.php';

$categories = $pdo->query("
    SELECT c.*,
        COALESCE(c.image, (SELECT p.image_principale FROM produits p WHERE p.categorie_id = c.id AND p.image_principale IS NOT NULL AND p.actif = true ORDER BY p.created_at DESC LIMIT 1)) AS img,
        (SELECT COUNT(*) FROM produits p WHERE p.categorie_id = c.id AND p.actif = true) AS nb_produits
    FROM categories c
    ORDER BY c.ordre
")->fetchAll();

// 🔥 MODIFICATION : LIMIT 3 → LIMIT 4 (4 produits = 2 lignes de 2 sur mobile)
$vedettes = $pdo->query("SELECT * FROM produits WHERE actif = true ORDER BY created_at DESC LIMIT 6")->fetchAll();
?>

<section class="hero">
    <div class="hero-glow"></div>
    <div class="hero-inner">
        <div class="hero-text">
            <h1>
                L'excellence <span class="accent-emeraude">esthétique</span><br>
                à portée de<br>
                main.
            </h1>
            <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-primary">Découvrir la boutique</a>
        </div>
        <div class="hero-visual">
            <img src="<?= BASE_URL ?>/assets/img/hero2.jpg" alt="DermaSoin">
        </div>
    </div>
</section>

<section class="section cat-showcase">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Nos univers</span>
                <h2>Nos Catégories </h2>
            </div>
        </div>
        <div class="bento-cat-grid">
            <?php foreach ($categories as $i => $cat): ?>
            <a href="<?= BASE_URL ?>/boutique.php?cat=<?= urlencode($cat['slug']) ?>"
               class="bento-cat-card <?= $i === 0 ? 'bento-cat-card--large' : '' ?>"
               style="--delay: <?= $i * 90 ?>ms;">
                <?php if (!empty($cat['img'])): ?>
                    <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($cat['img']) ?>" alt="<?= htmlspecialchars($cat['nom']) ?>" class="bento-cat-img">
                <?php else: ?>
                    <div class="bento-cat-fallback"><span><?= mb_substr($cat['nom'], 0, 1) ?></span></div>
                <?php endif; ?>
                <div class="bento-cat-shine"></div>
                <div class="bento-cat-overlay"></div>
                <span class="bento-cat-index"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
                <div class="bento-cat-body">
                    <h3 class="bento-cat-nom"><?= htmlspecialchars($cat['nom']) ?></h3>
                    <span class="bento-cat-arrow">Explorer <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
(function() {
    const cards = document.querySelectorAll('.bento-cat-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    cards.forEach((card) => observer.observe(card));
})();
</script>

<section class="section" style="background:var(--porcelaine-alt)">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow">Sélection</span>
                <h2>Nos produits</h2>
            </div>
            <a href="<?= BASE_URL ?>/boutique.php" class="btn btn-outline">Voir tout</a>
        </div>
        <div class="produits-grid">
            <?php foreach ($vedettes as $p): ?>
            <?php include __DIR__ . '/includes/produit-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================
   SECTION MARQUES - SLIDER SANS BORDURE
   ============================================ -->
<!-- ============================================
   SECTION MARQUES - SLIDER SANS BORDURE
   ============================================ -->
<section class="section marques-slider-section">
    <div class="container">
        <div class="section-head" style="text-align: center; display: block;">
            <span class="eyebrow">Nos Partenaires</span>
            <h2>Marques <span class="accent-emeraude">partenaires</span></h2>
        </div>

        <div class="marques-slider-wrapper">
            <div class="marques-slider" id="marquesSlider">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="marque-slide">
                    <img src="<?= BASE_URL ?>/assets/img/marques/img<?= $i ?>.jpeg" alt="Marque <?= $i ?>">
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>
<!-- JavaScript du slider - Drag to scroll -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.marques-slider');
    
    if (slider) {
        let isDown = false;
        let startX;
        let scrollLeft;

        // Mouse events
        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.style.cursor = 'grabbing';
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
            slider.classList.add('dragging');
        });

        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.style.cursor = 'grab';
            slider.classList.remove('dragging');
        });

        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.style.cursor = 'grab';
            slider.classList.remove('dragging');
        });

        slider.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 1.5;
            slider.scrollLeft = scrollLeft - walk;
        });

        // Touch events pour mobile
        slider.addEventListener('touchstart', (e) => {
            isDown = true;
            startX = e.touches[0].pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });

        slider.addEventListener('touchmove', (e) => {
            if (!isDown) return;
            const x = e.touches[0].pageX - slider.offsetLeft;
            const walk = (x - startX) * 1.5;
            slider.scrollLeft = scrollLeft - walk;
        });

        slider.addEventListener('touchend', () => {
            isDown = false;
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>