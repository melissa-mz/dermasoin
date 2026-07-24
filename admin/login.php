<?php
require_once __DIR__ . '/../config/db.php';

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $mdp = $_POST['mot_de_passe'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($mdp, $admin['mot_de_passe'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nom'] = $admin['nom'];
        header('Location: '.BASE_URL.'/admin/index.php');
        exit;
    } else {
        $erreur = 'Email ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion admin — DermaSoin</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/admin/admin.css">
</head>
<body style="background:var(--creme-chaud);display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;gap:20px;">

    <div style="background:var(--blanc);padding:44px;border-radius:var(--radius-lg);box-shadow:var(--shadow-soft);width:380px;">
        <h2 style="text-align:center;margin-bottom:24px;">Derma<span style="color:var(--petrole)">Soin</span> Admin</h2>
        <?php if ($erreur): ?><div class="alert alert-error"><?= htmlspecialchars($erreur) ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required autofocus>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="mot_de_passe" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
        </form>
    </div>

    <!-- Bouton retour accueil -->
    <a href="<?= BASE_URL ?>/index.php" class="btn-retour-accueil">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5"/>
            <path d="M12 5l-7 7 7 7"/>
        </svg>
        Retour à l'accueil
    </a>

</body>
</html>