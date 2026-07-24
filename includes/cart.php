<?php
// ============================================
// Gestion du panier (session)
// ============================================

if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

function panier_ajouter($produit_id, $quantite = 1) {
    if (isset($_SESSION['panier'][$produit_id])) {
        $_SESSION['panier'][$produit_id] += $quantite;
    } else {
        $_SESSION['panier'][$produit_id] = $quantite;
    }
}

function panier_modifier($produit_id, $quantite) {
    if ($quantite <= 0) {
        unset($_SESSION['panier'][$produit_id]);
    } else {
        $_SESSION['panier'][$produit_id] = $quantite;
    }
}

function panier_supprimer($produit_id) {
    unset($_SESSION['panier'][$produit_id]);
}

function panier_vider() {
    $_SESSION['panier'] = [];
}

function panier_nb_articles() {
    return array_sum($_SESSION['panier'] ?? []);
}

function panier_details($pdo) {
    if (empty($_SESSION['panier'])) return [];

    $ids = array_keys($_SESSION['panier']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM produits WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $produits = $stmt->fetchAll();

    $details = [];
    foreach ($produits as $p) {
        $qte = $_SESSION['panier'][$p['id']];
        $prix = $p['prix_promo'] ?? $p['prix'];
        $details[] = [
            'produit' => $p,
            'quantite' => $qte,
            'prix_unitaire' => $prix,
            'sous_total' => $prix * $qte,
        ];
    }
    return $details;
}

function panier_total($pdo) {
    $total = 0;
    foreach (panier_details($pdo) as $item) {
        $total += $item['sous_total'];
    }
    return $total;
}

function prix_format($montant) {
    return number_format($montant, 0, ',', ' ') . ' DA';
}
