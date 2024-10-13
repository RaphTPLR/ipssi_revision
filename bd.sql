-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 10 oct. 2024 à 15:04
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `devoirBanque`
--

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `clientId` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `telephone` varchar(15) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `mdp` varchar(150) NOT NULL,
  `dateCreation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`clientId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`clientId`, `nom`, `prenom`, `telephone`, `email`, `mdp`, `dateCreation`) VALUES
(1, 'Dupont', 'Jean', '0601020304', 'jean.dupont@example.com', 'mot_de_pass', '2024-10-10 14:26:35'),
(2, 'Martin', 'Claire', '0605060708', 'claire.martin@example.com', 'mot_de_pass', '2024-10-10 14:26:35');

-- --------------------------------------------------------

--
-- Structure de la table `devoirBanque`
--

DROP TABLE IF EXISTS `comptebancaire`;
CREATE TABLE IF NOT EXISTS `comptebancaire` (
  `compteId` int NOT NULL AUTO_INCREMENT,
  `numeroCompte` varchar(20) NOT NULL,
  `solde` decimal(10,2) DEFAULT '0.00',
  `typeDeCompte` enum('Courant','Epargne','Entreprise') NOT NULL,
  `dateOuverture` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `clientId` int DEFAULT NULL,
  PRIMARY KEY (`compteId`),
  UNIQUE KEY `numeroCompte` (`numeroCompte`),
  KEY `clientId` (`clientId`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `comptebancaire`
--

INSERT INTO `comptebancaire` (`compteId`, `numeroCompte`, `solde`, `typeDeCompte`, `dateOuverture`, `clientId`) VALUES
(1, 'FR761234567890123456', 1500.50, 'Courant', '2024-10-10 14:26:36', 1),
(2, 'FR768765432109876543', 2500.00, 'Epargne', '2024-10-10 14:26:36', 2);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comptebancaire`
--
ALTER TABLE `comptebancaire`
  ADD CONSTRAINT `comptebancaire_ibfk_1` FOREIGN KEY (`clientId`) REFERENCES `client` (`clientId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
