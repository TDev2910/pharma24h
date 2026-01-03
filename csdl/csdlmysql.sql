-- CREATE DATABASE  IF NOT EXISTS `pharma` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
-- USE `pharma`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: pharma
-- ------------------------------------------------------
-- Server version	8.0.30

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

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,'Chi nhánh Vũng Tàu','123 Đường Trần Phú, Phường 1, Thành phố Vũng Tàu, Tỉnh Bà Rịa - Vũng Tàu','0254.123.4567','Chi nhánh chính tại thành phố Vũng Tàu, phục vụ khách hàng khu vực miền Nam','2025-11-06 01:09:37','2025-11-06 01:09:37'),(2,'Chi nhánh Hồ Chí Minh','456 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, Thành phố Hồ Chí Minh','028.1234.5678','Chi nhánh tại trung tâm thành phố Hồ Chí Minh, phục vụ khách hàng khu vực nội thành','2025-11-06 01:09:37','2025-11-06 01:09:37');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('pct-pharma-cache-categories.tree','O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:1;s:4:\"name\";s:7:\"Thuốc\";s:9:\"parent_id\";N;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-06 11:11:49\";s:10:\"updated_at\";s:19:\"2025-11-06 11:11:49\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:1;s:4:\"name\";s:7:\"Thuốc\";s:9:\"parent_id\";N;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-06 11:11:49\";s:10:\"updated_at\";s:19:\"2025-11-06 11:11:49\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:4:{i:0;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:14;s:4:\"name\";s:26:\"Thực phẩm chức năng\";s:9:\"parent_id\";i:1;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-12-19 10:45:48\";s:10:\"updated_at\";s:19:\"2025-12-19 10:45:48\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:14;s:4:\"name\";s:26:\"Thực phẩm chức năng\";s:9:\"parent_id\";i:1;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-12-19 10:45:48\";s:10:\"updated_at\";s:19:\"2025-12-19 10:45:48\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:10;s:4:\"name\";s:22:\"Thuốc bổ & vitamin\";s:9:\"parent_id\";i:1;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-20 08:22:25\";s:10:\"updated_at\";s:19:\"2025-11-20 08:22:25\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:10;s:4:\"name\";s:22:\"Thuốc bổ & vitamin\";s:9:\"parent_id\";i:1;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-20 08:22:25\";s:10:\"updated_at\";s:19:\"2025-11-20 08:22:25\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:11;s:4:\"name\";s:12:\"Thuốc bổ\";s:9:\"parent_id\";i:10;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-20 08:22:41\";s:10:\"updated_at\";s:19:\"2025-11-20 08:22:41\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:11;s:4:\"name\";s:12:\"Thuốc bổ\";s:9:\"parent_id\";i:10;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-20 08:22:41\";s:10:\"updated_at\";s:19:\"2025-11-20 08:22:41\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:8;s:4:\"name\";s:18:\"Thuốc dị ứng\";s:9:\"parent_id\";i:1;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-20 08:21:41\";s:10:\"updated_at\";s:19:\"2025-11-20 08:21:41\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:8;s:4:\"name\";s:18:\"Thuốc dị ứng\";s:9:\"parent_id\";i:1;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-20 08:21:41\";s:10:\"updated_at\";s:19:\"2025-11-20 08:21:41\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:4;s:4:\"name\";s:23:\"Thuốc trị ho, cảm\";s:9:\"parent_id\";i:1;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-07 07:56:20\";s:10:\"updated_at\";s:19:\"2025-11-07 07:56:20\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:4;s:4:\"name\";s:23:\"Thuốc trị ho, cảm\";s:9:\"parent_id\";i:1;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-07 07:56:20\";s:10:\"updated_at\";s:19:\"2025-11-07 07:56:20\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:3;s:4:\"name\";s:11:\"Dịch vụ\";s:9:\"parent_id\";N;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-06 11:12:00\";s:10:\"updated_at\";s:19:\"2025-11-06 11:12:00\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:3;s:4:\"name\";s:11:\"Dịch vụ\";s:9:\"parent_id\";N;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-06 11:12:00\";s:10:\"updated_at\";s:19:\"2025-11-06 11:12:00\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:4:{i:0;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:17;s:4:\"name\";s:22:\"Dịch Vụ Tại Nhà\";s:9:\"parent_id\";i:3;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-12-23 10:44:12\";s:10:\"updated_at\";s:19:\"2025-12-23 10:44:12\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:17;s:4:\"name\";s:22:\"Dịch Vụ Tại Nhà\";s:9:\"parent_id\";i:3;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-12-23 10:44:12\";s:10:\"updated_at\";s:19:\"2025-12-23 10:44:12\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:18;s:4:\"name\";s:35:\"Khám bệnh & Tư vấn tại nhà\";s:9:\"parent_id\";i:17;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-12-23 10:44:19\";s:10:\"updated_at\";s:19:\"2025-12-23 10:44:19\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:18;s:4:\"name\";s:35:\"Khám bệnh & Tư vấn tại nhà\";s:9:\"parent_id\";i:17;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-12-23 10:44:19\";s:10:\"updated_at\";s:19:\"2025-12-23 10:44:19\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:7;s:4:\"name\";s:39:\"Khám sức khỏe đi học (trẻ em)\";s:9:\"parent_id\";i:3;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-08 09:00:36\";s:10:\"updated_at\";s:19:\"2025-11-08 09:00:36\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:7;s:4:\"name\";s:39:\"Khám sức khỏe đi học (trẻ em)\";s:9:\"parent_id\";i:3;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-08 09:00:36\";s:10:\"updated_at\";s:19:\"2025-11-08 09:00:36\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:15;s:4:\"name\";s:31:\"Khám Sức Khỏe Hành Chính\";s:9:\"parent_id\";i:3;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-12-23 10:43:42\";s:10:\"updated_at\";s:19:\"2025-12-23 10:43:42\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:15;s:4:\"name\";s:31:\"Khám Sức Khỏe Hành Chính\";s:9:\"parent_id\";i:3;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-12-23 10:43:42\";s:10:\"updated_at\";s:19:\"2025-12-23 10:43:42\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:16;s:4:\"name\";s:28:\"Khám sức khỏe đi học\";s:9:\"parent_id\";i:15;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-12-23 10:43:55\";s:10:\"updated_at\";s:19:\"2025-12-23 10:43:55\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:16;s:4:\"name\";s:28:\"Khám sức khỏe đi học\";s:9:\"parent_id\";i:15;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-12-23 10:43:55\";s:10:\"updated_at\";s:19:\"2025-12-23 10:43:55\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:6;s:4:\"name\";s:21:\"Tiêm vacxin Covid-19\";s:9:\"parent_id\";i:3;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-08 08:58:44\";s:10:\"updated_at\";s:19:\"2025-11-08 08:58:44\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:6;s:4:\"name\";s:21:\"Tiêm vacxin Covid-19\";s:9:\"parent_id\";i:3;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-08 08:58:44\";s:10:\"updated_at\";s:19:\"2025-11-08 08:58:44\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:2;s:4:\"name\";s:16:\"Vật tư y tế\";s:9:\"parent_id\";N;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-06 11:11:55\";s:10:\"updated_at\";s:19:\"2025-11-06 11:11:55\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:2;s:4:\"name\";s:16:\"Vật tư y tế\";s:9:\"parent_id\";N;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-06 11:11:55\";s:10:\"updated_at\";s:19:\"2025-11-06 11:11:55\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:5;s:4:\"name\";s:20:\"Chăm sóc cá nhân\";s:9:\"parent_id\";i:2;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-07 08:01:37\";s:10:\"updated_at\";s:19:\"2025-11-07 08:01:37\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:5;s:4:\"name\";s:20:\"Chăm sóc cá nhân\";s:9:\"parent_id\";i:2;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-07 08:01:37\";s:10:\"updated_at\";s:19:\"2025-11-07 08:01:37\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:13;s:4:\"name\";s:19:\"Chăm sóc da mặt\";s:9:\"parent_id\";i:5;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-20 08:40:58\";s:10:\"updated_at\";s:19:\"2025-11-20 08:40:58\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:13;s:4:\"name\";s:19:\"Chăm sóc da mặt\";s:9:\"parent_id\";i:5;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-20 08:40:58\";s:10:\"updated_at\";s:19:\"2025-11-20 08:40:58\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:26:\"App\\Models\\ProductCategory\":33:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"product_categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:12;s:4:\"name\";s:24:\"Dụng cụ chuyên khoa\";s:9:\"parent_id\";i:2;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-20 08:40:40\";s:10:\"updated_at\";s:19:\"2025-11-20 08:40:40\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:12;s:4:\"name\";s:24:\"Dụng cụ chuyên khoa\";s:9:\"parent_id\";i:2;s:10:\"sort_order\";i:0;s:10:\"created_at\";s:19:\"2025-11-20 08:40:40\";s:10:\"updated_at\";s:19:\"2025-11-20 08:40:40\";}s:10:\"\0*\0changes\";a:0:{}s:11:\"\0*\0previous\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:10:\"sort_order\";s:7:\"integer\";s:9:\"parent_id\";s:7:\"integer\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:17:\"childrenRecursive\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:27:\"\0*\0relationAutoloadCallback\";N;s:26:\"\0*\0relationAutoloadContext\";N;s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:9:\"parent_id\";i:2;s:10:\"sort_order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1767003017),('pct-pharma-cache-phone_otp_attempts_+84376193244','a:2:{s:8:\"attempts\";i:2;s:10:\"created_at\";O:25:\"Illuminate\\Support\\Carbon\":3:{s:4:\"date\";s:26:\"2025-12-27 09:45:41.835006\";s:13:\"timezone_type\";i:3;s:8:\"timezone\";s:3:\"UTC\";}}',1766829042),('pct-pharma-cache-phone_otp_rate_limit_+84376193244','i:1;',1767000307);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_id` bigint unsigned NOT NULL,
  `item_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(12,2) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_promotion` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carts_unique` (`user_id`,`session_id`,`item_id`,`item_type`,`is_promotion`),
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (52,NULL,'UYiJLG3sJl5RbN1PsJkhEwu7bKkL9IvUJPqXrD3O',1,'goods',1,246000.00,'Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',0,'2025-11-24 02:20:22','2025-11-24 02:20:22'),(60,NULL,'yfeOs8Vj6zutQlKAWo3Kwf21MxdSVgcWWB7q9Owk',1,'medicine',1,93000.00,'Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',0,'2025-11-29 00:42:27','2025-11-29 00:42:27'),(106,NULL,'g7YMiOlOe7ztbyCLOGoZyItB2L6Cv9dPHIhC3ZQ5',1,'medicine',1,93000.00,'Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',0,'2025-12-16 10:36:37','2025-12-16 10:36:37'),(108,NULL,'oF4AHUbFO5CSwG0V5AcDIl3BnBZC4M0fMHZ0HbUQ',1,'medicine',1,93000.00,'Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',0,'2025-12-18 02:46:57','2025-12-18 02:46:57'),(109,NULL,'mXZ9WETfPKokP21EyL6h5KJbPOoi3ZipKYpF5vOg',1,'medicine',1,93000.00,'Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',0,'2025-12-18 02:46:57','2025-12-18 02:46:57'),(110,NULL,'qnLVIsKfnTLuxWJKBdc4TmUr4wwbQo6Upt9XcFyV',1,'medicine',1,93000.00,'Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',0,'2025-12-18 02:47:12','2025-12-18 02:47:12'),(112,NULL,'kEKZDcEfN7G33rLoQdA813yfDTSJNSIkx749SJfL',1,'goods',1,246000.00,'Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',0,'2025-12-20 04:10:42','2025-12-20 04:10:42');
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'Phòng Kinh doanh','Phụ trách các hoạt động kinh doanh, bán hàng và chăm sóc khách hàng','2025-11-06 01:08:18','2025-11-06 01:08:18'),(2,'Phòng Kỹ thuật','Phụ trách các dịch vụ kỹ thuật, tư vấn và hỗ trợ khách hàng','2025-11-06 01:08:18','2025-11-06 01:08:18'),(3,'Phòng Hành chính','Phụ trách các công việc hành chính, nhân sự và quản lý nội bộ','2025-11-06 01:08:18','2025-11-06 01:08:18'),(4,'Phòng Kế toán','Phụ trách kế toán, tài chính và quản lý ngân sách','2025-11-06 01:08:18','2025-11-06 01:08:18'),(5,'Phòng Kho','Phụ trách quản lý kho hàng, nhập xuất và kiểm kê','2025-11-06 01:08:18','2025-11-06 01:08:18');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `doctor_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('Male','Female') COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province_district` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Province - District',
  `ward_commune` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ward/Commune',
  `specialty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qualification` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `doctors_doctor_code_unique` (`doctor_code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctors`
--

LOCK TABLES `doctors` WRITE;
/*!40000 ALTER TABLE `doctors` DISABLE KEYS */;
INSERT INTO `doctors` VALUES (1,'365642','Hồ Công Thiên Đạt','Male','0376193244','hctd123doctor@gmail.com','avatars/doctors/doctor_1762592263_690f060749d41.jpg','Số 45, đường Nguyễn An Ninh, phường 7, thành phố Vũng Tàu, tỉnh Bà Rịa – Vũng Tàu','Tỉnh Bà Rịa - Vũng Tàu','Phường 7 (Thành phố Vũng Tàu)','Khoa nhi','Thạc sĩ','Có hơn 10 năm kinh nghiệm','active','2025-11-08 01:58:31','2025-11-08 01:58:31'),(2,'636196','Quốc Anh','Female','0357960770','hctd12312525@gmail.com','avatars/doctors/doctor_1766484757_694a6b1509675.jpg','adad123123','Thành phố Hà Nội','Phường Bách Khoa (Quận Hai Bà Trưng)','Tim mạch','Bác sĩ chuyên khoa II',NULL,'active','2025-12-02 05:06:55','2025-12-23 03:12:38'),(3,'241465','Lê Hoàng Nam','Male','0933445566','nam.le@suckhoe24h.vn','avatars/doctors/doctor_1766484647_694a6aa7d5c63.jpg','12 Chu Văn An','Thành phố Hà Nội','Phường Bách Khoa (Quận Hai Bà Trưng)','Nội tổng quát','Thạc sĩ','Chuyên trách khám sức khỏe đi học và đi làm','active','2025-12-23 03:10:54','2025-12-23 03:10:54'),(4,'656924','Alex Đạt','Male','0912345678','alex.peter@suckhoe24h.vn','avatars/doctors/doctor_1766486449_694a71b1a8a34.jpg','45 đường số 8','Thành phố Đà Nẵng','Phường Hải Châu (Quận Hải Châu)','Y học dự phòng','Bác sĩ chuyên khoa I','Phụ trách dịch vụ khám sức khỏe tại nhà.','active','2025-12-23 03:41:50','2025-12-23 03:41:50');
/*!40000 ALTER TABLE `doctors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drug_routes`
--

DROP TABLE IF EXISTS `drug_routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drug_routes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drug_routes`
--

LOCK TABLES `drug_routes` WRITE;
/*!40000 ALTER TABLE `drug_routes` DISABLE KEYS */;
INSERT INTO `drug_routes` VALUES (1,'Uống',NULL,'2025-11-07 00:59:22','2025-11-07 00:59:22');
/*!40000 ALTER TABLE `drug_routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_allowances`
--

DROP TABLE IF EXISTS `employee_allowances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_allowances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `type` enum('fixed_daily','fixed_monthly','percent_salary') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_allowances_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_allowances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_allowances`
--

LOCK TABLES `employee_allowances` WRITE;
/*!40000 ALTER TABLE `employee_allowances` DISABLE KEYS */;
INSERT INTO `employee_allowances` VALUES (6,4,'Ăn trưa',30000.00,'fixed_daily','2025-11-06 02:14:35','2025-11-06 02:14:35'),(7,4,'Xăng xe',1000000.00,'fixed_monthly','2025-11-06 02:14:35','2025-11-06 02:14:35'),(9,7,'Ăn trưa',730000.00,'fixed_monthly','2025-12-23 11:20:22','2025-12-23 11:20:22'),(10,7,'Xăng xe',500000.00,'fixed_monthly','2025-12-23 11:20:22','2025-12-23 11:20:22'),(11,3,'Ăn trưa',30000.00,'fixed_daily','2025-12-28 02:26:36','2025-12-28 02:26:36');
/*!40000 ALTER TABLE `employee_allowances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_deductions`
--

DROP TABLE IF EXISTS `employee_deductions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_deductions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Vd: Tạm ứng, Trả góp',
  `amount` decimal(15,2) NOT NULL,
  `frequency` enum('one-time','monthly') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_deductions_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_deductions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_deductions`
--

LOCK TABLES `employee_deductions` WRITE;
/*!40000 ALTER TABLE `employee_deductions` DISABLE KEYS */;
INSERT INTO `employee_deductions` VALUES (4,4,'Quỹ hệ thống',200000.00,'monthly','2025-11-06 02:14:35','2025-11-06 02:14:35'),(5,7,'BHXH (10.5%)',1575000.00,'monthly','2025-12-23 11:20:22','2025-12-23 11:20:22'),(6,3,'Quỷ',200000.00,'monthly','2025-12-28 02:26:36','2025-12-28 02:26:36');
/*!40000 ALTER TABLE `employee_deductions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_schedules`
--

DROP TABLE IF EXISTS `employee_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `shift_id` bigint unsigned NOT NULL,
  `schedule_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_schedules_employee_id_shift_id_schedule_date_unique` (`employee_id`,`shift_id`,`schedule_date`),
  KEY `employee_schedules_shift_id_foreign` (`shift_id`),
  CONSTRAINT `employee_schedules_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employee_schedules_shift_id_foreign` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_schedules`
--

LOCK TABLES `employee_schedules` WRITE;
/*!40000 ALTER TABLE `employee_schedules` DISABLE KEYS */;
INSERT INTO `employee_schedules` VALUES (1,3,1,'2025-11-02','7:00 đến 11:00','2025-11-06 02:39:50','2025-11-06 03:55:46'),(2,3,2,'2025-11-06',NULL,'2025-11-06 02:39:57','2025-11-06 02:39:57'),(3,4,2,'2025-11-07',NULL,'2025-11-06 02:41:20','2025-11-06 02:41:20'),(4,3,3,'2025-11-02','18:00 đến 22:00','2025-11-06 02:44:11','2025-11-06 03:56:01'),(5,3,1,'2025-11-04',NULL,'2025-11-06 02:44:48','2025-11-06 02:44:48'),(6,3,2,'2025-11-04',NULL,'2025-11-06 02:44:55','2025-11-06 02:44:55'),(7,4,2,'2025-11-03',NULL,'2025-11-06 02:45:07','2025-11-06 02:45:07'),(8,4,3,'2025-11-03',NULL,'2025-11-06 02:45:11','2025-11-06 02:45:11'),(9,3,3,'2025-11-06','17:00 đến 23:00 ( Xin đi muộn 1 tiếng vì gia đình có việc)','2025-11-06 09:33:15','2025-11-06 09:34:03'),(10,5,1,'2025-11-02','7:00 đến 11:00','2025-11-06 10:55:52','2025-11-06 10:55:52'),(11,5,2,'2025-11-02','13:00 đến 16:00','2025-11-06 10:56:11','2025-11-06 10:56:11'),(12,5,1,'2025-11-03','9:00 đến 11:00 ( xin đi trễ 1 tiếng )','2025-11-06 10:56:38','2025-11-06 10:56:38'),(13,5,3,'2025-11-03','16:00 đến 22:00','2025-11-06 10:56:51','2025-11-06 10:56:51'),(14,3,1,'2025-11-09','8:00 đến 11:00','2025-11-10 03:42:42','2025-11-10 03:42:42'),(15,5,2,'2025-11-09','1:00 đến 16:00 ( Đi trễ 1 tiếng )','2025-11-10 03:43:31','2025-11-10 03:43:31'),(16,4,1,'2025-11-11','8:00 đến 11:00','2025-11-11 11:32:44','2025-11-11 11:32:44'),(17,4,2,'2025-11-11','1:00 đến 16:00','2025-11-11 11:33:14','2025-11-11 11:33:14'),(24,4,1,'2025-12-01',NULL,'2025-12-01 02:50:03','2025-12-01 02:50:03'),(25,4,2,'2025-12-01',NULL,'2025-12-01 02:50:06','2025-12-01 02:50:06'),(27,3,2,'2025-12-07',NULL,'2025-12-12 01:57:15','2025-12-12 01:57:15'),(28,5,1,'2025-12-11',NULL,'2025-12-13 00:46:20','2025-12-13 00:46:20'),(29,5,2,'2025-12-14','đi trễ','2025-12-14 01:01:13','2025-12-14 01:01:13'),(30,3,1,'2025-12-23',NULL,'2025-12-27 03:17:37','2025-12-27 03:17:37'),(38,3,2,'2025-12-27','sadada','2025-12-27 03:26:20','2025-12-27 03:26:20'),(41,4,1,'2025-12-25',NULL,'2025-12-27 04:08:15','2025-12-27 04:08:15'),(42,4,3,'2025-12-25',NULL,'2025-12-27 04:08:18','2025-12-27 04:08:18'),(43,5,2,'2025-12-26',NULL,'2025-12-27 04:08:20','2025-12-27 04:08:20'),(44,5,3,'2025-12-26',NULL,'2025-12-27 04:08:23','2025-12-27 04:08:23'),(45,7,1,'2025-12-25',NULL,'2025-12-27 04:08:27','2025-12-27 04:08:27'),(46,7,2,'2025-12-25',NULL,'2025-12-27 04:08:30','2025-12-27 04:08:30'),(47,7,3,'2025-12-25',NULL,'2025-12-27 04:08:35','2025-12-27 04:08:35');
/*!40000 ALTER TABLE `employee_schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_targets`
--

DROP TABLE IF EXISTS `employee_targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_targets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `activity_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Vd: Bán sản phẩm, Dịch vụ Spa',
  `target_amount` decimal(15,2) NOT NULL COMMENT 'Chỉ tiêu (X)',
  `bonus_type` enum('fixed','percent') COLLATE utf8mb4_unicode_ci NOT NULL,
  `bonus_value` decimal(15,2) NOT NULL COMMENT 'Thưởng (Y)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_targets_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_targets_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_targets`
--

LOCK TABLES `employee_targets` WRITE;
/*!40000 ALTER TABLE `employee_targets` DISABLE KEYS */;
INSERT INTO `employee_targets` VALUES (4,4,'Chuyên cần',800000.00,'fixed',0.00,'2025-11-06 02:14:35','2025-11-06 02:14:35'),(6,7,'Đạt doanh số tháng',2000000.00,'fixed',1.00,'2025-12-23 11:20:22','2025-12-23 11:20:22'),(7,3,'Bán Thực phẩm chức năng',30000000.00,'fixed',500000.00,'2025-12-28 02:26:36','2025-12-28 02:26:36');
/*!40000 ALTER TABLE `employee_targets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employee_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salary_type` enum('fixed','per_hour') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed',
  `salary_level` decimal(15,2) NOT NULL DEFAULT '0.00',
  `department_id` bigint unsigned DEFAULT NULL,
  `job_title_id` bigint unsigned DEFAULT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('nam','nữ') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `id_card_number` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_phone_number_unique` (`phone_number`),
  UNIQUE KEY `employees_employee_code_unique` (`employee_code`),
  KEY `employees_user_id_foreign` (`user_id`),
  KEY `employees_department_id_foreign` (`department_id`),
  KEY `employees_job_title_id_foreign` (`job_title_id`),
  KEY `employees_branch_id_foreign` (`branch_id`),
  CONSTRAINT `employees_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_job_title_id_foreign` FOREIGN KEY (`job_title_id`) REFERENCES `job_titles` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (3,9,'Peter Hồ','0919323727','NV0002','fixed',7000000.00,2,3,1,'2022-11-17','2001-11-11','nam','dewie pham','001234567890','2025-11-06 01:55:37','2025-12-28 02:26:36'),(4,10,'Quốc Anh','0357960770','NV0003','fixed',8000000.00,5,3,1,'2024-11-16','2000-11-03','nam','12 Đô lương','001234567890','2025-11-06 02:14:35','2025-11-06 02:14:35'),(5,11,'Hùng FKT','0899795209','NV0004','fixed',7000000.00,3,2,1,'2024-11-17','2004-11-25','nam','12 hùng khỉ','00144567898','2025-11-06 09:54:24','2025-11-06 09:54:24'),(7,30,'Nguyễn Minh Hoàng','0901234567','NV0006','fixed',13000000.00,1,5,2,'2025-12-01','1995-05-15','nam','123 Đường Láng, Phường Láng Thượng, Quận Đống Đa, Hà Nội','001095001234','2025-12-23 11:20:22','2025-12-23 11:20:22');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods`
--

DROP TABLE IF EXISTS `goods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `goods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ma_hang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ma_vach` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ten_hang_hoa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ten_viet_tat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nhom_hang_id` bigint unsigned DEFAULT NULL,
  `gia_von` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gia_ban` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gia_khuyen_mai` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ton_khuyen_mai` int NOT NULL DEFAULT '0',
  `quan_ly_theo_lo` tinyint(1) NOT NULL DEFAULT '0',
  `ton_kho` int NOT NULL DEFAULT '0',
  `ton_thap_nhat` int NOT NULL DEFAULT '0',
  `ton_cao_nhat` int NOT NULL DEFAULT '999999999',
  `quy_cach_dong_goi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer_id` bigint unsigned DEFAULT NULL,
  `nuoc_san_xuat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_id` bigint unsigned DEFAULT NULL,
  `trong_luong` decimal(8,2) NOT NULL DEFAULT '0.00',
  `don_vi_tinh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ban_truc_tiep` tinyint(1) NOT NULL DEFAULT '0',
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `khach_dat` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_ma_hang_unique` (`ma_hang`),
  UNIQUE KEY `goods_ma_vach_unique` (`ma_vach`),
  KEY `goods_nhom_hang_id_foreign` (`nhom_hang_id`),
  KEY `goods_manufacturer_id_foreign` (`manufacturer_id`),
  KEY `goods_position_id_foreign` (`position_id`),
  CONSTRAINT `goods_manufacturer_id_foreign` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturers` (`id`),
  CONSTRAINT `goods_nhom_hang_id_foreign` FOREIGN KEY (`nhom_hang_id`) REFERENCES `product_categories` (`id`),
  CONSTRAINT `goods_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods`
--

LOCK TABLES `goods` WRITE;
/*!40000 ALTER TABLE `goods` DISABLE KEYS */;
INSERT INTO `goods` VALUES (1,'69690283','38097201','Hada Labo Advanced Nourish Hyaluron Cream','HLCR',5,230000.00,246000.00,150000.00,1,0,198,50,300,'Hũ 50g',2,'Việt Nam',1,50.00,'Hũ',1,'<h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">Đối&nbsp;tượng&nbsp;sử&nbsp;dụng&nbsp;Kem&nbsp;Dưỡng&nbsp;Hada&nbsp;Labo&nbsp;Perfect&nbsp;White&nbsp;T.X.A&nbsp;Cream:</strong></h3><ul><li><strong style=\"background-color: rgb(255, 255, 255); color: rgb(50, 110, 81);\"><a href=\"https://hasaki.vn/danh-muc/xin-mau-tham-sam-c1887.html\" rel=\"noopener noreferrer\" target=\"_blank\">Da&nbsp;xỉn&nbsp;màu&nbsp;&amp;&nbsp;thâm&nbsp;sạm</a></strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">.</span></li><li><strong style=\"background-color: rgb(255, 255, 255); color: rgb(50, 110, 81);\"><a href=\"https://hasaki.vn/danh-muc/thieu-am-thieu-nuoc-c1883.html\" rel=\"noopener noreferrer\" target=\"_blank\">Da&nbsp;thiếu&nbsp;ẩm&nbsp;-&nbsp;thiếu&nbsp;nước</a></strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">.&nbsp;</span></li></ul><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">Ưu&nbsp;thế&nbsp;nổi&nbsp;bật&nbsp;của&nbsp;Kem&nbsp;Dưỡng&nbsp;Hada&nbsp;Labo&nbsp;Perfect&nbsp;White&nbsp;T.X.A&nbsp;Cream:</strong></h3><ul><li><strong style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">1%&nbsp;T.X.A</strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">&nbsp;có&nbsp;khả&nbsp;năng&nbsp;làm&nbsp;mờ&nbsp;các&nbsp;đốm&nbsp;nâu,&nbsp;cải&nbsp;thiện&nbsp;đều&nbsp;màu&nbsp;và&nbsp;làm&nbsp;sáng&nbsp;da.&nbsp;</span></li><li><strong style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">Niacinamide&nbsp;</strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">có&nbsp;hiệu&nbsp;quả&nbsp;dưỡng&nbsp;sáng&nbsp;chuyên&nbsp;sâu&nbsp;và&nbsp;làm&nbsp;mờ&nbsp;thâm&nbsp;nám,&nbsp;thúc&nbsp;đẩy&nbsp;tái&nbsp;tạo&nbsp;tế&nbsp;bào&nbsp;da&nbsp;mới,&nbsp;bật&nbsp;tông&nbsp;da&nbsp;rõ&nbsp;rệt.</span></li><li><strong style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">Vitamin&nbsp;C&nbsp;&amp;&nbsp;E</strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">&nbsp;tăng&nbsp;cường&nbsp;khả&nbsp;năng&nbsp;chống&nbsp;oxy&nbsp;hóa,&nbsp;bảo&nbsp;vệ&nbsp;da&nbsp;khỏi&nbsp;tác&nbsp;hại&nbsp;của&nbsp;tia&nbsp;UV,&nbsp;đồng&nbsp;thời&nbsp;dưỡng&nbsp;sáng&nbsp;các&nbsp;vùng&nbsp;da&nbsp;sậm&nbsp;màu,&nbsp;cho&nbsp;sắc&nbsp;da&nbsp;đều&nbsp;màu&nbsp;rạng&nbsp;rỡ.</span></li><li><strong style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">2x&nbsp;Hyaluronic&nbsp;Acid</strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">&nbsp;giúp&nbsp;cung&nbsp;cấp&nbsp;độ&nbsp;ẩm&nbsp;sâu,&nbsp;hỗ&nbsp;trợ&nbsp;tái&nbsp;tạo&nbsp;cấu&nbsp;trúc&nbsp;da&nbsp;đàn&nbsp;hồi,&nbsp;dưỡng&nbsp;da&nbsp;ẩm&nbsp;mượt&nbsp;&amp;&nbsp;mềm&nbsp;mịn.</span></li><li><strong style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">Chiết&nbsp;xuất&nbsp;hạt&nbsp;Ý&nbsp;Dĩ</strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">&nbsp;giúp&nbsp;dưỡng&nbsp;ẩm&nbsp;và&nbsp;dưỡng&nbsp;sáng&nbsp;da,&nbsp;giảm&nbsp;viêm&nbsp;và&nbsp;ngăn&nbsp;ngừa&nbsp;các&nbsp;dấu&nbsp;hiệu&nbsp;lão&nbsp;hóa.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">Dạng&nbsp;kem&nbsp;mỏng&nbsp;nhẹ,&nbsp;thẩm&nbsp;thấu&nbsp;các&nbsp;dưỡng&nbsp;chất&nbsp;vào&nbsp;sâu&nbsp;trong&nbsp;da.&nbsp;Sản&nbsp;phẩm&nbsp;nâng&nbsp;tông&nbsp;da&nbsp;tự&nbsp;nhiên.</span></li></ul><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">Độ&nbsp;an&nbsp;toàn:</strong></h3><ul><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">Công&nbsp;thức&nbsp;an&nbsp;toàn,&nbsp;dịu&nbsp;nhẹ,&nbsp;không&nbsp;chứa&nbsp;các&nbsp;thành&nbsp;phần&nbsp;có&nbsp;khả&nbsp;năng&nbsp;gây&nbsp;kích&nbsp;ứng&nbsp;da&nbsp;như&nbsp;cồn,&nbsp;</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(51, 51, 51);\">hương&nbsp;liệu,&nbsp;chất&nbsp;tạo&nbsp;màu,&nbsp;dầu&nbsp;khoáng.&nbsp;</span></li></ul><p></p>','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',0,'2025-11-07 01:07:18','2025-12-17 10:09:20',NULL),(3,'10871084','42295829','Tinh chất La Roche-Posay Hyalu B5 Serum','B5 Serum',13,1150000.00,1209000.00,0.00,0,0,19,200,700,'Hộp x 30ml',NULL,'Pháp',3,150.00,'Hộp',1,'<h2><span style=\"background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);\">Cách&nbsp;dùng&nbsp;Tinh&nbsp;chất&nbsp;Hyalu&nbsp;B5&nbsp;Serum</span></h2><p><strong style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Cách&nbsp;dùng</strong></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Sử&nbsp;dụng&nbsp;vào&nbsp;buổi&nbsp;sáng&nbsp;và/hoặc&nbsp;tối&nbsp;sau&nbsp;bước&nbsp;làm&nbsp;sạch.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Nên&nbsp;kết&nbsp;hợp&nbsp;với&nbsp;kem&nbsp;dưỡng&nbsp;để&nbsp;tăng&nbsp;hiệu&nbsp;quả&nbsp;dưỡng&nbsp;da&nbsp;tối&nbsp;đa.</span></p><p><strong style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Đối&nbsp;tượng&nbsp;sử&nbsp;dụng</strong></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Hyalu&nbsp;B5&nbsp;Serum&nbsp;sử&nbsp;dụng&nbsp;trong&nbsp;các&nbsp;trường&nbsp;hợp:</span></p><ul><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Da&nbsp;thiếu&nbsp;độ&nbsp;ẩm&nbsp;cần&nbsp;được&nbsp;phục&nbsp;hồi.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Da&nbsp;nhạy&nbsp;cảm.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Da&nbsp;sau&nbsp;các&nbsp;liệu&nbsp;trình&nbsp;chăm&nbsp;sóc&nbsp;thẩm&nbsp;mỹ.</span></li></ul><h2><span style=\"background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);\">Tác&nbsp;dụng&nbsp;phụ</span></h2><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Chưa&nbsp;có&nbsp;thông&nbsp;tin&nbsp;về&nbsp;tác&nbsp;dụng&nbsp;phụ&nbsp;của&nbsp;sản&nbsp;phẩm.</span></p><p></p>','goods/2otYHt9v6Vvn6cUOCEX3rxPEELmym88BgdzwcHaL.jpg',0,'2025-11-20 02:11:20','2025-12-04 03:16:59',NULL),(4,'30919136','71975776','Sữa Rửa Mặt Simple Sạch Sâu Và Cấp Ẩm','SRM Simple 150ml',13,85000.00,125000.00,0.00,0,0,0,50,200,'Tuýp 150ml',7,'Anh (UK)',4,180.00,'Tuýp',1,NULL,'goods/Oo3WPYN12jk7uQlCRKqGOMr7v5XOwX7QTIKqX7JF.jpg',0,'2025-12-19 03:26:05','2025-12-19 03:26:05',NULL);
/*!40000 ALTER TABLE `goods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_titles`
--

DROP TABLE IF EXISTS `job_titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_titles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_titles`
--

LOCK TABLES `job_titles` WRITE;
/*!40000 ALTER TABLE `job_titles` DISABLE KEYS */;
INSERT INTO `job_titles` VALUES (1,'Dược sĩ bán thuốc','Tư vấn và bán thuốc cho khách hàng, đảm bảo an toàn sử dụng thuốc','2025-11-06 01:08:18','2025-11-06 01:08:18'),(2,'Thu ngân','Thực hiện thanh toán, quản lý tiền mặt và giao dịch với khách hàng','2025-11-06 01:08:18','2025-11-06 01:08:18'),(3,'Nhân viên kho','Quản lý kho hàng, nhập xuất hàng hóa và kiểm kê tồn kho','2025-11-06 01:08:18','2025-11-06 01:08:18'),(4,'Quản lý','Quản lý và điều hành hoạt động của cửa hàng/chi nhánh','2025-11-06 01:08:18','2025-11-06 01:08:18'),(5,'Nhân viên','Nhân viên làm việc tại cửa hàng, hỗ trợ khách hàng và các công việc khác','2025-11-06 01:08:18','2025-11-06 01:08:18'),(6,'Kỹ thuật viên','Thực hiện các dịch vụ kỹ thuật, tư vấn và hỗ trợ khách hàng','2025-11-06 01:08:18','2025-11-06 01:08:18'),(7,'Kế toán viên','Thực hiện các công việc kế toán, quản lý tài chính và báo cáo','2025-11-06 01:08:18','2025-11-06 01:08:18');
/*!40000 ALTER TABLE `job_titles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manufacturers`
--

DROP TABLE IF EXISTS `manufacturers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `manufacturers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manufacturers`
--

LOCK TABLES `manufacturers` WRITE;
/*!40000 ALTER TABLE `manufacturers` DISABLE KEYS */;
INSERT INTO `manufacturers` VALUES (1,'Engelhard Arzneimittel GmbH & Co. KG',NULL,'2025-11-07 00:59:33','2025-11-07 00:59:33'),(2,'Rohto-Mentholatum','Việt Nam','2025-11-07 01:05:07','2025-11-07 01:05:07'),(3,'C. HEDENKAMP GMBH & CO. KG',NULL,'2025-11-20 01:24:44','2025-11-20 01:24:44'),(4,'DHG Pharma',NULL,'2025-11-20 01:50:11','2025-11-20 01:50:11'),(5,'Dược Hậu Giang (Việt Nam)',NULL,'2025-11-20 01:50:51','2025-11-20 01:50:51'),(6,'AN THIÊN',NULL,'2025-11-20 01:58:10','2025-11-20 01:58:10'),(7,'Simple (Unilever)',NULL,'2025-12-19 02:22:07','2025-12-19 02:22:07'),(8,'Blackmores',NULL,'2025-12-19 03:47:23','2025-12-19 03:47:23');
/*!40000 ALTER TABLE `manufacturers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicines`
--

DROP TABLE IF EXISTS `medicines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ma_hang` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ma_vach` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ten_thuoc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ten_viet_tat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nhom_hang_id` bigint unsigned DEFAULT NULL,
  `gia_von` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gia_ban` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gia_khuyen_mai` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ton_khuyen_mai` int NOT NULL DEFAULT '0',
  `ton_kho` int NOT NULL DEFAULT '0',
  `so_dang_ky` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hoat_chat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ham_luong` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drugusage_id` bigint unsigned DEFAULT NULL,
  `quy_cach_dong_goi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manufacturer_id` bigint unsigned DEFAULT NULL,
  `nuoc_san_xuat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ton_thap_nhat` int NOT NULL DEFAULT '0',
  `ton_cao_nhat` int NOT NULL DEFAULT '999999999',
  `position_id` bigint unsigned DEFAULT NULL,
  `trong_luong` decimal(8,2) NOT NULL DEFAULT '0.00',
  `don_vi_tinh` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ban_truc_tiep` tinyint(1) NOT NULL DEFAULT '0',
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `khach_dat` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `medicines_ma_hang_unique` (`ma_hang`),
  UNIQUE KEY `medicines_ma_vach_unique` (`ma_vach`),
  KEY `medicines_nhom_hang_id_foreign` (`nhom_hang_id`),
  KEY `medicines_drugusage_id_foreign` (`drugusage_id`),
  KEY `medicines_manufacturer_id_foreign` (`manufacturer_id`),
  KEY `medicines_position_id_foreign` (`position_id`),
  CONSTRAINT `medicines_drugusage_id_foreign` FOREIGN KEY (`drugusage_id`) REFERENCES `drug_routes` (`id`),
  CONSTRAINT `medicines_manufacturer_id_foreign` FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturers` (`id`),
  CONSTRAINT `medicines_nhom_hang_id_foreign` FOREIGN KEY (`nhom_hang_id`) REFERENCES `product_categories` (`id`),
  CONSTRAINT `medicines_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicines`
--

LOCK TABLES `medicines` WRITE;
/*!40000 ALTER TABLE `medicines` DISABLE KEYS */;
INSERT INTO `medicines` VALUES (1,'74860951','47355426','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','Siro ho',4,85000.00,93000.00,70000.00,0,86,'400200006124','Cao khô lá thường xuân','100ml',1,'Chai 100m',1,'Đức',50,200,NULL,10.00,'Chai',1,'<h2><span style=\"background-color: rgb(255, 255, 255); color: rgb(0, 0, 0);\">Cách&nbsp;dùng&nbsp;Siro&nbsp;ho&nbsp;Prospan</span></h2><h3><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Cách&nbsp;dùng</span></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Siro&nbsp;ho&nbsp;Prospan&nbsp;dùng&nbsp;đường&nbsp;uống,&nbsp;lắc&nbsp;chai&nbsp;kỹ&nbsp;trước&nbsp;khi&nbsp;dùng.</span></p><h3><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Liều&nbsp;dùng</span></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Trẻ&nbsp;sơ&nbsp;sinh&nbsp;và&nbsp;trẻ&nbsp;nhỏ&nbsp;(dưới&nbsp;6&nbsp;tuổi):&nbsp;Dùng&nbsp;liều&nbsp;2,5ml/lần&nbsp;x&nbsp;3&nbsp;lần&nbsp;mỗi&nbsp;ngày.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Trẻ&nbsp;ở&nbsp;độ&nbsp;tuổi&nbsp;đi&nbsp;học&nbsp;(6&nbsp;-&nbsp;9&nbsp;tuổi)&nbsp;và&nbsp;thiếu&nbsp;niên&nbsp;(&gt;&nbsp;10&nbsp;tuổi):&nbsp;Dùng&nbsp;liều&nbsp;5ml/lần&nbsp;x&nbsp;3&nbsp;lần&nbsp;mỗi&nbsp;ngày.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Người&nbsp;lớn:&nbsp;Dùng&nbsp;liều&nbsp;5&nbsp;-&nbsp;7,5&nbsp;ml/lần&nbsp;x&nbsp;3&nbsp;lần&nbsp;mỗi&nbsp;ngày.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Thời&nbsp;gian&nbsp;dùng&nbsp;thuốc&nbsp;tuỳ&nbsp;thuộc&nbsp;vào&nbsp;mức&nbsp;độ&nbsp;trầm&nbsp;trọng&nbsp;của&nbsp;các&nbsp;triệu&nbsp;chứng,&nbsp;nhưng&nbsp;phải&nbsp;dùng&nbsp;ít&nbsp;nhất&nbsp;là&nbsp;1&nbsp;tuần,&nbsp;</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">ngay&nbsp;cả&nbsp;khi&nbsp;chỉ&nbsp;bị&nbsp;nhiễm&nbsp;trùng&nbsp;đường&nbsp;hô&nbsp;hấp&nbsp;nhẹ.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Để&nbsp;đảm&nbsp;bảo&nbsp;việc&nbsp;điều&nbsp;trị&nbsp;được&nbsp;thành&nbsp;công,&nbsp;nên&nbsp;dùng&nbsp;thuốc&nbsp;thêm&nbsp;2&nbsp;-&nbsp;3&nbsp;ngày&nbsp;sau&nbsp;khi&nbsp;đã&nbsp;hết&nbsp;các&nbsp;triệu&nbsp;chứng&nbsp;bệnh.&nbsp;</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Trong&nbsp;trường&nbsp;hợp&nbsp;bệnh&nbsp;vẫn&nbsp;còn&nbsp;dai&nbsp;dẳng&nbsp;và&nbsp;xuất&nbsp;hiện&nbsp;tình&nbsp;trạng&nbsp;</span><a href=\"https://nhathuoclongchau.com.vn/benh/kho-tho-700.html\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"background-color: rgb(255, 255, 255); color: rgb(18, 80, 220);\">khó&nbsp;thở</a><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">,&nbsp;</span><a href=\"https://nhathuoclongchau.com.vn/benh/sot-618.html\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"background-color: rgb(255, 255, 255); color: rgb(18, 80, 220);\">sốt</a><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">,&nbsp;đàm&nbsp;có&nbsp;mủ&nbsp;hoặc&nbsp;máu,&nbsp;phải&nbsp;đi&nbsp;khám&nbsp;bác&nbsp;sĩ&nbsp;ngay&nbsp;lập&nbsp;tức.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Lưu&nbsp;ý:&nbsp;Liều&nbsp;dùng&nbsp;trên&nbsp;chỉ&nbsp;mang&nbsp;tính&nbsp;chất&nbsp;tham&nbsp;khảo.&nbsp;Liều&nbsp;dùng&nbsp;cụ&nbsp;thể&nbsp;tùy&nbsp;thuộc&nbsp;vào&nbsp;thể&nbsp;trạng&nbsp;và&nbsp;mức&nbsp;độ&nbsp;diễn&nbsp;tiến&nbsp;của&nbsp;bệnh.&nbsp;Để&nbsp;có&nbsp;liều&nbsp;dùng&nbsp;phù&nbsp;hợp,&nbsp;bạn&nbsp;cần&nbsp;tham&nbsp;khảo&nbsp;ý&nbsp;kiến&nbsp;bác&nbsp;sĩ&nbsp;hoặc&nbsp;chuyên&nbsp;viên&nbsp;y&nbsp;tế.</span></p><p></p>','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',0,'2025-11-07 01:00:53','2025-12-27 01:34:02',NULL),(6,'87997727','24121649','Viên uống Immuvita Easylife bổ sung vitamin và khoáng chất cho cơ thể, tăng sức khỏe','Immuvita Easylife',11,350000.00,390000.00,0.00,0,0,'638/2023/ĐKSP','Calcium hydrogen phosphate, Magie oxide, Canxi carbonat','Vitamin A, C, D3, K1, nhóm B (B1, B2, B5, B6, B9, B12)',1,'Hộp 100 Viên',3,'Đức',200,500,NULL,5.00,'Hộp',1,'<h3><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Hỗ&nbsp;trợ&nbsp;tăng&nbsp;cường&nbsp;sức&nbsp;khỏe</span></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Easylife&nbsp;Immuvita&nbsp;là&nbsp;thực&nbsp;phẩm&nbsp;bảo&nbsp;vệ&nbsp;sức&nbsp;khỏe&nbsp;giúp&nbsp;tăng&nbsp;cường&nbsp;sức&nbsp;khỏe&nbsp;dành&nbsp;cho&nbsp;người&nbsp;trưởng&nbsp;thành.&nbsp;</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Sản&nbsp;phẩm&nbsp;giúp&nbsp;bổ&nbsp;sung&nbsp;26&nbsp;vitamin&nbsp;và&nbsp;khoáng&nbsp;chất&nbsp;thiết&nbsp;yếu&nbsp;giúp&nbsp;nâng&nbsp;cao&nbsp;tinh&nbsp;thần&nbsp;và&nbsp;tăng&nbsp;cường&nbsp;chức&nbsp;năng&nbsp;miễn&nbsp;dịch&nbsp;của&nbsp;cơ&nbsp;thể.</span></p><p></p><h3><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Hỗ&nbsp;trợ&nbsp;tăng&nbsp;cường&nbsp;sức&nbsp;đề&nbsp;kháng</span></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Vitamin&nbsp;và&nbsp;khoáng&nbsp;chất&nbsp;đều&nbsp;là&nbsp;những&nbsp;yếu&nbsp;tố&nbsp;thiết&nbsp;yếu&nbsp;không&nbsp;thể&nbsp;thiếu&nbsp;trong&nbsp;cơ&nbsp;thể&nbsp;con&nbsp;người&nbsp;và&nbsp;hỗ&nbsp;trợ&nbsp;</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">rất&nbsp;nhiều&nbsp;trong&nbsp;việc&nbsp;tăng&nbsp;cường&nbsp;sức&nbsp;đề&nbsp;kháng,&nbsp;bảo&nbsp;vệ&nbsp;cơ&nbsp;thể&nbsp;trước&nbsp;các&nbsp;bệnh&nbsp;tật.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Các&nbsp;vitamin&nbsp;và&nbsp;khoáng&nbsp;chất&nbsp;tăng&nbsp;cường&nbsp;sức&nbsp;đề&nbsp;kháng&nbsp;bằng&nbsp;cách&nbsp;thúc&nbsp;đẩy&nbsp;sản&nbsp;xuất&nbsp;và&nbsp;hoạt&nbsp;động&nbsp;của&nbsp;tế&nbsp;bào&nbsp;miễn&nbsp;dịch,&nbsp;như&nbsp;tế&nbsp;bào&nbsp;bạch&nbsp;cầu,&nbsp;rất&nbsp;quan&nbsp;trọng&nbsp;trong&nbsp;việc&nbsp;phòng&nbsp;chống&nbsp;nhiễm&nbsp;trùng.&nbsp;</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">Một&nbsp;số&nbsp;vitamin&nbsp;như&nbsp;</span><a href=\"https://nhathuoclongchau.com.vn/thanh-phan/vitamin-a\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"background-color: rgb(255, 255, 255); color: rgb(18, 80, 220);\">vitamin&nbsp;A</a><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">&nbsp;và&nbsp;</span><a href=\"https://nhathuoclongchau.com.vn/thuc-pham-chuc-nang/vitamin-c\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"background-color: rgb(255, 255, 255); color: rgb(18, 80, 220);\">vitamin&nbsp;C</a><span style=\"background-color: rgb(255, 255, 255); color: rgb(2, 11, 39);\">,&nbsp;còn&nbsp;hoạt&nbsp;động&nbsp;như&nbsp;chất&nbsp;chống&nbsp;oxy&nbsp;hóa,&nbsp;giúp&nbsp;bảo&nbsp;vệ&nbsp;tế&nbsp;bào&nbsp;của&nbsp;cơ&nbsp;thể&nbsp;khỏi&nbsp;tổn&nbsp;thương&nbsp;do&nbsp;gặp&nbsp;phải&nbsp;các&nbsp;gốc&nbsp;tự&nbsp;do.</span></p><p></p><p></p>','products/gyzJJ9rDbFCWoQJlnNYKN9uLVurAelnucAtrnspx.jpg',0,'2025-11-20 01:31:09','2025-11-20 01:31:09',NULL),(7,'74960165','62458756','Viên nén Telfor 60mg trị viêm mũi dị ứng, mày đay','Telfor',8,10000.00,13000.00,0.00,0,0,'893100013900','Fexofenadin','60mg',1,'2 vỉ X 10 viên',5,'Việt Nam',50,200,NULL,0.00,'Vỉ',1,'<h2><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Cách&nbsp;dùng&nbsp;viên&nbsp;nén&nbsp;Telfor&nbsp;60mg</strong></h2><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Cách&nbsp;dùng</strong></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Dùng&nbsp;đường&nbsp;uống.</span></p><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Liều&nbsp;dùng</strong></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Người&nbsp;lớn&nbsp;và&nbsp;trẻ&nbsp;em&nbsp;trên&nbsp;12&nbsp;tuổi:&nbsp;Uống&nbsp;1&nbsp;viên&nbsp;x&nbsp;2&nbsp;lần/ngày.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Người&nbsp;lớn&nbsp;và&nbsp;trẻ&nbsp;em&nbsp;trên&nbsp;12&nbsp;tuổi&nbsp;bị&nbsp;suy&nbsp;thận&nbsp;hay&nbsp;phải&nbsp;thẩm&nbsp;phân&nbsp;máu:&nbsp;uống&nbsp;1&nbsp;viên&nbsp;x&nbsp;1&nbsp;lần/ngày.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Không&nbsp;cần&nbsp;điều&nbsp;chỉnh&nbsp;liều&nbsp;cho&nbsp;người&nbsp;suy&nbsp;gan.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Hoặc&nbsp;theo&nbsp;chỉ&nbsp;dẫn&nbsp;của&nbsp;thầy&nbsp;thuốc.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Lưu&nbsp;ý:&nbsp;Liều&nbsp;dùng&nbsp;trên&nbsp;chỉ&nbsp;mang&nbsp;tính&nbsp;chất&nbsp;tham&nbsp;khảo.&nbsp;Liều&nbsp;dùng&nbsp;cụ&nbsp;thể&nbsp;tùy&nbsp;thuộc&nbsp;vào&nbsp;thể&nbsp;trạng&nbsp;</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">và&nbsp;mức&nbsp;độ&nbsp;diễn&nbsp;tiến&nbsp;của&nbsp;bệnh.&nbsp;Để&nbsp;có&nbsp;liều&nbsp;dùng&nbsp;phù&nbsp;hợp,&nbsp;bạn&nbsp;cần&nbsp;tham&nbsp;khảo&nbsp;ý&nbsp;kiến&nbsp;bác&nbsp;sĩ&nbsp;hoặc&nbsp;chuyên&nbsp;viên&nbsp;y&nbsp;tế.</span></p><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Làm&nbsp;gì&nbsp;khi&nbsp;dùng&nbsp;quá&nbsp;liều?</strong></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Thông&nbsp;tin&nbsp;về&nbsp;độc&nbsp;tính&nbsp;cấp&nbsp;của&nbsp;fexofenadin&nbsp;còn&nbsp;hạn&nbsp;chế.&nbsp;Tuy&nbsp;nhiên,&nbsp;buồn&nbsp;ngủ,&nbsp;chóng&nbsp;mặt,&nbsp;khô&nbsp;miệng&nbsp;đã&nbsp;được&nbsp;báo&nbsp;cáo.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Xử&nbsp;trí:&nbsp;Sử&nbsp;dụng&nbsp;các&nbsp;biện&nbsp;pháp&nbsp;thông&nbsp;thường&nbsp;để&nbsp;loại&nbsp;bỏ&nbsp;phần&nbsp;thuốc&nbsp;còn&nbsp;chưa&nbsp;được&nbsp;hấp&nbsp;thu&nbsp;ở&nbsp;ống&nbsp;tiêu&nbsp;hóa.&nbsp;</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Điều&nbsp;trị&nbsp;hỗ&nbsp;trợ&nbsp;và&nbsp;điều&nbsp;trị&nbsp;triệu&nbsp;chứng.&nbsp;Thẩm&nbsp;phân&nbsp;máu&nbsp;làm&nbsp;giảm&nbsp;nồng&nbsp;độ&nbsp;thuốc&nbsp;trong&nbsp;máu&nbsp;không&nbsp;đáng&nbsp;kể&nbsp;(1,7%).&nbsp;Không&nbsp;có&nbsp;thuốc&nbsp;giải&nbsp;độc&nbsp;đặc&nbsp;hiệu.</span></p><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Làm&nbsp;gì&nbsp;khi&nbsp;quên&nbsp;1&nbsp;liều?</strong></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Uống&nbsp;ngay&nbsp;khi&nbsp;nhớ&nbsp;ra&nbsp;đảm&nbsp;bảo&nbsp;1&nbsp;viên,&nbsp;1&nbsp;lần/ngày.&nbsp;Không&nbsp;gấp&nbsp;đôi&nbsp;liều&nbsp;quy&nbsp;định&nbsp;để&nbsp;bù&nbsp;liều&nbsp;đã&nbsp;quên.</span></p><p></p><p></p>','products/g080ti6iUOFpf4TbiIBWcIkVW83ZxA7AAOx8JWzQ.jpg',0,'2025-11-20 01:54:13','2025-11-20 01:54:13',NULL),(8,'50134437',NULL,'Siro ATessen An Thiên hỗ trợ giảm ho khan, ho không đờm, ho mạn tính','ATessen',4,37000.00,50000.00,0.00,0,0,'893100202624','Guaifenesin, Oxomemazin hydroclorid','Trong mỗi 5ml (1 ống) chứa',1,'Hộp 30 Ống x 5 ml',6,'Việt Nam',200,500,1,0.00,'Hộp',1,'<h3>Ưu&nbsp;điểm</h3><p></p><ul><li><strong>Dạng&nbsp;bào&nbsp;chế&nbsp;tiện&nbsp;lợi:</strong>&nbsp;Đóng&nbsp;gói&nbsp;dạng&nbsp;<strong>ống&nbsp;$5&nbsp;\\text{&nbsp;ml}$</strong>&nbsp;(Siro/Dung&nbsp;dịch&nbsp;uống)&nbsp;rất&nbsp;tiện&nbsp;lợi&nbsp;cho&nbsp;việc&nbsp;định&nbsp;liều&nbsp;chính&nbsp;xác&nbsp;(đặc&nbsp;biệt&nbsp;cho&nbsp;trẻ&nbsp;em)&nbsp;và&nbsp;dễ&nbsp;mang&nbsp;theo.</li><li><strong>Hoạt&nbsp;chất&nbsp;đa&nbsp;tác&nbsp;dụng:</strong>&nbsp;Kết&nbsp;hợp&nbsp;<strong>Oxomemazin</strong>&nbsp;(kháng&nbsp;histamin&nbsp;H1,&nbsp;chống&nbsp;dị&nbsp;ứng,&nbsp;an&nbsp;thần&nbsp;nhẹ)&nbsp;và&nbsp;<strong>Guaifenesin</strong>&nbsp;(long&nbsp;đờm/tiêu&nbsp;chất&nbsp;nhầy).&nbsp;Sự&nbsp;kết&nbsp;hợp&nbsp;này&nbsp;giúp&nbsp;giảm&nbsp;ho&nbsp;khan&nbsp;do&nbsp;dị&nbsp;ứng&nbsp;và&nbsp;hỗ&nbsp;trợ&nbsp;tống&nbsp;xuất&nbsp;đờm&nbsp;nhẹ&nbsp;(mặc&nbsp;dù&nbsp;chỉ&nbsp;định&nbsp;chính&nbsp;là&nbsp;ho&nbsp;khan,&nbsp;ho&nbsp;không&nbsp;đờm).</li></ul><p></p><h3>Nhược&nbsp;điểm&nbsp;&amp;&nbsp;Lưu&nbsp;ý&nbsp;khi&nbsp;sử&nbsp;dụng&nbsp;</h3><p></p><ul><li><strong>Tác&nbsp;dụng&nbsp;phụ&nbsp;an&nbsp;thần:</strong>&nbsp;Do&nbsp;có&nbsp;chứa&nbsp;<strong>Oxomemazin</strong>&nbsp;(một&nbsp;phenothiazin),&nbsp;thuốc&nbsp;có&nbsp;thể&nbsp;gây&nbsp;buồn&nbsp;ngủ,&nbsp;an&nbsp;thần,&nbsp;đặc&nbsp;biệt&nbsp;lúc&nbsp;bắt&nbsp;đầu&nbsp;điều&nbsp;trị.<ul><li><strong>Lưu&nbsp;ý&nbsp;phỏng&nbsp;vấn:</strong>&nbsp;Cần&nbsp;cảnh&nbsp;báo&nbsp;khách&nbsp;hàng&nbsp;<strong>không&nbsp;lái&nbsp;xe</strong>&nbsp;hoặc&nbsp;vận&nbsp;hành&nbsp;máy&nbsp;móc&nbsp;sau&nbsp;khi&nbsp;dùng&nbsp;thuốc.</li></ul></li><li><strong>Hạn&nbsp;chế&nbsp;đối&nbsp;tượng:</strong>&nbsp;Thuốc&nbsp;thường&nbsp;được&nbsp;chỉ&nbsp;định&nbsp;cho&nbsp;trẻ&nbsp;em&nbsp;<strong>trên&nbsp;2&nbsp;tuổi</strong>&nbsp;(cần&nbsp;tuân&nbsp;thủ&nbsp;liều&nbsp;dùng&nbsp;và&nbsp;chỉ&nbsp;định&nbsp;của&nbsp;bác&nbsp;sĩ).</li></ul><table style=\"border: 1px solid #000;\"><tbody><tr><td data-row=\"1\"><strong>Chống&nbsp;chỉ&nbsp;định:</strong>&nbsp;Chống&nbsp;chỉ&nbsp;định&nbsp;với&nbsp;người&nbsp;bị&nbsp;suy&nbsp;hô&nbsp;hấp&nbsp;vì&nbsp;thuốc&nbsp;có&nbsp;thể&nbsp;làm&nbsp;tăng&nbsp;độ&nbsp;quánh&nbsp;chất&nbsp;tiết&nbsp;phế&nbsp;quản&nbsp;(mặc&nbsp;dù&nbsp;Guaifenesin&nbsp;có&nbsp;tác&nbsp;dụng&nbsp;ngược&nbsp;lại,&nbsp;nhưng&nbsp;Oxomemazin&nbsp;là&nbsp;yếu&nbsp;tố&nbsp;cần&nbsp;cân&nbsp;nhắc).</td></tr></tbody></table>','products/3t89A8n63maXN4UtFZCwkwEv9VpRMCpVJtNHb0eb.jpg',0,'2025-11-20 02:01:12','2025-11-20 02:01:12',NULL),(9,'28930117','52919038','eevfv',NULL,NULL,120000.00,0.00,0.00,0,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,50,200,NULL,0.00,NULL,0,NULL,NULL,0,'2025-12-13 00:42:35','2025-12-16 03:28:47','2025-12-16 03:28:47'),(10,'20236027','90032716','Viên uống Blackmores Omega Double High Strength Fish Oil','Blackmores Omega Double 90v',14,450000.00,584000.00,0.00,0,10,'Hiện tại chưa có','Dầu cá cô đặc Omega-3','EPA 360mg, DHA 240mg',1,'Hộp 1 chai 90 viên',8,'Úc (Australia)',50,200,NULL,250.00,'Hộp',1,'<h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Mô&nbsp;tả&nbsp;viên&nbsp;uống&nbsp;Blackmores&nbsp;Omega&nbsp;Double&nbsp;High&nbsp;Strength&nbsp;Fish&nbsp;Oil</strong></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Blackmores&nbsp;Omega&nbsp;Double&nbsp;High&nbsp;Strength&nbsp;Fish&nbsp;Oil&nbsp;là&nbsp;thực&nbsp;phẩm&nbsp;giúp&nbsp;bổ&nbsp;sung&nbsp;Omega-3&nbsp;cho&nbsp;cơ&nbsp;thể.&nbsp;Sản&nbsp;phẩm&nbsp;này&nbsp;phù&nbsp;hợp&nbsp;sử&nbsp;dụng&nbsp;cho&nbsp;người&nbsp;trưởng&nbsp;thành&nbsp;trên&nbsp;18&nbsp;tuổi.&nbsp;Hỗ&nbsp;trợ&nbsp;tăng&nbsp;cường&nbsp;sức&nbsp;khỏe&nbsp;tim&nbsp;mạch&nbsp;và&nbsp;duy&nbsp;trì&nbsp;phát&nbsp;triển&nbsp;não&nbsp;bộ.&nbsp;Góp&nbsp;phần&nbsp;cải&nbsp;thiện&nbsp;và&nbsp;hỗ&nbsp;trợ&nbsp;ngăn&nbsp;ngừa&nbsp;các&nbsp;vấn&nbsp;đề&nbsp;về&nbsp;thị&nbsp;giác.&nbsp;Nuôi&nbsp;dưỡng&nbsp;và&nbsp;chăm&nbsp;sóc&nbsp;làn&nbsp;da&nbsp;khỏe&nbsp;đẹp&nbsp;từ&nbsp;sâu&nbsp;bên&nbsp;trong.</span></p><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Mô&nbsp;tả&nbsp;viên&nbsp;uống&nbsp;Blackmores&nbsp;Omega&nbsp;Double&nbsp;High&nbsp;Strength&nbsp;Fish&nbsp;Oil</strong></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Blackmores&nbsp;Omega&nbsp;Double&nbsp;High&nbsp;Strength&nbsp;Fish&nbsp;Oil&nbsp;là&nbsp;thực&nbsp;phẩm&nbsp;giúp&nbsp;bổ&nbsp;sung&nbsp;Omega-3&nbsp;cho&nbsp;cơ&nbsp;thể.&nbsp;Sản&nbsp;phẩm&nbsp;này&nbsp;phù&nbsp;hợp&nbsp;sử&nbsp;dụng&nbsp;cho&nbsp;người&nbsp;trưởng&nbsp;thành&nbsp;trên&nbsp;18&nbsp;tuổi.&nbsp;Hỗ&nbsp;trợ&nbsp;tăng&nbsp;cường&nbsp;sức&nbsp;khỏe&nbsp;tim&nbsp;mạch&nbsp;và&nbsp;duy&nbsp;trì&nbsp;phát&nbsp;triển&nbsp;não&nbsp;bộ.&nbsp;Góp&nbsp;phần&nbsp;cải&nbsp;thiện&nbsp;và&nbsp;hỗ&nbsp;trợ&nbsp;ngăn&nbsp;ngừa&nbsp;các&nbsp;vấn&nbsp;đề&nbsp;về&nbsp;thị&nbsp;giác.&nbsp;Nuôi&nbsp;dưỡng&nbsp;và&nbsp;chăm&nbsp;sóc&nbsp;làn&nbsp;da&nbsp;khỏe&nbsp;đẹp&nbsp;từ&nbsp;sâu&nbsp;bên&nbsp;trong.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">&nbsp;</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\"><img src=\"https://prod-cdn.pharmacity.io/e-com/images/ecommerce/20240228034337-0-mceu_78174693821709091771044.png\"></span></p><p><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Blackmores&nbsp;Omega&nbsp;Double&nbsp;là&nbsp;gì?&nbsp;Công&nbsp;dụng,&nbsp;cách&nbsp;dùng&nbsp;đúng&nbsp;Blackmores&nbsp;Omega&nbsp;Double</strong></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Blackmores&nbsp;Omega&nbsp;Double&nbsp;High&nbsp;Strength&nbsp;Fish&nbsp;Oil&nbsp;là&nbsp;thực&nbsp;phẩm&nbsp;giúp&nbsp;bổ&nbsp;sung&nbsp;Omega-3&nbsp;cho&nbsp;cơ&nbsp;thể.&nbsp;Sản&nbsp;phẩm&nbsp;này&nbsp;phù&nbsp;hợp&nbsp;dùng&nbsp;cho&nbsp;người&nbsp;trưởng&nbsp;thành&nbsp;trên&nbsp;18&nbsp;tuổi.&nbsp;Hỗ&nbsp;trợ&nbsp;tăng&nbsp;cường&nbsp;sức&nbsp;khỏe&nbsp;tim&nbsp;mạch&nbsp;và&nbsp;duy&nbsp;trì&nbsp;phát&nbsp;triển&nbsp;não&nbsp;bộ.&nbsp;Góp&nbsp;phần&nbsp;cải&nbsp;thiện&nbsp;và&nbsp;hỗ&nbsp;trợ&nbsp;ngăn&nbsp;ngừa&nbsp;các&nbsp;vấn&nbsp;đề&nbsp;về&nbsp;thị&nbsp;giác.&nbsp;Nuôi&nbsp;dưỡng&nbsp;và&nbsp;chăm&nbsp;sóc&nbsp;làn&nbsp;da&nbsp;khỏe&nbsp;đẹp&nbsp;từ&nbsp;sâu&nbsp;bên&nbsp;trong.</span></p><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Thành&nbsp;phần&nbsp;của&nbsp;Blackmores&nbsp;Omega&nbsp;Double&nbsp;High&nbsp;Strength&nbsp;Fish&nbsp;Oil</strong></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Trong&nbsp;1&nbsp;viên&nbsp;nang&nbsp;mềm&nbsp;chứa:</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Dầu&nbsp;cá&nbsp;omega-3&nbsp;triglycerides&nbsp;cô&nbsp;đặc&nbsp;1g&nbsp;(1000mg)&nbsp;chứa&nbsp;600mg&nbsp;omega-3&nbsp;marine&nbsp;triglycerides&nbsp;bao&nbsp;gồm&nbsp;Eicosapentaenoic&nbsp;axit&nbsp;(EPA)&nbsp;360mg,&nbsp;Docosahexaenoic&nbsp;axit&nbsp;(DHA)&nbsp;240mg.</span></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Phụ&nbsp;liệu:&nbsp;Gelatin,&nbsp;glycerol,&nbsp;nước&nbsp;tinh&nbsp;khiết,&nbsp;vanillin.</span></p><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Công&nbsp;dụng</strong></h3><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Bổ&nbsp;sung&nbsp;Omega&nbsp;cho&nbsp;cơ&nbsp;thể,&nbsp;hỗ&nbsp;trợ&nbsp;tim,&nbsp;mắt&nbsp;và&nbsp;da</span></p><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Hướng&nbsp;dẫn&nbsp;cách&nbsp;dùng&nbsp;và&nbsp;đối&nbsp;tượng&nbsp;sử&nbsp;dụng</strong></h3><p><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Hướng&nbsp;dẫn&nbsp;sử&nbsp;dụng:</strong></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Sử&nbsp;dụng&nbsp;1&nbsp;viên/ngày&nbsp;kết&nbsp;hợp&nbsp;với&nbsp;chế&nbsp;độ&nbsp;ăn&nbsp;uống&nbsp;lành&nbsp;mạnh&nbsp;hoặc&nbsp;dùng&nbsp;theo&nbsp;chỉ&nbsp;định&nbsp;của&nbsp;chuyên&nbsp;gia&nbsp;y&nbsp;tế.</span></p><p><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Đối&nbsp;tượng&nbsp;sử&nbsp;dụng:</strong></p><ul><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Những&nbsp;người&nbsp;thiếu&nbsp;hụt&nbsp;Omega-3&nbsp;hoặc&nbsp;cần&nbsp;bổ&nbsp;sung&nbsp;Omega-3&nbsp;hàng&nbsp;ngày.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Học&nbsp;sinh,&nbsp;sinh&nbsp;viên&nbsp;bị&nbsp;áp&nbsp;lực&nbsp;học&nbsp;hành,&nbsp;thi&nbsp;cử.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Nhân&nbsp;viên&nbsp;văn&nbsp;phòng&nbsp;ngồi&nbsp;máy&nbsp;tính&nbsp;nhiều&nbsp;và&nbsp;xuất&nbsp;hiện&nbsp;hiện&nbsp;tượng&nbsp;mỏi&nbsp;hay&nbsp;đau&nbsp;mắt.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Người&nbsp;thường&nbsp;xuyên&nbsp;phải&nbsp;thức&nbsp;khuya&nbsp;hoặc&nbsp;có&nbsp;dấu&nbsp;hiệu&nbsp;căng&nbsp;thẳng,&nbsp;stress,&nbsp;đau&nbsp;đầu,&nbsp;chóng&nbsp;mặt.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Người&nbsp;lớn&nbsp;có&nbsp;trí&nbsp;nhớ&nbsp;kém,&nbsp;thị&nbsp;lực&nbsp;suy&nbsp;giảm&nbsp;hay&nbsp;tình&nbsp;trạng&nbsp;không&nbsp;minh&nbsp;mẫn.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Trẻ&nbsp;em&nbsp;muốn&nbsp;bổ&nbsp;sung&nbsp;Omega-3&nbsp;để&nbsp;tăng&nbsp;cường&nbsp;trí&nbsp;thông&nbsp;minh.&nbsp;Giúp&nbsp;đôi&nbsp;mắt&nbsp;sáng&nbsp;khỏe&nbsp;và&nbsp;đạt&nbsp;kết&nbsp;quả&nbsp;học&nbsp;tập&nbsp;cao.</span></li></ul><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Một&nbsp;số&nbsp;lưu&nbsp;ý&nbsp;cần&nbsp;nắm&nbsp;rõ&nbsp;trước&nbsp;khi&nbsp;sử&nbsp;dụng&nbsp;viên&nbsp;uống&nbsp;dầu&nbsp;cá</strong></h3><ul><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Không&nbsp;sử&nbsp;dụng&nbsp;cho&nbsp;người&nbsp;mẫn&nbsp;cảm&nbsp;với&nbsp;bất&nbsp;kỳ&nbsp;thành&nbsp;phần&nbsp;nào&nbsp;của&nbsp;sản&nbsp;phẩm.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Trẻ&nbsp;dưới&nbsp;18&nbsp;tuổi&nbsp;chỉ&nbsp;được&nbsp;sử&nbsp;dụng&nbsp;khi&nbsp;có&nbsp;chỉ&nbsp;định&nbsp;của&nbsp;chuyên&nbsp;gia&nbsp;y&nbsp;tế.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Chỉ&nbsp;nên&nbsp;hỗ&nbsp;trợ&nbsp;bổ&nbsp;sung&nbsp;sản&nbsp;phẩm&nbsp;khi&nbsp;chế&nbsp;độ&nbsp;ăn&nbsp;uống&nbsp;không&nbsp;đáp&nbsp;ứng&nbsp;đầy&nbsp;đủ&nbsp;dinh&nbsp;dưỡng.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Sản&nbsp;phẩm&nbsp;không&nbsp;chứa&nbsp;men,&nbsp;gluten,&nbsp;lúa&nbsp;mì,&nbsp;các&nbsp;chế&nbsp;phẩm&nbsp;từ&nbsp;sữa&nbsp;hay&nbsp;chất&nbsp;bảo&nbsp;quản.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Viên&nbsp;uống&nbsp;dầu&nbsp;cá&nbsp;Blackmores&nbsp;không&nbsp;chứa&nbsp;phẩm&nbsp;màu,&nbsp;hương&nbsp;liệu&nbsp;và&nbsp;chất&nbsp;tạo&nbsp;ngọt&nbsp;nhân&nbsp;tạo.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Sản&nbsp;phẩm&nbsp;Blackmores&nbsp;có&nbsp;chứa&nbsp;các&nbsp;chế&nbsp;phẩm&nbsp;từ&nbsp;cá,&nbsp;sulfites&nbsp;và&nbsp;đậu&nbsp;nành.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Đọc&nbsp;kỹ&nbsp;hướng&nbsp;dẫn&nbsp;sử&nbsp;dụng&nbsp;trước&nbsp;khi&nbsp;dùng.</span></li></ul><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Hướng&nbsp;dẫn&nbsp;bảo&nbsp;quản&nbsp;sản&nbsp;phẩm&nbsp;Blackmores&nbsp;Omega&nbsp;Double&nbsp;High&nbsp;Strength&nbsp;Fish&nbsp;Oil</strong></h3><ul><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Bảo&nbsp;quản&nbsp;sản&nbsp;phẩm&nbsp;ở&nbsp;nơi&nbsp;khô&nbsp;ráo,&nbsp;thoáng&nbsp;mát.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Bảo&nbsp;quản&nbsp;ở&nbsp;nhiệt&nbsp;độ&nbsp;dưới&nbsp;30&nbsp;độ&nbsp;C.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Hạn&nbsp;chế&nbsp;để&nbsp;sản&nbsp;phẩm&nbsp;tiếp&nbsp;xúc&nbsp;trực&nbsp;tiếp&nbsp;với&nbsp;ánh&nbsp;nắng&nbsp;mặt&nbsp;trời.</span></li><li><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Để&nbsp;xa&nbsp;tầm&nbsp;tay&nbsp;trẻ&nbsp;em</span></li></ul><h3><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Thông&nbsp;tin&nbsp;sản&nbsp;xuất&nbsp;của&nbsp;viên&nbsp;uống&nbsp;dầu&nbsp;cá</strong></h3><p><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Quy&nbsp;cách&nbsp;đóng&nbsp;gói:</strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">&nbsp;90&nbsp;viên&nbsp;nang&nbsp;mềm/lọ.</span></p><p><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Thương&nbsp;hiệu:</strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">&nbsp;Blackmores</span></p><p><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Nơi&nbsp;sản&nbsp;xuất:&nbsp;</strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Australia&nbsp;(Úc)</span></p><p><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Công&nbsp;ty&nbsp;chịu&nbsp;trách&nbsp;nhiệm&nbsp;và&nbsp;phân&nbsp;phối&nbsp;sản&nbsp;phẩm</strong></p><p><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">CÔNG&nbsp;TY&nbsp;TNHH&nbsp;DỊCH&nbsp;VỤ&nbsp;VÀ&nbsp;THƯƠNG&nbsp;MẠI&nbsp;MESA</span></p><p><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Số&nbsp;Giấy&nbsp;công&nbsp;bố:&nbsp;</strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">2/2020/0100520429-DKCB</span></p><p><strong style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">Số&nbsp;Giấy&nbsp;XNQC:</strong><span style=\"background-color: rgb(255, 255, 255); color: rgb(43, 43, 43);\">&nbsp;1319/2022/XNQC</span></p><p></p>','products/RFy4IS9bMdlrhjGMiPdKaloTZwuVXlDs8pA2BH1w.jpg',0,'2025-12-19 03:49:56','2025-12-24 00:51:22',NULL);
/*!40000 ALTER TABLE `medicines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2025_07_22_081959_create_users_table',1),(2,'2025_07_22_082018_create_password_reset_tokens_table',1),(3,'2025_07_22_082034_create_sessions_table',1),(4,'2025_07_23_000000_make_address_fields_nullable_in_users_table',1),(42,'2025_08_17_092156_create_product_categories_table',2),(43,'2025_08_17_092213_create_manufacturers_table',2),(44,'2025_08_17_092229_create_positions_table',2),(45,'2025_08_17_092245_create_drug_routes_table',2),(46,'2025_08_17_092303_create_medicines_table',2),(47,'2025_08_17_092324_create_goods_table',2),(48,'2025_08_18_034801_add_sort_order_to_product_categories_table',2),(49,'2025_08_18_103304_create_cache_table',2),(50,'2025_08_19_091016_create_supplier_categories_table',2),(51,'2025_08_20_042558_create_suppliers_table',2),(52,'2025_09_06_110657_add_ten_viet_tat_to_goods_table',2),(53,'2025_09_06_125018_create_carts_table',2),(54,'2025_09_09_092629_create_orders_table',2),(55,'2025_09_09_092650_create_order_items_table',2),(56,'2025_09_27_091122_add_ton_kho_to_medicines_table',2),(57,'2025_09_27_094719_create_stock_imports_table',2),(58,'2025_09_27_095722_create_stock_import_items_table',2),(59,'2025_09_27_095739_create_stock_import_payments_table',2),(60,'2025_09_30_101549_add_completed_at_to',2),(61,'2025_09_30_101601_add_completed_at_to_orders_table',2),(62,'2025_10_06_085410_add_total_discount_to_stock_imports_table',2),(63,'2025_10_06_093937_create_doctors_table',2),(64,'2025_10_11_094505_create_purchase_returns_table',2),(65,'2025_10_11_094534_create_purchase_return_items_table',2),(66,'2025_10_11_094552_create_purchase_return_payments_table',2),(67,'2025_10_19_072705_add_description_to_drug_routes_manufacturers_positions_tables',2),(68,'2025_10_21_092830_create_product_reviews_table',2),(69,'2025_10_24_000000_create_services_table',3),(70,'2025_10_25_175030_create_service_bookings_table',3),(71,'2025_11_05_085714_create_job_titles_table',3),(72,'2025_11_05_085748_create_departments_table',3),(73,'2025_11_05_085804_create_branches_table',3),(74,'2025_11_05_085819_create_employees_table',3),(75,'2025_11_05_085838_create_employee_allowances_table',3),(76,'2025_11_05_085852_create_employee_targets_table',3),(77,'2025_11_05_085907_create_employee_deductions_table',3),(78,'2025_11_05_085927_create_shifts_table',3),(79,'2025_11_05_085954_create_employee_schedules_table',3),(80,'2025_11_06_081221_make_address_nullable_in_users_table',4),(81,'2025_11_07_093653_add_firebase_fields_to_users_table',5),(83,'2025_11_08_024655_add_is_promotion_to_carts_table',6),(84,'2025_11_08_025014_update_carts_unique_constraint_to_include_is_promotion',6),(85,'2025_11_08_025331_add_is_promotion_and_price_at_purchase_to_order_items_table',7),(86,'2025_11_08_025714_add_promotion_fields_to_medicines_table',8),(87,'2025_11_08_025742_add_promotion_fields_to_goods_table',9),(88,'2025_11_09_185741_add_image_to_order_items_table',10),(89,'2025_11_10_100321_create_notifications_table',11),(90,'2025_11_17_081058_add_cancellation_fields_to_orders_table',12),(91,'2025_11_17_100000_add_status_before_cancellation_to_orders_table',13),(92,'2025_11_17_184006_add_ghn_fields_to_orders_table',14),(93,'2025_11_17_185417_add_district_ward_codes_to_orders_table',15),(95,'2025_12_04_092334_add_soft_deletes_to_medicines_and_goods_tables',16),(96,'2025_12_24_085459_create_support_tickets_table',17),(97,'2025_12_27_100506_add_shipping_fee_and_ghn_fee_to_orders_table',18);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES ('0e9ad9db-a6b7-40ad-8ca4-7c5dd04b133a','App\\Notifications\\OrderCancellationProcessed','App\\Models\\User',20,'{\"type\":\"order_cancellation_processed\",\"order_id\":19,\"order_code\":\"3112\",\"status\":\"approved\",\"status_label\":\"\\u0110\\u00e3 ch\\u1ea5p nh\\u1eadn h\\u1ee7y\",\"admin_note\":null,\"message\":\"Y\\u00eau c\\u1ea7u h\\u1ee7y \\u0111\\u01a1n #3112: \\u0110\\u00e3 ch\\u1ea5p nh\\u1eadn h\\u1ee7y.\",\"url\":\"\\/user\\/orders\\/19\",\"icon\":\"fa-regular fa-circle-check\"}','2025-11-17 02:51:52','2025-11-17 02:51:47','2025-11-17 02:51:52'),('23b82526-c46f-4b3f-822e-08c2627976ac','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',26,'{\"type\":\"order_status_updated\",\"order_id\":82,\"order_code\":\"1491\",\"old_status\":\"new\",\"new_status\":\"cancelled\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #1491 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ch\\u1edd x\\u1eed l\\u00fd\' sang \'\\u0110\\u00e3 h\\u1ee7y\'\",\"url\":\"\\/user\\/orders\\/82\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-12-14 08:59:03\"}','2025-12-14 01:59:10','2025-12-14 01:59:03','2025-12-14 01:59:10'),('241e24d9-1492-4a22-9782-d61a028ca0de','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',20,'{\"type\":\"order_status_updated\",\"order_id\":19,\"order_code\":\"3112\",\"old_status\":\"cancellation_requested\",\"new_status\":\"cancelled\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #3112 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'cancellation_requested\' sang \'\\u0110\\u00e3 h\\u1ee7y\'\",\"url\":\"\\/user\\/orders\\/19\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-17 09:33:30\"}','2025-11-17 02:33:46','2025-11-17 02:33:30','2025-11-17 02:33:46'),('283cc32f-1d10-4320-82a9-20b43be8a9ab','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',21,'{\"type\":\"order_status_updated\",\"order_id\":52,\"order_code\":\"6123\",\"old_status\":\"confirmed\",\"new_status\":\"cancelled\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #6123 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\' sang \'\\u0110\\u00e3 h\\u1ee7y\'\",\"url\":\"\\/user\\/orders\\/52\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-12-14 07:58:38\"}','2025-12-14 06:41:54','2025-12-14 00:58:38','2025-12-14 06:41:54'),('2a416870-36f2-4460-8a2c-519906f5052b','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',26,'{\"type\":\"order_status_updated\",\"order_id\":81,\"order_code\":\"9404\",\"old_status\":\"new\",\"new_status\":\"confirmed\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #9404 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ch\\u1edd x\\u1eed l\\u00fd\' sang \'\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\'\",\"url\":\"\\/user\\/orders\\/81\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-12-14 08:28:00\"}','2025-12-14 01:28:08','2025-12-14 01:28:00','2025-12-14 01:28:08'),('33fa2064-0b70-42be-97b2-ca29c7517720','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',21,'{\"type\":\"order_status_updated\",\"order_id\":52,\"order_code\":\"6123\",\"old_status\":\"new\",\"new_status\":\"confirmed\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #6123 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ch\\u1edd x\\u1eed l\\u00fd\' sang \'\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\'\",\"url\":\"\\/user\\/orders\\/52\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-24 09:08:29\"}','2025-11-24 02:10:17','2025-11-24 02:08:29','2025-11-24 02:10:17'),('403c7a0f-1c16-46e7-8b6f-00540615668d','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',21,'{\"type\":\"order_status_updated\",\"order_id\":30,\"order_code\":\"6416\",\"old_status\":\"new\",\"new_status\":\"cancelled\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #6416 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ch\\u1edd x\\u1eed l\\u00fd\' sang \'\\u0110\\u00e3 h\\u1ee7y\'\",\"url\":\"\\/user\\/orders\\/30\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-18 10:21:15\"}','2025-11-18 03:21:24','2025-11-18 03:21:15','2025-11-18 03:21:24'),('59832104-759a-487f-9217-e255503c0030','App\\Notifications\\OrderCancellationRequested','App\\Models\\User',2,'{\"type\":\"order_cancellation_requested\",\"order_id\":19,\"order_code\":\"3112\",\"customer_name\":\"Hungkhi\",\"reason\":\"other\",\"note\":\"zffsf\",\"requested_at\":\"2025-11-17 09:13:28\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #3112 v\\u1eeba \\u0111\\u01b0\\u1ee3c y\\u00eau c\\u1ea7u h\\u1ee7y. Vui l\\u00f2ng ki\\u1ec3m tra.\",\"url\":\"http:\\/\\/suckhoe24h.test\\/admin\\/orders?status=cancellation_requested\",\"icon\":\"fa-regular fa-circle-question\"}','2025-12-17 10:43:19','2025-11-17 02:13:30','2025-12-17 10:43:19'),('5a53e286-9440-42a1-aa9e-21a82002922f','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',21,'{\"type\":\"order_status_updated\",\"order_id\":31,\"order_code\":\"1855\",\"old_status\":\"new\",\"new_status\":\"confirmed\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #1855 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ch\\u1edd x\\u1eed l\\u00fd\' sang \'\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\'\",\"url\":\"\\/user\\/orders\\/31\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-19 08:04:59\"}','2025-11-19 01:07:54','2025-11-19 01:04:59','2025-11-19 01:07:54'),('5ebe2ecc-c4de-4d5d-94bd-b9ff6fb883d8','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',20,'{\"type\":\"order_status_updated\",\"order_id\":22,\"order_code\":\"2084\",\"old_status\":\"completed\",\"new_status\":\"confirmed\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #2084 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ho\\u00e0n th\\u00e0nh\' sang \'\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\'\",\"url\":\"\\/user\\/orders\\/22\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-17 10:08:51\"}','2025-11-17 10:06:52','2025-11-17 03:08:51','2025-11-17 10:06:52'),('67f813c0-0f41-48d7-9af2-2929922859b8','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',1,'{\"type\":\"order_status_updated\",\"order_id\":57,\"order_code\":\"8177\",\"old_status\":\"new\",\"new_status\":\"cancelled\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #8177 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ch\\u1edd x\\u1eed l\\u00fd\' sang \'\\u0110\\u00e3 h\\u1ee7y\'\",\"url\":\"\\/user\\/orders\\/57\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-27 08:27:28\"}','2025-12-27 03:36:02','2025-11-27 01:27:28','2025-12-27 03:36:02'),('681138a0-9e69-4da0-bc43-c5948d2962da','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',1,'{\"type\":\"order_status_updated\",\"order_id\":16,\"order_code\":\"8421\",\"old_status\":\"cancelled\",\"new_status\":\"completed\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #8421 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'\\u0110\\u00e3 h\\u1ee7y\' sang \'Ho\\u00e0n th\\u00e0nh\'\",\"url\":\"\\/user\\/orders\\/16\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-10 10:36:18\"}','2025-11-10 03:36:27','2025-11-10 03:36:18','2025-11-10 03:36:27'),('6e4e3c06-0530-4d31-9933-484cc12852f0','App\\Notifications\\OrderCancellationRequested','App\\Models\\User',2,'{\"type\":\"order_cancellation_requested\",\"order_id\":21,\"order_code\":\"9547\",\"customer_name\":\"Hungkhi\",\"reason\":\"found_better\",\"note\":\"dadadadad\",\"requested_at\":\"2025-11-17 10:05:50\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #9547 v\\u1eeba \\u0111\\u01b0\\u1ee3c y\\u00eau c\\u1ea7u h\\u1ee7y. Vui l\\u00f2ng ki\\u1ec3m tra.\",\"url\":\"http:\\/\\/suckhoe24h.test\\/admin\\/orders?status=cancellation_requested\",\"icon\":\"fa-regular fa-circle-question\"}','2025-12-17 10:43:19','2025-11-17 03:05:50','2025-12-17 10:43:19'),('72113f29-bc1f-4976-af9d-5ae23dc46cd5','App\\Notifications\\ServiceBookingStatusUpdated','App\\Models\\User',21,'{\"type\":\"service_booking_status_updated\",\"booking_id\":3,\"service_name\":\"Kh\\u00e1m s\\u1ee9c kh\\u1ecfe t\\u1ed5ng qu\\u00e1t cho tr\\u1ebb em\",\"old_status\":\"confirmed\",\"new_status\":\"cancelled\",\"message\":\"D\\u1ecbch v\\u1ee5 \'Kh\\u00e1m s\\u1ee9c kh\\u1ecfe t\\u1ed5ng qu\\u00e1t cho tr\\u1ebb em\' \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\' sang \'\\u0110\\u00e3 h\\u1ee7y\'\",\"url\":\"\\/user\\/services\\/3\",\"icon\":\"fas fa-file-medical\",\"created_at\":\"2025-12-28 09:30:24\"}','2025-12-28 02:30:49','2025-12-28 02:30:24','2025-12-28 02:30:49'),('78d5e591-75fb-4e5f-9e2d-e0fbf5e7f5bf','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',1,'{\"type\":\"order_status_updated\",\"order_id\":16,\"order_code\":\"8421\",\"old_status\":\"new\",\"new_status\":\"completed\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #8421 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ch\\u1edd x\\u1eed l\\u00fd\' sang \'Ho\\u00e0n th\\u00e0nh\'\",\"url\":\"\\/user\\/orders\\/16\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-10 10:35:42\"}','2025-11-10 03:35:48','2025-11-10 03:35:42','2025-11-10 03:35:48'),('7fa620e5-9372-460d-bc89-db9b10a58b29','App\\Notifications\\OrderCancellationProcessed','App\\Models\\User',20,'{\"type\":\"order_cancellation_processed\",\"order_id\":20,\"order_code\":\"9245\",\"status\":\"rejected\",\"status_label\":\"B\\u1ecb t\\u1eeb ch\\u1ed1i h\\u1ee7y\",\"admin_note\":\"\\u0110\\u01a1n h\\u00e0ng b\\u1ea1n \\u0111\\u00e3 ship r\\u1ed3i kh\\u00f4ng th\\u1ec3 h\\u1ee7y \\u0111\\u01b0\\u1ee3c n\\u1eefa , mong b th\\u00f4ng c\\u1ea3m\",\"message\":\"Y\\u00eau c\\u1ea7u h\\u1ee7y \\u0111\\u01a1n #9245: B\\u1ecb t\\u1eeb ch\\u1ed1i h\\u1ee7y.\",\"url\":\"\\/user\\/orders\\/20\",\"icon\":\"fa-regular fa-circle-xmark\"}','2025-11-17 02:53:27','2025-11-17 02:53:25','2025-11-17 02:53:27'),('8986be03-d4d9-4906-a2ca-b3f939880842','App\\Notifications\\OrderCancellationProcessed','App\\Models\\User',21,'{\"type\":\"order_cancellation_processed\",\"order_id\":32,\"order_code\":\"2478\",\"status\":\"approved\",\"status_label\":\"\\u0110\\u00e3 ch\\u1ea5p nh\\u1eadn h\\u1ee7y\",\"admin_note\":null,\"message\":\"Y\\u00eau c\\u1ea7u h\\u1ee7y \\u0111\\u01a1n #2478: \\u0110\\u00e3 ch\\u1ea5p nh\\u1eadn h\\u1ee7y.\",\"url\":\"\\/user\\/orders\\/32\",\"icon\":\"fa-regular fa-circle-check\"}','2025-11-19 01:18:29','2025-11-19 01:18:20','2025-11-19 01:18:29'),('89c77c2f-d732-41d7-bafa-f9a90aa1cc13','App\\Notifications\\OrderCancellationRequested','App\\Models\\User',2,'{\"type\":\"order_cancellation_requested\",\"order_id\":32,\"order_code\":\"2478\",\"customer_name\":\"B\\u00e1nh m\\u00ec b\\u01a1 s\\u1eefa\",\"reason\":\"wrong_product\",\"note\":null,\"requested_at\":\"2025-11-19 08:17:52\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #2478 v\\u1eeba \\u0111\\u01b0\\u1ee3c y\\u00eau c\\u1ea7u h\\u1ee7y. Vui l\\u00f2ng ki\\u1ec3m tra.\",\"url\":\"http:\\/\\/suckhoe24h.test\\/admin\\/orders?status=cancellation_requested\",\"icon\":\"fa-regular fa-circle-question\"}','2025-12-17 10:43:19','2025-11-19 01:17:52','2025-12-17 10:43:19'),('9769d6c1-59a4-4083-ac5e-b65a556ac06a','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',20,'{\"type\":\"order_status_updated\",\"order_id\":22,\"order_code\":\"2084\",\"old_status\":\"confirmed\",\"new_status\":\"completed\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #2084 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\' sang \'Ho\\u00e0n th\\u00e0nh\'\",\"url\":\"\\/user\\/orders\\/22\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-17 10:08:25\"}','2025-11-17 03:08:34','2025-11-17 03:08:25','2025-11-17 03:08:34'),('991ac7e6-8f42-40f1-ae23-c33906e0d9d8','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',20,'{\"type\":\"order_status_updated\",\"order_id\":20,\"order_code\":\"9245\",\"old_status\":\"cancellation_requested\",\"new_status\":\"confirmed\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #9245 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'cancellation_requested\' sang \'\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\'\",\"url\":\"\\/user\\/orders\\/20\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-17 09:52:53\"}','2025-11-17 02:52:58','2025-11-17 02:52:53','2025-11-17 02:52:58'),('a4ad1d63-51d7-4c05-a7c2-bc9829ca2a0d','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',1,'{\"type\":\"order_status_updated\",\"order_id\":18,\"order_code\":\"5480\",\"old_status\":\"completed\",\"new_status\":\"confirmed\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #5480 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ho\\u00e0n th\\u00e0nh\' sang \'\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\'\",\"url\":\"\\/user\\/orders\\/18\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-15 10:32:35\"}','2025-11-15 03:33:36','2025-11-15 03:32:35','2025-11-15 03:33:36'),('ad3d2e8d-b255-4d46-8229-06218feca872','App\\Notifications\\ServiceBookingStatusUpdated','App\\Models\\User',21,'{\"type\":\"service_booking_status_updated\",\"booking_id\":3,\"service_name\":\"Kh\\u00e1m s\\u1ee9c kh\\u1ecfe t\\u1ed5ng qu\\u00e1t cho tr\\u1ebb em\",\"old_status\":\"pending\",\"new_status\":\"confirmed\",\"message\":\"D\\u1ecbch v\\u1ee5 \'Kh\\u00e1m s\\u1ee9c kh\\u1ecfe t\\u1ed5ng qu\\u00e1t cho tr\\u1ebb em\' \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ch\\u1edd x\\u00e1c nh\\u1eadn\' sang \'\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\'\",\"url\":\"\\/user\\/services\\/3\",\"icon\":\"fas fa-file-medical\",\"created_at\":\"2025-12-19 11:35:49\"}','2025-12-20 02:33:44','2025-12-19 04:35:49','2025-12-20 02:33:44'),('b89fc0a8-84d3-4cdc-a4c3-f1d2828eb338','App\\Notifications\\OrderCancellationRequested','App\\Models\\User',2,'{\"type\":\"order_cancellation_requested\",\"order_id\":20,\"order_code\":\"9245\",\"customer_name\":\"Hungkhi\",\"reason\":\"wrong_product\",\"note\":\"ddatj ho\",\"requested_at\":\"2025-11-17 09:52:24\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #9245 v\\u1eeba \\u0111\\u01b0\\u1ee3c y\\u00eau c\\u1ea7u h\\u1ee7y. Vui l\\u00f2ng ki\\u1ec3m tra.\",\"url\":\"http:\\/\\/suckhoe24h.test\\/admin\\/orders?status=cancellation_requested\",\"icon\":\"fa-regular fa-circle-question\"}','2025-12-17 10:43:19','2025-11-17 02:52:24','2025-12-17 10:43:19'),('b8d54bce-ac18-4b6f-9203-2269bdf2bc0d','App\\Notifications\\OrderCancellationRequested','App\\Models\\User',2,'{\"type\":\"order_cancellation_requested\",\"order_id\":23,\"order_code\":\"1498\",\"customer_name\":\"Hungkhi\",\"reason\":\"wrong_product\",\"note\":\"adad\",\"requested_at\":\"2025-11-17 10:09:31\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #1498 v\\u1eeba \\u0111\\u01b0\\u1ee3c y\\u00eau c\\u1ea7u h\\u1ee7y. Vui l\\u00f2ng ki\\u1ec3m tra.\",\"url\":\"http:\\/\\/suckhoe24h.test\\/admin\\/orders?status=cancellation_requested\",\"icon\":\"fa-regular fa-circle-question\"}','2025-12-17 10:43:19','2025-11-17 03:09:31','2025-12-17 10:43:19'),('b9e27394-1fb5-4c2e-b39c-5649524252df','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',1,'{\"type\":\"order_status_updated\",\"order_id\":16,\"order_code\":\"8421\",\"old_status\":\"completed\",\"new_status\":\"cancelled\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #8421 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ho\\u00e0n th\\u00e0nh\' sang \'\\u0110\\u00e3 h\\u1ee7y\'\",\"url\":\"\\/user\\/orders\\/16\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-10 10:36:41\"}','2025-11-10 03:36:45','2025-11-10 03:36:41','2025-11-10 03:36:45'),('df0b3834-c1c3-4924-bdcf-3078266a8c2d','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',1,'{\"type\":\"order_status_updated\",\"order_id\":16,\"order_code\":\"8421\",\"old_status\":\"completed\",\"new_status\":\"cancelled\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #8421 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ho\\u00e0n th\\u00e0nh\' sang \'\\u0110\\u00e3 h\\u1ee7y\'\",\"url\":\"\\/user\\/orders\\/16\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-10 10:35:42\"}','2025-11-10 03:35:48','2025-11-10 03:35:42','2025-11-10 03:35:48'),('f1110957-a003-490f-8f1a-c49c47096dfb','App\\Notifications\\OrderStatusUpdated','App\\Models\\User',20,'{\"type\":\"order_status_updated\",\"order_id\":22,\"order_code\":\"2084\",\"old_status\":\"new\",\"new_status\":\"confirmed\",\"message\":\"\\u0110\\u01a1n h\\u00e0ng #2084 \\u0111\\u00e3 \\u0111\\u01b0\\u1ee3c c\\u1eadp nh\\u1eadt t\\u1eeb \'Ch\\u1edd x\\u1eed l\\u00fd\' sang \'\\u0110\\u00e3 x\\u00e1c nh\\u1eadn\'\",\"url\":\"\\/user\\/orders\\/22\",\"icon\":\"fas fa-clipboard-list\",\"created_at\":\"2025-11-17 10:06:44\"}','2025-11-17 03:08:34','2025-11-17 03:06:44','2025-11-17 03:08:34'),('fe5d5ee2-da6b-40ba-ae0e-4c54904b892e','App\\Notifications\\OrderCancellationProcessed','App\\Models\\User',20,'{\"type\":\"order_cancellation_processed\",\"order_id\":23,\"order_code\":\"1498\",\"status\":\"approved\",\"status_label\":\"\\u0110\\u00e3 ch\\u1ea5p nh\\u1eadn h\\u1ee7y\",\"admin_note\":null,\"message\":\"Y\\u00eau c\\u1ea7u h\\u1ee7y \\u0111\\u01a1n #1498: \\u0110\\u00e3 ch\\u1ea5p nh\\u1eadn h\\u1ee7y.\",\"url\":\"\\/user\\/orders\\/23\",\"icon\":\"fa-regular fa-circle-check\"}','2025-11-17 10:06:52','2025-11-17 03:09:42','2025-11-17 10:06:52');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `item_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,0) NOT NULL,
  `quantity` int NOT NULL,
  `subtotal` decimal(12,0) NOT NULL,
  `is_promotion` tinyint(1) NOT NULL DEFAULT '0',
  `price_at_purchase` decimal(12,0) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,6,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)',NULL,93000,2,186000,0,93000,'2025-11-07 19:54:06','2025-11-07 19:54:06'),(2,7,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)',NULL,93000,1,93000,0,93000,'2025-11-07 19:54:47','2025-11-07 19:54:47'),(3,8,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)',NULL,70000,1,70000,1,70000,'2025-11-08 02:08:14','2025-11-08 02:08:14'),(4,9,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)',NULL,93000,3,279000,0,93000,'2025-11-08 02:51:41','2025-11-08 02:51:41'),(5,10,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)',NULL,93000,3,279000,0,93000,'2025-11-08 02:52:33','2025-11-08 02:52:33'),(6,11,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)',NULL,93000,1,93000,0,93000,'2025-11-08 02:53:31','2025-11-08 02:53:31'),(7,12,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)',NULL,93000,1,93000,0,93000,'2025-11-09 11:02:26','2025-11-09 11:02:26'),(8,13,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)',NULL,93000,1,93000,0,93000,'2025-11-09 11:54:20','2025-11-09 11:54:20'),(9,14,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',246000,2,492000,0,246000,'2025-11-10 01:29:15','2025-11-10 01:29:15'),(10,14,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-10 01:29:15','2025-11-10 01:29:15'),(11,15,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',246000,2,492000,0,246000,'2025-11-10 01:30:25','2025-11-10 01:30:25'),(12,15,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-10 01:30:25','2025-11-10 01:30:25'),(13,16,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-10 01:31:36','2025-11-10 01:31:36'),(14,17,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',100000,1,100000,1,100000,'2025-11-12 02:11:09','2025-11-12 02:11:09'),(15,18,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-13 02:46:52','2025-11-13 02:46:52'),(16,19,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',246000,3,738000,0,246000,'2025-11-17 02:06:07','2025-11-17 02:06:07'),(17,20,5,'medicine','Hồ Đạt','products/NhfDoTSeE8ojW1607nH6ya4TJj8yjEg4g9vSNTfZ.jpg',150000,1,150000,0,150000,'2025-11-17 02:52:05','2025-11-17 02:52:05'),(19,22,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-17 03:06:38','2025-11-17 03:06:38'),(20,23,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,2,186000,0,93000,'2025-11-17 03:09:14','2025-11-17 03:09:14'),(21,24,5,'medicine','Hồ Đạt','products/NhfDoTSeE8ojW1607nH6ya4TJj8yjEg4g9vSNTfZ.jpg',150000,1,150000,0,150000,'2025-11-17 12:11:56','2025-11-17 12:11:56'),(22,25,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-17 12:24:30','2025-11-17 12:24:30'),(23,26,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-17 12:26:06','2025-11-17 12:26:06'),(24,27,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-17 12:27:39','2025-11-17 12:27:39'),(25,28,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-18 02:08:54','2025-11-18 02:08:54'),(26,29,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-18 02:41:05','2025-11-18 02:41:05'),(27,30,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-18 02:42:05','2025-11-18 02:42:05'),(28,31,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-19 01:04:36','2025-11-19 01:04:36'),(29,32,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-19 01:12:10','2025-11-19 01:12:10'),(33,36,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-20 10:46:38','2025-11-20 10:46:38'),(34,37,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-20 10:53:41','2025-11-20 10:53:41'),(49,52,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-24 02:08:01','2025-11-24 02:08:01'),(50,53,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-24 02:14:34','2025-11-24 02:14:34'),(51,54,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-24 04:21:56','2025-11-24 04:21:56'),(53,56,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,2,186000,0,93000,'2025-11-26 01:43:10','2025-11-26 01:43:10'),(54,57,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-26 02:18:01','2025-11-26 02:18:01'),(55,58,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-26 22:53:55','2025-11-26 22:53:55'),(56,59,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,2,186000,0,93000,'2025-11-28 06:04:08','2025-11-28 06:04:08'),(57,60,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-11-29 00:43:14','2025-11-29 00:43:14'),(58,61,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',150000,10,1500000,1,150000,'2025-12-01 20:37:34','2025-12-01 20:37:34'),(59,62,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',246000,1,246000,0,246000,'2025-12-02 11:56:13','2025-12-02 11:56:13'),(60,63,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',150000,1,150000,1,150000,'2025-12-04 01:20:03','2025-12-04 01:20:03'),(61,64,3,'goods','Tinh chất La Roche-Posay Hyalu B5 Serum','goods/2otYHt9v6Vvn6cUOCEX3rxPEELmym88BgdzwcHaL.jpg',1209000,1,1209000,0,1209000,'2025-12-04 03:16:34','2025-12-04 03:16:34'),(62,65,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-05 00:04:46','2025-12-05 00:04:46'),(63,66,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,3,279000,0,93000,'2025-12-05 11:27:38','2025-12-05 11:27:38'),(64,67,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',246000,1,246000,0,246000,'2025-12-06 06:27:44','2025-12-06 06:27:44'),(65,67,3,'goods','Tinh chất La Roche-Posay Hyalu B5 Serum','goods/2otYHt9v6Vvn6cUOCEX3rxPEELmym88BgdzwcHaL.jpg',1209000,1,1209000,0,1209000,'2025-12-06 06:27:44','2025-12-06 06:27:44'),(66,68,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-06 06:30:44','2025-12-06 06:30:44'),(67,69,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-12 11:23:07','2025-12-12 11:23:07'),(68,70,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',246000,1,246000,0,246000,'2025-12-13 00:44:26','2025-12-13 00:44:26'),(69,71,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-13 12:11:13','2025-12-13 12:11:13'),(70,72,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-13 12:18:37','2025-12-13 12:18:37'),(71,73,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-13 12:20:14','2025-12-13 12:20:14'),(72,74,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-13 12:20:53','2025-12-13 12:20:53'),(73,75,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,2,186000,0,93000,'2025-12-13 12:22:35','2025-12-13 12:22:35'),(74,76,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-13 12:22:57','2025-12-13 12:22:57'),(75,77,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-13 12:24:52','2025-12-13 12:24:52'),(76,78,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,2,186000,0,93000,'2025-12-13 12:29:21','2025-12-13 12:29:21'),(77,79,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-13 12:33:05','2025-12-13 12:33:05'),(78,80,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-13 12:35:45','2025-12-13 12:35:45'),(79,81,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-14 00:25:28','2025-12-14 00:25:28'),(80,82,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',246000,1,246000,0,246000,'2025-12-14 01:57:18','2025-12-14 01:57:18'),(81,83,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',246000,1,246000,0,246000,'2025-12-14 06:28:25','2025-12-14 06:28:25'),(82,84,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-16 10:39:12','2025-12-16 10:39:12'),(83,85,1,'goods','Hada Labo Advanced Nourish Hyaluron Cream','goods/1bY0IwmXWEI7kEMsqxQAUFYuOf4BdtOrFzasAajz.jpg',246000,1,246000,0,246000,'2025-12-20 04:22:47','2025-12-20 04:22:47'),(84,86,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',70000,1,70000,1,70000,'2025-12-27 01:34:02','2025-12-27 01:34:02'),(85,87,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-27 01:47:02','2025-12-27 01:47:02'),(87,89,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-27 02:48:28','2025-12-27 02:48:28'),(88,90,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-27 03:16:16','2025-12-27 03:16:16'),(89,91,1,'medicine','Siro ho Prospan Engelhard điều trị viêm phế quản mạn tính (100ml)','products/GF8SqGxYMgmJ2DYmqADQkO10huKRK2BvRyxyQqyX.jpg',93000,1,93000,0,93000,'2025-12-27 04:27:34','2025-12-27 04:27:34');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghn_order_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghn_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghn_fee` decimal(12,0) DEFAULT NULL,
  `ghn_expected_delivery_time` timestamp NULL DEFAULT NULL,
  `ghn_tracking_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghn_cod_amount` decimal(12,0) DEFAULT NULL,
  `ghn_shipper_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghn_shipper_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ghn_created_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shipping',
  `pickup_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_id` int DEFAULT NULL,
  `ward` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ward_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(12,0) NOT NULL,
  `shipping_fee` int NOT NULL DEFAULT '0' COMMENT 'Phí ship thu của khách',
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `order_status_before_cancellation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cancellation_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancellation_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancellation_user_note` text COLLATE utf8mb4_unicode_ci,
  `cancellation_admin_note` text COLLATE utf8mb4_unicode_ci,
  `cancellation_requested_at` timestamp NULL DEFAULT NULL,
  `cancellation_processed_at` timestamp NULL DEFAULT NULL,
  `cancellation_processed_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_ghn_order_code_unique` (`ghn_order_code`),
  KEY `orders_user_id_foreign` (`user_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (6,'5117',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'4IOjQiQreToF4uM7XPMnEsVpKTFivREDDpksCMJ7','Phạm Chí Trọng',NULL,'0901645269','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,186000,0,'cod','unpaid','pending',NULL,'2025-11-07 21:46:35',NULL,'Đã thanh toán','2025-11-07 19:54:06','2025-11-07 21:47:35',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'2387',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'3y59zpd74Yn5VXgbhD3kl24vPzs9LLkSamVBzIGC','Hồ Đạt Peter',NULL,'0902634252','shipping',NULL,'test','Tỉnh Bắc Giang','Huyện Sơn Động',NULL,'Xã Tuấn Đạo',NULL,93000,0,'vnpay','paid','completed',NULL,'2025-11-07 19:55:30','7_1762570487','Đã thanh toán','2025-11-07 19:54:47','2025-11-07 19:55:30',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'9932',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Trọng Chí',NULL,'0901645269','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,70000,0,'cod','paid','completed',NULL,'2025-11-08 02:08:43',NULL,'test','2025-11-08 02:08:14','2025-11-08 02:08:43',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'3997',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,14,NULL,'Tommy Quốc Anh',NULL,'0357960770','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,279000,0,'vnpay','cancelled','cancelled',NULL,NULL,'9_1762595501','Đã thanh toán','2025-11-08 02:51:41','2025-11-08 02:54:06',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'2564',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,14,NULL,'Tommy Quốc Anh',NULL,'0357960770','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,279000,0,'vnpay','paid','completed',NULL,'2025-11-08 02:53:58','10_1762595554','Đã thanh toán','2025-11-08 02:52:33','2025-11-08 02:53:58',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'5895',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,14,NULL,'Tommy Quốc Anh',NULL,'0357960770','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'cod','paid','completed',NULL,'2025-11-08 02:53:54',NULL,NULL,'2025-11-08 02:53:31','2025-11-08 02:53:54',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,'9458',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'Hồ Đạt',NULL,'0376193244','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 3',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'cod','cancelled','cancelled',NULL,NULL,NULL,'10h sáng đến thanh toán','2025-11-09 11:02:26','2025-11-09 11:49:41',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'4844',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'Hồ Đạt',NULL,'0376193244','shipping',NULL,'test','Tỉnh Bắc Giang','Thị xã Việt Yên',NULL,'Phường Vân Trung',NULL,93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-11-09 11:54:20','2025-11-09 11:54:20',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,'8288',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1J1wTm712rotfqvSQVkSGO9mbbELR256FVqNDYI8','Hồ Đạt Peter',NULL,'0904490909','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,585000,0,'cod','pending','new',NULL,NULL,NULL,'Demo','2025-11-10 01:29:15','2025-11-10 01:29:15',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,'3832',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'Hồ Đạt',NULL,'0898312389','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,585000,0,'cod','paid','completed',NULL,'2025-11-10 01:32:42',NULL,'Nhận hàng vào lúc 7h tối','2025-11-10 01:30:25','2025-11-10 01:32:42',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,'8421',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'Hồ Đạt',NULL,'0902634252','shipping',NULL,'test','Tỉnh Quảng Ninh','Thành phố Đông Triều',NULL,'Phường Hồng Phong',NULL,93000,0,'cod','cancelled','cancelled',NULL,'2025-11-10 03:36:18',NULL,NULL,'2025-11-10 01:31:36','2025-11-10 03:36:41',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,'7256',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'RmYyH4tt1Pv5Kb8AKDR5MnQF0x7Wa4OppXF0jDAr','Phạm Chí Trọng',NULL,'0904490909','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,100000,0,'cod','pending','new',NULL,NULL,NULL,'ád','2025-11-12 02:11:09','2025-11-12 02:11:09',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,'5480',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'Hồ Đạt',NULL,'0904490909','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'cod','unpaid','confirmed',NULL,'2025-11-13 02:47:05',NULL,'ádasdsa','2025-11-13 02:46:52','2025-11-15 03:32:33',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,'3112',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,20,NULL,'Hungkhi',NULL,'0901645269','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,738000,0,'cod','cancelled','cancelled',NULL,NULL,NULL,NULL,'2025-11-17 02:06:07','2025-11-17 02:51:46','approved','other','zffsf',NULL,'2025-11-17 02:13:28','2025-11-17 02:51:46',2),(20,'9245',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,20,NULL,'Hungkhi',NULL,'0901645269','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,150000,0,'cod','unpaid','pending',NULL,NULL,NULL,NULL,'2025-11-17 02:52:05','2025-11-17 02:53:25','rejected','wrong_product','ddatj ho','Đơn hàng bạn đã ship rồi không thể hủy được nữa , mong b thông cảm','2025-11-17 02:52:24','2025-11-17 02:53:25',2),(22,'2084',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,20,NULL,'Hungkhi',NULL,'0901645269','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'cod','unpaid','confirmed',NULL,'2025-11-17 03:08:25',NULL,NULL,'2025-11-17 03:06:38','2025-11-17 03:08:51',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,'1498',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,20,NULL,'Hungkhi',NULL,'0901645269','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,186000,0,'cod','cancelled','cancelled',NULL,NULL,NULL,NULL,'2025-11-17 03:09:14','2025-11-17 03:09:42','approved','wrong_product','adad',NULL,'2025-11-17 03:09:31','2025-11-17 03:09:42',2),(24,'5783',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,20,NULL,'Hungkhi',NULL,'0902634252','shipping',NULL,'Số nhà 56, ấp 5, Tỉnh Lộ 328','Tỉnh Bà Rịa - Vũng Tàu','Huyện Xuyên Mộc',NULL,'Xã Phước Tân',NULL,150000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-11-17 12:11:56','2025-11-17 12:11:56',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,'1753',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'A9v3MJrLRzzWHD4d9HQYLsrKEdlIWBuh1p9vjqKV','Hồ Hùng',NULL,'0902634252','shipping',NULL,'Số 123, Đường D2 (Nguyễn Gia Trí)','Thành phố Hồ Chí Minh','Quận Bình Thạnh',NULL,'Phường 25',NULL,93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-11-17 12:24:30','2025-11-17 12:24:30',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,'2233',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1B9KqWGZ4r8FoOuyEnSfAsBpuhOv47pKkSKOgpAn','Hungkhi',NULL,'0904490909','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'vnpay','pending','new',NULL,NULL,'26_1763407566',NULL,'2025-11-17 12:26:06','2025-11-17 12:26:06',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,'7634','GYNDEQV8','ready_to_pick',29001,'2025-11-19 09:59:59',NULL,NULL,NULL,NULL,'2025-11-18 01:50:47',NULL,'mK8oYRF84ZeZaohL4Z0AkyINJ1XcNT4rvx1g6Hch','Phạm Chí Trọng',NULL,'0376193244','shipping',NULL,'Số 123, Đường D2 (Nguyễn Gia Trí)','Thành phố Hồ Chí Minh','Quận Bình Thạnh',1462,'Phường 25','21617',93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-11-17 12:27:39','2025-11-18 01:50:47',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,'1488',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'P99BUr0GrjvnzGGyU5ovrJlgxETnSOS4ptl6lhIW','Phạm Chí Trọng',NULL,'0904490909','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-11-18 02:08:54','2025-11-18 02:08:54',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,'4193',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,21,NULL,'Bánh mì bơ sữa',NULL,'0909330624','shipping',NULL,'Số 123, Đường D2 (Nguyễn Gia Trí)','Tỉnh Bà Rịa - Vũng Tàu','Thành phố Vũng Tàu',1544,'Phường Nguyễn An Ninh','520112',93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-11-18 02:41:05','2025-11-18 02:41:05',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,'6416','GYNPLBH7','ready_to_pick',29001,'2025-11-20 09:59:59',NULL,NULL,NULL,NULL,'2025-11-18 02:44:19',21,NULL,'Bánh mì bơ sữa',NULL,'0909330624','shipping',NULL,'Số 126, Đường D2 (Nguyễn Gia Trí)','Thành phố Hồ Chí Minh','Quận Bình Thạnh',1462,'Phường 25','21617',93000,0,'cod','cancelled','cancelled',NULL,NULL,NULL,NULL,'2025-11-18 02:42:05','2025-11-18 03:21:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,'1855','GYNBLNP6','ready_to_pick',29001,'2025-11-20 09:59:59',NULL,NULL,NULL,NULL,'2025-11-19 01:05:06',21,NULL,'Bánh mì bơ sữa',NULL,'0909330624','shipping',NULL,'Số 123, Đường D2 (Nguyễn Gia Trí)','Thành phố Hồ Chí Minh','Quận Bình Thạnh',1462,'Phường 25','21617',93000,0,'cod','unpaid','confirmed',NULL,NULL,NULL,NULL,'2025-11-19 01:04:36','2025-11-19 01:05:06',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,'2478',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,21,NULL,'Bánh mì bơ sữa',NULL,'0909330624','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'cod','cancelled','cancelled',NULL,NULL,NULL,NULL,'2025-11-19 01:12:10','2025-11-19 01:18:20','approved','wrong_product',NULL,NULL,'2025-11-19 01:17:52','2025-11-19 01:18:20',2),(36,'2536',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'3TA4DyVx4i2il11pac5vHjJuyOHR1VvCUAq3a7Y7','Hungkhi',NULL,'0901645269','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'vnpay','pending','new',NULL,NULL,'36_1763660799',NULL,'2025-11-20 10:46:38','2025-11-20 10:46:39',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37,'5124',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'fCSGOgcDWz1FO7BgNKoY7mKn28Ia2DorMDOOugBo','Đạt gay',NULL,'0357960770','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'vnpay','pending','new',NULL,NULL,'37_1763661222',NULL,'2025-11-20 10:53:41','2025-11-20 10:53:42',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(52,'6123',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,21,NULL,'Bánh mì bơ sữa','bmibosu123@gmail.com','0909330624','shipping',NULL,'207 Lê Hồng Phong, Phường 8, Vũng Tàu, Bà Rịa - Vũng Tàu','Tỉnh Bà Rịa - Vũng Tàu','Thành phố Vũng Tàu',1544,'Phường 8','520110',93000,0,'cod','cancelled','cancelled',NULL,NULL,NULL,NULL,'2025-11-24 02:08:01','2025-12-14 00:58:37',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(53,'7103',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,20,NULL,'Hungkhi','dat280248@gmail.com','0902634252','shipping',NULL,'Số 123, Đường D2 (Nguyễn Gia Trí)','Thành phố Hồ Chí Minh','Quận Bình Thạnh',1462,'Phường 25','21617',93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-11-24 02:14:34','2025-11-24 02:14:34',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(54,'9718','GYNA9LAG','ready_to_pick',29001,'2025-11-30 09:59:59',NULL,NULL,NULL,NULL,'2025-11-26 01:37:04',21,NULL,'Bánh mì bơ sữa','bmibosu123@gmail.com','0909330624','shipping',NULL,'Số 123, Đường D2 (Nguyễn Gia Trí)','Tỉnh Thái Nguyên','Thành phố Sông Công',1684,'Phường Mỏ Chè','120205',93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-11-24 04:21:56','2025-11-26 01:37:04',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(56,'4814','GYNAVCL6','ready_to_pick',34000,'2025-11-28 09:59:59',NULL,NULL,NULL,NULL,'2025-11-26 02:00:44',21,NULL,'Bánh mì bơ sữa','bmibosu123@gmail.com','0909330624','shipping',NULL,'45 đường 23-4, Phường Phước Hội, Thị xã La Gi, Tỉnh Bình Thuận.','Tỉnh Bình Thuận','Thị xã La Gi',1778,'Phường Phước Hội','471002',186000,0,'cod','pending','pending',NULL,NULL,NULL,NULL,'2025-11-26 01:43:10','2025-11-26 02:00:44',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(57,'8177','GYNA6FCU','ready_to_pick',15501,'2025-11-27 09:59:59',NULL,NULL,NULL,NULL,'2025-11-26 02:19:38',1,NULL,'Hồ Đạt','phamchitrong29102002@gmail.com','0376193244','shipping',NULL,'207 Lê Hồng Phong, Phường 8, Vũng Tàu, Bà Rịa - Vũng Tàu','Tỉnh Bà Rịa - Vũng Tàu','Thành phố Vũng Tàu',1544,'Phường 8','520110',93000,0,'cod','cancelled','cancelled',NULL,NULL,NULL,NULL,'2025-11-26 02:18:01','2025-11-27 01:27:27',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(58,'1417',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,21,NULL,'Bánh mì bơ sữa','bmibosu123@gmail.com','0909330624','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'vnpay','paid','new',NULL,NULL,'58_1764222835',NULL,'2025-11-26 22:53:55','2025-11-26 22:54:32',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(59,'1640',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'nh0ghNOWLznjKsPOYQx9e2q5DBTXIe1MotPWGFgK','Phạm Chí Trọng','phamchitrong2910@gmail.com','0901645269','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,186000,0,'vnpay','paid','new',NULL,NULL,'59_1764335049',NULL,'2025-11-28 06:04:08','2025-11-28 06:04:51',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(60,'1827',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'a3wrcznZMZ2dPyyA5cnKnAbPCEDmydcLhNqMucRE','Phạm Trọng','phamchitrong2910@gmail.com','0901645269','shipping',NULL,'Tổ 32/5 ô3 ấp hải điền , long hải , bà rịa vũng tàu','Tỉnh Vĩnh Phúc','Huyện Yên Lạc',1734,'Xã Liên Châu','160508',93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-11-29 00:43:14','2025-11-29 00:43:14',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(61,'5856',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'PtK0VjjQV7mgrMd1PWvuEzNL6gWUwJj3rUzCy2PF','Hồ Đạt','hctd123@gmail.com','0376193244','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,1500000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-12-01 20:37:34','2025-12-01 20:37:34',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(62,'3286','GYDLRDNH','cancel',29001,'2025-12-06 09:59:59',NULL,NULL,NULL,NULL,'2025-12-02 11:58:04',21,NULL,'Bánh mì bơ sữa','bmibosu123@gmail.com','0909330624','shipping',NULL,'Số 25, đường Nguyễn Văn Cừ, phường Hà Khẩu, thành phố Hạ Long, tỉnh Quảng Ninh.','Tỉnh Quảng Ninh','Thành phố Hạ Long',1604,'Phường Hà Khẩu','170108',246000,0,'vnpay','paid','cancelled',NULL,NULL,'62_1764701774',NULL,'2025-12-02 11:56:13','2025-12-12 03:59:16',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(63,'5855',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'qm3UvZqx57f0IVvonvQ8qIXPjEoG33BjWFZuqRfd','Thịnh Đạt','hctd123@gmail.com','0376193244','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,150000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-12-04 01:20:03','2025-12-04 01:20:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(64,'5899',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'bPaj7L50fuVXudsyuShQKj7F7stEhGO3VAJofi8D','Đạt gay','hctd123@gmail.com','0376193244','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 3',NULL,NULL,NULL,NULL,NULL,NULL,1209000,0,'cod','paid','completed',NULL,'2025-12-04 03:16:59',NULL,NULL,'2025-12-04 03:16:34','2025-12-04 03:16:59',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(65,'1383','GYDGA6GA','ready_to_pick',29001,'2025-12-06 09:59:59',NULL,NULL,NULL,NULL,'2025-12-05 00:05:17',21,NULL,'Bánh mì bơ sữa','bmibosu123@gmail.com','0909330624','shipping',NULL,'Số 123, Đường D2 (Nguyễn Gia Trí)','Thành phố Hồ Chí Minh','Quận Bình Thạnh',1462,'Phường 25','21617',93000,0,'cod','cancelled','cancelled',NULL,NULL,NULL,'adadada','2025-12-05 00:04:46','2025-12-05 00:06:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(66,'1106',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,21,NULL,'Bánh mì bơ sữa','bmibosu123@gmail.com','0909330624','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,279000,0,'cod','paid','completed',NULL,'2025-12-05 11:27:53',NULL,NULL,'2025-12-05 11:27:38','2025-12-05 11:27:53',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(67,'1051','GYD3Q6UY','cancel',15501,'2025-12-13 09:59:59',NULL,NULL,NULL,NULL,'2025-12-12 02:42:17',NULL,'w9AbYUbXpIqV40nnUzIqOEzR3MxqsSZzzh9FbLqm','Test','phamchitrong2910@gmail.com','0901645269','shipping',NULL,'sfsdfsdf','Tỉnh Bà Rịa - Vũng Tàu','Thành phố Vũng Tàu',1544,'Phường Nguyễn An Ninh','520112',1455000,0,'vnpay','paid','cancelled',NULL,NULL,'67_1765027664',NULL,'2025-12-06 06:27:44','2025-12-12 02:42:51',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(68,'2115','GYDCVBGE','cancel',15501,'2025-12-07 09:59:59',NULL,NULL,NULL,NULL,'2025-12-06 06:31:44',21,NULL,'Bánh mì bơ sữa','bmibosu123@gmail.com','0909330624','shipping',NULL,'sfsdfsdf','Tỉnh Bà Rịa - Vũng Tàu','Thành phố Vũng Tàu',1544,'Phường 1','520104',93000,0,'cod','pending','cancelled',NULL,NULL,NULL,NULL,'2025-12-06 06:30:44','2025-12-12 02:16:36',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(69,'1424',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'UqwpWhIdneW3X8QjAXxDXE5HaxkaDv1C1kQETH9z','Xà bị chưởng','xyz.anr.2004@gmail.com','090123134','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-12-12 11:23:07','2025-12-12 11:23:07',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(70,'1277','GYD47NDH','cancel',29001,'2025-12-14 09:59:59',NULL,NULL,NULL,NULL,'2025-12-13 00:44:54',21,NULL,'Bánh mì bơ sữa','bmibosu123@gmail.com','0909330624','shipping',NULL,'Hẻm 685/32 phường 25','Thành phố Hồ Chí Minh','Quận Bình Thạnh',1462,'Phường 25','21617',246000,0,'cod','pending','cancelled',NULL,NULL,NULL,NULL,'2025-12-13 00:44:26','2025-12-13 00:45:32',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(71,'6067',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'IDYrmyMtvSjbcJDVF8fYB3ARBGSVUPsDmi6Op5Py','Xà bị chưởng','hctd123@gmail.com','0149149149','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-12-13 12:11:13','2025-12-13 12:11:13',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(72,'8505',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'59RYcDI4LDs0rUNrcfXjywbqNnu9TcPKLCTd3EjI','Hồ Đạt','hctd123@gmail.com','0912414959','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'vnpay','pending','new',NULL,NULL,'72_1765653518',NULL,'2025-12-13 12:18:37','2025-12-13 12:18:39',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(73,'8577',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ZU3LBITB3OpbUnyqWr29Aw964bQ0jkAWFdSrVrxB','Xà bị chưởng','hctd123@gmail.com','0912414959','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-12-13 12:20:14','2025-12-13 12:20:14',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(74,'3597',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'hYNXe9Xe2IKAmqbLUyvvSGpv5FfZ44HBUCTAsiep','Bánh mì bơ sữa','phamchitrong2910@gmail.com','0901645269','shipping',NULL,'sfsdfsdf','Tỉnh Bắc Ninh','Thị xã Thuận Thành',1767,'Xã Nguyệt Đức','190611',93000,0,'vnpay','pending','new',NULL,NULL,'74_1765653653',NULL,'2025-12-13 12:20:53','2025-12-13 12:20:53',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(75,'3796',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'S5onVaZKIQEpdpgFBWrrdqJYiyw2rCwjlBhqPs74','Test','phamchitrong2910@gmail.com','0901645269','shipping',NULL,'sfsdfsdf','Tỉnh Phú Thọ','Huyện Lâm Thao',1959,'Xã Vĩnh Lại','151012',186000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-12-13 12:22:35','2025-12-13 12:22:35',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(76,'1047',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'5j4UO1pTB2A8YvYFd4mWkVK4JhcQ4b3p5wKyssRx','Xà bị chưởng','hctd123@gmail.com','0912414959','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'vnpay','pending','new',NULL,NULL,'76_1765653777',NULL,'2025-12-13 12:22:57','2025-12-13 12:22:57',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(77,'8410',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Z1SzJsbejtQIUFNybWLK3DpNqqbANTYxA4Tabwr7','Xà bị chưởng','hctd123@gmail.com','0912414959','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'vnpay','pending','new',NULL,NULL,'77_1765653892',NULL,'2025-12-13 12:24:52','2025-12-13 12:24:52',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(78,'4449',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'m5znyuffEuihjKOKFmOOhdotvNpDIngRjHYCEvmQ','Xà bị chưởng','hctd123@gmail.com','0912414959','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,186000,0,'vnpay','pending','new',NULL,NULL,'78_1765654161',NULL,'2025-12-13 12:29:21','2025-12-13 12:29:21',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(79,'3534',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'x83z4gHSB2WvHFdWKCa0yQuSw0ONjS7HFKsJopWG','Xà bị chưởng','hctd123@gmail.com','0912414959','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'vnpay','pending','new',NULL,NULL,'79_1765654386',NULL,'2025-12-13 12:33:05','2025-12-13 12:33:06',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(80,'9678',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ezqTOzJ84Rng6JUFgc91vWEBkBa7ynp4atLGkQe4','Xà bị chưởng','hctd123@gmail.com','0912414959','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'vnpay','pending','new',NULL,NULL,'80_1765654545',NULL,'2025-12-13 12:35:45','2025-12-13 12:35:45',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(81,'9404',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,26,NULL,'Bảo Cute','quocbaonguyen724@gmail.com','0792338714','shipping',NULL,'459 Đường Sư Vạn Hạnh, Phường 10, Quận 10, Thành phố Hồ Chí Minh.','Thành phố Hồ Chí Minh','Quận 10',1452,'Phường 10','21010',93000,0,'vnpay','unpaid','confirmed',NULL,NULL,'81_1765697128',NULL,'2025-12-14 00:25:28','2025-12-14 01:28:00',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(82,'1491',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,26,NULL,'Bảo Cute','quocbaonguyen724@gmail.com','0792338714','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,246000,0,'cod','cancelled','cancelled',NULL,NULL,NULL,NULL,'2025-12-14 01:57:18','2025-12-14 01:59:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(83,'6751',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,26,NULL,'Bảo Cute','quocbaonguyen724@gmail.com','0792338714','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 1',NULL,NULL,NULL,NULL,NULL,NULL,246000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-12-14 06:28:25','2025-12-14 06:28:25',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(84,'6640',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'MhmoylYpIARCBZAfySKFDX7K6zMmFGZHTnqV3lLJ','Xà bị chưởng','hctd123@gmail.com','0901643334','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-12-16 10:39:12','2025-12-16 10:39:12',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(85,'2543',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,29,NULL,'Diddy Quốc Anh','quocanh11004@gmail.com','0357960770','pickup','Nhà thuốc Sức Khỏe 24h - Chi nhánh 2',NULL,NULL,NULL,NULL,NULL,NULL,246000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-12-20 04:22:47','2025-12-20 04:22:47',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(86,'1652',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,26,NULL,'Bảo Cute','quocbaonguyen724@gmail.com','0792338714','shipping',NULL,'772 Sư Vạn Hạnh','Thành phố Hồ Chí Minh','Quận 7',1449,'Phường Tân Phong','20706',70000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-12-27 01:34:02','2025-12-27 01:34:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(87,'2925',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,26,NULL,'Bảo Cute','quocbaonguyen724@gmail.com','0792338714','shipping',NULL,'459 Đường Sư Vạn Hạnh, Phường 10, Quận 10, Thành phố Hồ Chí Minh.','Thành phố Hồ Chí Minh','Quận 10',1452,'Phường 10','21010',93000,0,'cod','pending','new',NULL,NULL,NULL,NULL,'2025-12-27 01:47:02','2025-12-27 01:47:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(89,'5574','GYPUBELK','cancel',29001,'2025-12-29 09:59:59',NULL,NULL,NULL,NULL,'2025-12-27 02:53:07',20,NULL,'Hungkhi','dat280248@gmail.com','0376193244','shipping',NULL,'5/24 Nơ Trang Long, P. 7, Bình Thạnh, Thành phố Hồ Chí Minh, Việt Nam','Thành phố Hồ Chí Minh','Quận Bình Thạnh',1462,'Phường 7','21619',122001,0,'cod','pending','cancelled',NULL,NULL,NULL,NULL,'2025-12-27 02:48:28','2025-12-27 02:53:55',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(90,'4717','GYPU3TGT','cancel',29001,'2025-12-30 09:59:59',NULL,NULL,NULL,NULL,'2025-12-27 03:16:37',1,NULL,'Hồ Đạt','phamchitrong29102002@gmail.com','0909332399','shipping',NULL,'122 Nguyễn Đình Chiểu, Phường Hàm Tiến, Phan Thiết.','Tỉnh Bình Thuận','Thành phố Phan Thiết',1666,'Phường Hàm Tiến','470105',122001,29001,'cod','pending','cancelled',NULL,NULL,NULL,NULL,'2025-12-27 03:16:16','2025-12-27 04:10:31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(91,'7031','GYPUKUBU','cancel',29001,'2025-12-29 09:59:59',NULL,NULL,NULL,NULL,'2025-12-27 04:28:52',NULL,'4Cced2v87wZzJGf6Ne0ztLfvjgvpPSH1i4WKc2ql','Bánh Mì Bơ Sữa','quocbaonguyen724@gmail.com','0901645262','shipping',NULL,'459 Đường Sư Vạn Hạnh, Phường 10, Quận 10, Thành phố Hồ Chí Minh.','Thành phố Hồ Chí Minh','Quận 10',1452,'Phường 10','21010',122001,29001,'vnpay','paid','cancelled',NULL,NULL,'91_1766834854','Hello hello','2025-12-27 04:27:34','2025-12-27 07:48:52',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES (1,'Kệ A3, Ngăn 2',NULL,'2025-11-07 00:59:57','2025-11-07 00:59:57'),(2,'Tủ A1','Chứa thuốc dị ứng','2025-11-20 01:53:16','2025-11-20 01:53:16'),(3,'Kệ C3 - Tủ kéo','Nơi chứa sản phẩm chăm sóc da mặt','2025-11-20 02:07:38','2025-11-20 02:07:38'),(4,'Kệ mỹ phẩm - Tầng 1','Nơi chứa đựng mỹ phẩm','2025-12-19 02:25:52','2025-12-19 02:25:52'),(5,'Kệ thực phẩm chức năng - Tầng 2','Nơi chưa các thực phẩm chức năng','2025-12-19 03:47:53','2025-12-19 03:47:53');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_categories`
--

DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `product_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_categories`
--

LOCK TABLES `product_categories` WRITE;
/*!40000 ALTER TABLE `product_categories` DISABLE KEYS */;
INSERT INTO `product_categories` VALUES (1,'Thuốc',NULL,0,'2025-11-06 04:11:49','2025-11-06 04:11:49'),(2,'Vật tư y tế',NULL,0,'2025-11-06 04:11:55','2025-11-06 04:11:55'),(3,'Dịch vụ',NULL,0,'2025-11-06 04:12:00','2025-11-06 04:12:00'),(4,'Thuốc trị ho, cảm',1,0,'2025-11-07 00:56:20','2025-11-07 00:56:20'),(5,'Chăm sóc cá nhân',2,0,'2025-11-07 01:01:37','2025-11-07 01:01:37'),(6,'Tiêm vacxin Covid-19',3,0,'2025-11-08 01:58:44','2025-11-08 01:58:44'),(7,'Khám sức khỏe đi học (trẻ em)',3,0,'2025-11-08 02:00:36','2025-11-08 02:00:36'),(8,'Thuốc dị ứng',1,0,'2025-11-20 01:21:41','2025-11-20 01:21:41'),(10,'Thuốc bổ & vitamin',1,0,'2025-11-20 01:22:25','2025-11-20 01:22:25'),(11,'Thuốc bổ',10,0,'2025-11-20 01:22:41','2025-11-20 01:22:41'),(12,'Dụng cụ chuyên khoa',2,0,'2025-11-20 01:40:40','2025-11-20 01:40:40'),(13,'Chăm sóc da mặt',5,0,'2025-11-20 01:40:58','2025-11-20 01:40:58'),(14,'Thực phẩm chức năng',1,0,'2025-12-19 03:45:48','2025-12-19 03:45:48'),(15,'Khám Sức Khỏe Hành Chính',3,0,'2025-12-23 03:43:42','2025-12-23 03:43:42'),(16,'Khám sức khỏe đi học',15,0,'2025-12-23 03:43:55','2025-12-23 03:43:55'),(17,'Dịch Vụ Tại Nhà',3,0,'2025-12-23 03:44:12','2025-12-23 03:44:12'),(18,'Khám bệnh & Tư vấn tại nhà',17,0,'2025-12-23 03:44:19','2025-12-23 03:44:19');
/*!40000 ALTER TABLE `product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `product_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` tinyint unsigned NOT NULL COMMENT 'Điểm đánh giá từ 1-5',
  `comment` text COLLATE utf8mb4_unicode_ci COMMENT 'Nội dung bình luận',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved' COMMENT 'Trạng thái duyệt',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_reviews_product_id_product_type_index` (`product_id`,`product_type`),
  KEY `product_reviews_user_id_index` (`user_id`),
  KEY `product_reviews_status_index` (`status`),
  CONSTRAINT `product_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_return_items`
--

DROP TABLE IF EXISTS `purchase_return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` bigint unsigned NOT NULL,
  `product_type` enum('medicine','goods') COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(15,2) NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`),
  CONSTRAINT `purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_return_items`
--

LOCK TABLES `purchase_return_items` WRITE;
/*!40000 ALTER TABLE `purchase_return_items` DISABLE KEYS */;
INSERT INTO `purchase_return_items` VALUES (1,1,'medicine',1,10,70000.00,0.00,700000.00,NULL,'2025-12-04 08:21:47','2025-12-04 08:21:47');
/*!40000 ALTER TABLE `purchase_return_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_return_payments`
--

DROP TABLE IF EXISTS `purchase_return_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_return_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` bigint unsigned NOT NULL,
  `payment_method` enum('cash','card','transfer') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_payments_purchase_return_id_foreign` (`purchase_return_id`),
  CONSTRAINT `purchase_return_payments_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_return_payments`
--

LOCK TABLES `purchase_return_payments` WRITE;
/*!40000 ALTER TABLE `purchase_return_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_return_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_returns`
--

DROP TABLE IF EXISTS `purchase_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `return_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `return_date` date NOT NULL,
  `status` enum('pending','returned','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_returns_return_code_unique` (`return_code`),
  KEY `purchase_returns_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `purchase_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_returns`
--

LOCK TABLES `purchase_returns` WRITE;
/*!40000 ALTER TABLE `purchase_returns` DISABLE KEYS */;
INSERT INTO `purchase_returns` VALUES (1,'4876427',1,'2025-12-04','returned',700000.00,0.00,0.00,700000.00,NULL,'2025-12-04 08:21:47','2025-12-04 08:21:47');
/*!40000 ALTER TABLE `purchase_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_bookings`
--

DROP TABLE IF EXISTS `service_bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_bookings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `service_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pay_at_pharmacy',
  `payment_status` enum('unpaid','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `status` enum('pending','confirmed','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_service_bookings_service_id` (`service_id`),
  KEY `fk_service_bookings_user_id` (`user_id`),
  CONSTRAINT `fk_service_bookings_service_id` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_service_bookings_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_bookings`
--

LOCK TABLES `service_bookings` WRITE;
/*!40000 ALTER TABLE `service_bookings` DISABLE KEYS */;
INSERT INTO `service_bookings` VALUES (2,2,1,'Hồ Đạt Peter','0376193244',NULL,'2025-11-20','09:30:00',320000.00,'pay_at_pharmacy','paid','completed','Có thể sẽ đến trễ khoảng 30 phút','2025-11-10 02:20:23','2025-11-10 02:39:17'),(3,2,21,'Bánh Mì Bơ Sữa','0901645262','bmibosu123@gmail.com','2025-12-19','02:55:00',320000.00,'pay_at_pharmacy','unpaid','cancelled','Có thể đến trễ hơn 30 phút','2025-12-16 10:55:30','2025-12-28 02:30:22'),(4,3,NULL,'Demo','012931939','tusnoflex1302@gmail.com','2026-01-12','03:53:00',150000.00,'pay_at_pharmacy','unpaid','pending','Demo nhé','2025-12-29 01:53:35','2025-12-29 01:53:35'),(5,3,NULL,'Hồ Lương Đạt','0376193244','dat280248@gmail.com','2026-01-15','18:59:00',150000.00,'pay_at_pharmacy','unpaid','pending','Hello','2025-12-29 01:58:13','2025-12-29 01:58:13'),(6,4,NULL,'Bánh mì','0901645269','phamchitrong2910@gmail.com','2026-02-15','08:06:00',500000.00,'pay_at_pharmacy','unpaid','confirmed','Tôi sẽ thanh toán tại phòng khám','2025-12-29 02:08:13','2025-12-29 02:09:38');
/*!40000 ALTER TABLE `service_bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ma_dich_vu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ten_dich_vu` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nhom_hang_id` bigint unsigned DEFAULT NULL,
  `doctor_id` bigint unsigned DEFAULT NULL,
  `gia_dich_vu` decimal(10,2) NOT NULL DEFAULT '0.00',
  `mo_ta` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ban_truc_tiep` tinyint(1) NOT NULL DEFAULT '0',
  `hinh_thuc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tai_nha_thuoc',
  `thoi_gian_thuc_hien` int DEFAULT NULL,
  `trang_thai` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kich_hoat',
  `ghi_chu` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `services_nhom_hang_id_foreign` (`nhom_hang_id`),
  KEY `services_doctor_id_foreign` (`doctor_id`),
  KEY `services_created_by_foreign` (`created_by`),
  KEY `services_updated_by_foreign` (`updated_by`),
  CONSTRAINT `services_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `services_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL,
  CONSTRAINT `services_nhom_hang_id_foreign` FOREIGN KEY (`nhom_hang_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `services_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (2,'DV259332','Khám sức khỏe tổng quát cho trẻ em',7,1,320000.00,'<p>Khám&nbsp;sức&nbsp;khỏe&nbsp;cho&nbsp;trẻ&nbsp;đi&nbsp;học&nbsp;bao&nbsp;gồm&nbsp;khám&nbsp;thể&nbsp;chất&nbsp;tổng&nbsp;quát,&nbsp;khám&nbsp;tai&nbsp;mũi&nbsp;họng,&nbsp;mắt,&nbsp;răng&nbsp;hàm&nbsp;mặt,&nbsp;xét&nbsp;nghiệm&nbsp;máu,&nbsp;nước&nbsp;tiểu&nbsp;để&nbsp;phát&nbsp;hiện&nbsp;sớm&nbsp;các&nbsp;vấn&nbsp;đề&nbsp;sức&nbsp;khỏe,&nbsp;tư&nbsp;vấn&nbsp;dinh&nbsp;dưỡng&nbsp;và&nbsp;kiểm&nbsp;tra&nbsp;tình&nbsp;trạng&nbsp;tiêm&nbsp;chủng.&nbsp;Phụ&nbsp;huynh&nbsp;có&nbsp;thể&nbsp;cho&nbsp;bé&nbsp;đi&nbsp;khám&nbsp;tại&nbsp;trạm&nbsp;y&nbsp;tế&nbsp;phường/xã,&nbsp;bệnh&nbsp;viện&nbsp;nhi&nbsp;hoặc&nbsp;các&nbsp;phòng&nbsp;khám&nbsp;tư&nbsp;nhân&nbsp;uy&nbsp;tín.&nbsp;</p><p>Nội&nbsp;dung&nbsp;khám&nbsp;sức&nbsp;khỏe</p><p>Khám&nbsp;tổng&nbsp;quát:&nbsp;Đo&nbsp;chiều&nbsp;cao,&nbsp;cân&nbsp;nặng,&nbsp;huyết&nbsp;áp,&nbsp;kiểm&nbsp;tra&nbsp;tim&nbsp;mạch,&nbsp;hô&nbsp;hấp&nbsp;và&nbsp;các&nbsp;chỉ&nbsp;số&nbsp;sinh&nbsp;lý&nbsp;khác&nbsp;để&nbsp;đánh&nbsp;giá&nbsp;sự&nbsp;phát&nbsp;triển&nbsp;của&nbsp;trẻ.</p><p>Khám&nbsp;tai&nbsp;mũi&nbsp;họng:&nbsp;Phát&nbsp;hiện&nbsp;các&nbsp;bệnh&nbsp;như&nbsp;viêm&nbsp;amidan,&nbsp;viêm&nbsp;tai&nbsp;giữa&nbsp;và&nbsp;đảm&nbsp;bảo&nbsp;trẻ&nbsp;nghe,&nbsp;thở&nbsp;tốt.</p><p>Khám&nbsp;mắt:&nbsp;Phát&nbsp;hiện&nbsp;các&nbsp;tật&nbsp;khúc&nbsp;xạ&nbsp;như&nbsp;cận&nbsp;thị,&nbsp;loạn&nbsp;thị&nbsp;để&nbsp;không&nbsp;ảnh&nbsp;hưởng&nbsp;đến&nbsp;việc&nbsp;học&nbsp;tập&nbsp;của&nbsp;trẻ.</p><p>Khám&nbsp;răng&nbsp;hàm&nbsp;mặt:&nbsp;Kiểm&nbsp;tra&nbsp;sâu&nbsp;răng,&nbsp;lệch&nbsp;khớp&nbsp;cắn&nbsp;và&nbsp;hướng&nbsp;dẫn&nbsp;vệ&nbsp;sinh&nbsp;răng&nbsp;miệng&nbsp;đúng&nbsp;cách.</p><p>Xét&nbsp;nghiệm:&nbsp;Thực&nbsp;hiện&nbsp;xét&nbsp;nghiệm&nbsp;máu,&nbsp;nước&nbsp;tiểu&nbsp;để&nbsp;đánh&nbsp;giá&nbsp;các&nbsp;vấn&nbsp;đề&nbsp;sức&nbsp;khỏe&nbsp;như&nbsp;thiếu&nbsp;máu,&nbsp;nhiễm&nbsp;trùng&nbsp;hoặc&nbsp;thiếu&nbsp;vi&nbsp;chất.</p><p>Chẩn&nbsp;đoán&nbsp;hình&nbsp;ảnh:&nbsp;Bác&nbsp;sĩ&nbsp;có&nbsp;thể&nbsp;chỉ&nbsp;định&nbsp;chụp&nbsp;X-quang&nbsp;tim&nbsp;phổi&nbsp;hoặc&nbsp;siêu&nbsp;âm&nbsp;ổ&nbsp;bụng&nbsp;nếu&nbsp;cần&nbsp;thiết.</p><p>Tư&nbsp;vấn:&nbsp;Bác&nbsp;sĩ&nbsp;sẽ&nbsp;tư&nbsp;vấn&nbsp;chế&nbsp;độ&nbsp;dinh&nbsp;dưỡng,&nbsp;sinh&nbsp;hoạt&nbsp;phù&nbsp;hợp&nbsp;và&nbsp;kiểm&nbsp;tra&nbsp;lịch&nbsp;sử&nbsp;tiêm&nbsp;chủng&nbsp;của&nbsp;trẻ&nbsp;để&nbsp;đảm&nbsp;bảo&nbsp;bé&nbsp;được&nbsp;bảo&nbsp;vệ&nbsp;tốt&nbsp;nhất.&nbsp;</p>','services/1762766365_vGCDd9dXQV.jpg',0,'tai_nha_thuoc',60,'kich_hoat',NULL,2,2,'2025-11-10 02:19:25','2025-11-10 02:19:25'),(3,'DV960526','Khám sức khỏe đi học (Hồ sơ học sinh/sinh viên)',3,3,150000.00,'<p>Cấp&nbsp;giấy&nbsp;khám&nbsp;sức&nbsp;khỏe&nbsp;theo&nbsp;mẫu&nbsp;thông&nbsp;tư&nbsp;14&nbsp;của&nbsp;Bộ&nbsp;Y&nbsp;tế&nbsp;cho&nbsp;học&nbsp;sinh,&nbsp;sinh&nbsp;viên&nbsp;nhập&nbsp;học.</p>','services/1766486202_TZjsTvT6ZY.jpg',0,'tai_phong_kham',30,'kich_hoat',NULL,2,2,'2025-12-23 03:36:42','2025-12-23 03:36:42'),(4,'DV704044','Khám bệnh và tư vấn sức khỏe tại nhà',18,4,500000.00,'<p>Dịch&nbsp;vụ&nbsp;dành&nbsp;cho&nbsp;người&nbsp;già&nbsp;hoặc&nbsp;bệnh&nbsp;nhân&nbsp;khó&nbsp;di&nbsp;chuyển,&nbsp;bác&nbsp;sĩ&nbsp;tới&nbsp;tận&nbsp;nơi&nbsp;thăm&nbsp;khám&nbsp;và&nbsp;tư&nbsp;vấn&nbsp;điều&nbsp;trị.</p>','services/1766486741_xNIf46UCS9.jpg',0,'tai_nha_khach',60,'kich_hoat',NULL,2,2,'2025-12-23 03:45:41','2025-12-23 03:45:41');
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('2R7YoetP0nicGJ2Y83pQ0ku2z6ZYcrPcRJzszgfl',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ2tlMFRXc1p2WjZpQktHU3k0RUJXSVQxaUJhUG1zQWQxdW9TT3Q2ZCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjI6Imh0dHA6Ly9zdWNraG9lMjRoLnRlc3QiO3M6NToicm91dGUiO3M6NDoiaG9tZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1766998078),('fdqhhJdEemFAyFNVh2gzMAjo7W7jpqNWRMXpBkhD',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTzdhWmNndWswNW5wODhlbVBuclZNdUo4bFN5VEV2VHhtQ2NRUDJmRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NzI6Imh0dHA6Ly9zdWNraG9lMjRoLnRlc3QvYWRtaW4vZGFzaGJvYXJkL3JldmVudWUvc2VydmljZXM/cGVyaW9kPXRoaXNNb250aCI7czo1OiJyb3V0ZSI7czozMjoiYWRtaW4uZGFzaGJvYXJkLnJldmVudWUuc2VydmljZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjIyOiJwaG9uZV9mb3JfdmVyaWZpY2F0aW9uIjtzOjEyOiIrODQzNzYxOTMyNDQiO3M6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==',1766999862),('NYPMN4kRKjj6fyYHeGQrHvxEFJZwE6n9bJ26fZId',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiczk3UHJuSlBISTFmMEJabUVuQlp4dHJ2UkVjYWlBOHhCbnFYY2N0SiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly9zdWNraG9lMjRoLnRlc3Qvc2VydmljZXMvMyI7czo1OiJyb3V0ZSI7czoxNToic2VydmljZXMuZGV0YWlsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1766999065),('s1IqOi2AmoE5nkPOxqVoIlopu1WyCkCCj53v9mdn',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZUZUOE9UUlZsbVNUTEN5eE1PakExOWpoZnVxYk02RW1ZeVZDY0dPTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly9zdWNraG9lMjRoLnRlc3QvbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1766999299);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shifts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `branch_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `shifts_branch_id_foreign` (`branch_id`),
  CONSTRAINT `shifts_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES (1,'Buổi Sáng','07:00:00','11:00:00',1,'2025-11-06 02:38:57','2025-11-06 02:38:57'),(2,'Buổi Chiều','13:00:00','17:00:00',1,'2025-11-06 02:38:57','2025-11-06 02:38:57'),(3,'Buổi Tối','18:00:00','22:00:00',1,'2025-11-06 02:38:57','2025-11-06 02:38:57');
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_import_items`
--

DROP TABLE IF EXISTS `stock_import_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_import_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stock_import_id` bigint unsigned NOT NULL,
  `product_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(15,2) NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_import_items_stock_import_id_foreign` (`stock_import_id`),
  CONSTRAINT `stock_import_items_stock_import_id_foreign` FOREIGN KEY (`stock_import_id`) REFERENCES `stock_imports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_import_items`
--

LOCK TABLES `stock_import_items` WRITE;
/*!40000 ALTER TABLE `stock_import_items` DISABLE KEYS */;
INSERT INTO `stock_import_items` VALUES (1,1,'medicine',1,100,70000.00,0.00,7000000.00,NULL,'2025-11-07 19:42:47','2025-11-07 19:42:47'),(2,2,'goods',1,100,230000.00,0.00,23000000.00,NULL,'2025-11-10 01:28:34','2025-11-10 01:28:34'),(3,3,'medicine',4,20,120000.00,0.00,2400000.00,NULL,'2025-11-15 02:47:44','2025-11-15 02:47:44'),(4,4,'medicine',5,15,120000.00,0.00,1800000.00,NULL,'2025-11-15 02:49:32','2025-11-15 02:49:32'),(5,5,'goods',2,1,120000.00,0.00,120000.00,NULL,'2025-11-15 02:59:34','2025-11-15 02:59:34'),(6,6,'goods',1,100,230000.00,0.00,23000000.00,NULL,'2025-11-16 10:09:14','2025-11-16 10:09:14'),(7,7,'goods',3,20,1150000.00,0.00,23000000.00,NULL,'2025-12-04 03:16:01','2025-12-04 03:16:01'),(8,8,'medicine',1,10,70000.00,0.00,700000.00,NULL,'2025-12-04 07:58:10','2025-12-04 07:58:10'),(9,9,'medicine',9,1,120000.00,0.00,120000.00,NULL,'2025-12-13 00:42:44','2025-12-13 00:42:44'),(10,10,'medicine',10,10,450000.00,0.00,4500000.00,NULL,'2025-12-24 00:51:22','2025-12-24 00:51:22');
/*!40000 ALTER TABLE `stock_import_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_import_payments`
--

DROP TABLE IF EXISTS `stock_import_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_import_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stock_import_id` bigint unsigned NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `amount` decimal(15,2) NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_import_payments_stock_import_id_foreign` (`stock_import_id`),
  CONSTRAINT `stock_import_payments_stock_import_id_foreign` FOREIGN KEY (`stock_import_id`) REFERENCES `stock_imports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_import_payments`
--

LOCK TABLES `stock_import_payments` WRITE;
/*!40000 ALTER TABLE `stock_import_payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_import_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_imports`
--

DROP TABLE IF EXISTS `stock_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `import_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `import_date` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_discount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_imports_import_code_unique` (`import_code`),
  KEY `stock_imports_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `stock_imports_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_imports`
--

LOCK TABLES `stock_imports` WRITE;
/*!40000 ALTER TABLE `stock_imports` DISABLE KEYS */;
INSERT INTO `stock_imports` VALUES (1,'3907006',1,'2025-11-08','imported',7000000.00,0.00,0.00,7000000.00,'Thanh toán tại cửa hàng','2025-11-07 19:42:47','2025-11-07 19:42:47'),(2,'3700713',1,'2025-11-10','imported',23000000.00,0.00,0.00,23000000.00,'thanh toán tại cửa hàng','2025-11-10 01:28:34','2025-11-10 01:28:34'),(3,'6015898',2,'2025-11-15','imported',2400000.00,0.00,0.00,2400000.00,NULL,'2025-11-15 02:47:44','2025-11-15 02:47:44'),(4,'6654888',2,'2025-11-15','imported',1800000.00,0.00,0.00,1800000.00,'test','2025-11-15 02:49:32','2025-11-15 02:49:32'),(5,'4251427',1,'2025-11-15','imported',120000.00,0.00,0.00,120000.00,'adad','2025-11-15 02:59:34','2025-11-15 02:59:34'),(6,'1619995',2,'2025-11-16','imported',23000000.00,0.00,0.00,23000000.00,NULL,'2025-11-16 10:09:14','2025-11-16 10:09:14'),(7,'1974648',2,'2025-12-04','imported',23000000.00,0.00,0.00,23000000.00,'Thanh toán tại cửa hàng','2025-12-04 03:16:01','2025-12-04 03:16:01'),(8,'9754393',1,'2025-12-04','imported',700000.00,0.00,0.00,700000.00,NULL,'2025-12-04 07:58:10','2025-12-04 07:58:10'),(9,'9827161',1,'2025-12-13','imported',120000.00,0.00,0.00,120000.00,NULL,'2025-12-13 00:42:44','2025-12-13 00:42:44'),(10,'7995277',3,'2025-12-24','imported',4500000.00,0.00,0.00,4500000.00,'Thanh toán tại phòng khám','2025-12-24 00:51:22','2025-12-24 00:51:22');
/*!40000 ALTER TABLE `stock_imports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_categories`
--

DROP TABLE IF EXISTS `supplier_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_categories_name_index` (`name`),
  KEY `supplier_categories_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_categories`
--

LOCK TABLES `supplier_categories` WRITE;
/*!40000 ALTER TABLE `supplier_categories` DISABLE KEYS */;
INSERT INTO `supplier_categories` VALUES (1,'Nhà cung cấp thuốc','Cung cấp các loại thuốc ho và siro','active','2025-11-07 19:39:44','2025-11-07 19:39:44'),(2,'Cung cấp thuốc','adadadad','active','2025-11-13 20:38:28','2025-11-13 20:38:28'),(3,'Nhóm Vật tư Y tế','Chuyên cung cấp các loại vật tư tiêu hao y tế, thiết bị chăm sóc sức khỏe gia đình','active','2025-12-16 23:04:13','2025-12-16 23:04:13'),(4,'Vật tư & Thiết bị Y tế','Chuyên cung cấp khẩu trang, bông băng, máy đo huyết áp.','active','2025-12-24 00:44:12','2025-12-24 00:44:12'),(5,'Thực phẩm chức năng','Các đơn vị cung cấp vitamin, thực phẩm bảo vệ sức khỏe.','active','2025-12-24 00:44:46','2025-12-24 00:44:46'),(6,'Mỹ phẩm & Hóa mỹ phẩm','Các dòng dược mỹ phẩm chăm sóc da, vệ sinh cá nhân.','active','2025-12-24 00:44:53','2025-12-24 00:44:53');
/*!40000 ALTER TABLE `supplier_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ten_nha_cung_cap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tên nhà cung cấp',
  `ma_nha_cung_cap` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Mã nhà cung cấp',
  `dien_thoai` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Số điện thoại',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email',
  `dia_chi` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Địa chỉ',
  `khu_vuc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tỉnh thành - Quận/Huyện',
  `phuong_xa` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Phường/Xã',
  `nhom_nha_cung_cap_id` bigint unsigned NOT NULL,
  `ghi_chu` text COLLATE utf8mb4_unicode_ci COMMENT 'Ghi chú',
  `ten_cong_ty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tên công ty',
  `ma_so_thue` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mã số thuế',
  `trang_thai` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'Trạng thái',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_ma_nha_cung_cap_unique` (`ma_nha_cung_cap`),
  UNIQUE KEY `suppliers_email_unique` (`email`),
  UNIQUE KEY `suppliers_ten_cong_ty_unique` (`ten_cong_ty`),
  UNIQUE KEY `suppliers_ma_so_thue_unique` (`ma_so_thue`),
  KEY `suppliers_nhom_nha_cung_cap_id_foreign` (`nhom_nha_cung_cap_id`),
  KEY `suppliers_ten_nha_cung_cap_index` (`ten_nha_cung_cap`),
  KEY `suppliers_ma_nha_cung_cap_index` (`ma_nha_cung_cap`),
  CONSTRAINT `suppliers_nhom_nha_cung_cap_id_foreign` FOREIGN KEY (`nhom_nha_cung_cap_id`) REFERENCES `supplier_categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'Công ty Dược phẩm An Khang','NCC001','0901234567','lienhe@ankhangpharma.vn','Số 123, đường Nguyễn Văn Cừ, Phường 4','Thành phố Hồ Chí Minh','Phường 4 (Quận 5)',1,'Giao hàng nhanh trong 2 giờ. Liên hệ anh Bình.','Công ty TNHH Dược phẩm An Khang','0312345678','active','2025-11-07 19:42:22','2025-11-07 19:42:22'),(2,'Công ty Dược phẩm ABC','NCC002','0901645229','tesst@gmail.com','ADADA','Tỉnh Hà Giang','Phường Minh Khai (Thành phố Hà Giang)',2,NULL,'Công ty TNHH Dược phẩm Minh AnHGHG','0409876543','active','2025-11-13 20:39:06','2025-11-13 20:39:06'),(3,'Công ty Cổ phần Dược phẩm Minh An','NCC003','0908123456','contact@minhanpharma.vn','Số 45, đường Nguyễn Văn Linh','Thành phố Hồ Chí Minh','Phường Phú Mỹ (Quận 7)',1,'Nhà cung cấp thuốc kê đơn và không kê đơn, hợp tác lâu dài.','CÔNG TY CỔ PHẦN DƯỢC PHẨM MINH AN','0100109106','active','2025-12-24 00:50:35','2025-12-24 00:50:35');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `support_tickets`
--

DROP TABLE IF EXISTS `support_tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `support_tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin_reply` text COLLATE utf8mb4_unicode_ci,
  `responded_by` bigint unsigned DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `support_tickets_ticket_id_unique` (`ticket_id`),
  KEY `support_tickets_responded_by_foreign` (`responded_by`),
  KEY `support_tickets_user_id_foreign` (`user_id`),
  CONSTRAINT `support_tickets_responded_by_foreign` FOREIGN KEY (`responded_by`) REFERENCES `users` (`id`),
  CONSTRAINT `support_tickets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `support_tickets`
--

LOCK TABLES `support_tickets` WRITE;
/*!40000 ALTER TABLE `support_tickets` DISABLE KEYS */;
INSERT INTO `support_tickets` VALUES (1,'SUP-FPKXOQ','Hồ Công Dark','dat280248@gmail.com','product','Làm việc với nhiều DEV Việt Nam. Mình nhận thấy mọi người quen với việc làm màu, giấu dốt. Đặc biệt là nói quá về thứ mình không biết.\nNhưng điều đó lại chính là rào cản có thể là lớn nhất cho việc phát triển kiến thức, tư duy và network của bạn.','Tôi xin lỗi',2,'2025-12-25 03:38:52','replied',21,'2025-12-25 02:19:59','2025-12-25 03:38:52'),(2,'SUP-ZL7L94','Mr Diddy','diddy123@gmail.com','medical','adadsadasdasdasdasdasdadadasdassa',NULL,NULL,NULL,'pending',NULL,'2025-12-25 03:43:55','2025-12-25 03:43:55'),(3,'SUP-PAOSFN','Đạt monkey','hctd123@gmail.com','medical','ádasdadsadasdsadsadadasdasd','oke đạt hồ',2,'2025-12-25 03:59:46','replied',NULL,'2025-12-25 03:59:33','2025-12-25 03:59:46'),(4,'SUP-ACM0MV','Peter Đạt','dat280248@gmail.com','product','NCS: The Best of 2016 [Album MIX] | NCS - Copyright Free Music','I miss the glory days of 2016. Cheers to everyone still watching to this day!',2,'2025-12-25 09:08:51','replied',NULL,'2025-12-25 09:08:25','2025-12-25 09:08:51'),(5,'SUP-TB610E','Test','Ngduyen1910@gmail.com','other','Ai là người đẹp trai nhất long hải','Đó là Phạm Chí Trọng',2,'2025-12-25 09:11:44','replied',NULL,'2025-12-25 09:11:22','2025-12-25 09:11:44');
/*!40000 ALTER TABLE `support_tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `firebase_uid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ward` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_firebase_uid_unique` (`firebase_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'PQR0vl3rfkMhHDrQkB2P5TT9h0e2','google','Hồ Đạt','phamchitrong29102002@gmail.com',NULL,'PLicoXHosW2d56zCgWbVhxjcktXFpNiQ3VQzpsbk.jpg','2025-12-27 03:14:22','$2y$12$CP.TRhUr4NLBUuxid3hLn.6FRBYIvnACl2wDo6JaywvXthk0t9bRK',NULL,NULL,NULL,NULL,NULL,'user','2025-11-05 01:54:15','2025-12-27 03:14:22'),(2,NULL,NULL,'Admin','admin@example.com',NULL,NULL,'2025-11-05 09:18:31','$2y$12$2Z6pXs/BYtoiSQBvAstByuzSOIuK1TPdMcW4J4l6f7jz597du7Evm',NULL,'','','','','admin','2025-11-05 09:18:31','2025-11-05 09:18:31'),(9,NULL,NULL,'Peter Hồ','dewiepham2002@gmail.com',NULL,NULL,NULL,'$2y$12$ZwK3iWD5ROli0zcNFT1vKOC/8YKxm2MhnF3H57iiP.qf0zPhOe/bK',NULL,NULL,NULL,NULL,NULL,'staff','2025-11-06 01:55:37','2025-11-06 01:55:37'),(10,NULL,NULL,'Quốc Anh','quocanh123@gmail.com',NULL,NULL,NULL,'$2y$12$MVuIYSu7lEizza0uHL.Kt.KZJ8gWCN5HwswBhB9aJU3wthm03/.7K',NULL,NULL,NULL,NULL,NULL,'staff','2025-11-06 02:14:35','2025-11-06 02:14:35'),(11,NULL,NULL,'Hùng FKT','hungfkt123@gmail.com',NULL,NULL,NULL,'$2y$12$81oTGlX0Y08pRu0xyAPtwefwElQX4O1KdIzJLEzdEeSGdzo7Mg4ba',NULL,NULL,NULL,NULL,NULL,'staff','2025-11-06 09:54:24','2025-11-06 09:54:24'),(14,NULL,NULL,'Tommy Quốc Anh','quocanh123lgbt@gmail.com','0357960770',NULL,NULL,'$2y$12$5Ii5JFLO5nCDlVp0cVXJheHDm8LWJEntj2Hbnv.Q6HlCLZBixMqTi',NULL,NULL,NULL,NULL,NULL,'user','2025-11-08 02:51:13','2025-12-27 02:36:19'),(20,'QIuG25Vl3BP8VZQk0L6O4dqOnt22','google','Hungkhi','dat280248@gmail.com',NULL,'5qYKi1P8REDf40lDMbrkRWV328WjAD4Llek9DWAs.jpg','2025-12-27 02:46:32','$2y$12$VgMbHSiFt9WLORm/RKX.zufjKCpFyfcy/Ac/rdXLIN25j8guL/UXi',NULL,NULL,NULL,NULL,NULL,'user','2025-11-15 11:48:07','2025-12-27 02:46:32'),(21,NULL,NULL,'Bánh mì bơ sữa','bmibosu123@gmail.com','0909330624',NULL,NULL,'$2y$12$YNI9N5GoDsNV1lxfuSrJm.wPC0y4K5LBzO8Z4cNQM3ix4VaBoJ1SW',NULL,NULL,NULL,NULL,NULL,'user','2025-11-18 02:16:02','2025-11-18 02:16:02'),(22,'FKZkMfgKMSZWxVTKfgWOvYXUTc03','google','Trọng Chí','phamchitrong2910@gmail.com',NULL,'https://lh3.googleusercontent.com/a/ACg8ocIJkMjWZ7KcGTZSEgYmb_52n3Cff2lBJjJj8nWDSVJ_TPdepMj7=s96-c','2025-12-22 04:08:32',NULL,NULL,NULL,NULL,NULL,NULL,'user','2025-11-19 01:24:49','2025-12-22 04:08:32'),(23,NULL,NULL,'test','test123@gmail.com','0915164143',NULL,NULL,'$2y$12$KFwO6TYoHIOWP6Jxd0XNfenGi8CFmTblOJrS0lYl4/ofr9hrAO6s.',NULL,NULL,NULL,NULL,NULL,'user','2025-11-21 04:37:34','2025-11-21 04:38:32'),(25,NULL,NULL,'Đạt Sona','hctd123@gmail.com','0376193244',NULL,NULL,'$2y$12$l9qCqNMG4RkEQ2DiOAPe9.ibZnScrD8or.hYdUqCiyFEU6JzyxoeC',NULL,'Số 25, đường Nguyễn Văn Cừ, phường Hà Khẩu, thành phố Hạ Long, tỉnh Quảng Ninh.',NULL,NULL,NULL,'user','2025-12-03 03:44:51','2025-12-03 03:47:02'),(26,NULL,NULL,'Bảo Cute','quocbaonguyen724@gmail.com','0792338714',NULL,NULL,'$2y$12$SJaWgwIfpLDRw.raq2TjmeZX7JitJ7EW6yQ3TEQcjfG16XOVjRsRq',NULL,NULL,NULL,NULL,NULL,'user','2025-12-14 00:25:01','2025-12-14 00:25:01'),(27,NULL,NULL,'Hùng Lương','luongkho123@gmail.com','0344435786',NULL,NULL,'$2y$12$7NH3XpVITYwVtZ8oBMFmXeXnijY9F4eynFq7jEmrvBAAvQp1.gsJG',NULL,NULL,NULL,NULL,NULL,'user','2025-12-14 11:13:47','2025-12-14 11:13:47'),(28,NULL,NULL,'Ngọc Nhiệm','ngocnhiembrvt123@gmail.com',NULL,'ER5LvfjzXGw1Lesa4zAwhC5EvSa3lpWXJCEnaecG.jpg',NULL,'$2y$12$0dXTmK2EfY3a99Fd05qQtuFsL6eFEmGLVXgQ5/N6M84QNT.OVEFgi',NULL,NULL,NULL,NULL,NULL,'user','2025-12-20 01:18:16','2025-12-20 01:33:05'),(29,NULL,NULL,'Diddy Quốc Anh','quocanh11004@gmail.com',NULL,'TCVwAEWZnPeFjIFTL9qOfmli4lLRhixJwhw6TBQr.jpg',NULL,'$2y$12$gydQoTrhE.Rz.umXq/MwsOgHJydRPLnvOMA1/5ZvXPnZfgVM0Ibke',NULL,NULL,NULL,NULL,NULL,'user','2025-12-20 04:22:31','2025-12-20 04:23:34'),(30,NULL,NULL,'Nguyễn Minh Hoàng','hoang.nm@suckhoe24h.test',NULL,NULL,NULL,'$2y$12$TRJmgRwMYYs/f20BPsBfn.siw8vQFiAZui0VmUKNcWkw09Av9Dts2',NULL,NULL,NULL,NULL,NULL,'staff','2025-12-23 11:20:22','2025-12-23 11:20:22');
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

-- Dump completed on 2025-12-29 16:48:20
