-- Création de la base de données
CREATE DATABASE IF NOT EXISTS game;
USE game;

-- Création de la table achats
CREATE TABLE `achats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `fournisseur_id` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `date_achat` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `fournisseur_id` (`fournisseur_id`),
  CONSTRAINT `achats_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`),
  CONSTRAINT `achats_ibfk_2` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseurs` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création de la table armes
CREATE TABLE `armes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marque` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `prix` decimal(10,2) DEFAULT NULL,
  `fournisseur` int(11) DEFAULT NULL,
  `calibre` varchar(50) DEFAULT NULL,
  `etat_achat` enum('neuf','occasion') DEFAULT NULL,
  `date_achat` date DEFAULT NULL,
  `num_serie` varchar(255) DEFAULT NULL,
  `date_revente` date DEFAULT NULL,
  `prix_revente` decimal(10,2) DEFAULT NULL,
  `etat_revente` varchar(255) DEFAULT NULL,
  `date_reparation` date DEFAULT NULL,
  `prix_reparation` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création de la table articles
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `marque` varchar(50) DEFAULT NULL,
  `model` varchar(50) DEFAULT NULL,
  `stock` enum('achete','reglementaire') DEFAULT NULL,
  `prix_unite` decimal(10,2) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `type` varchar(255) NOT NULL,
  `cartouches_par_boite` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création de la table fournisseurs
CREATE TABLE `fournisseurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `code_postal` varchar(20) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création de la table reference_suffix
CREATE TABLE `reference_suffix` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prefix` varchar(10) NOT NULL,
  `current_number` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création de la table roles
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` enum('administrateur','utilisateur') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création de la table seance_tir
CREATE TABLE `seance_tir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) NOT NULL,
  `arme` int(11) DEFAULT NULL,
  `stock` enum('achete','reglementaire') DEFAULT NULL,
  `stand_de_tir` int(11) DEFAULT NULL,
  `date_seance` date DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `nom_invite` varchar(100) DEFAULT NULL,
  `nombre_munitions_tirees` int(11) NOT NULL DEFAULT 0,
  `prix_total_cart_achete` decimal(10,2) DEFAULT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `prix_boite` decimal(10,2) NOT NULL,
  `tarif` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `arme` (`arme`),
  KEY `seance_tir_ibfk_2` (`stand_de_tir`),
  CONSTRAINT `seance_tir_ibfk_1` FOREIGN KEY (`arme`) REFERENCES `armes` (`id`),
  CONSTRAINT `seance_tir_ibfk_2` FOREIGN KEY (`stand_de_tir`) REFERENCES `stands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création de la table stands
CREATE TABLE `stands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `code_postal` varchar(20) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `prix_par_invite` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création de la table stock_reglementaire
CREATE TABLE `stock_reglementaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) DEFAULT NULL,
  `quantite_boites` int(11) DEFAULT NULL,
  `quantite_cartouches` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `stock_reglementaire_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Création de la table utilisateurs
CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `identifiant` varchar(50) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('administrateur','utilisateur') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `identifiant` (`identifiant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertion des données initiales
INSERT INTO `fournisseurs` (`id`, `nom`, `adresse`, `code_postal`, `ville`, `pays`, `telephone`, `email`) VALUES
(1, 'Armurerie du Château', '6 Rte de Villiers', '77780', 'Bourron-Marlotte', 'FRANCE', '0160719164', 'armduchateau@orange.fr');

INSERT INTO `reference_suffix` (`id`, `prefix`, `current_number`) VALUES
(1, 'ART-', 3),
(2, 'SEA-', 3);

INSERT INTO `roles` (`id`, `role`) VALUES
(1, 'administrateur');

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `identifiant`, `mot_de_passe`, `role`) VALUES
(1, 'PRADOS', 'Georges', 'g.prados', '$2y$10$dL8RIGzsHhFnRyezWuMyKeLQQpAx.EcrKbwTgRh5YPKxA8JFyXLS.', 'administrateur');

-- Insertion des données pour tests
INSERT INTO `achats` (`id`, `article_id`, `fournisseur_id`, `quantite`, `date_achat`) VALUES
(1, 2, 1, 1, '2024-06-13');

INSERT INTO `armes` (`id`, `marque`, `model`, `prix`, `fournisseur`, `calibre`, `etat_achat`, `date_achat`, `num_serie`, `date_revente`, `prix_revente`, `etat_revente`, `date_reparation`, `prix_reparation`) VALUES
(1, 'HK', 'SFP9', 750.00, 1, '9 mm', 'neuf', '2016-07-25', '56898989xvc63541050x', '2024-06-13', 720.00, 'occasion', '2024-06-13', 150.00),
(2, 'SIG-SAUER', 'P 226 ', 2950.00, 1, '9 mm', 'neuf', '2020-06-08', 'YFGDFYTGH', '2024-06-18', 2000.00, 'occasion', '2024-06-01', 265.00),
(3, 'GLOCK', '19', 500.00, 1, '9 mm', 'neuf', '2021-01-01', 'GTGTHT', NULL, NULL, 'neuf', NULL, NULL),
(4, 'GLOCK', '17', 450.00, 1, '9mm', 'neuf', '2019-02-01', '5282585', NULL, NULL, 'neuf', NULL, NULL),
(5, 'glock', '35', 555.00, 1, '40', 'neuf', '0215-02-01', '2858', NULL, NULL, 'neuf', NULL, NULL),
(6, 'beretta', '92x', 1400.00, 1, '9mm', 'neuf', '2023-01-01', '5285282', NULL, NULL, 'neuf', NULL, NULL),
(7, 'cz', 'shadow 2 compact', 1500.00, 1, '9 mm', 'neuf', '2024-04-15', '285858', NULL, NULL, 'neuf', NULL, NULL),
(8, 'sig sauer', '1911', 1400.00, 1, '45', 'neuf', '2014-02-01', '58585', NULL, NULL, 'neuf', NULL, NULL);

INSERT INTO `articles` (`id`, `marque`, `model`, `stock`, `prix_unite`, `reference`, `status`, `type`, `cartouches_par_boite`) VALUES
(2, 'fgbnv', 'vbnvn', NULL, 50.00, '', 1, 'munition', 25),
(3, 'dsfsdf', 'sfsdfs', NULL, 16.50, 'ART-1', 1, 'munition', 50),
(4, 'fhfgh', 'gvhvh', NULL, 14.00, 'ART-2', 1, 'consommable', NULL);

INSERT INTO `seance_tir` (`id`, `reference`, `arme`, `stock`, `stand_de_tir`, `date_seance`, `commentaire`, `nom_invite`, `nombre_munitions_tirees`, `prix_total_cart_achete`, `heure_debut`, `heure_fin`, `prix_boite`, `tarif`) VALUES
(1, '', 1, 'achete', 1, '2024-06-19', '', 'jghjghg', 100, NULL, '08:38:00', '09:38:00', 20.00, 'club'),
(2, '', 1, 'reglementaire', 1, '2024-06-18', '', '', 50, NULL, '09:12:00', '10:12:00', 0.00, ''),
(3, '', 1, 'reglementaire', 1, '2024-06-12', '', '', 50, NULL, '09:13:00', '10:13:00', 0.00, ''),
(4, '', 1, 'reglementaire', 1, '2024-06-18', '', 'gfhdfhcg', 50, NULL, '11:44:00', '12:44:00', 0.00, ''),
(5, '', 1, 'reglementaire', 1, '2024-06-19', '', 'hjgjfchd', 50, NULL, '11:52:00', '12:52:00', 0.00, ''),
(6, 'SEA-00002', 1, 'reglementaire', 1, '2024-06-20', '', '', 25, NULL, '14:08:00', '15:08:00', 0.00, '');

INSERT INTO `stands` (`id`, `nom`, `adresse`, `code_postal`, `ville`, `pays`, `telephone`, `email`, `prix_par_invite`) VALUES
(1, 'Armurerie du Château', 'Z.I Route de Villiers', '77780', 'BOURRON MARLOTTE', 'FRANCE', '0160719164', 'armduchateau@orange.fr', 20.00);

INSERT INTO `stock_reglementaire` (`id`, `article_id`, `quantite_boites`, `quantite_cartouches`) VALUES
(1, 2, 10, 500);
