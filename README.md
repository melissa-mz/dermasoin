# DermaSoin — Site e-commerce (PHP / MySQL)

## Installation en local (XAMPP / WAMP / Laragon)

1. **Copie le dossier** `dermasoin` dans ton `htdocs` (XAMPP/WAMP) ou `www` (Laragon).

2. **Crée la base de données** :
   - Ouvre phpMyAdmin
   - Importe le fichier `database/schema.sql` (il crée la base `dermasoin`, toutes les tables, et insère des données d'exemple + un compte admin)

3. **Configure la connexion** dans `config/db.php` :
   - Par défaut : `DB_USER = root`, `DB_PASS = ''` (vide) — c'est généralement le cas sur XAMPP/Laragon.
   - Si ton MySQL local a un mot de passe, mets-le dans `$DB_PASS`.

4. **Lance le site** : `http://localhost/dermasoin/index.php`

5. **Accès admin** : `http://localhost/dermasoin/admin/login.php`
   - Email : `admin@dermasoin.dz`
   - Mot de passe : `admin123`
   - ⚠️ Change ce mot de passe une fois en prod (ajoute une page "changer mon mot de passe" ou modifie directement en base avec un nouveau hash `password_hash()`).

## Structure du projet

```
dermasoin/
├── config/db.php          → connexion BDD + constantes du site
├── includes/               → header, footer, panier, carte produit
├── assets/css/style.css    → design system (palette, typo, composants)
├── admin/                  → back-office (login, dashboard, produits, commandes)
├── database/schema.sql     → structure + données d'exemple
├── index.php               → accueil
├── boutique.php            → catalogue (avec filtre par catégorie)
├── produit.php             → fiche produit
├── panier.php               → panier (session)
├── commande.php            → checkout (COD / Edahabia / virement)
└── commande-confirmee.php  → confirmation de commande
```

## Ce qu'il te reste à faire

1. **Photos produits** : les cartes produits affichent un placeholder (icône 🧴). Ajoute tes vraies photos dans `assets/img/products/`, puis dans `includes/produit-card.php` et `produit.php`, remplace le `<span>🧴</span>` par une balise `<img>` pointant vers `produit['image_principale']`.

2. **Edahabia / CIB** : l'intégration réelle nécessite un **compte marchand SATIM** (la plateforme qui gère les paiements CIB/Edahabia en Algérie). Le checkout est prêt à recevoir cette option (voir le commentaire `TODO Melissa` dans `commande.php`) — une fois que tu as tes identifiants marchand SATIM, on branche l'appel API réel. En attendant, la commande est enregistrée normalement et le client est prévenu que le paiement sera finalisé par contact direct.

3. **Virement bancaire** : ajoute votre RIB dans le message de `commande-confirmee.php` (actuellement un message générique).

4. **Frais de livraison** : actuellement un forfait fixe (`FRAIS_LIVRAISON` dans `config/db.php`). Si tu veux un tarif par wilaya, dis-le-moi, je peux faire une table `tarifs_livraison`.

5. **Sécurité avant mise en prod** : change le mot de passe admin, désactive l'affichage des erreurs PHP (`display_errors`), mets le site derrière HTTPS, et pense à un `.htaccess` pour bloquer l'accès direct à `/config` et `/database`.
