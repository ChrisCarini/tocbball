SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `tocbballstats`
--
CREATE DATABASE IF NOT EXISTS `tocbballstats` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `tocbballstats`;

--
-- Table structure for table `stats`
--

DROP TABLE IF EXISTS `stats`;
CREATE TABLE IF NOT EXISTS `stats` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `shooterName` varchar(16) NOT NULL,
  `scorerName` varchar(16) NOT NULL,
  `round1Points` varchar(2) NOT NULL,
  `round2Points` varchar(2) NOT NULL,
  `round3Points` varchar(2) NOT NULL,
  `totalPoints` varchar(2) NOT NULL,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  `dateadded` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

COMMIT;