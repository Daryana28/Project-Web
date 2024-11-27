-- MySQL dump 10.13  Distrib 9.0.1, for macos15.1 (arm64)
--
-- Host: localhost    Database: login
-- ------------------------------------------------------
-- Server version	9.0.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `aduan`
--

DROP TABLE IF EXISTS `aduan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `aduan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul_aduan` varchar(255) NOT NULL,
  `jenis_aduan` varchar(255) NOT NULL,
  `kampung` varchar(255) NOT NULL,
  `isi_aduan` text NOT NULL,
  `file_upload` varchar(255) DEFAULT NULL,
  `user_email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `notification` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aduan`
--

LOCK TABLES `aduan` WRITE;
/*!40000 ALTER TABLE `aduan` DISABLE KEYS */;
INSERT INTO `aduan` VALUES (2,'Jalan rusak','Kerusakan','Citoke','jalan di daerah citoke dekat irigasi mengalami kerusakan, yang menyebabkan perjalanan warna terhambat','Daryana 4x6.jpeg','daryana@gmail.com','2024-11-27 05:30:05','accepted',NULL),(3,'ffgf','hhh','Semplek','fytyft','hpImg_1697497987.jpg','daryana@gmail.com','2024-11-27 05:33:11','accepted',NULL),(4,'sd','ssss','Krajan','ss','Hitam Minimalis Podcast Instagram Post-2.png','daryana@gmail.com','2024-11-27 07:02:09','accepted',NULL),(5,'ass','s','Citoke','sss','Hitam Minimalis Podcast Instagram Post.png','daryana@gmail.com','2024-11-27 07:02:23','accepted',NULL),(6,'sdd','dd','Citoke','sdd','BgWhite 3.jpg','daryana@gmail.com','2024-11-27 07:02:36','accepted',NULL),(7,'ini ya','woe','Citoke','dd','BgWhite 3.jpg','daryana@gmail.com','2024-11-27 09:07:15','accepted',NULL),(8,'selokan mampet','selokan','Krajan','tolong di FU','Hitam Minimalis Podcast Instagram Post-2.png','daryana@gmail.com','2024-11-27 09:38:50','rejected',NULL);
/*!40000 ALTER TABLE `aduan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `berita`
--

DROP TABLE IF EXISTS `berita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `berita` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `berita`
--

LOCK TABLES `berita` WRITE;
/*!40000 ALTER TABLE `berita` DISABLE KEYS */;
INSERT INTO `berita` VALUES (1,'f','f','uploads/6746fbca3ceda-Hitam Minimalis Podcast Instagram Post-3.png','2024-11-27 11:00:26'),(2,'banjir','banjir didepan kantor sampalan menyebabkan kucing dan semut mati','uploads/6746fbe9b7113- -7.jpg','2024-11-27 11:00:57'),(3,'Berita Test','Ini adalah berita test.',NULL,'2024-11-27 11:15:59'),(4,'Berita Contoh','Ini adalah konten berita contoh.',NULL,'2024-11-27 11:17:33');
/*!40000 ALTER TABLE `berita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  UNIQUE KEY `login` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login`
--

LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` VALUES ('admin123@gmail.com','admin123',NULL),('user123@gmail.com','user123',NULL),('daryana123@gmail.com','daryana123','Daryana');
/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','sampalan@siprakyat.com','sampalan123','admin'),(3,'Daryana','daryana@gmail.com','daryana123','user');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-27 19:26:07
