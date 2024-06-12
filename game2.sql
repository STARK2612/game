CREATE DATABASE game;

USE game;

CREATE TABLE armes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    photo VARCHAR(255),
    prix DECIMAL(10, 2),
    fournisseur INT,
    calibre VARCHAR(50),
    etat ENUM('neuf', 'occasion'),
    date_achat DATE,
    date_revente DATE,
    prix_revente DECIMAL(10, 2),
    date_reparation DATE,
    prix_reparation DECIMAL(10, 2)
);

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('munitions', 'equipements') NOT NULL,
    fournisseur INT,
    prix DECIMAL(10, 2),
    quantite INT,
    date_achat DATE,
    photo VARCHAR(255),
    marque VARCHAR(50),
    model VARCHAR(50),
    stock ENUM('achete', 'reglementaire')
);

CREATE TABLE fournisseurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    adresse VARCHAR(255),
    code_postal VARCHAR(20),
    ville VARCHAR(100),
    pays VARCHAR(100),
    telephone VARCHAR(20),
    email VARCHAR(100)
);

CREATE TABLE stands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    adresse VARCHAR(255),
    code_postal VARCHAR(20),
    ville VARCHAR(100),
    pays VARCHAR(100),
    telephone VARCHAR(20),
    email VARCHAR(100),
    prix_par_invite DECIMAL(10, 2)
);

CREATE TABLE seance_tir (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arme INT,
    nombre_munitions INT,
    stock ENUM('achete', 'reglementaire'),
    stand_de_tir INT,
    date_seance DATE,
    heure_seance TIME,
    commentaire TEXT,
    nom_invite VARCHAR(100),
    prenom_invite VARCHAR(100),
    date_naissance_invite DATE,
    FOREIGN KEY (arme) REFERENCES armes(id),
    FOREIGN KEY (stand_de_tir) REFERENCES stands(id)
);

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    identifiant VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('administrateur', 'utilisateur') NOT NULL
);

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('administrateur', 'utilisateur')
);
