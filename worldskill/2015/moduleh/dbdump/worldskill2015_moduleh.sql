-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: worldskill2015_moduleh
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `worldskill2015_moduleh`
--

/*!40000 DROP DATABASE IF EXISTS `worldskill2015_moduleh`*/;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `worldskill2015_moduleh` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `worldskill2015_moduleh`;

--
-- Table structure for table `booking`
--

DROP TABLE IF EXISTS `booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `booking_no` varchar(20) NOT NULL COMMENT '對外顯示的訂位編號',
  `booking_contact_id` int(10) unsigned NOT NULL,
  `booking_type` enum('individual','group') NOT NULL DEFAULT 'individual',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_booking_no` (`booking_no`),
  KEY `idx_booking_contact` (`booking_contact_id`),
  CONSTRAINT `fk_booking_contact` FOREIGN KEY (`booking_contact_id`) REFERENCES `booking_contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking`
--

LOCK TABLES `booking` WRITE;
/*!40000 ALTER TABLE `booking` DISABLE KEYS */;
INSERT INTO `booking` VALUES (1,'201500001',2,'individual','2026-08-11 17:25:13'),(2,'201500002',1,'group','2026-08-11 17:25:13'),(3,'201500003',3,'group','2026-08-11 17:25:13');
/*!40000 ALTER TABLE `booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_contact`
--

DROP TABLE IF EXISTS `booking_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `organization` varchar(120) DEFAULT NULL,
  `email` varchar(160) NOT NULL,
  `phone` varchar(60) DEFAULT NULL,
  `country` char(3) NOT NULL COMMENT '國家代碼',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `notified_at` datetime DEFAULT NULL COMMENT '最後一次產生通知信的時間',
  PRIMARY KEY (`id`),
  KEY `idx_booking_contact_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_contact`
--

LOCK TABLES `booking_contact` WRITE;
/*!40000 ALTER TABLE `booking_contact` DISABLE KEYS */;
INSERT INTO `booking_contact` VALUES (1,'Sarah Rogers','WSI','sarah.rogers@worldskills.org','+51 342 31 95 31','US','2026-08-11 17:25:13','2026-08-11 17:25:13'),(2,'Jimmy Hendrix',NULL,'jimmy.hendrix@example.com','+1 555 0100','US','2026-08-11 17:25:13','2026-08-11 17:25:13'),(3,'Pierre Dupont','WorldSkills France','pierre.dupont@example.fr','+33 1 23 45 67 89','FR','2026-08-11 17:25:13','2026-08-11 17:25:13');
/*!40000 ALTER TABLE `booking_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `competition_day`
--

DROP TABLE IF EXISTS `competition_day`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `competition_day` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) NOT NULL COMMENT '競賽日代碼，例如 C1',
  `day_date` date NOT NULL COMMENT '實際日期',
  `sort_order` smallint(6) NOT NULL DEFAULT 0 COMMENT '顯示排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_competition_day_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `competition_day`
--

LOCK TABLES `competition_day` WRITE;
/*!40000 ALTER TABLE `competition_day` DISABLE KEYS */;
INSERT INTO `competition_day` VALUES (1,'C1','2015-08-04',1),(2,'C2','2015-08-05',2),(3,'C3','2015-08-06',3),(4,'C4','2015-08-07',4);
/*!40000 ALTER TABLE `competition_day` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dining_module`
--

DROP TABLE IF EXISTS `dining_module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dining_module` (
  `name` varchar(60) NOT NULL COMMENT '模組名稱',
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL COMMENT '首頁顯示的用餐體驗說明',
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dining_module_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dining_module`
--

LOCK TABLES `dining_module` WRITE;
/*!40000 ALTER TABLE `dining_module` DISABLE KEYS */;
INSERT INTO `dining_module` VALUES ('Casual Dining',1,'This dining is like a bistro/café. Casual service for sandwiches, cakes, cheese plates, salads, alcoholic and non-alcoholic beverages. Guests can choose from a limited menu.',1),('Bar Service',2,'Competitors will prepare international cocktails and serve with light snacks.',2),('Fine Dining',3,'This is formal dining with a four course set menu with alcoholic beverages. The service includes the waiter preparing all dishes at the table by flambé, carving or assembling. Appropriate for VIPs.',3),('Banquet Dining',4,'This is a three course set menu with coffee and alcoholic beverages in a banquet format.',4);
/*!40000 ALTER TABLE `dining_module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_log`
--

DROP TABLE IF EXISTS `email_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `booking_contact_id` int(10) unsigned NOT NULL,
  `file_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_email_log_contact` (`booking_contact_id`),
  CONSTRAINT `fk_email_log_contact` FOREIGN KEY (`booking_contact_id`) REFERENCES `booking_contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_log`
--

LOCK TABLES `email_log` WRITE;
/*!40000 ALTER TABLE `email_log` DISABLE KEYS */;
INSERT INTO `email_log` VALUES (1,1,'20260811-172513_contact-1_sarah.rogers-worldskills.org.txt','2026-08-11 17:25:13'),(2,2,'20260811-172513_contact-2_jimmy.hendrix-example.com.txt','2026-08-11 17:25:13'),(3,3,'20260811-172513_contact-3_pierre.dupont-example.fr.txt','2026-08-11 17:25:13');
/*!40000 ALTER TABLE `email_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `booking_id` int(10) unsigned NOT NULL,
  `competition_day_id` int(10) unsigned NOT NULL,
  `seating_id` int(10) unsigned NOT NULL,
  `guest_name` varchar(120) DEFAULT NULL COMMENT '賓客姓名，可留空（僅知國家）',
  `guest_country` char(3) NOT NULL COMMENT '賓客國家，必填',
  `status` enum('requested','confirmed','waitlisted','declined') NOT NULL DEFAULT 'requested',
  `needs_reschedule` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_reservation_booking` (`booking_id`),
  KEY `idx_reservation_slot` (`competition_day_id`,`seating_id`,`status`),
  KEY `idx_reservation_country` (`competition_day_id`,`seating_id`,`guest_country`),
  KEY `idx_reservation_seating` (`seating_id`),
  CONSTRAINT `fk_reservation_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reservation_day` FOREIGN KEY (`competition_day_id`) REFERENCES `competition_day` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_reservation_seating` FOREIGN KEY (`seating_id`) REFERENCES `seating` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservation`
--

LOCK TABLES `reservation` WRITE;
/*!40000 ALTER TABLE `reservation` DISABLE KEYS */;
INSERT INTO `reservation` VALUES (1,1,2,1,'Jimmy Hendrix','US','requested',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(2,2,2,1,'Simon Bartley','UK','confirmed',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(3,2,2,1,'David Hoey','AU','confirmed',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(4,2,2,1,'Jane Stokie','AU','requested',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(5,2,2,1,'Brigitte Collins','AU','declined',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(6,2,2,1,'Nieman Anders','AU','waitlisted',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(7,2,4,3,'Jane Stokie','AU','confirmed',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(8,2,4,3,'Brigitte Collins','AU','confirmed',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(9,2,4,3,'Skills Emirates','AE','confirmed',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(10,3,2,3,'WS france sponsor','FR','confirmed',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(11,3,2,3,'WS france sponsor','FR','confirmed',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(12,3,2,3,'WS france sponsor','FR','confirmed',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(13,3,2,3,'WS france sponsor','FR','requested',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(14,3,2,3,'WS france sponsor','CA','requested',0,'2026-08-11 17:25:13','2026-08-11 17:25:13'),(15,3,2,3,'WS france sponsor','BE','waitlisted',0,'2026-08-11 17:25:13','2026-08-11 17:25:13');
/*!40000 ALTER TABLE `reservation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seating`
--

DROP TABLE IF EXISTS `seating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seating` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dining_module_id` int(10) unsigned NOT NULL,
  `name` varchar(40) NOT NULL COMMENT '場次名稱，例如 Seating 1',
  `configuration` varchar(120) NOT NULL COMMENT '桌型設定，例如 1 table of 4 and 1 table of 2',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `seats_per_competitor` smallint(5) unsigned NOT NULL COMMENT '每位餐飲服務選手服務的座位數',
  `competitor_count` smallint(5) unsigned NOT NULL COMMENT '餐飲服務選手人數',
  `sort_order` smallint(6) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_seating_module` (`dining_module_id`),
  CONSTRAINT `fk_seating_module` FOREIGN KEY (`dining_module_id`) REFERENCES `dining_module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seating`
--

LOCK TABLES `seating` WRITE;
/*!40000 ALTER TABLE `seating` DISABLE KEYS */;
INSERT INTO `seating` VALUES (1,1,'Seating 1','1 table of 4 and 1 table of 2','10:50:00','12:30:00',6,6,1),(2,1,'Seating 2','1 table of 4 and 1 table of 2','13:30:00','14:40:00',6,6,2),(3,2,'Seating','1 table of 6','13:15:00','14:45:00',6,6,3),(4,3,'Seating','1 table of 4','13:00:00','15:15:00',4,6,4),(5,4,'Seating','1 table of 6','12:45:00','15:00:00',6,6,5);
/*!40000 ALTER TABLE `seating` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
