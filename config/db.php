<?php
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Connexion distante PostgreSQL (sur Render / Supabase)
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
        die("Erreur de connexion PostgreSQL : " . $e->getMessage());
    }
    
    // ⚠️ CORRECTION : Définir BASE_URL pour Render
    define('BASE_URL', 'https://dermasoin.onrender.com');
    
} else {
    // Connexion locale MySQL (sur Wampserver)
    $DB_HOST = 'localhost';
    $DB_NAME = 'dermasoin';
    $DB_USER = 'root';
    $DB_PASS = ''; // Sur Wamp, le mot de passe root est souvent vide par défaut

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
        die("Erreur de connexion Wamp : " . $e->getMessage());
    }

    define('BASE_URL', '/dermasoin');
}

// Duplication pour éviter toute erreur de variable non définie dans vos autres fichiers
$conn = $pdo;
$db = $pdo;
$connect = $pdo;
$connexion = $pdo;

// Constantes globales du site
define('SITE_NOM', 'DermaSoin');
define('SITE_TEL', '+213 550 02 02 63');
define('SITE_EMAIL', 'dermasoindz@gmail.com');
define('SITE_ADRESSE', 'Dar El Beida, 16033, Alger - Algérie');
define('FRAIS_LIVRAISON', 500);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}