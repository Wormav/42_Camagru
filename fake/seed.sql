-- MySQL dump 10.13  Distrib 8.0.46, for Linux (aarch64)
--
-- Host: localhost    Database: camagru
-- ------------------------------------------------------
-- Server version	8.0.46

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
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_verified`, `verification_token`, `reset_token`, `reset_token_expires`, `notify_comments`, `avatar_path`, `created_at`, `updated_at`) VALUES (1,'Joe_Macmillan','joe@gmail.com','$2y$12$59Bl3C6PVjO7sWVXw0B9aOsx6cqLdv6L8YXScDrEJsuFN7Y6JlCPa',1,NULL,NULL,NULL,1,'/uploads/avatars/5d140a61a0d94d0e163963eddd67bf1b.jpg','2026-05-14 12:28:22','2026-05-14 12:51:33'),(2,'Donna_Clark','donna@gmail.com','$2y$12$0PzDm6kEKNp2GZ.fjqid2O2X1fp99fcoNNYUeRXaNR8Z2sUlI1l6S',1,NULL,NULL,NULL,1,'/uploads/avatars/3fa3fa3ba342bbd901596f699b53c704.jpg','2026-05-14 12:32:14','2026-05-14 12:49:32'),(3,'Cameron_Howe','cameron@gmail.com','$2y$12$gMbxOnszUZGSOcbzlKOlceknmSLBP5t6PLOvqK7xTYU5lXnNIQZjO',1,NULL,NULL,NULL,1,'/uploads/avatars/0cb8e1cc31aace202097f6f77bedc249.jpg','2026-05-14 12:33:50','2026-05-14 12:41:29'),(4,'Gordon_Clark','gordon@gmail.com','$2y$12$mLftiVANlNjHy1rINIoUWu3n6P2lz6IFVBjE5KlpUm7VMRsGu52SC',1,NULL,NULL,NULL,1,'/uploads/avatars/42010f226d090e27e9bbdd10b6d02f61.webp','2026-05-14 12:34:31','2026-05-14 12:37:44');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
INSERT INTO `images` (`id`, `user_id`, `image_path`, `overlay_used`, `created_at`) VALUES (1,4,'/uploads/snaps/snap_20260514123901_747a61c896de7701.jpg','frame-shadow-lime','2026-05-03 04:51:18'),(2,4,'/uploads/snaps/snap_20260514123911_7bd609ab77935b55.jpg','strips-film-coral','2026-05-01 02:34:11'),(3,4,'/uploads/snaps/snap_20260514123920_4ddfe2a45e7b625d.jpg','frame-double-pink','2026-05-07 04:12:58'),(4,4,'/uploads/snaps/snap_20260514123929_d44968885bf1a424.jpg','doggo-sticker','2026-05-06 10:39:35'),(9,3,'/uploads/snaps/snap_20260514124707_52b2782ceeae492c.jpg','stars-scattered','2026-05-05 22:01:46'),(10,3,'/uploads/snaps/snap_20260514124714_a10564da55a22cb4.jpg','frame-double-pink','2026-05-03 21:17:12'),(11,3,'/uploads/snaps/snap_20260514124746_7834e488ea4b8da9.jpg','frame-shadow-lime','2026-05-02 23:42:27'),(12,2,'/uploads/snaps/snap_20260514125012_9078c301746c1d1f.jpg','frame-shadow-lime','2026-05-13 17:41:56'),(13,2,'/uploads/snaps/snap_20260514125018_cdcfffea52fdf026.jpg','stars-scattered','2026-05-02 14:38:43'),(14,2,'/uploads/snaps/snap_20260514125030_cd0657cbd2d230c2.jpg','frame-double-pink','2026-05-10 17:47:14'),(15,1,'/uploads/snaps/snap_20260514125220_a478f161d11535a0.jpg','strips-film-coral','2026-05-01 06:30:53'),(16,1,'/uploads/snaps/snap_20260514125225_cb08ed1d91af9168.jpg','stars-scattered','2026-05-01 05:21:29'),(17,1,'/uploads/snaps/snap_20260514125231_835601d5fe851a88.jpg','frame-double-pink','2026-05-02 18:34:53'),(18,1,'/uploads/snaps/snap_20260514125236_1265b6d5b4d38819.jpg','frame-shadow-lime','2026-05-05 19:21:57');
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` (`id`, `image_id`, `user_id`, `created_at`) VALUES (1,1,1,'2026-05-05 14:46:14'),(2,1,3,'2026-05-11 21:24:38'),(3,2,4,'2026-05-01 06:21:19'),(4,2,1,'2026-05-04 23:34:40'),(5,2,2,'2026-05-11 08:45:37'),(6,2,3,'2026-05-09 08:46:48'),(7,3,2,'2026-05-08 09:59:13'),(8,3,1,'2026-05-08 07:13:49'),(9,3,4,'2026-05-11 18:52:40'),(10,4,3,'2026-05-13 18:30:27'),(11,4,2,'2026-05-09 15:42:25'),(12,9,4,'2026-05-07 10:23:15'),(13,9,3,'2026-05-10 12:17:08'),(14,10,3,'2026-05-11 09:26:36'),(15,10,4,'2026-05-14 08:16:37'),(16,11,2,'2026-05-03 13:03:17'),(17,11,3,'2026-05-11 00:18:51'),(18,11,1,'2026-05-05 18:05:15'),(19,12,1,'2026-05-14 07:32:19'),(20,12,4,'2026-05-14 03:49:10'),(21,12,3,'2026-05-14 10:12:25'),(22,13,3,'2026-05-05 03:40:07'),(23,13,1,'2026-05-10 17:50:52'),(24,13,2,'2026-05-05 20:24:29'),(25,13,4,'2026-05-11 03:03:54'),(26,14,1,'2026-05-11 17:35:03'),(27,14,3,'2026-05-13 13:06:31'),(28,14,4,'2026-05-13 01:02:57'),(29,14,2,'2026-05-12 09:06:04'),(30,15,2,'2026-05-06 19:56:41'),(31,15,3,'2026-05-02 01:12:52'),(32,15,4,'2026-05-08 22:16:45'),(33,15,1,'2026-05-11 00:12:36'),(34,16,1,'2026-05-06 09:13:19'),(35,16,4,'2026-05-13 08:10:52'),(36,16,2,'2026-05-10 19:49:49'),(37,17,4,'2026-05-05 18:25:00'),(38,17,1,'2026-05-11 19:34:30'),(39,17,2,'2026-05-09 14:05:30'),(40,17,3,'2026-05-09 07:34:11'),(41,18,4,'2026-05-10 04:48:17'),(42,18,3,'2026-05-08 11:14:51'),(43,18,2,'2026-05-07 11:39:28');
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` (`id`, `image_id`, `user_id`, `content`, `created_at`) VALUES (1,1,1,'this is the one','2026-05-03 18:34:43'),(2,1,1,'ship it','2026-05-10 19:36:42'),(3,1,2,'demo day material right here','2026-05-11 11:03:34'),(4,2,1,'love the energy here','2026-05-10 08:52:44'),(5,2,4,'BRB framing this','2026-05-07 05:01:44'),(6,2,1,'the lighting tho','2026-05-03 21:18:03'),(7,3,3,'you nailed it','2026-05-10 17:42:15'),(8,3,4,'looking sharp','2026-05-12 16:22:21'),(9,9,2,'BRB framing this','2026-05-07 05:01:25'),(10,9,3,'love it','2026-05-13 16:08:13'),(11,10,2,'okay how','2026-05-13 03:23:02'),(12,11,1,'we are so back','2026-05-06 22:07:29'),(13,12,1,'you nailed it','2026-05-14 06:54:52'),(14,12,3,'this slaps','2026-05-13 19:48:28'),(15,12,2,'instant fav','2026-05-13 20:33:58'),(16,14,1,'this is the one','2026-05-13 23:22:05'),(17,14,2,'ship it','2026-05-13 15:00:18'),(18,14,2,'instant classic','2026-05-13 22:38:07'),(19,15,2,'more of this please','2026-05-14 08:40:38'),(20,15,2,'iconic','2026-05-08 20:06:09'),(21,15,4,'the lighting tho','2026-05-10 08:02:52'),(22,16,4,'you nailed it','2026-05-07 05:46:24'),(23,16,2,'ok this is fire ðŸ”¥','2026-05-09 10:18:25'),(24,16,1,'elite content','2026-05-06 19:24:34'),(25,17,1,'ok this is fire ðŸ”¥','2026-05-11 08:45:18');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-14 13:04:14
