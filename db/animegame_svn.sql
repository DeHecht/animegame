-- MySQL dump 10.15  Distrib 10.0.31-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 10.8.0.1    Database: ag_server_2
-- ------------------------------------------------------
-- Server version	5.7.20-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ag_chat`
--

DROP TABLE IF EXISTS `ag_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ag_chat` (
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `nachricht` text COLLATE utf8_unicode_ci,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `anon_chatter`
--

DROP TABLE IF EXISTS `anon_chatter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `anon_chatter` (
  `ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `anon_id` int(11) NOT NULL,
  `online` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `muted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`anon_id`),
  KEY `ips` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `arena`
--

DROP TABLE IF EXISTS `arena`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `arena` (
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'no name arena',
  `luxus` int(10) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `besitzer` int(11) NOT NULL DEFAULT '0',
  `exp` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0,2000',
  `level` int(3) NOT NULL DEFAULT '1',
  `skill` int(3) NOT NULL DEFAULT '0',
  `loge` int(11) NOT NULL DEFAULT '0',
  `sitz` int(11) NOT NULL DEFAULT '5',
  `steh` int(11) NOT NULL DEFAULT '10',
  `hausmeister` int(11) NOT NULL DEFAULT '0',
  `zustand` float NOT NULL DEFAULT '1',
  `mietlasten` int(11) NOT NULL DEFAULT '0',
  `einnahmen` int(11) NOT NULL DEFAULT '0',
  `steuerlasten` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `besitzer` (`besitzer`),
  CONSTRAINT `fk_arena_besitzer` FOREIGN KEY (`besitzer`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attack_conditions`
--

DROP TABLE IF EXISTS `attack_conditions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attack_conditions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `formula` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attacken`
--

DROP TABLE IF EXISTS `attacken`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attacken` (
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `starke` int(10) NOT NULL DEFAULT '0',
  `verteidigung` int(10) NOT NULL DEFAULT '0',
  `speed` int(10) NOT NULL DEFAULT '0',
  `hp` int(10) NOT NULL DEFAULT '0',
  `mp` int(10) NOT NULL DEFAULT '0',
  `rassen` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `level` int(3) NOT NULL DEFAULT '0',
  `geld` int(10) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `info` text COLLATE utf8_unicode_ci NOT NULL,
  `req_atk` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0,0,0,0,0',
  `req_lvl` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0,0,0,0,0',
  `Frucht` int(11) DEFAULT NULL,
  `runden` int(10) NOT NULL DEFAULT '0',
  `maxlvl` int(20) NOT NULL DEFAULT '20',
  `options` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `level` (`level`),
  KEY `fk_attacken_Frucht` (`Frucht`),
  CONSTRAINT `fk_attacken_Frucht` FOREIGN KEY (`Frucht`) REFERENCES `wochen_markt` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attackenset`
--

DROP TABLE IF EXISTS `attackenset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attackenset` (
  `char_id` int(11) NOT NULL,
  `attack_id` int(11) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '1',
  `round` int(2) NOT NULL,
  PRIMARY KEY (`char_id`,`type`,`round`),
  KEY `attackenset_attack_id` (`attack_id`),
  KEY `attackenset_char_id` (`char_id`),
  CONSTRAINT `attackenset_char_id` FOREIGN KEY (`char_id`) REFERENCES `chars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attackentyp`
--

DROP TABLE IF EXISTS `attackentyp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attackentyp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `anzahl` int(11) NOT NULL DEFAULT '1',
  `farbe` varchar(32) COLLATE utf8_unicode_ci DEFAULT 'white',
  `kategorie` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auktion`
--

DROP TABLE IF EXISTS `auktion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auktion` (
  `auktionsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT '1',
  `anbieter` int(11) DEFAULT NULL,
  `bieter` int(11) DEFAULT NULL,
  `itemid` int(10) unsigned NOT NULL,
  `itemname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tablename` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `anzahl` int(10) unsigned NOT NULL,
  `startgebot` decimal(10,0) unsigned NOT NULL,
  `aktuellesgebot` decimal(10,0) unsigned DEFAULT NULL,
  `deadline` timestamp NULL DEFAULT NULL,
  `startdate` timestamp NULL DEFAULT NULL,
  `cheatingverdacht` int(10) unsigned NOT NULL,
  PRIMARY KEY (`auktionsid`),
  KEY `auktion_anbieter` (`anbieter`),
  KEY `auktion_bieter` (`bieter`),
  CONSTRAINT `fk_auktion_anbieter` FOREIGN KEY (`anbieter`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_auktion_bieter` FOREIGN KEY (`bieter`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auktion_transaktionen`
--

DROP TABLE IF EXISTS `auktion_transaktionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auktion_transaktionen` (
  `transaktionsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT '1',
  `anbieter` int(11) DEFAULT NULL,
  `bieter` int(11) DEFAULT NULL,
  `itemid` int(10) unsigned NOT NULL,
  `tablename` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `anzahl` int(10) unsigned NOT NULL,
  `betrag` decimal(10,0) unsigned NOT NULL,
  `deadline` timestamp NULL DEFAULT NULL,
  `cheatingverdacht` int(10) unsigned NOT NULL,
  PRIMARY KEY (`transaktionsid`),
  KEY `fk_auktion_transaktionen_anbieter` (`anbieter`),
  KEY `fk_auktion_transaktionen_bieter` (`bieter`),
  CONSTRAINT `fk_auktion_transaktionen_anbieter` FOREIGN KEY (`anbieter`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_auktion_transaktionen_bieter` FOREIGN KEY (`bieter`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `blog`
--

DROP TABLE IF EXISTS `blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `blog` longtext COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `tags` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bot_image_failures`
--

DROP TABLE IF EXISTS `bot_image_failures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bot_image_failures` (
  `userid` int(11) NOT NULL,
  `inserted` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `valid` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `used` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`,`used`),
  CONSTRAINT `new_bot_image_failures_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bot_images`
--

DROP TABLE IF EXISTS `bot_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bot_images` (
  `id` char(32) COLLATE utf8_unicode_ci NOT NULL,
  `phrase` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `compressed_phrase` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `userid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_bot_images` (`userid`),
  CONSTRAINT `fk_bot_images` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `char_abholcodes`
--

DROP TABLE IF EXISTS `char_abholcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `char_abholcodes` (
  `charid` int(10) NOT NULL,
  `code` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`charid`),
  CONSTRAINT `fk_char_abholcodes_charid` FOREIGN KEY (`charid`) REFERENCES `chars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chars`
--

DROP TABLE IF EXISTS `chars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chars` (
  `level` int(3) NOT NULL DEFAULT '1',
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `rasse` int(11) NOT NULL DEFAULT '0',
  `bild` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `bild_edit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `besitzer` int(11) NOT NULL DEFAULT '0',
  `hp` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '100,100',
  `exp` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0,400',
  `starke` double(7,1) NOT NULL DEFAULT '10.0',
  `speed` double(7,1) NOT NULL DEFAULT '10.0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verteidigung` double(6,1) NOT NULL DEFAULT '10.0',
  `mp` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '10,10',
  `attacken` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0,0,0,0,0,0,0,0',
  `auswahl1` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0,0,0,0,0,0,0,0',
  `auswahl2` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0,0,0,0,0,0,0,0',
  `auswahl3` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0,0,0,0,0,0,0,0',
  `attacken2` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0,0,0,0,0,0,0,0',
  `siege` int(5) NOT NULL DEFAULT '0',
  `niederlagen` int(5) NOT NULL DEFAULT '0',
  `item` int(5) NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Frei',
  `ausdauer` double(5,1) NOT NULL DEFAULT '10.0',
  `glueck` double(5,1) NOT NULL DEFAULT '10.0',
  `rustung` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'nein',
  `aufgeben` int(3) NOT NULL DEFAULT '0',
  `liga_punkte` int(55) NOT NULL DEFAULT '0',
  `fusion` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'nein',
  `fusion_rasse` int(11) NOT NULL DEFAULT '0',
  `training` text COLLATE utf8_unicode_ci NOT NULL,
  `lernpunkte` int(5) NOT NULL DEFAULT '10',
  `frucht` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `kampf_item` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0,0,0,0,0',
  `ort` int(11) NOT NULL DEFAULT '13',
  `changes` int(2) NOT NULL DEFAULT '0',
  `training_points` int(20) NOT NULL DEFAULT '30',
  `liga_siege` int(3) NOT NULL DEFAULT '0',
  `liga_niederlagen` int(3) NOT NULL DEFAULT '0',
  `clan_train` int(30) NOT NULL DEFAULT '0',
  `dead_list` text COLLATE utf8_unicode_ci NOT NULL,
  `preis` int(50) NOT NULL DEFAULT '1000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `fk_chars_besitzer` (`besitzer`),
  KEY `fk_chars_frucht` (`frucht`),
  CONSTRAINT `fk_chars_besitzer` FOREIGN KEY (`besitzer`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_chars_frucht` FOREIGN KEY (`frucht`) REFERENCES `wochen_markt` (`item`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cheater`
--

DROP TABLE IF EXISTS `cheater`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cheater` (
  `userid` int(11) NOT NULL,
  `delikt` text COLLATE utf8_unicode_ci NOT NULL,
  `zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `userid` (`userid`,`zeit`),
  CONSTRAINT `fk_cheater_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan`
--

DROP TABLE IF EXISTS `clan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan` (
  `clanname` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `clanz_pre` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `clanz_suff` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `geld` int(15) NOT NULL DEFAULT '0',
  `info` text COLLATE utf8_unicode_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `leader` int(11) DEFAULT '0',
  `siege` int(25) NOT NULL DEFAULT '0',
  `niederlagen` int(25) NOT NULL DEFAULT '0',
  `co_leader` int(11) DEFAULT NULL,
  `homepage` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Keine',
  `banner` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `level` int(2) NOT NULL DEFAULT '1',
  `exp` int(20) NOT NULL DEFAULT '0',
  `elo` int(11) NOT NULL DEFAULT '1000',
  `inactive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `background` varchar(80) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `leader` (`leader`),
  KEY `co_leader` (`co_leader`),
  CONSTRAINT `fk_clan_co_leader` FOREIGN KEY (`co_leader`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_clan_leader` FOREIGN KEY (`leader`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_challenge_clans`
--

DROP TABLE IF EXISTS `clan_challenge_clans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_challenge_clans` (
  `clan_challenge_id` int(11) NOT NULL,
  `id` int(11) NOT NULL,
  `clan_id` int(11) NOT NULL,
  `elo` int(11) NOT NULL,
  `clan_coordinator` int(11) DEFAULT NULL,
  `elo_change` int(11) DEFAULT NULL,
  `exp_change` int(11) DEFAULT NULL,
  `points` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`clan_challenge_id`,`id`) USING BTREE,
  UNIQUE KEY `clan_challenge_clans` (`clan_challenge_id`,`clan_id`),
  KEY `fk_clan_ch_coordinator` (`clan_coordinator`),
  CONSTRAINT `clan_ch_clans_ch_id` FOREIGN KEY (`clan_challenge_id`) REFERENCES `clan_challenges` (`clan_challenge_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_clan_ch_coordinator` FOREIGN KEY (`clan_coordinator`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_challenge_participants`
--

DROP TABLE IF EXISTS `clan_challenge_participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_challenge_participants` (
  `clan_challenge_id` int(11) NOT NULL,
  `clan_id` int(11) NOT NULL,
  `char_id` int(11) NOT NULL,
  `slot` int(11) NOT NULL,
  `forced` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`clan_challenge_id`,`clan_id`,`slot`),
  UNIQUE KEY `new_index` (`clan_challenge_id`,`clan_id`,`slot`),
  KEY `clan_challenge_participants_clan_id` (`clan_id`),
  KEY `clan_challenge_participants_char_id` (`char_id`),
  CONSTRAINT `clan_challenge_participants_char_id` FOREIGN KEY (`char_id`) REFERENCES `chars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `clan_challenge_participants_clan_id` FOREIGN KEY (`clan_challenge_id`, `clan_id`) REFERENCES `clan_challenge_clans` (`clan_challenge_id`, `clan_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_challenge_requests`
--

DROP TABLE IF EXISTS `clan_challenge_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_challenge_requests` (
  `clan_requester` int(11) NOT NULL,
  `clan_challenged` int(11) NOT NULL,
  `challenged_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `clan_challenge_creator` int(11) DEFAULT NULL,
  `type` int(11) NOT NULL,
  `anzahl_chars` int(11) NOT NULL DEFAULT '5',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`clan_requester`,`clan_challenged`,`challenged_time`),
  KEY `fk_clan_challenge_requests_challenged` (`clan_challenged`),
  KEY `fk_clan_challenge_requests_creator` (`clan_challenge_creator`),
  CONSTRAINT `fk_clan_challenge_requests_challenged` FOREIGN KEY (`clan_challenged`) REFERENCES `clan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_clan_challenge_requests_creator` FOREIGN KEY (`clan_challenge_creator`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `fk_clan_challenge_requests_requester` FOREIGN KEY (`clan_requester`) REFERENCES `clan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_challenge_whitelist`
--

DROP TABLE IF EXISTS `clan_challenge_whitelist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_challenge_whitelist` (
  `clan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`clan_id`,`user_id`),
  KEY `fk_clan_ch_whitelist_usr` (`user_id`),
  CONSTRAINT `fk_clan_ch_whitelist_clan` FOREIGN KEY (`clan_id`) REFERENCES `clan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_clan_ch_whitelist_usr` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_challenges`
--

DROP TABLE IF EXISTS `clan_challenges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_challenges` (
  `clan_challenge_id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `enddate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `calculated` tinyint(1) NOT NULL,
  `type` int(11) NOT NULL,
  `anzahl_chars` int(11) NOT NULL,
  `event_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`clan_challenge_id`),
  UNIQUE KEY `event_id` (`event_id`),
  CONSTRAINT `clan_challenges_event_id` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_chatroom_clans`
--

DROP TABLE IF EXISTS `clan_chatroom_clans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_chatroom_clans` (
  `clan_chat_id` int(11) NOT NULL,
  `clan_id` int(11) NOT NULL,
  KEY `clan_chatroom_clans_id` (`clan_chat_id`),
  KEY `clan_chatroom_clans_clan` (`clan_id`),
  CONSTRAINT `clan_chatroom_clans_clan` FOREIGN KEY (`clan_id`) REFERENCES `clan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `clan_chatroom_clans_id` FOREIGN KEY (`clan_chat_id`) REFERENCES `clan_chatrooms` (`clan_chat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_chatroom_messages`
--

DROP TABLE IF EXISTS `clan_chatroom_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_chatroom_messages` (
  `clan_chat_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `msg_id` int(11) NOT NULL,
  `zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`clan_chat_id`,`msg_id`),
  CONSTRAINT `clan_chatroom_messages_id` FOREIGN KEY (`clan_chat_id`) REFERENCES `clan_chatrooms` (`clan_chat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_chatrooms`
--

DROP TABLE IF EXISTS `clan_chatrooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_chatrooms` (
  `clan_chat_id` int(11) NOT NULL AUTO_INCREMENT,
  `foreign_id` varchar(255) NOT NULL,
  PRIMARY KEY (`clan_chat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_item`
--

DROP TABLE IF EXISTS `clan_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_item` (
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `preis` bigint(20) NOT NULL DEFAULT '0',
  `anzahl` int(20) NOT NULL DEFAULT '0',
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `info` text COLLATE utf8_unicode_ci NOT NULL,
  `ausdauer` int(10) NOT NULL DEFAULT '0',
  `staerke` int(20) NOT NULL DEFAULT '0',
  `verteidigung` int(20) NOT NULL DEFAULT '0',
  `geschwindigkeit` int(20) NOT NULL DEFAULT '0',
  `tp` int(5) NOT NULL DEFAULT '0',
  `glueck` int(20) NOT NULL DEFAULT '0',
  `miete` int(20) NOT NULL,
  `nutzung` int(20) NOT NULL,
  `req_level` int(20) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `anzahl` (`anzahl`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_locked`
--

DROP TABLE IF EXISTS `clan_locked`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_locked` (
  `clanid` int(11) NOT NULL,
  `locked_until` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`clanid`),
  CONSTRAINT `clan_locked_clanid` FOREIGN KEY (`clanid`) REFERENCES `clan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='this table holds the data for clans beeing locked out of cla';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_ticker`
--

DROP TABLE IF EXISTS `clan_ticker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_ticker` (
  `clanid` int(11) NOT NULL,
  `msgid` int(11) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`date`,`clanid`,`msgid`),
  KEY `fk_clan_ticker_clanid` (`clanid`),
  CONSTRAINT `fk_clan_ticker_clanid` FOREIGN KEY (`clanid`) REFERENCES `clan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clan_ware`
--

DROP TABLE IF EXISTS `clan_ware`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clan_ware` (
  `item_id` int(20) NOT NULL DEFAULT '0',
  `clan` int(20) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  UNIQUE KEY `clan_item` (`clan`,`item_id`),
  KEY `id` (`id`),
  KEY `fk_clan_ware_item_id` (`item_id`),
  CONSTRAINT `fk_clan_ware_clan` FOREIGN KEY (`clan`) REFERENCES `clan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_clan_ware_item_id` FOREIGN KEY (`item_id`) REFERENCES `clan_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blog_id` int(11) DEFAULT NULL,
  `user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comment` longtext COLLATE utf8_unicode_ci NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_9474526CDAE07E97` (`blog_id`),
  CONSTRAINT `FK_9474526CDAE07E97` FOREIGN KEY (`blog_id`) REFERENCES `blog` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `defines`
--

DROP TABLE IF EXISTS `defines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `defines` (
  `key` varchar(30) NOT NULL,
  `value` varchar(20) NOT NULL,
  `editable` tinyint(1) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_char_metadata`
--

DROP TABLE IF EXISTS `event_char_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_char_metadata` (
  `event_id` int(10) unsigned NOT NULL,
  `event_char_id` int(10) unsigned NOT NULL,
  `key` varchar(20) NOT NULL,
  `value` varchar(20) NOT NULL,
  PRIMARY KEY (`event_id`,`event_char_id`,`key`),
  CONSTRAINT `fk_event_char_metadata_1` FOREIGN KEY (`event_id`, `event_char_id`) REFERENCES `event_chars` (`event_id`, `event_char_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_chars`
--

DROP TABLE IF EXISTS `event_chars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_chars` (
  `event_char_id` int(11) unsigned NOT NULL,
  `char_id` int(11) DEFAULT NULL,
  `char_name` varchar(30) NOT NULL,
  `char_bild` varchar(100) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(30) NOT NULL,
  `event_id` int(11) unsigned NOT NULL,
  `block_begin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `block_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hp` int(11) NOT NULL,
  `mp` int(11) NOT NULL,
  `strength` int(11) NOT NULL,
  `speed` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `luck` int(11) NOT NULL,
  `stamina` int(11) NOT NULL,
  `abgeholt` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`event_id`,`event_char_id`),
  KEY `char_bild` (`char_bild`),
  CONSTRAINT `event_chars_event_id` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_fight_metadata`
--

DROP TABLE IF EXISTS `event_fight_metadata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_fight_metadata` (
  `event_id` int(10) unsigned NOT NULL,
  `event_fight_id` int(10) unsigned NOT NULL,
  `key` varchar(20) NOT NULL,
  `value` varchar(20) NOT NULL,
  PRIMARY KEY (`event_id`,`event_fight_id`,`key`),
  CONSTRAINT `event_fight_metadata_fightid` FOREIGN KEY (`event_id`, `event_fight_id`) REFERENCES `event_fights` (`event_id`, `event_fight_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_fight_rounds`
--

DROP TABLE IF EXISTS `event_fight_rounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_fight_rounds` (
  `event_id` int(10) unsigned NOT NULL,
  `event_fight_id` int(10) unsigned NOT NULL,
  `event_char_id` int(10) unsigned NOT NULL,
  `round` int(10) unsigned NOT NULL,
  `hp` int(11) NOT NULL,
  `mp` int(11) NOT NULL,
  `strength` int(11) NOT NULL,
  `speed` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `luck` int(11) NOT NULL,
  `stamina` int(11) NOT NULL,
  `attack` int(11) DEFAULT NULL,
  `damage` varchar(30) DEFAULT NULL,
  `visible` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`event_id`,`event_fight_id`,`event_char_id`,`round`),
  KEY `event_fight_rounds_chars` (`event_id`,`event_char_id`),
  CONSTRAINT `event_fight_rounds_chars` FOREIGN KEY (`event_id`, `event_char_id`) REFERENCES `event_chars` (`event_id`, `event_char_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `event_fight_rounds_event_figths` FOREIGN KEY (`event_id`, `event_fight_id`) REFERENCES `event_fights` (`event_id`, `event_fight_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `event_fights`
--

DROP TABLE IF EXISTS `event_fights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_fights` (
  `event_id` int(10) unsigned NOT NULL,
  `event_fight_id` int(10) unsigned NOT NULL,
  `visible` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `starting` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `host` int(10) unsigned NOT NULL,
  `winner` int(10) unsigned NOT NULL,
  PRIMARY KEY (`event_id`,`event_fight_id`),
  KEY `event_fights_host` (`event_id`,`host`),
  KEY `event_fights_winner` (`event_id`,`winner`),
  CONSTRAINT `event_fights_event_id` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `event_fights_host` FOREIGN KEY (`event_id`, `host`) REFERENCES `event_chars` (`event_id`, `event_char_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `event_fights_winner` FOREIGN KEY (`event_id`, `winner`) REFERENCES `event_chars` (`event_id`, `event_char_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `event_id` int(11) unsigned NOT NULL,
  `event_type` int(11) unsigned NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `faq_entries`
--

DROP TABLE IF EXISTS `faq_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `faq_entries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL,
  `header` text COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ff11_komments`
--

DROP TABLE IF EXISTS `ff11_komments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ff11_komments` (
  `user` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `news_id` int(10) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datum` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `zeit` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  KEY `id` (`id`),
  KEY `news_id` (`news_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ff11_news`
--

DROP TABLE IF EXISTS `ff11_news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ff11_news` (
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `betreff` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zeit` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `datum` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gm_actions`
--

DROP TABLE IF EXISTS `gm_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gm_actions` (
  `userid` int(10) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`userid`,`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `highscore`
--

DROP TABLE IF EXISTS `highscore`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `highscore` (
  `runde` int(11) NOT NULL,
  `art` varchar(35) COLLATE utf8_unicode_ci NOT NULL,
  `datum` date NOT NULL,
  `charid` int(11) DEFAULT NULL,
  `charname` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `tournament_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`art`,`datum`),
  KEY `fk_highscore_charid` (`charid`),
  KEY `fk_highscore_userid` (`userid`),
  KEY `fk_highscore_1_idx` (`tournament_id`),
  CONSTRAINT `fk_highscore_1` FOREIGN KEY (`tournament_id`) REFERENCES `tournament` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_highscore_charid` FOREIGN KEY (`charid`) REFERENCES `chars` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_highscore_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ignolist`
--

DROP TABLE IF EXISTS `ignolist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ignolist` (
  `user` int(11) NOT NULL DEFAULT '0',
  `besitzer` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  UNIQUE KEY `user_besitzer` (`user`,`besitzer`),
  KEY `id` (`id`),
  KEY `fk_ignolist_besitzer` (`besitzer`),
  CONSTRAINT `fk_ignolist_besitzer` FOREIGN KEY (`besitzer`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ignolist_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inverse_auktion_gebote`
--

DROP TABLE IF EXISTS `inverse_auktion_gebote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inverse_auktion_gebote` (
  `auktionsid` int(10) unsigned NOT NULL,
  `bieter` int(11) NOT NULL,
  `range_from` int(11) NOT NULL,
  `range_to` int(11) NOT NULL,
  KEY `fk_inverse_auktion_gebote_2_idx` (`bieter`),
  KEY `inverse_auktion_gebote_auktionsid_idx` (`auktionsid`),
  CONSTRAINT `fk_inverse_auktion_gebote_1` FOREIGN KEY (`auktionsid`) REFERENCES `auktion` (`auktionsid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_inverse_auktion_gebote_2` FOREIGN KEY (`bieter`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item` (
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `hp` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `mp` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `starke` int(100) NOT NULL DEFAULT '0',
  `verteidigung` int(100) NOT NULL DEFAULT '0',
  `speed` int(100) NOT NULL DEFAULT '0',
  `s_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `preis` int(20) NOT NULL DEFAULT '0',
  `anzahl` int(20) NOT NULL DEFAULT '0',
  `rassen_type` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `info` text COLLATE utf8_unicode_ci NOT NULL,
  `level` int(20) NOT NULL DEFAULT '0',
  `tausch_lvl` int(2) unsigned NOT NULL DEFAULT '1',
  `tausch_anzahl` int(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `anzahl` (`anzahl`),
  KEY `fk_item_1_idx` (`rassen_type`),
  CONSTRAINT `fk_item_1` FOREIGN KEY (`rassen_type`) REFERENCES `rassen_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kampf`
--

DROP TABLE IF EXISTS `kampf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kampf` (
  `hp1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `hp2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mp1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mp2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `attacke1` text COLLATE utf8_unicode_ci NOT NULL,
  `attacke2` text COLLATE utf8_unicode_ci NOT NULL,
  `schaden1` text COLLATE utf8_unicode_ci NOT NULL,
  `schaden2` text COLLATE utf8_unicode_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `char1` int(20) NOT NULL DEFAULT '0',
  `char2` int(20) NOT NULL DEFAULT '0',
  `starke1` text COLLATE utf8_unicode_ci NOT NULL,
  `ver1` text COLLATE utf8_unicode_ci NOT NULL,
  `speed1` text COLLATE utf8_unicode_ci NOT NULL,
  `starke2` text COLLATE utf8_unicode_ci NOT NULL,
  `ver2` text COLLATE utf8_unicode_ci NOT NULL,
  `speed2` text COLLATE utf8_unicode_ci NOT NULL,
  `ausdauer1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ausdauer2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `glueck1` text COLLATE utf8_unicode_ci NOT NULL,
  `glueck2` text COLLATE utf8_unicode_ci NOT NULL,
  `exp1` int(20) NOT NULL DEFAULT '0',
  `exp2` int(20) NOT NULL DEFAULT '0',
  `geld1` int(10) NOT NULL DEFAULT '0',
  `geld2` int(10) NOT NULL DEFAULT '0',
  `win` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lose` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `arena_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `db_satz` int(20) NOT NULL DEFAULT '0',
  `dauer` int(50) NOT NULL DEFAULT '0',
  `art` varchar(50) COLLATE utf8_unicode_ci DEFAULT '...',
  `art2` varchar(50) COLLATE utf8_unicode_ci DEFAULT '...',
  `preis` int(20) NOT NULL DEFAULT '0',
  `arena_exp` int(20) NOT NULL DEFAULT '0',
  `loge` int(11) NOT NULL,
  `sitz` int(11) NOT NULL,
  `steh` int(11) NOT NULL,
  `arena_geld` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `db_satz_2` (`db_satz`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kampf_list`
--

DROP TABLE IF EXISTS `kampf_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kampf_list` (
  `kampfname` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `charakter` int(11) NOT NULL DEFAULT '0',
  `rasse` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ALL',
  `passwort` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `besitzer` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `zeit` int(50) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zeit_rec` int(50) NOT NULL DEFAULT '0',
  `lvlmax` int(4) NOT NULL DEFAULT '0',
  `ip` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `lvlmin` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `charakter` (`charakter`),
  KEY `ip` (`ip`),
  CONSTRAINT `fk_kampf_list_charakter` FOREIGN KEY (`charakter`) REFERENCES `chars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lernen`
--

DROP TABLE IF EXISTS `lernen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lernen` (
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `at_id` int(11) NOT NULL DEFAULT '0',
  `besitzer` int(11) NOT NULL DEFAULT '0',
  `dauer` int(30) NOT NULL DEFAULT '0',
  `aktiv` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `benutzt` int(6) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Gna` (`besitzer`,`at_id`),
  KEY `fk_lernen_atid` (`at_id`),
  KEY `fk_lernen_name` (`name`),
  CONSTRAINT `fk_lernen_atid` FOREIGN KEY (`at_id`) REFERENCES `attacken` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_lernen_besitzer` FOREIGN KEY (`besitzer`) REFERENCES `chars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_lernen_name` FOREIGN KEY (`name`) REFERENCES `attacken` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `liga_kampf`
--

DROP TABLE IF EXISTS `liga_kampf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `liga_kampf` (
  `hp1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `hp2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mp1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mp2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `attacke1` text COLLATE utf8_unicode_ci NOT NULL,
  `attacke2` text COLLATE utf8_unicode_ci NOT NULL,
  `schaden1` text COLLATE utf8_unicode_ci NOT NULL,
  `schaden2` text COLLATE utf8_unicode_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `char1` int(20) NOT NULL DEFAULT '0',
  `char2` int(20) NOT NULL DEFAULT '0',
  `starke1` text COLLATE utf8_unicode_ci NOT NULL,
  `ver1` text COLLATE utf8_unicode_ci NOT NULL,
  `speed1` text COLLATE utf8_unicode_ci NOT NULL,
  `starke2` text COLLATE utf8_unicode_ci NOT NULL,
  `ver2` text COLLATE utf8_unicode_ci NOT NULL,
  `speed2` text COLLATE utf8_unicode_ci NOT NULL,
  `ausdauer1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ausdauer2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `glueck1` text COLLATE utf8_unicode_ci NOT NULL,
  `glueck2` text COLLATE utf8_unicode_ci NOT NULL,
  `exp1` int(20) NOT NULL DEFAULT '0',
  `exp2` int(20) NOT NULL DEFAULT '0',
  `geld1` int(10) NOT NULL DEFAULT '0',
  `geld2` int(10) NOT NULL DEFAULT '0',
  `arena_geld` int(20) NOT NULL DEFAULT '0',
  `besucher` int(20) NOT NULL DEFAULT '0',
  `win` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lose` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `arena_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `db_satz` int(20) NOT NULL DEFAULT '0',
  `dauer` int(50) NOT NULL DEFAULT '0',
  `art` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `art2` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `preis` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `char1` (`char1`),
  KEY `char2` (`char2`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `multiuser`
--

DROP TABLE IF EXISTS `multiuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `multiuser` (
  `user1` int(11) NOT NULL,
  `user2` int(11) NOT NULL,
  `occured` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`user1`,`user2`,`occured`),
  KEY `multiuser_user2` (`user2`),
  CONSTRAINT `multiuser_user1` FOREIGN KEY (`user1`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `multiuser_user2` FOREIGN KEY (`user2`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Eine Tabelle die Logbuch ueber multiuser haelt!';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nachricht`
--

DROP TABLE IF EXISTS `nachricht`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nachricht` (
  `betreff` varchar(35) COLLATE utf8_unicode_ci DEFAULT NULL,
  `von` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `text` text COLLATE utf8_unicode_ci,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `besitzer` int(11) NOT NULL DEFAULT '0',
  `ag` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'neu',
  `aktiv` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `besitzer` (`besitzer`),
  CONSTRAINT `fk_nachricht_besitzer` FOREIGN KEY (`besitzer`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `npc_item`
--

DROP TABLE IF EXISTS `npc_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `npc_item` (
  `charakter` int(11) NOT NULL DEFAULT '0',
  `item` int(20) NOT NULL DEFAULT '0',
  `mal` int(3) NOT NULL DEFAULT '0',
  `datensatz` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `feld` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `wieviel` int(3) NOT NULL DEFAULT '1',
  `preis` int(50) NOT NULL DEFAULT '0',
  `req_charakter` int(11) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `fk_npc_item_charakter` (`charakter`),
  KEY `fk_npc_item_req_charakter` (`req_charakter`),
  CONSTRAINT `fk_npc_item_charakter` FOREIGN KEY (`charakter`) REFERENCES `chars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_npc_item_req_charakter` FOREIGN KEY (`req_charakter`) REFERENCES `chars` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `npc_ware`
--

DROP TABLE IF EXISTS `npc_ware`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `npc_ware` (
  `user` int(11) NOT NULL DEFAULT '0',
  `charakter` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `fk_npc_ware_user` (`user`),
  KEY `fk_npc_ware_charakter` (`charakter`),
  CONSTRAINT `fk_npc_ware_charakter` FOREIGN KEY (`charakter`) REFERENCES `chars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_npc_ware_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `online`
--

DROP TABLE IF EXISTS `online`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `online` (
  `anzahl` int(20) NOT NULL DEFAULT '0',
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `turnier` text COLLATE utf8_unicode_ci NOT NULL,
  `liga` text COLLATE utf8_unicode_ci NOT NULL,
  `turnier_klein` text COLLATE utf8_unicode_ci NOT NULL,
  `turnier_gross` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online`
--

LOCK TABLES `online` WRITE;
/*!40000 ALTER TABLE `online` DISABLE KEYS */;
INSERT INTO `online` (`id`) VALUES ('1');
/*!40000 ALTER TABLE `online` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poll`
--

DROP TABLE IF EXISTS `poll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poll` (
  `pollid` int(11) NOT NULL AUTO_INCREMENT,
  `ersteller` int(11) NOT NULL,
  `thema` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `open` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `close` timestamp NULL DEFAULT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`pollid`),
  UNIQUE KEY `thema` (`thema`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `poll_options`
--

DROP TABLE IF EXISTS `poll_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poll_options` (
  `pollid` int(11) NOT NULL,
  `polloptionid` int(11) NOT NULL,
  `text` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`pollid`,`polloptionid`),
  CONSTRAINT `fk_poll_options_pollid` FOREIGN KEY (`pollid`) REFERENCES `poll` (`pollid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `poll_votes`
--

DROP TABLE IF EXISTS `poll_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poll_votes` (
  `pollid` int(11) NOT NULL AUTO_INCREMENT,
  `polloptionid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `comment` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`pollid`,`userid`),
  KEY `fk_poll_votes_pollid` (`pollid`,`polloptionid`),
  CONSTRAINT `fk_poll_votes_pollid` FOREIGN KEY (`pollid`, `polloptionid`) REFERENCES `poll_options` (`pollid`, `polloptionid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quest_fights`
--

DROP TABLE IF EXISTS `quest_fights`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quest_fights` (
  `charid` int(11) NOT NULL,
  `fightnr` int(11) NOT NULL,
  `monsterid` int(11) NOT NULL,
  `exp` int(11) DEFAULT '0',
  `geld` int(11) DEFAULT '0',
  `sieger` int(11) DEFAULT '0',
  PRIMARY KEY (`charid`,`fightnr`),
  CONSTRAINT `fk_quest_fights` FOREIGN KEY (`charid`) REFERENCES `chars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='in dieser Tabelle werden die Fights eingetragen';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quest_item_stats`
--

DROP TABLE IF EXISTS `quest_item_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quest_item_stats` (
  `id` int(11) NOT NULL,
  `monster` int(11) NOT NULL,
  `anzahl` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`,`monster`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quest_monster`
--

DROP TABLE IF EXISTS `quest_monster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quest_monster` (
  `name` varchar(75) COLLATE utf8_unicode_ci NOT NULL,
  `starke` int(5) NOT NULL DEFAULT '0',
  `speed` int(5) NOT NULL DEFAULT '0',
  `verteidigung` int(5) NOT NULL DEFAULT '0',
  `ausdauer` int(5) NOT NULL DEFAULT '0',
  `hp` int(10) NOT NULL DEFAULT '0',
  `mp` int(5) NOT NULL DEFAULT '0',
  `exp` int(10) NOT NULL DEFAULT '0',
  `level` int(3) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `glueck` int(11) NOT NULL DEFAULT '5',
  `anzahl` int(11) NOT NULL DEFAULT '1',
  `bild` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quest_monster_orte`
--

DROP TABLE IF EXISTS `quest_monster_orte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quest_monster_orte` (
  `monsterid` int(11) NOT NULL,
  `ortid` int(11) NOT NULL,
  PRIMARY KEY (`monsterid`,`ortid`),
  KEY `quest_monster_orte_ortid` (`ortid`),
  CONSTRAINT `quest_monster_orte_monsterid` FOREIGN KEY (`monsterid`) REFERENCES `quest_monster` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `quest_monster_orte_ortid` FOREIGN KEY (`ortid`) REFERENCES `quest_ort` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quest_ort`
--

DROP TABLE IF EXISTS `quest_ort`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quest_ort` (
  `stadt` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `reisende` int(20) NOT NULL DEFAULT '0',
  `kosten` int(20) NOT NULL DEFAULT '0',
  `info` text COLLATE utf8_unicode_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `x_coord` int(11) NOT NULL DEFAULT '0',
  `y_coord` int(11) NOT NULL DEFAULT '0',
  `bild` varchar(40) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'red.png',
  `name_bild` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name_x` int(11) DEFAULT NULL,
  `name_y` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quest_rounds`
--

DROP TABLE IF EXISTS `quest_rounds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quest_rounds` (
  `charid` int(11) NOT NULL,
  `fightnr` int(11) NOT NULL,
  `roundnr` int(11) NOT NULL,
  `c_attack` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `c_dmg` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `m_attack` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `m_dmg` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `c_st` int(11) NOT NULL,
  `c_sp` int(11) NOT NULL,
  `c_ver` int(11) NOT NULL,
  `c_hp` int(11) NOT NULL,
  `c_mp` int(11) NOT NULL,
  `c_aus` int(11) NOT NULL,
  `c_gl` int(11) NOT NULL,
  `m_st` int(11) NOT NULL,
  `m_sp` int(11) NOT NULL,
  `m_ver` int(11) NOT NULL,
  `m_hp` int(11) NOT NULL,
  `m_mp` int(11) NOT NULL,
  `m_aus` int(11) NOT NULL,
  `m_gl` int(11) NOT NULL,
  PRIMARY KEY (`charid`,`fightnr`,`roundnr`),
  CONSTRAINT `fk_quest_rounds_fight` FOREIGN KEY (`charid`, `fightnr`) REFERENCES `quest_fights` (`charid`, `fightnr`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Hier sind die einzelnen Rounden der Fights eingetragen';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quest_stats`
--

DROP TABLE IF EXISTS `quest_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quest_stats` (
  `monster` int(11) NOT NULL,
  `zeit` int(11) NOT NULL,
  `anzahl` int(11) NOT NULL DEFAULT '1',
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`monster`,`zeit`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quests`
--

DROP TABLE IF EXISTS `quests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quests` (
  `charid` int(11) NOT NULL,
  `dauer` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cancel` timestamp NULL DEFAULT NULL,
  `st` int(11) NOT NULL,
  `sp` int(11) NOT NULL,
  `ver` int(11) NOT NULL,
  `gl` int(11) NOT NULL,
  `aus` int(11) NOT NULL,
  `hp` int(11) NOT NULL,
  `mp` int(11) NOT NULL,
  `ortid` int(11) DEFAULT '13',
  `std` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`charid`),
  KEY `fk_quests_ortid` (`ortid`),
  CONSTRAINT `fk_quests_charid` FOREIGN KEY (`charid`) REFERENCES `chars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_quests_ortid` FOREIGN KEY (`ortid`) REFERENCES `quest_ort` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rassen`
--

DROP TABLE IF EXISTS `rassen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rassen` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `hp` int(11) NOT NULL DEFAULT '0',
  `mp` int(11) NOT NULL DEFAULT '0',
  `str` int(11) NOT NULL DEFAULT '0',
  `def` int(11) NOT NULL DEFAULT '0',
  `spd` int(11) NOT NULL DEFAULT '0',
  `lck` int(11) NOT NULL DEFAULT '0',
  `stm` int(11) NOT NULL DEFAULT '0',
  `special` tinyint(4) NOT NULL DEFAULT '0',
  `item` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_rassen_type` (`type`),
  CONSTRAINT `fk_rassen_type` FOREIGN KEY (`type`) REFERENCES `rassen_type` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rassen_type`
--

DROP TABLE IF EXISTS `rassen_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rassen_type` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `gm_only` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referer`
--

DROP TABLE IF EXISTS `referer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referer` (
  `url` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `datum` date NOT NULL,
  `anzahl` int(11) NOT NULL,
  PRIMARY KEY (`url`,`datum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `semaphore`
--

DROP TABLE IF EXISTS `semaphore`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `semaphore` (
  `ressource` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ressource`),
  KEY `Zeitindex` (`zeit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Die Tabelle, die hoffentlich alle Zugriffsprobleme loesen wi';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shop`
--

DROP TABLE IF EXISTS `shop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shop` (
  `item` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `mal` int(3) NOT NULL DEFAULT '0',
  `datensatz` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `preis` int(50) NOT NULL DEFAULT '0',
  `preis_req` int(10) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `com` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Kein Kommentar',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sp_item`
--

DROP TABLE IF EXISTS `sp_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sp_item` (
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `preis` int(10) NOT NULL DEFAULT '0',
  `wert` mediumint(5) NOT NULL DEFAULT '0',
  `info` text COLLATE utf8_unicode_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `slot` int(2) NOT NULL DEFAULT '0',
  `tausch_lvl` int(2) unsigned NOT NULL DEFAULT '1',
  `tausch_anzahl` int(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sp_set_ingredient`
--

DROP TABLE IF EXISTS `sp_set_ingredient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sp_set_ingredient` (
  `set_id` int(11) NOT NULL,
  `table_name` varchar(15) NOT NULL,
  `item_id` int(11) NOT NULL,
  PRIMARY KEY (`set_id`,`table_name`,`item_id`),
  CONSTRAINT `fk_sp_set_ingredient_1` FOREIGN KEY (`set_id`) REFERENCES `sp_sets` (`set_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sp_set_topics`
--

DROP TABLE IF EXISTS `sp_set_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sp_set_topics` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sp_sets`
--

DROP TABLE IF EXISTS `sp_sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sp_sets` (
  `set_id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(15) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `set_topic_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`set_id`),
  KEY `fk_sp_sets_topic_idx` (`set_topic_id`),
  CONSTRAINT `fk_sp_sets_topic` FOREIGN KEY (`set_topic_id`) REFERENCES `sp_set_topics` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sp_ware`
--

DROP TABLE IF EXISTS `sp_ware`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sp_ware` (
  `item` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `item` (`item`),
  KEY `user` (`user`),
  CONSTRAINT `fk_sp_ware_item` FOREIGN KEY (`item`) REFERENCES `sp_item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_sp_ware_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `spammer`
--

DROP TABLE IF EXISTS `spammer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spammer` (
  `userid` int(11) NOT NULL,
  `bann_until` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`,`bann_until`),
  CONSTRAINT `fk_spammer_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_kampf`
--

DROP TABLE IF EXISTS `t_kampf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_kampf` (
  `hp1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `hp2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mp1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mp2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `attacke1` text COLLATE utf8_unicode_ci NOT NULL,
  `attacke2` text COLLATE utf8_unicode_ci NOT NULL,
  `schaden1` text COLLATE utf8_unicode_ci NOT NULL,
  `schaden2` text COLLATE utf8_unicode_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `char1` int(20) NOT NULL DEFAULT '0',
  `char2` int(20) NOT NULL DEFAULT '0',
  `starke1` text COLLATE utf8_unicode_ci NOT NULL,
  `ver1` text COLLATE utf8_unicode_ci NOT NULL,
  `speed1` text COLLATE utf8_unicode_ci NOT NULL,
  `starke2` text COLLATE utf8_unicode_ci NOT NULL,
  `ver2` text COLLATE utf8_unicode_ci NOT NULL,
  `speed2` text COLLATE utf8_unicode_ci NOT NULL,
  `ausdauer1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ausdauer2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `exp1` int(20) NOT NULL DEFAULT '0',
  `exp2` int(20) NOT NULL DEFAULT '0',
  `geld1` int(10) NOT NULL DEFAULT '0',
  `geld2` int(10) NOT NULL DEFAULT '0',
  `arena_geld` int(20) NOT NULL DEFAULT '0',
  `besucher` int(20) NOT NULL DEFAULT '0',
  `win` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lose` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `arena_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `db_satz` int(20) NOT NULL DEFAULT '0',
  `dauer` int(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `besucher` (`besucher`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tasking`
--

DROP TABLE IF EXISTS `tasking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tasking` (
  `task_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `script_name` varchar(15) NOT NULL,
  `parameter` varchar(255) NOT NULL,
  `schedule_time` int(10) unsigned NOT NULL,
  `mode` smallint(5) unsigned NOT NULL,
  `catchup` tinyint(1) NOT NULL,
  PRIMARY KEY (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `top_kampf`
--

DROP TABLE IF EXISTS `top_kampf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `top_kampf` (
  `hp1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `hp2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mp1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mp2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `attacke1` text COLLATE utf8_unicode_ci NOT NULL,
  `attacke2` text COLLATE utf8_unicode_ci NOT NULL,
  `schaden1` text COLLATE utf8_unicode_ci NOT NULL,
  `schaden2` text COLLATE utf8_unicode_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `char1` int(20) NOT NULL DEFAULT '0',
  `char2` int(20) NOT NULL DEFAULT '0',
  `starke1` text COLLATE utf8_unicode_ci NOT NULL,
  `ver1` text COLLATE utf8_unicode_ci NOT NULL,
  `speed1` text COLLATE utf8_unicode_ci NOT NULL,
  `starke2` text COLLATE utf8_unicode_ci NOT NULL,
  `ver2` text COLLATE utf8_unicode_ci NOT NULL,
  `speed2` text COLLATE utf8_unicode_ci NOT NULL,
  `ausdauer1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ausdauer2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `glueck1` text COLLATE utf8_unicode_ci NOT NULL,
  `glueck2` text COLLATE utf8_unicode_ci NOT NULL,
  `exp1` int(20) NOT NULL DEFAULT '0',
  `exp2` int(20) NOT NULL DEFAULT '0',
  `geld1` int(10) NOT NULL DEFAULT '0',
  `geld2` int(10) NOT NULL DEFAULT '0',
  `arena_exp` int(20) NOT NULL DEFAULT '0',
  `besucher` int(20) NOT NULL DEFAULT '0',
  `win` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lose` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `arena_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `db_satz` int(20) NOT NULL DEFAULT '0',
  `dauer` int(50) NOT NULL DEFAULT '0',
  `art` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `art2` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `preis` int(20) NOT NULL DEFAULT '0',
  `loge` int(11) NOT NULL,
  `steh` int(11) NOT NULL,
  `sitz` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tournament`
--

DROP TABLE IF EXISTS `tournament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tournament` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT NULL,
  `datum` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_id` int(11) unsigned NOT NULL,
  `ausgewertet` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_turniere_1_idx` (`event_id`),
  KEY `fk_tournament_2_idx` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tournament_registration`
--

DROP TABLE IF EXISTS `tournament_registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tournament_registration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `besitzer` int(20) NOT NULL DEFAULT '0',
  `charakter` int(25) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `besitzer` (`besitzer`),
  KEY `charakter` (`charakter`),
  KEY `fk_turnier1_1_idx` (`type`),
  CONSTRAINT `fk_turnier1_1` FOREIGN KEY (`type`) REFERENCES `tournament_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `turnier1_besitzer` FOREIGN KEY (`besitzer`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `turnier1_char` FOREIGN KEY (`charakter`) REFERENCES `chars` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tournament_types`
--

DROP TABLE IF EXISTS `tournament_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tournament_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `abbrevation` varchar(20) DEFAULT NULL,
  `icon` varchar(45) NOT NULL DEFAULT '',
  `competitors` int(11) NOT NULL,
  `excl_fusions` tinyint(4) NOT NULL DEFAULT '1',
  `excl_winners` tinyint(4) NOT NULL DEFAULT '1',
  `excl_fusion_particip_count` int(11) NOT NULL DEFAULT '0',
  `excl_particip_count` int(11) NOT NULL DEFAULT '0',
  `excl_special_chars` int(11) NOT NULL DEFAULT '0',
  `min_level` int(11) NOT NULL DEFAULT '1',
  `max_level` int(11) DEFAULT NULL,
  `gain` int(11) NOT NULL DEFAULT '0',
  `randomize` tinyint(4) NOT NULL DEFAULT '1',
  `without_equip` tinyint(4) NOT NULL DEFAULT '0',
  `fruit_chance` int(11) NOT NULL DEFAULT '0',
  `fruit_type` varchar(45) DEFAULT NULL,
  `day_of_week` int(11) DEFAULT NULL,
  `day_of_month` int(11) DEFAULT NULL,
  `hour_of_day` int(11) DEFAULT '0',
  `entrance_fee` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `abbrevation_UNIQUE` (`abbrevation`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transaktionen`
--

DROP TABLE IF EXISTS `transaktionen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaktionen` (
  `kaeufer` int(11) DEFAULT NULL,
  `verkaeufer` int(11) DEFAULT NULL,
  `item` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `anzahl` int(10) DEFAULT NULL,
  `verkaeufer_ip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `kaeufer_ip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `betrag` int(30) DEFAULT NULL,
  `zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `Kaeufer` (`kaeufer`,`verkaeufer`,`zeit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tutorial`
--

DROP TABLE IF EXISTS `tutorial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tutorial` (
  `id` int(11) NOT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `umsatz`
--

DROP TABLE IF EXISTS `umsatz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `umsatz` (
  `betrag` decimal(10,2) NOT NULL,
  `instanz` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `kommentar` text COLLATE utf8_unicode_ci NOT NULL,
  `datum` date NOT NULL,
  `malus` decimal(10,2) NOT NULL,
  PRIMARY KEY (`instanz`,`datum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Hier werden alle Umsaetze verzeichnet';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `nickname` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `passwort` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mail` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `homepage` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `geschlecht` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `geld` int(20) NOT NULL DEFAULT '300',
  `ip` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `online_zeit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `bann` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'nein',
  `icq` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `clan` int(11) DEFAULT NULL,
  `item_res` int(2) NOT NULL DEFAULT '0',
  `char_max` int(11) NOT NULL DEFAULT '2',
  `agb` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'nein',
  `post` int(1) NOT NULL DEFAULT '1',
  `schnelllink` int(1) NOT NULL DEFAULT '1',
  `pw_fight` int(2) NOT NULL DEFAULT '4',
  `cheater` int(11) NOT NULL DEFAULT '0',
  `summon` int(50) NOT NULL DEFAULT '0',
  `chat` int(1) NOT NULL DEFAULT '1',
  `chat_anzahl` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '5',
  `code` int(11) NOT NULL DEFAULT '8',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nickname_2` (`nickname`),
  KEY `passwort` (`passwort`),
  KEY `online_zeit` (`online_zeit`),
  KEY `ip` (`ip`),
  KEY `fk_user_clan` (`clan`),
  CONSTRAINT `fk_user_clan` FOREIGN KEY (`clan`) REFERENCES `clan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_activation`
--

DROP TABLE IF EXISTS `user_activation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_activation` (
  `code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `passwort` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `registerdate` date NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_adblock`
--

DROP TABLE IF EXISTS `user_adblock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_adblock` (
  `userid` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`,`time`),
  CONSTRAINT `user_adblock_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_clan`
--

DROP TABLE IF EXISTS `user_clan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_clan` (
  `spende` bigint(20) NOT NULL,
  `userid` int(11) NOT NULL,
  `clanid` int(11) NOT NULL,
  `spende_today` int(11) NOT NULL DEFAULT '0',
  `spende_yesterday` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`,`clanid`),
  KEY `fk_user_clan_clanid` (`clanid`),
  CONSTRAINT `fk_user_clan_clanid` FOREIGN KEY (`clanid`) REFERENCES `clan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_clan_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_clan_invitations`
--

DROP TABLE IF EXISTS `user_clan_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_clan_invitations` (
  `userid` int(11) NOT NULL,
  `clanid` int(11) NOT NULL,
  `valid` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`,`clanid`),
  KEY `user_clan_invitations_clanid` (`clanid`),
  CONSTRAINT `user_clan_invitations_clanid` FOREIGN KEY (`clanid`) REFERENCES `clan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_clan_invitations_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_gruppe`
--

DROP TABLE IF EXISTS `user_gruppe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_gruppe` (
  `gruppen_id` int(11) NOT NULL AUTO_INCREMENT,
  `gruppen_name` varchar(50) NOT NULL,
  `gruppen_beschreibung` text,
  PRIMARY KEY (`gruppen_id`),
  UNIQUE KEY `name` (`gruppen_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_gruppe`
--

LOCK TABLES `user_gruppe` WRITE;
/*!40000 ALTER TABLE `user_gruppe` DISABLE KEYS */;
INSERT INTO `user_gruppe` (`gruppen_id`, `gruppen_name`, `gruppen_beschreibung`) VALUES (1,'admin',NULL),(2,'tester',NULL),(3,'entwickler',NULL),(4,'designer',NULL),(5,'werbung_an',NULL),(6,'inaktiv_loeschschutz',NULL),(7,'aktiv_loeschschutz',NULL),(8,'aktiv_chat',NULL);
/*!40000 ALTER TABLE `user_gruppe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_gruppe_zuordnung`
--

DROP TABLE IF EXISTS `user_gruppe_zuordnung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_gruppe_zuordnung` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `gruppen_id` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `user_id` (`user_id`,`gruppen_id`),
  KEY `fk_gruppe_user_gruppe` (`gruppen_id`),
  KEY `user_gruppe_zuordnung_userid` (`user_id`),
  CONSTRAINT `user_gruppe_zuordnung_userid` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_meta_data`
--

DROP TABLE IF EXISTS `user_meta_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_meta_data` (
  `user_id` int(11) NOT NULL,
  `key` varchar(32) NOT NULL,
  `value` varchar(32) NOT NULL,
  PRIMARY KEY (`user_id`,`key`),
  CONSTRAINT `user_meta_data_userid` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_rename`
--

DROP TABLE IF EXISTS `user_rename`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_rename` (
  `userid` int(11) NOT NULL,
  `datum` date NOT NULL,
  `pre_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `post_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `wunsch` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userid`,`datum`),
  CONSTRAINT `fk_user_rename_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_ticker`
--

DROP TABLE IF EXISTS `user_ticker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_ticker` (
  `userid` int(11) NOT NULL,
  `text` text COLLATE utf8_unicode_ci NOT NULL,
  `msgid` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `new_index` (`userid`,`msgid`,`date`),
  CONSTRAINT `fk_user_ticker_userid` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vote`
--

DROP TABLE IF EXISTS `vote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vote` (
  `user` int(20) NOT NULL DEFAULT '0',
  `vote` int(20) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wanted_kampf`
--

DROP TABLE IF EXISTS `wanted_kampf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wanted_kampf` (
  `hp1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `hp2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mp1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `mp2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `attacke1` text COLLATE utf8_unicode_ci NOT NULL,
  `attacke2` text COLLATE utf8_unicode_ci NOT NULL,
  `schaden1` text COLLATE utf8_unicode_ci NOT NULL,
  `schaden2` text COLLATE utf8_unicode_ci NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `char1` int(20) NOT NULL DEFAULT '0',
  `char2` int(20) NOT NULL DEFAULT '0',
  `starke1` text COLLATE utf8_unicode_ci NOT NULL,
  `ver1` text COLLATE utf8_unicode_ci NOT NULL,
  `speed1` text COLLATE utf8_unicode_ci NOT NULL,
  `starke2` text COLLATE utf8_unicode_ci NOT NULL,
  `ver2` text COLLATE utf8_unicode_ci NOT NULL,
  `speed2` text COLLATE utf8_unicode_ci NOT NULL,
  `ausdauer1` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ausdauer2` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `glueck1` text COLLATE utf8_unicode_ci NOT NULL,
  `glueck2` text COLLATE utf8_unicode_ci NOT NULL,
  `exp1` int(20) NOT NULL DEFAULT '0',
  `exp2` int(20) NOT NULL DEFAULT '0',
  `geld1` int(10) NOT NULL DEFAULT '0',
  `geld2` int(10) NOT NULL DEFAULT '0',
  `arena_geld` int(20) NOT NULL DEFAULT '0',
  `besucher` int(20) NOT NULL DEFAULT '0',
  `win` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `lose` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `arena_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `db_satz` int(20) NOT NULL DEFAULT '0',
  `dauer` int(50) NOT NULL DEFAULT '0',
  `art` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `art2` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `preis` int(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `char1` (`char1`),
  KEY `char2` (`char2`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ware`
--

DROP TABLE IF EXISTS `ware`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ware` (
  `item_id` int(20) NOT NULL DEFAULT '0',
  `user` int(20) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ru_mal` char(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_ware_user` (`user`),
  KEY `fk_ware_item_id` (`item_id`),
  CONSTRAINT `fk_ware_item_id` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ware_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wochen_markt`
--

DROP TABLE IF EXISTS `wochen_markt`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wochen_markt` (
  `item` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `starke` int(2) NOT NULL DEFAULT '0',
  `ver` int(2) NOT NULL DEFAULT '0',
  `speed` int(2) NOT NULL DEFAULT '0',
  `ausdauer` int(2) NOT NULL DEFAULT '0',
  `hp` int(5) NOT NULL DEFAULT '0',
  `mp` int(2) NOT NULL DEFAULT '0',
  `glueck` int(2) NOT NULL DEFAULT '0',
  `type` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Teufels Frucht',
  `preis` int(25) NOT NULL DEFAULT '0',
  `anzahl` int(3) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `verteilung` int(10) NOT NULL DEFAULT '0',
  `aktiv` varchar(4) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ja',
  `art` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'normal',
  `info` text COLLATE utf8_unicode_ci NOT NULL,
  `kategorie` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `tausch_lvl` int(2) unsigned NOT NULL DEFAULT '1',
  `tausch_anzahl` int(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `item` (`item`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `wochen_ware`
--

DROP TABLE IF EXISTS `wochen_ware`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wochen_ware` (
  `item` int(25) NOT NULL DEFAULT '0',
  `user` int(25) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  KEY `id` (`id`),
  KEY `fk_wochen_ware_item` (`item`),
  KEY `fk_wochen_ware_user` (`user`),
  CONSTRAINT `fk_wochen_ware_item` FOREIGN KEY (`item`) REFERENCES `wochen_markt` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_wochen_ware_user` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-07 18:11:45
