<?php
ob_start(); // met en tampon la sortie HTML pour pouvoir rediriger (header()) plus loin dans le script
$page_title = 'Finaliser la commande';
require_once __DIR__ . '/includes/header.php';

$items = panier_details($pdo);
if (empty($items)) {
    header('Location: '.BASE_URL.'/panier.php');
    exit;
}
$sous_total = panier_total($pdo);
$total = $sous_total + FRAIS_LIVRAISON;

$wilayas = [
    'Adrar','Chlef','Laghouat','Oum El Bouaghi','Batna','Béjaïa','Biskra','Béchar','Blida','Bouira',
    'Tamanrasset','Tébessa','Tlemcen','Tiaret','Tizi Ouzou','Alger','Djelfa','Jijel','Sétif','Saïda',
    'Skikda','Sidi Bel Abbès','Annaba','Guelma','Constantine','Médéa','Mostaganem','M\'Sila','Mascara','Ouargla',
    'Oran','El Bayadh','Illizi','Bordj Bou Arréridj','Boumerdès','El Tarf','Tindouf','Tissemsilt','El Oued','Khenchela',
    'Souk Ahras','Tipaza','Mila','Aïn Defla','Naâma','Aïn Témouchent','Ghardaïa','Relizane',
    'Timimoune','Bordj Badji Mokhtar','Ouled Djellal','Béni Abbès','In Salah','In Guezzam','Touggourt','Djanet','El M\'Ghair','El Menia',
    'Aflou','Barika','El Kantara','Bir El Ater','El Aricha','Ksar Chellala','Aïn Ouessara','Messaad','Ksar El Boukhari','Bou Saâda','El Abiodh Sidi Cheikh'
];

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_famille = trim($_POST['nom_famille'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($nom_famille . ' ' . $prenom);
    $tel = trim($_POST['telephone'] ?? '');
    $wilaya = trim($_POST['wilaya'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $mode_paiement = $_POST['mode_paiement'] ?? '';

    if (!$nom_famille || !$prenom || !$tel || !$wilaya || !$adresse || !in_array($mode_paiement, ['cod','baridimob'])) {
        $erreur = 'Merci de remplir tous les champs obligatoires.';
    } else {
        $numero = 'DS' . date('ymd') . rand(1000, 9999);
        $commune = ''; // colonne conservée en base pour compatibilité, plus demandée séparément au client
        
        // PostgreSQL : utiliser des paramètres nommés ou ?, ?, ?
        $stmt = $pdo->prepare("INSERT INTO commandes
            (numero_commande, nom_client, telephone, email, wilaya, commune, adresse, mode_paiement, sous_total, frais_livraison, total)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([$numero, $nom, $tel, null, $wilaya, $commune, $adresse, $mode_paiement, $sous_total, FRAIS_LIVRAISON, $total]);
        $commande_id = $pdo->lastInsertId();

        $stmt2 = $pdo->prepare("INSERT INTO commande_articles (commande_id, produit_id, nom_produit, prix_unitaire, quantite) VALUES (?,?,?,?,?)");
        foreach ($items as $item) {
            $stmt2->execute([$commande_id, $item['produit']['id'], $item['produit']['nom'], $item['prix_unitaire'], $item['quantite']]);
            $pdo->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?")->execute([$item['quantite'], $item['produit']['id']]);
        }

        panier_vider();

        header("Location: " . BASE_URL . "/commande-confirmee.php?num=$numero");
        exit;
    }
}
?>

<section class="section">
    <div class="container" style="max-width:1100px;">
        <div style="text-align:center;margin-bottom:44px;">
            <span class="eyebrow">Dernière étape</span>
            <h2 style="margin-bottom:8px;">Finaliser votre commande</h2>
            <p style="color:var(--charbon-soft);font-size:0.95rem;">Quelques informations, et c'est parti.</p>
        </div>

        <?php if ($erreur): ?><div class="alert alert-error" style="max-width:700px;margin:0 auto 24px;"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>

        <form method="post" class="checkout-grid">
            <div class="checkout-form-card">

                <h3 style="display:flex;align-items:center;gap:10px;margin-bottom:24px;">
                    <span style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:var(--petrole-transparent);color:var(--petrole);flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    Vos informations
                </h3>

                <div class="form-row">
                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" name="nom_famille" required placeholder="Nom / اللقب" value="<?= htmlspecialchars($_POST['nom_famille'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Prénom *</label>
                        <input type="text" name="prenom" required placeholder="Prénom / الاسم" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Numéro de téléphone *</label>
                    <input type="tel" name="telephone" required placeholder="05XX XX XX XX" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Wilaya *</label>
                    <select name="wilaya" required>
                        <option value="">Sélectionner...</option>
                        <?php foreach ($wilayas as $w): ?>
                            <option value="<?= $w ?>" <?= (($_POST['wilaya'] ?? '') === $w) ? 'selected' : '' ?>><?= $w ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Adresse complète *</label>
                    <textarea name="adresse" rows="3" required placeholder="Quartier, rue, commune, repère..."><?= htmlspecialchars($_POST['adresse'] ?? '') ?></textarea>
                </div>

                <h3 style="display:flex;align-items:center;gap:10px;margin:32px 0 18px;">
                    <span style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:50%;background:var(--petrole-transparent);color:var(--petrole);flex-shrink:0;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </span>
                    Mode de paiement
                </h3>
                <div class="payment-options">
                    <label class="payment-option">
                        <input type="radio" name="mode_paiement" value="cod" checked>
                        <div>
                            <div class="p-title">Paiement à la livraison</div>
                            <div class="p-desc">Espèces, remis en main propre au livreur à la réception de votre colis</div>
                        </div>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="mode_paiement" value="baridimob">
                        <div>
                            <div class="p-title">BaridiMob / CCP</div>
                            <div class="p-desc">Virement via l'app BaridiMob ou versement CCP — les coordonnées vous seront envoyées après validation</div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="summary-box checkout-summary">
                <h3 style="margin-bottom:20px;">Votre commande</h3>
                <?php foreach ($items as $item): ?>
                    <div class="summary-row">
                        <span><?= htmlspecialchars($item['produit']['nom']) ?> × <?= $item['quantite'] ?></span>
                        <span><?= prix_format($item['sous_total']) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="summary-row"><span>Sous-total</span><span><?= prix_format($sous_total) ?></span></div>
                <div class="summary-row"><span>Livraison</span><span><?= prix_format(FRAIS_LIVRAISON) ?></span></div>
                <div class="summary-row total"><span>Total</span><span><?= prix_format($total) ?></span></div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px;">Confirmer la commande</button>
            </div>
        </form>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>