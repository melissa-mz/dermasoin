<?php
// Récupération de l'URL de connexion fournie par Render (Supabase)
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    $url = parse_url($database_url);
    
    $host = $url["host"] ?? '';
    $port = $url["port"] ?? '5432';
    $user = $url["user"] ?? '';
    $password = $url["pass"] ?? '';
    $dbname = ltrim($url["path"] ?? '', '/');

    // Connexion PostgreSQL pour Supabase
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $db_user = $user;
    $db_pass = $password;
} else {
    // Configuration locale de secours (si vous testez sur votre PC en local)
    $host = 'localhost';
    $dbname = 'dermasoin';
    $db_user = 'root';
    $db_pass = '';
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
}

try {
    $pdo = new PDO(
        $dsn,
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

define('BASE_URL', '');
define('SITE_NOM', 'DermaSoin');
define('SITE_TEL', '+213 550 02 02 63');
define('SITE_EMAIL', 'dermasoindz@gmail.com');
define('SITE_ADRESSE', 'Dar El Beida, 16033, Alger - Algérie');
define('FRAIS_LIVRAISON', 500);

session_start();