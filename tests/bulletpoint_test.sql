-- Adminer 4.2.4-dev MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `bulletpoints`;
CREATE TABLE `bulletpoints` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` varchar(255) CHARACTER SET utf8 NOT NULL,
  `information_source_id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `document_id,content` (`document_id`,`content`),
  KEY `document_id` (`document_id`),
  KEY `information_source_id` (`information_source_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


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
  PRIMARY KEY (`ID`),
  KEY `information_source_id` (`information_source_id`),
  KEY `author` (`author`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `bulletpoint_ratings`;
CREATE TABLE `bulletpoint_ratings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `bulletpoint_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `point` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `bulletpoint_id,point,user_id` (`bulletpoint_id`,`point`,`user_id`),
  UNIQUE KEY `bulletpoind_id,user_id` (`bulletpoint_id`,`user_id`),
  KEY `bulletpoint_id,point` (`bulletpoint_id`,`point`),
  KEY `user_id` (`user_id`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` text COLLATE utf8_czech_ci NOT NULL,
  `posted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `document_id` int(11) NOT NULL,
  `visible` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`ID`),
  KEY `document_id` (`document_id`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `comment_complaints`;
CREATE TABLE `comment_complaints` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reason` enum('Jiné','Spam','Vulgarita') COLLATE utf8_czech_ci NOT NULL DEFAULT 'Jiné',
  `settled` bit(1) NOT NULL DEFAULT b'0',
  `complained_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`),
  KEY `comment_id` (`comment_id`),
  KEY `comment_id,user_id` (`comment_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `information_source_id` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `title` (`title`),
  KEY `information_source_id` (`information_source_id`),
  KEY `user_id` (`user_id`),
  FULLTEXT KEY `title_2` (`title`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `document_proposals`;
CREATE TABLE `document_proposals` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `author` int(11) NOT NULL,
  `decision` enum('+1','-1','0') COLLATE utf8_czech_ci NOT NULL DEFAULT '0',
  `decided_at` datetime DEFAULT NULL,
  `proposed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `arbiter` int(11) DEFAULT NULL,
  `arbiter_comment` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `information_source_id` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `information_source_id` (`information_source_id`),
  KEY `author` (`author`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `document_slugs`;
CREATE TABLE `document_slugs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `origin` int(11) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `origin` (`origin`),
  UNIQUE KEY `slug,origin` (`slug`,`origin`)
) ENGINE=MyIsam DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `forgotten_passwords`;
CREATE TABLE `forgotten_passwords` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `reminder` varchar(141) CHARACTER SET latin1 NOT NULL,
  `reminded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `used` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `reminder` (`reminder`),
  KEY `user_id` (`user_id`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `information_sources`;
CREATE TABLE `information_sources` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `place` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `author` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `message_templates`;
CREATE TABLE `message_templates` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `message` text COLLATE utf8_czech_ci NOT NULL,
  `designation` enum('activation','forgotten-password') COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `punishments`;
CREATE TABLE `punishments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `sinner_id` int(11) NOT NULL,
  `reason` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `expiration` datetime NOT NULL,
  `author_id` int(11) NOT NULL,
  `forgiven` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`ID`),
  KEY `sinner_id` (`sinner_id`),
  KEY `author_id` (`author_id`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(160) COLLATE utf8_czech_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `role` enum('member','administrator','creator') COLLATE utf8_czech_ci NOT NULL DEFAULT 'member',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `verification_codes`;
CREATE TABLE `verification_codes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(91) CHARACTER SET latin1 NOT NULL,
  `used` tinyint(4) NOT NULL DEFAULT '0',
  `used_at` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- 2016-04-13 12:33:35
