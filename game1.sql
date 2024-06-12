CREATE DATABASE game;

USE game;

CREATE TABLE armes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(255),
    model VARCHAR(255),
    photo VARCHAR(255),
    prix DECIMAL(10, 2),
    fournisseur VARCHAR(255),
    calibre VARCHAR(255),
    etat ENUM('neuf', 'occasion'),
    date_achat DATE,
    date_revente DATE,
    prix_revente DECIMAL(10, 2),
    date_reparation DATE,
    prix_reparation DECIMAL(10, 2)
);

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('munitions', 'equipements'),
    fournisseur VARCHAR(255),
    prix DECIMAL(10, 2),
    quantite INT,
    date_achat DATE,
    photo VARCHAR(255),
    marque VARCHAR(255),
    model VARCHAR(255),
    stock ENUM('achete', 'reglementaire')
);

CREATE TABLE fournisseurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255),
    adresse VARCHAR(255),
    code_postal VARCHAR(10),
    ville VARCHAR(255),
    pays VARCHAR(255),
    telephone VARCHAR(20),
    email VARCHAR(255)
);

CREATE TABLE stands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255),
    adresse VARCHAR(255),
    code_postal VARCHAR(10),
    ville VARCHAR(255),
    pays VARCHAR(255),
    telephone VARCHAR(20),
    email VARCHAR(255),
    prix_invite DECIMAL(10, 2)
);

CREATE TABLE seances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arme_id INT,
    nombre_munitions INT,
    stock ENUM('achete', 'reglementaire'),
    stand_id INT,
    date_seance DATE,
    heure_seance TIME,
    commentaire TEXT,
    invite_nom VARCHAR(255),
    invite_prenom VARCHAR(255),
    invite_date_naissance DATE,
    munitions_tirees INT,
    FOREIGN KEY (arme_id) REFERENCES armes(id),
    FOREIGN KEY (stand_id) REFERENCES stands(id)
);

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255),
    prenom VARCHAR(255),
    identifiant VARCHAR(255),
    mot_de_passe VARCHAR(255),
    role ENUM('administrateur', 'utilisateur')
);

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('administrateur', 'utilisateur')
);
