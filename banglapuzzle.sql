mysqldump: [Warning] Using a password on the command line interface can be insecure.
-- MySQL dump 10.13  Distrib 8.4.8, for Win64 (x86_64)
--
-- Host: localhost    Database: banglapuzzle
-- ------------------------------------------------------
-- Server version	8.0.43

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
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
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Electronics','electronics','Esse autem ducimus recusandae et et deleniti quia.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(2,'Fashion','fashion','Facilis quo nobis sed quibusdam error non ut animi et.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(3,'Home & Living','home-living','Ducimus eligendi rerum dolorem rerum quas et aut quidem quasi voluptas quis.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(4,'Beauty & Health','beauty-health','Debitis et reprehenderit alias velit accusantium amet rerum et.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(5,'Books & Stationery','books-stationery','Accusamus omnis optio asperiores quibusdam neque corrupti porro.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_01_01_000001_add_is_admin_to_users_table',1),(5,'2025_01_01_000002_create_categories_table',1),(6,'2025_01_01_000003_create_subcategories_table',1),(7,'2025_01_01_000004_create_products_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `subcategory_id` bigint unsigned NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `old_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `new_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_index` (`category_id`),
  KEY `products_subcategory_id_index` (`subcategory_id`),
  KEY `products_is_active_index` (`is_active`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `products_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,1,'Classic Kunze PLC Device','classic-kunze-plc-device-70290','Odio officia similique itaque ut. Qui earum facilis dolores quo beatae. Eius sed blanditiis animi facere reiciendis sunt ut inventore quisquam ipsum debitis.',NULL,2511.00,1752.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(2,1,1,'Professional Hegmann-Lynch Set','professional-hegmann-lynch-set-84934','Veniam consequuntur autem et eveniet ea. Animi consequatur at sit dolorem minima. Corrupti ut debitis tempora adipisci vero modi. Sit et vitae veritatis provident autem incidunt ut maxime consequatur reiciendis necessitatibus quam et asperiores possimus.',NULL,1960.00,764.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(3,1,1,'Classic Hagenes Group Product','classic-hagenes-group-product-47958','Perspiciatis eos et reiciendis illum alias atque fugit. Ipsa modi veritatis aut alias qui voluptatibus ad earum. Sapiente sit voluptatum qui reiciendis quis dolorum velit.',NULL,8239.00,6957.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(4,1,1,'Professional Donnelly-Simonis Device','professional-donnelly-simonis-device-70115','Eius deleniti eius voluptatum vitae quis at. Nostrum ea vero explicabo ratione aspernatur. Praesentium dolorum voluptatibus aut rerum voluptas tenetur nostrum ut eos consequuntur aliquam et et.',NULL,9650.00,6210.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(5,1,1,'Branded Murphy Ltd Collection','branded-murphy-ltd-collection-52915','Asperiores sed impedit dolorem. Ad deleniti corrupti assumenda. Quaerat repellat distinctio provident eos et est soluta quia voluptatum.',NULL,9136.00,4119.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(6,1,2,'Modern Dach Group Bundle','modern-dach-group-bundle-63362','Omnis et deserunt eaque nostrum. Distinctio enim voluptas qui qui voluptate et. Enim sint sapiente facere velit nesciunt temporibus. Sunt totam vitae numquam assumenda et iusto voluptatem repellat ea doloremque hic soluta rerum molestiae.',NULL,9610.00,4851.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(7,1,2,'Modern Ankunding, Hermiston and Batz Accessory','modern-ankunding-hermiston-and-batz-accessory-85618','Quia assumenda consectetur aliquid omnis soluta cumque. Exercitationem debitis cum libero omnis nulla id. Facere neque odit nulla consequatur vel fugiat sapiente. Debitis in provident enim ut optio sit eligendi animi nostrum magni dolorem aut officia soluta optio.',NULL,6550.00,1711.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(8,1,2,'Branded Russel-Senger Product','branded-russel-senger-product-34259','Cumque aliquam optio et voluptatum. Velit quia id qui nihil rerum cum inventore. Fugiat accusamus hic exercitationem voluptas quia dicta assumenda optio numquam facilis dolor tempora praesentium.',NULL,9696.00,8005.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(9,1,2,'Classic Kshlerin, Reilly and Zemlak Accessory','classic-kshlerin-reilly-and-zemlak-accessory-85239','Natus mollitia officiis et velit. Fuga rerum eaque eum voluptas deleniti. Tempore maxime autem eius illum sit a. Saepe quo esse odit omnis sint maiores explicabo voluptatem quasi omnis in similique.',NULL,9404.00,2236.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(10,1,2,'Stylish Moen PLC Bundle','stylish-moen-plc-bundle-10061','Facilis nobis quod assumenda impedit suscipit molestias nihil. Dolores quae vitae sint enim voluptas. Libero quas ad ipsam eligendi impedit omnis placeat amet.',NULL,2315.00,1760.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(11,1,3,'Premium Kutch-Upton Kit','premium-kutch-upton-kit-34074','Sed magni vel ut id voluptatem et quia animi. Sit voluptas magni laborum. Cumque quam ullam et a perferendis est labore qui dolores exercitationem qui voluptate autem adipisci qui officiis.',NULL,3000.00,891.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(12,1,3,'Imported Rice-Murphy Device','imported-rice-murphy-device-18250','Eum et est est atque. Aliquid ipsa expedita sed corrupti et similique. Vero soluta porro ullam veniam voluptatum quos maxime rerum placeat cupiditate necessitatibus pariatur sequi minima ipsam repellendus.',NULL,3475.00,1983.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(13,1,3,'Modern Smith-McGlynn Pack','modern-smith-mcglynn-pack-70957','Veritatis doloremque sit consequuntur qui facere. Quidem quis non aut laborum doloremque nihil molestias ad. Ducimus odit similique quo eveniet. Non repellendus adipisci vel accusamus est unde eum temporibus nesciunt aut officiis veniam et quasi.',NULL,6888.00,1870.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(14,1,3,'Branded Kozey, Connelly and Murray Gadget','branded-kozey-connelly-and-murray-gadget-35802','Aut quidem qui aspernatur aliquam. Nemo omnis eum dolore expedita. Fugit ad aut itaque vitae voluptates quis nihil.',NULL,7476.00,453.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(15,1,3,'Imported Zulauf, Balistreri and Metz Device','imported-zulauf-balistreri-and-metz-device-45294','Quia aperiam quis est animi ex voluptas optio et. Sunt dolorem ipsum corrupti aut consequuntur voluptatem cum. Enim id adipisci nesciunt sunt sit vel consequuntur ab omnis molestias laudantium odit autem dolorem.',NULL,4784.00,3816.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(16,2,4,'Classic Nolan-McGlynn Pack','classic-nolan-mcglynn-pack-71973','Non non adipisci mollitia eum consequuntur asperiores quis porro. Et laborum non dolorum. Laboriosam consequuntur similique consequatur ex quis beatae impedit ratione accusamus quisquam repellat ipsam officiis voluptates et eligendi.',NULL,7532.00,4457.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(17,2,4,'Stylish Beatty, Dickinson and Hegmann Set','stylish-beatty-dickinson-and-hegmann-set-36487','Ipsum et est qui nihil animi. Velit accusantium est eaque dolorum aspernatur voluptatem. Exercitationem ratione error eos quo ut velit. Earum cum qui praesentium repudiandae saepe repellendus dolores cum placeat rerum ipsam deserunt dolore reiciendis et.',NULL,2057.00,754.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(18,2,4,'Professional Pouros, Torphy and Jast Set','professional-pouros-torphy-and-jast-set-88936','Assumenda totam fugiat laboriosam dolor eligendi. Saepe reiciendis eos in commodi. Nihil quia atque et optio error voluptas alias hic.',NULL,6620.00,1717.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(19,2,4,'Premium O\'Hara-Leannon Accessory','premium-ohara-leannon-accessory-66598','Recusandae est ut modi praesentium beatae perferendis. Id rerum sed et ut. Iure et dolorem tempora dolorem qui. Officia eius est saepe velit sunt eos tempora vitae voluptatem.',NULL,9119.00,2489.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(20,2,4,'Branded Dietrich, Hoeger and Pacocha Pack','branded-dietrich-hoeger-and-pacocha-pack-75724','Non et assumenda asperiores omnis et ipsa. Unde dicta totam sint. Consequatur deleniti vero eligendi excepturi sed. Possimus et labore voluptas architecto nulla facilis et quam atque quia provident sit mollitia voluptas.',NULL,8801.00,2616.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(21,2,5,'Premium Stamm, Heidenreich and Nader Pack','premium-stamm-heidenreich-and-nader-pack-38267','Maiores rerum iure sint placeat. Magni repellat non est dicta delectus et. Omnis tenetur laudantium ipsa similique earum minus ad. Nostrum sunt consequatur consequatur nemo nihil nemo reprehenderit dolorem ullam repellendus dolorum quia architecto quia iste eligendi.',NULL,7585.00,1830.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(22,2,5,'Original Friesen-Leffler Accessory','original-friesen-leffler-accessory-15891','Velit laborum qui fuga tempore sunt. Consequatur vel quo tempora sunt. Odio deserunt voluptatum itaque assumenda dolore eos autem. Doloribus in voluptate aliquam commodi enim error fugit ullam consectetur animi nostrum expedita.',NULL,3176.00,2787.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(23,2,5,'Imported Wyman, Hane and Hilpert Accessory','imported-wyman-hane-and-hilpert-accessory-87509','Quam deserunt quo fuga consequatur nam a. Ex aut quis commodi illo maxime sit. Totam est maxime hic necessitatibus nemo. Aperiam corrupti dolores distinctio facere dolore quasi adipisci qui libero.',NULL,7549.00,2198.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(24,2,5,'Imported Wiegand Inc Product','imported-wiegand-inc-product-37851','Aliquid excepturi voluptatem et eum laborum. Deleniti qui ut asperiores doloribus eum earum omnis. Veniam vero quibusdam dolorem architecto accusamus enim veritatis minus sed.',NULL,9204.00,8284.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(25,2,5,'Original Trantow, Swift and Cassin Gadget','original-trantow-swift-and-cassin-gadget-6584','Eveniet accusamus distinctio quae nemo et sit est. Vel numquam id ex quidem ea occaecati. Qui quis quo incidunt explicabo. Sed odio et blanditiis corrupti ipsa nostrum sed ut modi harum voluptatum ea non eligendi adipisci.',NULL,1022.00,656.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(26,2,6,'Branded Auer, Heller and Terry Collection','branded-auer-heller-and-terry-collection-27788','Perspiciatis id voluptatem expedita voluptatem. Corporis commodi qui sunt minus sunt eius. Aut minima unde sapiente veritatis voluptate consequatur modi praesentium enim deleniti enim velit.',NULL,6584.00,3897.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(27,2,6,'Professional Smith Ltd Pack','professional-smith-ltd-pack-52327','Architecto non ea voluptatum. Doloremque nostrum eos nisi tempore doloribus est. Aliquam est fugit fuga fuga ut. Dignissimos molestiae exercitationem est omnis rerum harum sint pariatur praesentium animi beatae incidunt architecto ut rem est.',NULL,6187.00,5118.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(28,2,6,'Original Braun Inc Accessory','original-braun-inc-accessory-43796','Perferendis impedit id ratione numquam et adipisci neque ut. Est molestias inventore suscipit cum perferendis ipsum. Assumenda et atque quas consequatur voluptatum aut perferendis aut quam veniam qui minus.',NULL,1299.00,246.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(29,2,6,'Local Predovic-Wiza Collection','local-predovic-wiza-collection-7351','Ipsam aut quos eos voluptatem. Ipsam omnis in aut saepe. Sed maiores ullam itaque accusantium repellat. Excepturi non aut molestias animi nihil cupiditate et porro corporis sed officiis.',NULL,4679.00,4122.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(30,2,6,'Genuine Veum-Dietrich Bundle','genuine-veum-dietrich-bundle-1025','Nemo consequuntur necessitatibus accusantium quibusdam numquam. Aut et assumenda dolores minus ratione recusandae doloribus. Consequatur nisi omnis nisi iste rerum doloribus modi non libero.',NULL,6523.00,4596.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(31,3,7,'Classic Kautzer, White and Mitchell Bundle','classic-kautzer-white-and-mitchell-bundle-45816','Impedit dolores at quidem et perferendis. Voluptatum sunt quia voluptatem unde id velit sunt. Voluptates quae dolorum natus esse. Et dolorem unde et sint fugiat dicta quae magnam nostrum.',NULL,7100.00,3664.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(32,3,7,'Branded Grant Inc Accessory','branded-grant-inc-accessory-24661','Sint sed eos in corporis. Ut omnis nam sed molestias. Amet omnis est mollitia est cumque. Exercitationem aut maxime nam aut ea quod rerum.',NULL,4257.00,1571.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(33,3,7,'Modern Blick-Bergstrom Product','modern-blick-bergstrom-product-85199','Dolorum eos voluptas ducimus et. Error necessitatibus error ut consequatur nesciunt animi delectus dolorum. Et alias accusamus aliquid explicabo saepe delectus fuga laboriosam possimus excepturi magnam reiciendis.',NULL,7939.00,6190.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(34,3,7,'Modern Bode, Koch and Hermiston Device','modern-bode-koch-and-hermiston-device-95075','Voluptates sit corrupti amet provident quos deserunt. Quia fugit deleniti qui est amet fugit veritatis. Natus voluptas sunt officiis debitis ex dolorum et ex fugit dolor repellendus quisquam quos.',NULL,8723.00,1282.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(35,3,7,'Classic Kohler, Barrows and Jaskolski Gadget','classic-kohler-barrows-and-jaskolski-gadget-61926','Ut a placeat corrupti facere in ad. Odio dolores quia voluptas. Maxime sunt aut alias similique. Eos quo architecto nostrum sit pariatur quis consequuntur consectetur placeat.',NULL,2637.00,1054.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(36,3,8,'Genuine Nikolaus-Fay Device','genuine-nikolaus-fay-device-37535','Perspiciatis provident et voluptas voluptatem cumque placeat. Perspiciatis et dignissimos sunt sunt voluptas. Facere quae nobis id tenetur voluptate ut sit quibusdam.',NULL,4162.00,2219.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(37,3,8,'Classic Swaniawski Group Gadget','classic-swaniawski-group-gadget-28128','Non quibusdam velit voluptatem nostrum neque qui. Cum ipsa ut ea cumque exercitationem odio ut aut. Molestias quia omnis incidunt a impedit corporis et rerum voluptatem.',NULL,6363.00,3063.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(38,3,8,'Local Parker Ltd Item','local-parker-ltd-item-64234','Quidem totam id omnis ut aut ut officia. Eum et est illum corrupti officia quam. Aut placeat numquam magnam nemo iure. In facere voluptatum dolore et aliquid temporibus corporis rerum qui illum ullam aliquid eius occaecati nisi.',NULL,9975.00,5147.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(39,3,8,'Imported Wiegand-Corkery Accessory','imported-wiegand-corkery-accessory-81848','Eum voluptatem provident quibusdam corporis. Et fugiat voluptatem illo est at. Aut dolores eum iusto et. Ducimus ullam doloremque non quod ad officiis quam officiis.',NULL,3648.00,3193.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(40,3,8,'Classic Ward and Sons Collection','classic-ward-and-sons-collection-62214','Qui id tenetur necessitatibus nesciunt neque ullam. Sit blanditiis ea est. Ullam qui similique ut id. Voluptatem laudantium nobis ipsam nobis est delectus ipsa debitis dolorem.',NULL,1263.00,525.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(41,3,9,'Branded Walsh, Cruickshank and Wolff Device','branded-walsh-cruickshank-and-wolff-device-59233','Velit rerum quis natus cupiditate voluptatem totam animi. Dolores iusto in illo debitis. Esse ducimus repellat impedit nihil ducimus qui saepe laborum.',NULL,881.00,703.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(42,3,9,'Genuine McDermott PLC Gadget','genuine-mcdermott-plc-gadget-69668','Nihil possimus ex et eos et minus non. Omnis quis voluptatem sed debitis distinctio voluptatem cupiditate. Odio et itaque quae eos tempora et assumenda repellendus.',NULL,4160.00,1276.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(43,3,9,'Local Skiles, Kemmer and Abbott Item','local-skiles-kemmer-and-abbott-item-87507','Nostrum vitae eius saepe veniam ut harum reiciendis. Voluptates maxime deleniti temporibus quas perspiciatis praesentium ut. Molestiae voluptatibus voluptatem cupiditate quis ut quibusdam sed at et voluptates totam laudantium corrupti.',NULL,8175.00,3913.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(44,3,9,'Stylish Howe, Hoppe and Hilpert Item','stylish-howe-hoppe-and-hilpert-item-65337','Deserunt sit pariatur maiores voluptates minus eligendi consequatur. Molestiae officia accusantium labore ab ad repellat. Dolorem fugit voluptatum facilis officia harum est ullam nulla magni fugit vel.',NULL,7194.00,1378.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(45,3,9,'Stylish Raynor, O\'Keefe and Kuvalis Product','stylish-raynor-okeefe-and-kuvalis-product-43200','Voluptatem hic sed voluptatem. Eveniet quae beatae soluta veritatis quasi. Dolor nemo veniam et corrupti debitis. Esse fugiat aliquam modi numquam nostrum enim nihil.',NULL,3395.00,1840.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(46,4,10,'Premium Orn LLC Bundle','premium-orn-llc-bundle-46129','Sapiente autem iste nesciunt repellat quis dolores. Deserunt doloremque dignissimos ut veritatis excepturi deserunt. Quo quaerat veniam porro nulla ducimus deleniti cumque accusamus quis eius repudiandae non magnam illum officia soluta.',NULL,5606.00,5532.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(47,4,10,'Stylish Howell LLC Collection','stylish-howell-llc-collection-51625','Voluptatem voluptas et a. Consectetur nemo et minus dolores alias consequatur. Corporis ut pariatur architecto impedit excepturi quia tempora minima. Rerum sapiente velit ut ea perferendis nisi molestias possimus repellendus qui placeat.',NULL,1235.00,655.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(48,4,10,'Modern Hansen-Veum Pack','modern-hansen-veum-pack-96236','Quo architecto explicabo esse aut eligendi non officia. Ea excepturi qui doloribus odit consequatur sunt ea eos. Voluptas porro ut ea sed eum voluptate officiis ad omnis enim ipsam quia quis minima.',NULL,3736.00,3062.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(49,4,10,'Original Quigley-Fahey Item','original-quigley-fahey-item-14001','Quidem iste repudiandae optio iure quidem aut. Voluptatem vel rerum et ad aut minima dolorum. Ducimus cupiditate iure nihil tenetur necessitatibus. Magnam cumque rerum sint ducimus error sed et et quasi doloremque ex.',NULL,9157.00,4593.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(50,4,10,'Classic Becker-Nolan Item','classic-becker-nolan-item-12253','Doloremque cumque ipsam saepe iusto aut. Cumque distinctio ex error vitae facilis consequatur sunt. Dolore sunt magnam praesentium unde perferendis culpa repudiandae. Illum sit libero quam ipsa optio nihil eum accusantium cupiditate est aspernatur quod explicabo autem ut at.',NULL,5835.00,1512.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(51,4,11,'Imported Becker PLC Kit','imported-becker-plc-kit-75169','Quas impedit iusto dolore. Ex autem numquam cum ipsum. Nihil in nisi debitis autem saepe quasi nulla. Et eveniet rem asperiores ut officiis aspernatur quas blanditiis.',NULL,8003.00,889.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(52,4,11,'Imported Romaguera, Little and Hane Product','imported-romaguera-little-and-hane-product-17024','Recusandae nulla perferendis quis excepturi omnis nam est. Numquam nulla expedita nihil et. Animi quia eum architecto fugit ea ex ad optio minus eum inventore numquam esse et.',NULL,1327.00,932.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(53,4,11,'Branded Gislason Inc Gadget','branded-gislason-inc-gadget-25552','Ea ea facere autem sit rerum dolorum. Et enim perferendis ducimus qui perspiciatis voluptatem nulla. Praesentium et enim voluptatum natus quae cum ea. Esse in sunt officiis et eos sit voluptas perspiciatis porro laudantium quo.',NULL,9632.00,7932.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(54,4,11,'Classic Schmitt, Schuster and Hartmann Accessory','classic-schmitt-schuster-and-hartmann-accessory-97118','Numquam velit mollitia sunt. Quam cupiditate atque voluptatem sunt. Sequi voluptatem tenetur et maxime dolor. Est aut magnam praesentium ut error iusto fugiat rerum excepturi perspiciatis itaque.',NULL,1182.00,168.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(55,4,11,'Premium Fritsch-King Accessory','premium-fritsch-king-accessory-46088','Tempora quam facilis velit. Soluta enim aspernatur repellendus sunt. Voluptate cumque voluptas dolorem nihil facere temporibus dignissimos eum. Non et labore quia illum itaque totam et veniam.',NULL,2610.00,2147.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(56,4,12,'Premium Gislason LLC Collection','premium-gislason-llc-collection-68420','Molestias placeat quia quo hic quia. Blanditiis repellendus facere dignissimos sapiente. Nemo et non velit sit modi. Dolor qui enim vitae ipsa rem velit esse.',NULL,1989.00,1453.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(57,4,12,'Premium Jacobson-Dach Bundle','premium-jacobson-dach-bundle-67025','Quis qui ut sunt. Non quam et nemo veniam et voluptas blanditiis. Sequi delectus qui commodi est quae aut omnis facilis vel.',NULL,8371.00,7010.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(58,4,12,'Classic Heidenreich, Cole and Emard Set','classic-heidenreich-cole-and-emard-set-29299','Rem et quia enim nesciunt itaque eveniet. Necessitatibus unde nulla ipsa aperiam. Voluptatem sit fugit quod qui. Cum voluptatem reiciendis eos cumque suscipit aspernatur inventore animi dolorem illo sapiente repudiandae illum.',NULL,4527.00,1177.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(59,4,12,'Imported Gutkowski, Douglas and Lueilwitz Accessory','imported-gutkowski-douglas-and-lueilwitz-accessory-67014','Ipsum deserunt voluptas vitae assumenda amet temporibus nam. Voluptate assumenda aspernatur ut voluptatibus sed id magni. Ab quae quam laboriosam deserunt velit. Totam aut aut corporis et eos veniam eius illo modi.',NULL,1960.00,1200.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(60,4,12,'Imported Padberg, Kuhn and VonRueden Gadget','imported-padberg-kuhn-and-vonrueden-gadget-53767','Expedita delectus odio accusantium aut recusandae totam fuga omnis. Corporis quo maiores veniam deserunt blanditiis. Et et facilis facilis sit enim optio sit non vel necessitatibus cum sapiente.',NULL,714.00,538.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(61,5,13,'Stylish Gaylord-Kohler Gadget','stylish-gaylord-kohler-gadget-74078','Culpa in voluptas est explicabo fugit saepe ut. Qui sint asperiores exercitationem libero dignissimos. Voluptatem velit dolorum laborum qui velit corrupti sunt consectetur quod accusantium eligendi et voluptatibus dolorem qui.',NULL,9277.00,8998.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(62,5,13,'Local Rolfson Inc Product','local-rolfson-inc-product-77607','Sit minima aperiam id maiores alias. Saepe veritatis sint id reprehenderit quo magni. Ea beatae voluptatem at consequatur. Minima error est velit recusandae ipsa voluptas sed consequatur nostrum omnis dolor qui quia sit.',NULL,2420.00,1717.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(63,5,13,'Original Pollich-Hagenes Device','original-pollich-hagenes-device-66632','Repudiandae reprehenderit enim iusto laboriosam neque omnis. Error rerum dolor aut. Perferendis dolor distinctio voluptatem iusto doloremque et sapiente. Et architecto blanditiis eveniet sed non id omnis fugiat.',NULL,7676.00,508.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(64,5,13,'Stylish Walsh Ltd Item','stylish-walsh-ltd-item-27289','Libero enim qui sit sunt quisquam placeat fugit quia. Exercitationem expedita quam quia temporibus facilis. Optio optio accusamus illo sunt non aliquam consequatur quisquam et ad non beatae.',NULL,4747.00,1476.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(65,5,13,'Stylish Bruen and Sons Device','stylish-bruen-and-sons-device-78329','Aliquid non et et fugit iste. Debitis mollitia libero omnis. Aliquid ut ut ex odio ut amet dolor molestiae voluptas amet saepe voluptas dolores molestiae voluptatem.',NULL,5354.00,554.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(66,5,14,'Premium Kerluke, Breitenberg and Hammes Set','premium-kerluke-breitenberg-and-hammes-set-43244','Voluptatem iste dolorem tempore aut. Corrupti possimus omnis officia at ipsam ducimus quo. Aliquid dicta autem ipsam molestiae ipsum expedita necessitatibus voluptatem quod.',NULL,5974.00,5215.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(67,5,14,'Professional Schuster Inc Gadget','professional-schuster-inc-gadget-6454','Et quis labore aut est aperiam dolorem excepturi earum. Voluptatem dolorem fugiat impedit. Qui velit eveniet voluptas modi hic aperiam id.',NULL,6001.00,1249.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(68,5,14,'Professional Dickinson Group Item','professional-dickinson-group-item-35629','Recusandae nisi accusamus expedita cupiditate fugiat voluptatibus. Atque at repellendus et officiis nulla molestiae nihil eum. Deleniti quia distinctio quo nulla non a fugiat. Rerum tempora similique similique enim totam dolorem aliquid ex.',NULL,9251.00,2688.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(69,5,14,'Local Stehr, Zboncak and Skiles Kit','local-stehr-zboncak-and-skiles-kit-75588','Placeat voluptates recusandae expedita voluptas. Voluptatem quia deleniti in minima dolore. A ea nostrum sapiente modi saepe totam omnis maiores.',NULL,9190.00,1199.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(70,5,14,'Original Shanahan LLC Pack','original-shanahan-llc-pack-20399','Et ut velit dicta qui culpa sapiente autem. Sapiente est voluptatem et impedit quidem quia. Velit incidunt dolor esse adipisci eaque dignissimos ipsam cupiditate aut iste sit quae.',NULL,4843.00,1557.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(71,5,15,'Branded Streich, Block and Wilderman Collection','branded-streich-block-and-wilderman-collection-32128','Repellendus voluptas doloremque fugiat. Ullam voluptates qui corporis. Vel vel architecto qui et. Dolore omnis beatae animi autem sed cumque ab praesentium molestiae nesciunt.',NULL,6690.00,5427.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(72,5,15,'Branded Keebler, Abernathy and Nitzsche Bundle','branded-keebler-abernathy-and-nitzsche-bundle-34125','Exercitationem suscipit ea occaecati inventore voluptatem deserunt. Eos est quasi necessitatibus. Reiciendis est eos culpa et eveniet quia ut enim veritatis officiis provident et repellat possimus dignissimos sunt.',NULL,9356.00,3845.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(73,5,15,'Imported Ryan, Klocko and Wisoky Accessory','imported-ryan-klocko-and-wisoky-accessory-80579','Quod ad molestiae et quo placeat. Temporibus et iure sed rerum voluptatem sit eveniet. Officia rerum voluptatum perferendis et asperiores. Quia iusto omnis ut assumenda quasi voluptatem et officia vitae illum qui nihil illo sed dolore.',NULL,1059.00,380.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(74,5,15,'Modern Abbott, Rippin and Hermann Collection','modern-abbott-rippin-and-hermann-collection-19483','Doloremque voluptatem autem ipsum exercitationem. Suscipit illo eos ratione voluptatem ab similique. Aut numquam autem asperiores natus. Excepturi dolores qui eveniet nulla expedita quasi quisquam asperiores tenetur.',NULL,5383.00,2318.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(75,5,15,'Classic Frami-Hagenes Set','classic-frami-hagenes-set-74782','Iusto autem voluptates ipsum aut accusantium ad. Quidem saepe amet ut nostrum veritatis perspiciatis. Perspiciatis laboriosam deserunt est amet perferendis consequuntur sequi a magni ipsum est.',NULL,7583.00,2447.00,1,'2026-11-06 12:38:09','2026-11-06 12:38:09');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
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
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subcategories`
--

DROP TABLE IF EXISTS `subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subcategories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subcategories_category_id_slug_unique` (`category_id`,`slug`),
  KEY `subcategories_category_id_index` (`category_id`),
  CONSTRAINT `subcategories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subcategories`
--

LOCK TABLES `subcategories` WRITE;
/*!40000 ALTER TABLE `subcategories` DISABLE KEYS */;
INSERT INTO `subcategories` VALUES (1,1,'Mobile Phones','mobile-phones','Voluptatem ea consequatur et vitae quidem quaerat ut et ex laudantium reprehenderit ut vel.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(2,1,'Laptops & Computers','laptops-computers','Voluptatem unde delectus et veniam assumenda voluptas mollitia tempora aut nisi eum.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(3,1,'Cameras & Photography','cameras-photography','Animi adipisci ad facilis similique quis sit et quae et aliquam.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(4,2,'Audio & Headphones','audio-headphones','Officiis aliquam sint voluptas tenetur explicabo quia temporibus nemo perferendis.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(5,2,'Smart Watches','smart-watches','Voluptatibus rem aut architecto voluptate sit autem vel porro non reprehenderit voluptas.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(6,2,'Men\'s Clothing','mens-clothing','Ipsum culpa quisquam tenetur vel consequuntur id blanditiis.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(7,3,'Women\'s Clothing','womens-clothing','Iste occaecati laborum quidem et sunt odio ipsa aliquid dolorum doloribus asperiores ipsum aut.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(8,3,'Footwear','footwear','Quae eum commodi inventore fugit omnis deserunt fuga.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(9,3,'Bags & Luggage','bags-luggage','Aut ab dolorum est facere numquam debitis ipsam laboriosam molestias.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(10,4,'Jewellery & Accessories','jewellery-accessories','Possimus eligendi consequatur eaque in quam voluptatem totam voluptatum voluptates exercitationem.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(11,4,'Furniture','furniture','Expedita qui blanditiis est ipsa quaerat nostrum.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(12,4,'Kitchen & Dining','kitchen-dining','Consequatur ex molestias et autem labore cupiditate recusandae quasi aut et.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(13,5,'Bedding & Bath','bedding-bath','Earum itaque repellat ut aut quasi beatae enim enim consequuntur.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(14,5,'Home D├⌐cor','home-decor','Mollitia tempora aliquid voluptatem nam minus reprehenderit repudiandae et non nobis provident eligendi sunt.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09'),(15,5,'Lighting','lighting','Aut perferendis quas aspernatur iure assumenda sit enim ut tempora rerum molestiae dignissimos minus.',1,'2026-11-06 12:38:09','2026-11-06 12:38:09');
/*!40000 ALTER TABLE `subcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@banglapuzzle.com','2026-11-06 12:38:09','$2y$12$HQchOntBhEhXd8ubyK60meZLgXh4jznA2eiY859nMTynFbS1KQS/u',NULL,1,'2026-11-06 12:38:09','2026-11-06 12:38:09');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'banglapuzzle'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-07  0:43:23
