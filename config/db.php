<?php
// ============================================
// CONNEXION À LA BASE DE DONNÉES
// ============================================

// Détecter l'environnement
$is_local = (strpos($_SERVER['SERVER_NAME'] ?? '', 'localhost') !== false || 
             strpos($_SERVER['SERVER_NAME'] ?? '', '127.0.0.1') !== false);

if ($is_local) {
    // ============================================
    // LOCAL : MySQL (Wamp)
    // ============================================
    $DB_HOST = 'localhost';
    $DB_NAME = 'dermasoin';
    $DB_USER = 'root';
    $DB_PASS = '';

    try {
        $pdo = new PDO(
            "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
            $DB_USER,
            $DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (PDOException $e) {
        die("Erreur de connexion MySQL : " . $e->getMessage());
    }

    define('BASE_URL', '/dermasoin');
    
} else {
    // ============================================
    // PRODUCTION : Supabase (Render)
    // ============================================
    $database_url = 'postgresql://postgres.blquaulqgsifbeuvoegs:dermasoin2026@aws-0-eu-west-1.pooler.supabase.com:5432/postgres';

    $url = parse_url($database_url);
    $host = $url["host"] ?? '';
    $port = $url["port"] ?? '5432';
    $user = $url["user"] ?? '';
    $password = $url["pass"] ?? '';
    $dbname = ltrim($url["path"] ?? '', '/');

    try {
        $pdo = new PDO(
            "pgsql:host=$host;port=$port;dbname=$dbname",
            $user,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (PDOException $e) {
        die("Erreur de connexion Supabase : " . $e->getMessage());
    }

    define('BASE_URL', 'https://dermasoin-elhc.onrender.com');
}

$conn = $pdo;
$db = $pdo;
$connect = $pdo;
$connexion = $pdo;

define('SITE_NOM', 'DermaSoin');
define('SITE_TEL', '+213 550 02 02 63');
define('SITE_EMAIL', 'dermasoindz@gmail.com');
define('SITE_ADRESSE', 'Alger - Algérie');
define('FRAIS_LIVRAISON', 500);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}