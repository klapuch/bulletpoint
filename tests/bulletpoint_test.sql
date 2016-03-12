-- Adminer 4.2.4-dev MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';

DROP TABLE IF EXISTS `banned_users`;
CREATE TABLE `banned_users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reason` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `expiration` datetime NOT NULL,
  `author_id` int(11) NOT NULL,
  `canceled` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `bulletpoints`;
CREATE TABLE `bulletpoints` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` varchar(255) CHARACTER SET utf8 NOT NULL,
  `information_source_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `bulletpoint_proposals`;
CREATE TABLE `bulletpoint_proposals` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `document_id` int(11) NOT NULL,
  `content` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `author` int(11) NOT NULL,
  `decision` enum('+1','-1','0') COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  `decided_at` datetime DEFAULT NULL,
  `proposed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `arbiter` int(11) DEFAULT NULL,
  `arbiter_comment` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `information_source_id` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `bulletpoint_ratings`;
CREATE TABLE `bulletpoint_ratings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `bulletpoint_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` enum('+1','-1','0') CHARACTER SET armscii8 COLLATE armscii8_bin NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `user_id,bulletpoint_id` (`user_id`,`bulletpoint_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  `posted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `document_id` int(11) NOT NULL,
  `visible` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `comment_complaints`;
CREATE TABLE `comment_complaints` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` enum('Jiné','Vulgarita','Spam') COLLATE utf8_czech_ci NOT NULL DEFAULT 'Jiné',
  `settled` bit(1) NOT NULL DEFAULT b'0',
  `complained_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `information_source_id` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `document_proposals`;
CREATE TABLE `document_proposals` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `author` int(11) NOT NULL,
  `decision` enum('+1','-1','0') COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  `decided_at` datetime DEFAULT NULL,
  `proposed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `arbiter` int(11) DEFAULT NULL,
  `arbiter_comment` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `information_source_id` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `document_slugs`;
CREATE TABLE `document_slugs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `origin` int(11) NOT NULL,
  `slug` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `forgotten_passwords`;
CREATE TABLE `forgotten_passwords` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reminder` varchar(200) COLLATE utf8_czech_ci NOT NULL,
  `reminded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `used` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `information_sources`;
CREATE TABLE `information_sources` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `place` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `author` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `message_templates`;
CREATE TABLE `message_templates` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8_czech_ci NOT NULL,
  `designation` enum('activation','forgotten-password') COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  `role` varchar(50) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `verification_codes`;
CREATE TABLE `verification_codes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `used` tinyint(4) NOT NULL DEFAULT '0',
  `used_at` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `token` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2016-02-25 13:37:58