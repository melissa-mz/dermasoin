<?php
// admin/auth.php
require_once __DIR__ . '/../config/db.php';

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function admin_requis() {
    // Vérifie si l'utilisateur est connecté
    if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

// Fonction pour vérifier si l'utilisateur est déjà connecté (pour login.php)
function est_connecte() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}
?>