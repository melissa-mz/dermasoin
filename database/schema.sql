-- ============================================
-- DermaSoin - Schéma de base de données
-- ============================================

CREATE DATABASE IF NOT EXISTS dermasoin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dermasoin;

-- ---------- Catégories ----------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    ordre INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- Produits ----------
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categorie_id INT,
    nom VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    description TEXT,
    description_courte VARCHAR(255),
    actifs VARCHAR(255) COMMENT 'ex: Acide hyaluronique, Vitamine C',
    prix DECIMAL(10,2) NOT NULL,
    prix_promo DECIMAL(10,2) DEFAULT NULL,
    stock INT DEFAULT 0,
    image_principale VARCHAR(255),
    en_vedette TINYINT(1) DEFAULT 0,
    necessite_agrement TINYINT(1) DEFAULT 0 COMMENT 'produit réservé aux professionnels de santé, agrément requis à la commande',
    actif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- ---------- Images produits (galerie) ----------
CREATE TABLE produit_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    ordre INT DEFAULT 0,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
);

-- ---------- Clients ----------
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    telephone VARCHAR(20) NOT NULL,
    mot_de_passe VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------- Commandes ----------
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_commande VARCHAR(20) NOT NULL UNIQUE,
    client_id INT DEFAULT NULL,
    nom_client VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(150),
    wilaya VARCHAR(60) NOT NULL,
    commune VARCHAR(100) NOT NULL,
    adresse TEXT NOT NULL,
    mode_paiement ENUM('cod','edahabia','virement') NOT NULL,
    agrement_pro VARCHAR(100) DEFAULT NULL COMMENT 'numéro agrément/carte pro, requis si commande contient un produit à usage professionnel',
    statut_paiement ENUM('en_attente','paye','echoue') DEFAULT 'en_attente',
    statut_commande ENUM('nouvelle','confirmee','preparee','expediee','livree','annulee') DEFAULT 'nouvelle',
    sous_total DECIMAL(10,2) NOT NULL,
    frais_livraison DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL
);

-- ---------- Détails commande ----------
CREATE TABLE commande_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT,
    nom_produit VARCHAR(150) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    quantite INT NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE SET NULL
);

-- ---------- Administrateurs ----------
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Compte admin par défaut : email admin@dermasoin.dz / mot de passe: admin123
INSERT INTO admins (nom, email, mot_de_passe) VALUES
('Admin DermaSoin', 'admin@dermasoin.dz', '$2b$10$EF8LGv34jR86V.ammGTCaupw3q9m8jU6hrj7is07Xw./bGcDhzdqS');
-- (hash bcrypt vérifié pour "admin123" — pense à le changer une fois en prod)

-- ---------- Données d'exemple : catégories ----------
INSERT INTO categories (nom, slug, description, ordre) VALUES
('Soins du visage', 'soins-visage', 'Sérums, crèmes et nettoyants pour une peau éclatante', 1),
('Soins du corps', 'soins-corps', 'Hydratation et soin corporel professionnel', 2),
('Compléments beauté', 'complements', 'Compléments alimentaires pour la peau et les cheveux', 3),
('Appareils esthétiques', 'appareils', 'Dispositifs de soin à usage domestique', 4),
('Coffrets & Rituels', 'coffrets', 'Sets complets pour une routine ciblée', 5),
('Neurotoxines', 'neurotoxines', 'Toxine botulique de type A, usage professionnel exclusif', 6),
('Exosomes', 'exosomes', 'Régénération cellulaire avancée, usage professionnel exclusif', 7);

-- ---------- Produits réels (ajoutés par la cliente) ----------
INSERT INTO produits (categorie_id, nom, slug, description, description_courte, actifs, prix, prix_promo, stock, en_vedette, necessite_agrement) VALUES
(7, 'Hairna Exosome Hair Fill', 'hairna-exosome-hair-fill', 'La réparation capillaire au niveau cellulaire. Exosomes d\'origine humaine pour stimuler la régénération du cuir chevelu, favoriser la croissance des cheveux et améliorer la santé du cuir chevelu.', 'Régénération capillaire au niveau cellulaire', 'Exosomes, Facteurs de croissance', 18000.00, NULL, 10, 1, 0),
(6, 'Metox 100U', 'metox-100u', 'Toxine botulique de type A, complexe neurotoxine purifié. Haute pureté, performance constante. Réservé exclusivement aux professionnels de santé.', 'La performance en toute confiance', 'Toxine botulique type A', 22000.00, NULL, 15, 0, 1),
(6, 'Metox 200U', 'metox-200u', 'Toxine botulique de type A, complexe neurotoxine purifié, dosage 200 unités. Réservé exclusivement aux professionnels de santé.', 'La performance en toute confiance', 'Toxine botulique type A', 38000.00, NULL, 10, 0, 1),
(6, 'Botulax 100U', 'botulax-100u', 'Toxine botulique de type A, haute pureté, performance constante. Réservé exclusivement aux professionnels de santé.', 'La précision en toute confiance', 'Toxine botulique type A', 20000.00, NULL, 12, 0, 1),
(6, 'Botulax 200U', 'botulax-200u', 'Toxine botulique de type A, dosage 200 unités. Réservé exclusivement aux professionnels de santé.', 'La précision en toute confiance', 'Toxine botulique type A', 34000.00, NULL, 10, 0, 1),
(6, 'Botulax 300U', 'botulax-300u', 'Toxine botulique de type A, dosage 300 unités. Réservé exclusivement aux professionnels de santé.', 'La précision en toute confiance', 'Toxine botulique type A', 48000.00, NULL, 8, 0, 1),
(6, 'Nabota 100U', 'nabota-100u', 'Toxine botulique de type A développée par Daewoong. Pureté et constance clinique, réservé exclusivement aux professionnels de santé.', 'La pureté en toute confiance', 'Toxine botulique type A', 21000.00, NULL, 12, 0, 1),
(6, 'Nabota 200U', 'nabota-200u', 'Toxine botulique de type A développée par Daewoong, dosage 200 unités. Réservé exclusivement aux professionnels de santé.', 'La pureté en toute confiance', 'Toxine botulique type A', 36000.00, NULL, 8, 0, 1);

UPDATE produits SET image_principale = 'nabota-100-200.jpg' WHERE slug IN ('nabota-100u','nabota-200u');

UPDATE produits SET image_principale = 'hairna-exosome-hair-fill.jpg' WHERE slug = 'hairna-exosome-hair-fill';
UPDATE produits SET image_principale = 'metox-100-200.jpg' WHERE slug IN ('metox-100u','metox-200u');
UPDATE produits SET image_principale = 'botulax-100-200-300.jpg' WHERE slug IN ('botulax-100u','botulax-200u','botulax-300u');
