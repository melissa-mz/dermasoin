<?php
$page_title = 'Produits';

// ============================================
// CHARGER LA CONFIGURATION D'ABORD
// ============================================
require_once __DIR__ . '/../config/db.php';

// ============================================
// DÉMARRER LA SESSION
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier que l'admin est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}

// ============================================
// SUPPRESSION
// ============================================
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    
    // Récupérer le nom de l'image pour la supprimer du dossier
    $stmt = $pdo->prepare("SELECT image_principale FROM produits WHERE id = ?");
    $stmt->execute([$id]);
    $produit = $stmt->fetch();
    
    if ($produit && !empty($produit['image_principale'])) {
        $chemin_image = __DIR__ . '/../assets/img/products/' . $produit['image_principale'];
        if (file_exists($chemin_image)) {
            unlink($chemin_image);
        }
    }
    
    $pdo->prepare("DELETE FROM produits WHERE id = ?")->execute([$id]);
    
    header('Location: ' . BASE_URL . '/admin/produits.php');
    exit;
}

// ============================================
// AJOUT / ÉDITION
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nom = trim($_POST['nom']);

    if ($id) {
        $slug_stmt = $pdo->prepare("SELECT slug FROM produits WHERE id = ?");
        $slug_stmt->execute([$id]);
        $slug = $slug_stmt->fetchColumn();
    } else {
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $nom))) . '-' . substr(md5(microtime()), 0, 4);
    }

    // Upload de l'image
    $image_principale = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $extensions_ok = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $extensions_ok)) {
            $nom_fichier = $slug . '-' . time() . '.' . $ext;
            $dest = __DIR__ . '/../assets/img/products/' . $nom_fichier;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
                $image_principale = $nom_fichier;
            }
        }
    }

    // Construction des données pour PostgreSQL
    $data = [
        'categorie_id' => $_POST['categorie_id'] ?: null,
        'nom' => $nom,
        'slug' => $slug,
        'description' => trim($_POST['description']),
        'description_courte' => trim($_POST['description_courte']),
        'actifs' => trim($_POST['actifs']),
        'prix' => (float)$_POST['prix'],
        'prix_promo' => $_POST['prix_promo'] !== '' ? (float)$_POST['prix_promo'] : null,
        'stock' => (int)$_POST['stock'],
        'en_vedette' => isset($_POST['en_vedette']) ? true : false,
        'necessite_agrement' => isset($_POST['necessite_agrement']) ? true : false,
    ];

    try {
        if ($id) {
            // Mise à jour
            if ($image_principale) {
                $sql = "UPDATE produits SET 
                    categorie_id = :categorie_id,
                    nom = :nom,
                    slug = :slug,
                    description = :description,
                    description_courte = :description_courte,
                    actifs = :actifs,
                    prix = :prix,
                    prix_promo = :prix_promo,
                    stock = :stock,
                    en_vedette = :en_vedette,
                    necessite_agrement = :necessite_agrement,
                    image_principale = :image_principale
                    WHERE id = :id";
                $data['image_principale'] = $image_principale;
                $data['id'] = $id;
            } else {
                $sql = "UPDATE produits SET 
                    categorie_id = :categorie_id,
                    nom = :nom,
                    slug = :slug,
                    description = :description,
                    description_courte = :description_courte,
                    actifs = :actifs,
                    prix = :prix,
                    prix_promo = :prix_promo,
                    stock = :stock,
                    en_vedette = :en_vedette,
                    necessite_agrement = :necessite_agrement
                    WHERE id = :id";
                $data['id'] = $id;
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
        } else {
            // Insertion
            $sql = "INSERT INTO produits (
                categorie_id, nom, slug, description, description_courte, actifs, 
                prix, prix_promo, stock, en_vedette, necessite_agrement, image_principale
            ) VALUES (
                :categorie_id, :nom, :slug, :description, :description_courte, :actifs,
                :prix, :prix_promo, :stock, :en_vedette, :necessite_agrement, :image_principale
            )";
            $data['image_principale'] = $image_principale;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
        }
    } catch (PDOException $e) {
        die("Erreur lors de l'enregistrement : " . $e->getMessage());
    }
    
    header('Location: '.BASE_URL.'/admin/produits.php');
    exit;
}

// ============================================
// MAINTENANT, ON INCLUT LE HEADER
// ============================================
require_once __DIR__ . '/includes-header.php';

$categories = $pdo->query("SELECT * FROM categories ORDER BY ordre")->fetchAll();
$produits = $pdo->query("SELECT p.*, c.nom AS cat_nom FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id ORDER BY p.created_at DESC")->fetchAll();

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}
?>

<h2>Produits</h2>

<div style="display:grid;grid-template-columns:1fr 1.3fr;gap:30px;align-items:start;margin-top:24px;">

    <div style="background:var(--blanc);padding:26px;border-radius:var(--radius-lg);box-shadow:var(--shadow-card);">
        <h3><?= $edit ? 'Modifier le produit' : 'Nouveau produit' ?></h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
            <div class="form-group">
                <label>Nom du produit *</label>
                <input type="text" name="nom" required value="<?= htmlspecialchars($edit['nom'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Catégorie</label>
                <select name="categorie_id">
                    <option value="">—</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (($edit['categorie_id'] ?? null) == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Photo du produit</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp">
                <?php if (!empty($edit['image_principale'])): ?>
                    <div style="margin-top:10px;display:flex;align-items:center;gap:10px;">
                        <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($edit['image_principale']) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:6px;">
                        <span style="font-size:0.8rem;color:var(--charbon-soft);">Photo actuelle — choisis un fichier pour la remplacer</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Description courte</label>
                <input type="text" name="description_courte" value="<?= htmlspecialchars($edit['description_courte'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Description complète</label>
                <textarea name="description" rows="3"><?= htmlspecialchars($edit['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Actifs (séparés par des virgules)</label>
                <input type="text" name="actifs" value="<?= htmlspecialchars($edit['actifs'] ?? '') ?>" placeholder="Vitamine C, Acide hyaluronique">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Prix (DA) *</label>
                    <input type="number" step="0.01" name="prix" required value="<?= htmlspecialchars($edit['prix'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Prix promo (DA)</label>
                    <input type="number" step="0.01" name="prix_promo" value="<?= htmlspecialchars($edit['prix_promo'] ?? '') ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Stock *</label>
                <input type="number" name="stock" required value="<?= htmlspecialchars($edit['stock'] ?? 0) ?>">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="en_vedette" style="width:auto;" <?= !empty($edit['en_vedette']) ? 'checked' : '' ?>> Produit en vedette (page d'accueil)</label>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="necessite_agrement" style="width:auto;" <?= !empty($edit['necessite_agrement']) ? 'checked' : '' ?>> Réservé aux professionnels de santé (agrément requis à la commande)</label>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><?= $edit ? 'Enregistrer les modifications' : 'Ajouter le produit' ?></button>
            <?php if ($edit): ?><a href="<?= BASE_URL ?>/admin/produits.php" style="display:block;text-align:center;margin-top:10px;font-size:0.85rem;">Annuler</a><?php endif; ?>
        </form>
    </div>

    <table class="admin-table">
        <thead><tr><th>Produit</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($produits as $p): ?>
            <tr>
                <td>
                    <?php if (!empty($p['image_principale'])): ?>
                        <img src="<?= BASE_URL ?>/assets/img/products/<?= htmlspecialchars($p['image_principale']) ?>" style="width:32px;height:32px;object-fit:cover;border-radius:4px;vertical-align:middle;margin-right:8px;">
                    <?php endif; ?>
                    <?= htmlspecialchars($p['nom']) ?>
                    <?php if (!empty($p['necessite_agrement'])): ?><span class="status-pill status-annulee" style="margin-left:6px;">Pro</span><?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['cat_nom'] ?? '—') ?></td>
                <td><?= prix_format($p['prix_promo'] ?? $p['prix']) ?></td>
                <td><?= $p['stock'] ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/produits.php?edit=<?= $p['id'] ?>">Modifier</a> ·
                    <a href="<?= BASE_URL ?>/admin/produits.php?supprimer=<?= $p['id'] ?>" onclick="return confirm('Supprimer ce produit ?')" style="color:var(--erreur)">Suppr.</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</main></div>
</body>
</html>