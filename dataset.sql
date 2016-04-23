-- Adminer 4.2.4-dev MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

TRUNCATE `bulletpoints`;
INSERT INTO `bulletpoints` (`ID`, `user_id`, `content`, `information_source_id`, `document_id`, `created_at`) VALUES
(1,	1,	'Jednoduchost',	4,	1,	'2016-04-20 18:34:11'),
(2,	1,	'Verze 7',	2,	1,	'2016-04-20 20:23:14'),
(3,	1,	'Strmá křivka učení',	3,	1,	'2016-04-20 20:23:21');

TRUNCATE `bulletpoint_proposals`;
INSERT INTO `bulletpoint_proposals` (`ID`, `document_id`, `content`, `author`, `decision`, `decided_at`, `proposed_at`, `arbiter`, `arbiter_comment`, `information_source_id`) VALUES
(1,	1,	'Verze 7',	1,	'+1',	'2016-04-20 22:23:15',	'2016-04-20 18:33:46',	1,	NULL,	2),
(2,	1,	'Strmá křivka učení',	1,	'+1',	'2016-04-20 22:23:21',	'2016-04-20 18:33:54',	1,	NULL,	3),
(3,	1,	'Jednoduchost',	1,	'+1',	'2016-04-20 20:34:11',	'2016-04-20 18:34:03',	1,	NULL,	4);

TRUNCATE `bulletpoint_ratings`;
INSERT INTO `bulletpoint_ratings` (`ID`, `bulletpoint_id`, `user_id`, `point`) VALUES
(1,	1,	1,	1),
(2,	2,	1,	1),
(4,	2,	2,	1),
(3,	3,	1,	0);

TRUNCATE `comments`;

TRUNCATE `comment_complaints`;

TRUNCATE `documents`;
INSERT INTO `documents` (`ID`, `user_id`, `title`, `description`, `created_at`, `information_source_id`) VALUES
(1,	1,	'PHP programovací jazyk',	'PHP (rekurzivní zkratka PHP: Hypertext Preprocessor, česky „PHP: Hypertextový preprocesor“, původně Personal Home Page) je skriptovací programovací jazyk. Je určený především pro programování dynamických internetových stránek a webových aplikací například ve formátu HTML, XHTML či WML. PHP lze použít i k tvorbě konzolových a desktopových aplikací. Pro desktopové použití existuje kompilovaná forma jazyka.\n\nPři použití PHP pro dynamické stránky jsou skripty prováděny na straně serveru – k uživateli je přenášen až výsledek jejich činnosti. Interpret PHP skriptu je možné volat pomocí příkazového řádku, dotazovacích metod HTTP nebo pomocí webových služeb. Syntaxe jazyka je inspirována několika programovacími jazyky (Perl, C, Pascal a Java). PHP je nezávislý na platformě, rozdíly v různých operačních systémech se omezují na několik systémově závislých funkcí a skripty lze většinou mezi operačními systémy přenášet bez jakýchkoli úprav.\n\nPHP podporuje mnoho knihoven pro různé účely – např. zpracování textu, grafiky, práci se soubory, přístup k většině databázových systémů (mj. MySQL, ODBC, Oracle, PostgreSQL, MSSQL), podporu celé řady internetových protokolů (HTTP, SMTP, SNMP, FTP, IMAP, POP3, LDAP, …).\n\nPHP je nejrozšířenějším skriptovacím jazykem pro web, v současnosti (listopad 2014) s podílem 82 %. Oblíbeným se stal především díky jednoduchosti použití, bohaté zásobě funkcí. V kombinaci s operačním systémem Linux, databázovým systémem (obvykle MySQL nebo PostgreSQL) a webovým serverem Apache je často využíván k tvorbě webových aplikací. Pro tuto kombinaci se vžila zkratka LAMP – tedy spojení Linux, Apache, MySQL a PHP, Perl nebo Python.\n\nV PHP jsou napsány i velké internetové internetové projekty, včetně Wikipedie nebo Facebooku (Facebook používá PHP transformované do C++ pomocí aplikace HipHop for PHP a to především kvůli vyšší rychlosti).',	'2016-04-20 18:32:34',	1),
(2,	1,	'Automobil Škoda auto',	'Just Škoda',	'2016-04-20 18:32:34',	5),
(3,	1,	'Automobil Seat',	'Just Seat',	'2016-04-20 18:32:34',	6);

TRUNCATE `document_proposals`;
INSERT INTO `document_proposals` (`ID`, `title`, `description`, `author`, `decision`, `decided_at`, `proposed_at`, `arbiter`, `arbiter_comment`, `information_source_id`) VALUES
(1,	'PHP programovací jazyk',	'PHP (rekurzivní zkratka PHP: Hypertext Preprocessor, česky „PHP: Hypertextový preprocesor“, původně Personal Home Page) je skriptovací programovací jazyk. Je určený především pro programování dynamických internetových stránek a webových aplikací například ve formátu HTML, XHTML či WML. PHP lze použít i k tvorbě konzolových a desktopových aplikací. Pro desktopové použití existuje kompilovaná forma jazyka.\n\nPři použití PHP pro dynamické stránky jsou skripty prováděny na straně serveru – k uživateli je přenášen až výsledek jejich činnosti. Interpret PHP skriptu je možné volat pomocí příkazového řádku, dotazovacích metod HTTP nebo pomocí webových služeb. Syntaxe jazyka je inspirována několika programovacími jazyky (Perl, C, Pascal a Java). PHP je nezávislý na platformě, rozdíly v různých operačních systémech se omezují na několik systémově závislých funkcí a skripty lze většinou mezi operačními systémy přenášet bez jakýchkoli úprav.\n\nPHP podporuje mnoho knihoven pro různé účely – např. zpracování textu, grafiky, práci se soubory, přístup k většině databázových systémů (mj. MySQL, ODBC, Oracle, PostgreSQL, MSSQL), podporu celé řady internetových protokolů (HTTP, SMTP, SNMP, FTP, IMAP, POP3, LDAP, …).\n\nPHP je nejrozšířenějším skriptovacím jazykem pro web, v současnosti (listopad 2014) s podílem 82 %. Oblíbeným se stal především díky jednoduchosti použití, bohaté zásobě funkcí. V kombinaci s operačním systémem Linux, databázovým systémem (obvykle MySQL nebo PostgreSQL) a webovým serverem Apache je často využíván k tvorbě webových aplikací. Pro tuto kombinaci se vžila zkratka LAMP – tedy spojení Linux, Apache, MySQL a PHP, Perl nebo Python.\n\nV PHP jsou napsány i velké internetové internetové projekty, včetně Wikipedie nebo Facebooku (Facebook používá PHP transformované do C++ pomocí aplikace HipHop for PHP a to především kvůli vyšší rychlosti).',	1,	'+1',	'2016-04-20 20:32:34',	'2016-04-20 18:31:31',	1,	NULL,	1),
(2,	'Automobil Škoda auto',	'Just Škoda',	1,	'+1',	'2016-04-20 20:32:34',	'2016-04-20 18:31:31',	1,	NULL,	1),
(3,	'Automobil Seat',	'Just Seat',	1,	'+1',	'2016-04-20 20:32:34',	'2016-04-20 18:31:31',	1,	NULL,	1);

TRUNCATE `document_slugs`;
INSERT INTO `document_slugs` (`ID`, `origin`, `slug`) VALUES
(1,	1,	'php-programovaci-jazyk'),
(2,	2,	'automobil-skoda-auto'),
(3,	3,	'automobil-seat');

TRUNCATE `forgotten_passwords`;

TRUNCATE `information_sources`;
INSERT INTO `information_sources` (`ID`, `place`, `year`, `author`) VALUES
(1,	'https://cs.wikipedia.org/wiki/PHP',	2016,	''),
(2,	'',	NULL,	''),
(3,	'',	NULL,	''),
(4,	'',	NULL,	''),
(5,	'',	NULL,	''),
(6,	'',	NULL,	'');

TRUNCATE `message_templates`;
INSERT INTO `message_templates` (`ID`, `message`, `designation`) VALUES
(1,	'<h1>Vítej na bulletpoint</h1>\r\n<p>Pro aktivaci účtu navštiv <a href=\"https://www.bulletpoint.cz/aktivace/aktivovat?code=%s\">tento odkaz</a> nebo do adresního řádku zkopíruj tento odkaz: https://www.bulletpoint.cz/aktivace/aktivovat?code=%s</p>\r\n\r\n<p>S přáním krásného dne bulletpoint</p>',	'activation'),
(2,	'<h1>Zapomenuté heslo</h1>\r\n<p>Pro obnovu zapomenutého hesla navštiv <a href=\"https://www.bulletpoint.cz/zapomenute-heslo/reset?reminder=%s\">tento odkaz</a> nebo do adresního řádku zkopíruj tento odkaz: https://www.bulletpoint.cz/zapomenute-heslo/reset?reminder=%s</p>\r\n\r\n<p>S přáním krásného dne bulletpoint</p>',	'forgotten-password');

TRUNCATE `punishments`;
INSERT INTO `punishments` (`ID`, `sinner_id`, `reason`, `expiration`, `author_id`, `forgiven`) VALUES
(1, 2, "Nevhodné chování", NOW() + INTERVAL 1 DAY, 1, 0);

TRUNCATE `users`;
INSERT INTO `users` (`ID`, `username`, `password`, `email`, `role`) VALUES
(1,	'facedown',	'c01d99ef43aed3f295446f747b8bdc16d9cce0e7116342a8da1214ab0c57f9dd81e1da2325a9cf90b9001c3d0980d295d61641f684d6ea20da2e6fe1139c2d40a60471ab0d0b6d4c43dea2b892126ac7',	'email@email.com',	'creator'),
(2,	'banned',	'022b235f6cd6931f1bbd12c7fbcc7971b2d7df11014274d484553dfdc63a259d5d946edfd24dcd2a3e4b10c1be735be068d798ae6e92b7d4c1fc6cd4b03f2b83e1d1a1a9cb6e9b9e4db99ecbcd91b900',	'test@test.test',	'member'),
(3,	'test2',	'022b235f6cd6931f1bbd12c7fbcc7971b2d7df11014274d484553dfdc63a259d5d946edfd24dcd2a3e4b10c1be735be068d798ae6e92b7d4c1fc6cd4b03f2b83e1d1a1a9cb6e9b9e4db99ecbcd91b900',	'test2@test.test',	'member');

TRUNCATE `verification_codes`;
INSERT INTO `verification_codes` (`ID`, `user_id`, `code`, `used`, `used_at`) VALUES
(1,	1,	'eff63c46355bf9bd1cd56fd72b30abf3b6c46c7f3fc22b6bef:8b87701f7b668dd1020f069ad99072a2a165dcbc',	1,	'2016-04-20 20:30:26'),
(2,	2,	'91730cdf7acd80ec74f1027ede147b34c99e15aaa71b048063:c10c76ce17e59c4d304ba37e62d41da353afdd69',	1,	'2016-04-20 21:30:26'),
(3,	3,	'81730cdf7acd80ec74f1027ede147b34c99e15aaa71b048063:c10c76ce17e59c4d304ba37e62d41da353afdd69',	0,	NULL);

-- 2016-04-21 21:18:22
