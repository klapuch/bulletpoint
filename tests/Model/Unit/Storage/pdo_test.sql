SET NAMES utf8;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS pdo_test;
USE pdo_test;

DROP TABLE IF EXISTS `test`;
CREATE TABLE `test` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;