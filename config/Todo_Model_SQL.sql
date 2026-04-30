CREATE DATABASE  IF NOT EXISTS `mydb` /*!40100 DEFAULT CHARACTER SET utf8mb3 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `mydb`;
-- MySQL dump 10.13  Distrib 8.0.45, for macos15 (arm64)
--
-- Host: localhost    Database: mydb
-- ------------------------------------------------------
-- Server version	9.6.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '2aad719e-1ba5-11f1-9545-0aef920b8167:1-642';

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(90) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#D3D3D3',
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'www.iconos-por-defecto.com',
  `user_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tags_users1_idx` (`user_id`),
  CONSTRAINT `fk_tags_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
INSERT INTO `tags` VALUES (1,'work','#3B8BD4','?',NULL),(2,'home','#1D9E75','?',NULL),(3,'study','#8B5CF6','?',NULL),(4,'health','#EF4444','?',NULL);
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_tags`
--

DROP TABLE IF EXISTS `task_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_tags` (
  `task_id` int unsigned NOT NULL,
  `tag_id` int unsigned NOT NULL,
  PRIMARY KEY (`task_id`,`tag_id`),
  KEY `fk_task-tags_tasks1_idx` (`task_id`),
  KEY `fk_task-tags_tags1` (`tag_id`),
  CONSTRAINT `fk_task-tags_tags1` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`),
  CONSTRAINT `fk_task-tags_tasks1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_tags`
--

LOCK TABLES `task_tags` WRITE;
/*!40000 ALTER TABLE `task_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasks`
--

DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(90) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tasks_users_idx` (`user_id`),
  CONSTRAINT `fk_tasks_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasks`
--

LOCK TABLES `tasks` WRITE;
/*!40000 ALTER TABLE `tasks` DISABLE KEYS */;
INSERT INTO `tasks` VALUES (1,'Tarea de prueba MySQL','Esta tarea viene de MySQL','in_progress','2026-04-28 22:42:11','2026-04-28 22:45:00',1),(2,'Preparar presentación','Slides para la reunión del lunes','in_progress','2026-04-28 22:43:15','2026-06-01 04:15:00',1),(3,'Comprar fruta','Manzanas, plátanos y naranjas','completed','2026-04-25 09:00:00','2026-04-25 10:30:00',1),(4,'Llamar al médico','Pedir cita para revisión anual','pending','2026-04-28 22:43:15',NULL,1),(6,'Hacer la compra semanal','Lista completa en el móvil','pending','2026-04-28 22:43:15',NULL,1),(7,'Prueba tarea',' Hecha con sql persistencia','pending','2026-04-28 23:21:35','2026-04-21 02:22:00',1),(8,'Preparar presentación','Slides para la reunión','in_progress','2026-04-29 17:42:04',NULL,1),(9,'Estudiar MySQL','Practicar PDO y queries','pending','2026-04-29 17:42:04',NULL,1),(10,'Llamar al médico','Pedir cita revisión','completed','2026-04-28 09:00:00','2026-04-28 10:00:00',1);
/*!40000 ALTER TABLE `tasks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'alvaro','1234'),(2,'alvaro','$2y$10$t0IgGDXZM/Q4.CprxP2Hz.WsabR8Eka0iNrSfn2ND37dsf5e4VXe.'),(3,'martina','$2y$12$TD/TxYqWLVWADypoAnqZ2eSf2wmif8RzSOEx9GpnvOHbIYAOlN4z.'),(4,'kent','$2y$12$Mn52RkVzyVFPxHQ3cbcJb.ZjkEmA7DfrjMr4tsT/1kArMCJrXErka'),(5,'alvaro','$2y$10$t0IgGDXZM/Q4.CprxP2Hz.WsabR8Eka0iNrSfn2ND37dsf5e4VXe.'),(6,'martina','$2y$12$TD/TxYqWLVWADypoAnqZ2eSf2wmif8RzSOEx9GpnvOHbIYAOlN4z.'),(7,'kent','$2y$12$Mn52RkVzyVFPxHQ3cbcJb.ZjkEmA7DfrjMr4tsT/1kArMCJrXErka'),(8,'alvaro3','$2y$10$Ar97PoLwad5aQ18cB0HMdOT6d/41EKMwDhA32.jGlDHWCxtcV16kW');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-30 12:38:02
