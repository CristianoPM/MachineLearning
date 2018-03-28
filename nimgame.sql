-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Mer 28 Mars 2018 à 10:40
-- Version du serveur :  5.6.15-log
-- Version de PHP :  5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `nimgame`
--
CREATE DATABASE IF NOT EXISTS `nimgame` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `nimgame`;

DELIMITER $$
--
-- Procédures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `SelectCoups`(IN `parameterIdGame` INT(11))
BEGIN
SET @id := (SELECT idPremierCoup FROM games WHERE idGame = parameterIdGame);
DROP TABLE IF EXISTS tmpCoupsValues;
CREATE TABLE tmpCoupsValues(
coupValue INT(1) UNSIGNED
);
WHILE @id <> NULL DO
INSERT INTO tmpCoupsValues(coupValue) SELECT CoupValue FROM coups WHERE idCoup = @id;
SET @id = (SELECT idCoup FROM coups WHERE idParent = @id);
END WHILE;
SELECT * FROM tmpCoupsValues;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `coups`
--

CREATE TABLE IF NOT EXISTS `coups` (
  `idCoup` int(11) NOT NULL AUTO_INCREMENT,
  `idParent` int(11) DEFAULT NULL,
  `CoupValue` int(1) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `idUser` int(11) NOT NULL,
  PRIMARY KEY (`idCoup`),
  KEY `idUser` (`idUser`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=88 ;

--
-- Contenu de la table `coups`
--

INSERT INTO `coups` (`idCoup`, `idParent`, `CoupValue`, `Date`, `idUser`) VALUES
(68, NULL, 2, '2018-03-28 06:59:07', 1),
(69, 68, 3, '2018-03-28 06:59:08', 2),
(70, NULL, 2, '2018-03-28 07:35:01', 1),
(71, 70, 1, '2018-03-28 07:35:02', 2),
(72, 71, 1, '2018-03-28 07:35:04', 1),
(73, 72, 1, '2018-03-28 07:35:04', 2),
(74, NULL, 2, '2018-03-28 07:37:56', 1),
(75, 74, 2, '2018-03-28 07:37:58', 2),
(76, 75, 1, '2018-03-28 07:38:05', 1),
(77, NULL, 2, '2018-03-28 07:38:10', 1),
(78, 77, 2, '2018-03-28 07:38:15', 2),
(79, 78, 2, '2018-03-28 07:38:15', 1),
(80, NULL, 2, '2018-03-28 08:11:18', 1),
(81, 80, 3, '2018-03-28 08:12:00', 2),
(82, NULL, 2, '2018-03-28 08:12:05', 1),
(83, 82, 3, '2018-03-28 08:12:05', 2),
(84, NULL, 1, '2018-03-28 08:33:51', 1),
(85, 84, 1, '2018-03-28 08:33:51', 2),
(86, 85, 1, '2018-03-28 08:33:52', 1),
(87, 86, 3, '2018-03-28 08:33:52', 2);

-- --------------------------------------------------------

--
-- Structure de la table `games`
--

CREATE TABLE IF NOT EXISTS `games` (
  `idGame` int(11) NOT NULL AUTO_INCREMENT,
  `NbBilles` int(2) NOT NULL DEFAULT '5',
  `joueur1Won` tinyint(1) DEFAULT NULL,
  `idPremierCoup` int(11) DEFAULT NULL,
  PRIMARY KEY (`idGame`),
  KEY `idPremierCoup` (`idPremierCoup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Contenu de la table `games`
--

INSERT INTO `games` (`idGame`, `NbBilles`, `joueur1Won`, `idPremierCoup`) VALUES
(31, 5, 1, 68),
(32, 5, 0, 70),
(33, 5, 1, 74),
(34, 5, 1, 77),
(35, 5, 1, 80),
(36, 5, 1, 82),
(37, 5, NULL, NULL),
(38, 5, NULL, NULL),
(39, 5, NULL, NULL),
(40, 5, NULL, NULL),
(41, 5, 1, 84);

-- --------------------------------------------------------

--
-- Structure de la table `tmpcoupsvalues`
--

CREATE TABLE IF NOT EXISTS `tmpcoupsvalues` (
  `coupValue` int(1) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `idUser` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(45) NOT NULL,
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`idUser`, `nom`) VALUES
(1, 'joueur1'),
(2, 'IA');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `coups`
--
ALTER TABLE `coups`
  ADD CONSTRAINT `Coups_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `users` (`idUser`);

--
-- Contraintes pour la table `games`
--
ALTER TABLE `games`
  ADD CONSTRAINT `games_ibfk_1` FOREIGN KEY (`idPremierCoup`) REFERENCES `coups` (`idCoup`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
