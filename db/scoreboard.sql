/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 8.4.3 : Database - futsal_scoreboard
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`futsal_scoreboard` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `futsal_scoreboard`;

/*Table structure for table `matches` */

DROP TABLE IF EXISTS `matches`;

CREATE TABLE `matches` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `team_a` varchar(100) NOT NULL,
  `team_b` varchar(100) NOT NULL,
  `score_a` int unsigned NOT NULL DEFAULT '0',
  `score_b` int unsigned NOT NULL DEFAULT '0',
  `score_a_ht` tinyint unsigned DEFAULT NULL,
  `score_b_ht` tinyint unsigned DEFAULT NULL,
  `period` varchar(30) NOT NULL DEFAULT '1st Half',
  `match_time` int unsigned NOT NULL DEFAULT '0',
  `status` enum('scheduled','live','finished') NOT NULL DEFAULT 'live',
  `finished_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `logo_a` varchar(255) DEFAULT NULL,
  `logo_b` varchar(255) DEFAULT NULL,
  `fouls_a` int NOT NULL DEFAULT '0',
  `fouls_b` int NOT NULL DEFAULT '0',
  `timeout_a` tinyint(1) NOT NULL DEFAULT '0',
  `timeout_b` tinyint(1) NOT NULL DEFAULT '0',
  `time_left` int NOT NULL DEFAULT '1200',
  `half_minutes` tinyint unsigned NOT NULL DEFAULT '20',
  `scheduled_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `matches` */

insert  into `matches`(`id`,`team_a`,`team_b`,`score_a`,`score_b`,`score_a_ht`,`score_b_ht`,`period`,`match_time`,`status`,`finished_at`,`created_at`,`updated_at`,`logo_a`,`logo_b`,`fouls_a`,`fouls_b`,`timeout_a`,`timeout_b`,`time_left`,`half_minutes`,`scheduled_at`) values 
(7,'Warshok','Ponkan',3,2,2,0,'2',0,'finished','2026-10-06 08:20:15','2026-10-06 08:17:27','2026-10-06 08:20:15',NULL,NULL,1,2,0,0,1175,20,NULL),
(8,'FCB','Manchester',2,1,NULL,NULL,'1st Half',0,'finished','2026-10-06 08:23:18','2026-10-06 08:21:57','2026-10-06 08:23:18',NULL,NULL,0,0,0,0,585,10,NULL),
(9,'LAD FC','FRIENDSHIP',6,2,2,1,'2',0,'finished','2026-10-07 01:40:32','2026-10-07 01:33:40','2026-10-07 01:40:32',NULL,NULL,4,0,0,0,0,3,NULL),
(10,'ANFC','Ponkan',0,0,NULL,NULL,'1st Half',0,'live',NULL,'2026-10-07 01:57:14','2026-10-07 01:57:48',NULL,NULL,0,0,0,0,1200,20,'2026-10-07 10:00:00'),
(11,'Samanea','AFC',0,0,NULL,NULL,'1st Half',0,'live',NULL,'2026-10-07 02:02:10','2026-10-07 02:11:38',NULL,NULL,0,0,0,0,1200,20,'2026-10-07 13:00:00'),
(12,'Pogi','Gwapo',2,4,1,4,'2',0,'finished','2026-10-07 02:06:22','2026-10-07 02:04:04','2026-10-07 02:06:22',NULL,NULL,2,0,0,0,0,1,NULL),
(13,'Pongkan FT','FRIENDSHIP',11,9,2,3,'2',0,'finished','2026-10-07 02:10:09','2026-10-07 02:09:30','2026-10-07 02:10:09',NULL,NULL,0,0,0,0,586,10,NULL),
(14,'Pongkan FT','CFC',4,2,4,0,'2',0,'finished','2026-10-07 02:13:25','2026-10-07 02:12:50','2026-10-07 02:13:25','uploads/logos/1791339170_b677aa2cd18650958944.jpg','uploads/logos/1791339170_8ad372c810f97e512c19.jpg',0,0,0,0,593,10,NULL),
(15,'FCB','CFC',0,0,NULL,NULL,'1st Half',0,'scheduled',NULL,'2026-10-07 02:14:15','2026-10-07 02:14:15',NULL,NULL,0,0,0,0,900,15,'2026-10-07 14:00:00');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
