<?php
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
    die("Erreur de connexion : " . $e->getMessage());
}

define('BASE_URL', '/dermasoin');
define('SITE_NOM', 'DermaSoin');
define('SITE_TEL', '+213 550 02 02 63');
define('SITE_EMAIL', 'dermasoindz@gmail.com');
define('SITE_ADRESSE', 'Dar El Beida, 16033, Alger - Algérie');
define('FRAIS_LIVRAISON', 500);

session_start();