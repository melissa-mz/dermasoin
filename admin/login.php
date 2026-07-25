<?php
require_once __DIR__ . '/auth.php';

// Si déjà connecté, direct au tableau de bord (pas de vérification admin_requis() ici !)
if (est_connecte()) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (!$email || !$mot_de_passe) {
        $erreur = 'Merci de remplir tous les champs.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($mot_de_passe, $admin['mot_de_passe'])) {
            $_SESSION['admin_id'] = $admin['id'];
            header('Location: ' . BASE_URL . '/admin/index.php');
            exit;
        } else {
            $erreur = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Connexion admin — DermaSoin</title>
<link rel="icon" type="image/jpeg" href="<?= BASE_URL ?>/assets/img/logo.jpg">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/admin/admin.css?v=5">
<link href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,wght@0,400;0,600;0,700;0,900;1,400&family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body style="min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--admin-bg-start, #FDFBF7);">

    <div style="width:100%;max-width:400px;padding:0 20px;">
        <div style="text-align:center;margin-bottom:28px;">
            <img src="<?= BASE_URL ?>/assets/img/logo.jpg" alt="DermaSoin" width="64" height="64" style="border-radius:50%;margin:0 auto 14px;display:block;">
            <h1 style="font-size:1.3rem;margin:0;">Derma<span style="color:var(--admin-primary, #0497A7);">Soin</span></h1>
            <p style="font-size:0.85rem;color:var(--admin-text-light, #5A6F6F);margin-top:4px;">Espace administrateur</p>
        </div>

        <form method="post" style="background:#FFFFFF;border-radius:var(--admin-radius, 14px);box-shadow:var(--admin-shadow, 0 4px 20px rgba(0,0,0,0.06));padding:32px;">
            <?php if ($erreur): ?>
                <div class="alert alert-error" style="margin-bottom:18px;"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="mot_de_passe" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">Se connecter</button>
        </form>

        <div style="text-align:center;margin-top:18px;">
            <a href="<?= BASE_URL ?>/index.php" class="btn-retour-accueil">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Retour à l'accueil
            </a>
        </div>

    </div>

</body>
</html>