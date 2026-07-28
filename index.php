<?php
$page_title = 'Accueil';
require_once __DIR__ . '/includes/header.php';

$categories = $pdo->query("
    SELECT c.*,
        COALESCE(c.image, (SELECT p.image_principale FROM produits p WHERE p.categorie_id = c.id AND p.image_principale IS NOT NULL AND p.actif = 1 ORDER BY p.created_at DESC LIMIT 1)) AS img,
        (SELECT COUNT(*) FROM produits p WHERE p.categorie_id = c.id AND p.actif = 1) AS nb_produits
    FROM categories c
    ORDER BY c.ordre
")->fetchAll();

// 🔥 MODIFICATION : LIMIT 3 → LIMIT 4 (4 produits = 2 lignes de 2 sur mobile)
$vedettes = $pdo->query("SELECT * FROM produits WHERE actif = 1 ORDER BY created_at DESC LIMIT 6")->fetchAll();
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

<!-- ============================================
     SECTION À PROPOS
============================================ -->
<section class="section about-section">
    <div class="container">

       <div class="section-head about-head">
<span class="about-eyebrow">À propos de Nous</span>
</div>

        <div class="about-grid">

        <div class="about-image-wrap">
    <div class="about-image">
        <img src="<?= BASE_URL ?>/assets/img/about.jpg" alt="DermaSoin médecine esthétique">
    </div>
    <div class="about-badge">
        <span class="about-badge-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
        </span>
        <span class="about-badge-text">Produits certifiés<span>Qualité professionnelle</span></span>
    </div>
</div>

            <!-- TEXTE À DROITE -->
            <div class="about-content">

            <h2>
    Votre expertise mérite <span class="accent-emeraude">l'excellence</span>
</h2>
               <<p class="about-lead">
    <strong>DERMASOIN</strong> est spécialisée dans la distribution de produits de médecine esthétique destinés aux professionnels. Nous sélectionnons des marques reconnues pour leur qualité, leur innovation et leur fiabilité.
</p>

                <div class="about-points">

                    <div>
                        <span>01</span>
                        <p>+50 références</p>
                    </div>

                    <div>
                        <span>02</span>
                        <p>+20 marques internationales</p>
                    </div>

                    <div>
                        <span>03</span>
                        <p>Produits 100% authentiques</p>
                    </div>

                </div>

            </div>

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
<!-- ============================================
   SECTION TÉMOIGNAGES - WOW AESTHETIC
============================================ -->
<section class="section testimonials-section">
    <div class="container">
        <div class="section-head" style="text-align: center; display: block;">
            <span class="eyebrow" style="color: #1C1F1F; font-weight: 700;">Ils nous font confiance</span>
            <h2>Ce que nos clients <span class="accent-emeraude">disent</span></h2>
        </div>

        <div class="testimonials-grid">
            <!-- Témoignage 1 -->
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <p class="testimonial-text">
                    "Merci beaucoup pour votre réactivité ! J'avais commandé un peu tard et j'étais vraiment pressée, mais vous avez assuré en me l'envoyant à temps. Je vous remercie sincèrement pour votre professionnalisme. A très bientôt !"
                </p>
            </div>

            <!-- Témoignage 2 -->
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <p class="testimonial-text">
                    "Merci beaucoup pour votre professionnalisme et votre ponctualité. Les livraisons sont toujours à l'heure, c'est un vrai plaisir de travailler avec vous. Merci 😊 À très bientôt !"
                </p>
            </div>

            <!-- Témoignage 3 -->
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#D4AF37"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="#E8E0D0"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <p class="testimonial-text">
                    "Je viens de découvrir votre compte aujourd'hui grâce aux partages des consoeurs. Ravie de cette découverte !"
                </p>
            </div>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>