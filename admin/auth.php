<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/cart.php';

function admin_requis() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: '.BASE_URL.'/admin/login.php');
        exit;
    }
}