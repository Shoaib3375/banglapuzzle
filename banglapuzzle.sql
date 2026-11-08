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
INSERT INTO `categories` VALUES (1,'Electronics','electronics','Est molestias quia reiciendis eius odit sunt amet fuga sit est doloribus.',1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(2,'Fashion','fashion','Quisquam consequuntur architecto quisquam est dolorem autem occaecati eum et harum.',1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(3,'Home & Living','home-living','Illum eum atque est omnis illum quo eius repellendus unde porro non qui est.',1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(4,'Beauty & Health','beauty-health','Consectetur vitae repellat perferendis est dicta recusandae omnis nihil.',1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(5,'Books & Stationery','books-stationery','Commodi dolorem et facilis ad quidem ut excepturi voluptates deserunt totam eligendi aut quo.',1,'2026-11-08 06:49:02','2026-11-08 06:49:02');
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_01_01_000001_add_is_admin_to_users_table',1),(5,'2025_01_01_000002_create_categories_table',1),(6,'2025_01_01_000003_create_subcategories_table',1),(7,'2025_01_01_000004_create_products_table',1),(8,'2026_11_06_232444_create_product_slug_histories_table',1),(9,'2026_11_07_164500_add_profile_fields_to_users_table',1),(10,'2026_11_07_174235_create_orders_table',1),(11,'2026_11_07_174236_create_order_items_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
  `product_id` bigint unsigned DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `old_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `quantity` int unsigned NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
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
  `user_id` bigint unsigned DEFAULT NULL,
  `order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `landmark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cod',
  `subtotal` decimal(10,2) NOT NULL,
  `delivery_charge` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_index` (`user_id`),
  KEY `orders_status_index` (`status`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
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
-- Table structure for table `product_slug_histories`
--

DROP TABLE IF EXISTS `product_slug_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_slug_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `old_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_slug_histories_old_slug_unique` (`old_slug`),
  KEY `product_slug_histories_product_id_foreign` (`product_id`),
  KEY `product_slug_histories_old_slug_index` (`old_slug`),
  CONSTRAINT `product_slug_histories_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_slug_histories`
--

LOCK TABLES `product_slug_histories` WRITE;
/*!40000 ALTER TABLE `product_slug_histories` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_slug_histories` ENABLE KEYS */;
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
INSERT INTO `products` VALUES (1,1,1,'Professional Walter Ltd Product','professional-walter-ltd-product-65223','Dolores autem est dolorum sunt ex facilis. Quia iusto consequatur harum est enim tempora consequatur in. Assumenda quos aut dolore. Assumenda assumenda itaque explicabo consectetur officia rem dolor.','https://picsum.photos/seed/730/600/600',5148.00,4899.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(2,1,1,'Premium Lebsack, Steuber and Hegmann Accessory','premium-lebsack-steuber-and-hegmann-accessory-1772','Architecto aut et et quia. Dolorum similique accusamus ut tenetur ut natus blanditiis. Quis non animi impedit ea mollitia sed sequi. Placeat et voluptatibus molestias ratione est aut molestias atque unde eos magni sapiente atque maiores reiciendis vel.','https://picsum.photos/seed/482/600/600',4021.00,2566.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(3,1,1,'Local Hoppe, Abbott and Rath Kit','local-hoppe-abbott-and-rath-kit-7626','Labore consequatur cum perspiciatis dignissimos quidem itaque ullam. Natus ipsa et asperiores ipsa velit. Eius autem omnis adipisci et laboriosam repudiandae pariatur et amet.','https://picsum.photos/seed/728/600/600',3662.00,489.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(4,1,1,'Stylish Koepp PLC Set','stylish-koepp-plc-set-89828','Quia commodi qui laudantium modi. Alias provident voluptatibus architecto fugiat in corrupti. Quod iure animi natus repellendus voluptatum ab laboriosam ut eaque animi aut ea ipsa quod.','https://picsum.photos/seed/361/600/600',1170.00,1164.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(5,1,1,'Modern Lubowitz, Becker and Ferry Kit','modern-lubowitz-becker-and-ferry-kit-29793','Architecto iste molestiae quis quae. Reiciendis dolore occaecati ut. Sunt libero est blanditiis temporibus accusamus et nobis ipsam temporibus voluptas occaecati nemo animi architecto doloremque.','https://picsum.photos/seed/497/600/600',6442.00,886.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(6,1,2,'Classic Kovacek, Mante and Hand Collection','classic-kovacek-mante-and-hand-collection-76607','Nesciunt laborum et repellat eveniet nobis. Cupiditate iste deserunt eaque ad in unde. Qui ipsa molestias qui velit est quisquam totam debitis voluptatem placeat labore est quod rerum facere.','https://picsum.photos/seed/985/600/600',5198.00,1230.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(7,1,2,'Branded Witting, Lemke and Frami Bundle','branded-witting-lemke-and-frami-bundle-57294','Perferendis perferendis ex velit unde. Nesciunt dolor dignissimos cum modi saepe. Molestiae itaque nam nobis eum ut provident adipisci. Debitis rerum et ipsam voluptate repellendus natus voluptates voluptates odit error consequatur.','https://picsum.photos/seed/641/600/600',7662.00,5124.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(8,1,2,'Imported Paucek-Daugherty Collection','imported-paucek-daugherty-collection-50241','Ut mollitia sed vel sunt. Vel corrupti nihil corporis et. Voluptatem veniam non est aspernatur aut quo quia totam porro deleniti excepturi officia dolores et nulla facilis.','https://picsum.photos/seed/461/600/600',6588.00,2953.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(9,1,2,'Modern Towne-Buckridge Accessory','modern-towne-buckridge-accessory-87930','Incidunt eaque dolor ut id aut earum. Cupiditate nostrum est quas dolorum voluptas. Vel voluptatem quo sed voluptates aspernatur dolorum minus autem a molestiae eius non aperiam deleniti dolore.','https://picsum.photos/seed/697/600/600',7276.00,2896.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(10,1,2,'Premium Goldner, Koch and Graham Gadget','premium-goldner-koch-and-graham-gadget-2520','Quidem laboriosam aut soluta nostrum sint exercitationem. Ab officiis aperiam eaque et voluptatibus quia voluptate dolorum. Ab et et totam dicta quo magnam aut ducimus.','https://picsum.photos/seed/841/600/600',8497.00,1421.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(11,1,3,'Classic Breitenberg-Kilback Accessory','classic-breitenberg-kilback-accessory-74625','Quod doloremque dolore suscipit quia veniam suscipit. Voluptatem veritatis soluta vero eum dolorem ea. Molestiae qui sunt consequatur ut recusandae et et et reprehenderit quisquam magnam quia rerum aut.','https://picsum.photos/seed/343/600/600',3141.00,2974.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(12,1,3,'Local Abernathy and Sons Pack','local-abernathy-and-sons-pack-13783','Magnam repellat fugit est ut odit magni. Dignissimos velit asperiores eos unde sint voluptatem. Incidunt dolorem non et in ut consequatur fuga rerum id quo qui et.','https://picsum.photos/seed/991/600/600',2419.00,1189.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(13,1,3,'Premium Powlowski-McClure Device','premium-powlowski-mcclure-device-7819','Deserunt quo voluptas adipisci quia ipsum at sunt magni. Et quidem itaque numquam officia. Sit qui qui ex molestiae consequatur quibusdam voluptas vero nihil et magnam dolores a et.','https://picsum.photos/seed/375/600/600',5527.00,1563.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(14,1,3,'Professional Kertzmann-Weissnat Kit','professional-kertzmann-weissnat-kit-25905','Quis quas vitae iste dolorem vel sunt. Et unde esse explicabo rerum. Sed quidem explicabo nesciunt consequatur quo optio et dolores maxime.','https://picsum.photos/seed/176/600/600',3694.00,3251.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(15,1,3,'Branded Douglas, Marquardt and Jenkins Gadget','branded-douglas-marquardt-and-jenkins-gadget-14818','Optio tempore mollitia ut beatae. Quibusdam molestiae voluptates pariatur consequatur dolor dignissimos inventore. Aut eaque voluptatem velit. Ab doloribus aut ut tenetur tempore cumque eum.','https://picsum.photos/seed/391/600/600',1531.00,1106.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(16,2,4,'Professional VonRueden, Mann and Grimes Set','professional-vonrueden-mann-and-grimes-set-66944','Quisquam nihil veniam omnis qui adipisci. Non nihil excepturi dolor rerum suscipit iusto beatae. Eligendi aspernatur necessitatibus optio et dolores fugiat veritatis nam nulla distinctio reiciendis et molestiae possimus.','https://picsum.photos/seed/257/600/600',9156.00,5398.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(17,2,4,'Original Wilderman, Kessler and Cassin Set','original-wilderman-kessler-and-cassin-set-64907','Ea aperiam quo ea. Est dolorum accusantium aut aperiam. Dolorem illo nisi labore ullam nobis qui quia.','https://picsum.photos/seed/848/600/600',9872.00,8880.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(18,2,4,'Genuine Marvin, Kerluke and Cruickshank Set','genuine-marvin-kerluke-and-cruickshank-set-11354','Dolore ipsa rerum qui et labore molestiae rerum. Quos aut unde provident et eos velit inventore. Vitae eveniet officia quo id. Adipisci quibusdam molestiae reprehenderit ab tempora autem ipsam velit aut suscipit praesentium.','https://picsum.photos/seed/978/600/600',245.00,203.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(19,2,4,'Modern Osinski LLC Pack','modern-osinski-llc-pack-40751','Sint modi deserunt dolores culpa eius voluptas. Est et rerum aperiam pariatur fugit numquam. Nihil delectus dolorem atque esse facere aut necessitatibus incidunt. Deleniti consequatur adipisci quos iusto aliquid quisquam similique.','https://picsum.photos/seed/325/600/600',7993.00,5138.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(20,2,4,'Genuine Kshlerin, Brakus and Schneider Pack','genuine-kshlerin-brakus-and-schneider-pack-14953','Vel et veniam quasi asperiores vitae porro ea. Ea fuga odio laboriosam et voluptatem. Aut maiores optio autem vel placeat expedita. In libero incidunt dolore cum exercitationem consectetur id placeat sed exercitationem consequuntur.','https://picsum.photos/seed/522/600/600',2545.00,349.00,1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(21,2,5,'Stylish McCullough, Anderson and Jacobs Kit','stylish-mccullough-anderson-and-jacobs-kit-42819','Qui cupiditate qui in aut nihil. Eaque sed architecto laudantium nisi. Et maxime quod dolorem quia architecto itaque nam neque.','https://picsum.photos/seed/438/600/600',3168.00,223.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(22,2,5,'Professional Bayer PLC Gadget','professional-bayer-plc-gadget-67176','Eveniet eligendi in velit et modi est aliquid. Autem sint quaerat doloribus. Quidem et aut illo est sed ducimus voluptatem et omnis mollitia exercitationem quos error ipsa itaque.','https://picsum.photos/seed/849/600/600',8746.00,2301.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(23,2,5,'Premium Reynolds-Baumbach Gadget','premium-reynolds-baumbach-gadget-37421','Possimus nisi mollitia explicabo amet provident. Sed molestiae porro optio qui inventore. Voluptatem ipsam rerum quaerat nam quam est vel saepe sed eos ipsam ipsam repellat voluptatem.','https://picsum.photos/seed/615/600/600',3982.00,1171.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(24,2,5,'Classic Larkin, Borer and Fritsch Kit','classic-larkin-borer-and-fritsch-kit-68505','Odit quas laboriosam laborum excepturi fugiat delectus neque. Distinctio aliquid pariatur consequuntur. In fugit illo et voluptatum et. Eveniet ut quia omnis magni officiis eos qui a nobis optio aut laboriosam.','https://picsum.photos/seed/525/600/600',5391.00,162.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(25,2,5,'Stylish Waters Group Device','stylish-waters-group-device-5050','Unde modi sit est deserunt. Provident quasi officiis fuga nemo labore in delectus quaerat. Quibusdam occaecati sunt maxime quod. Adipisci aspernatur dolore illum excepturi saepe nisi sunt excepturi minima adipisci officiis esse et quo nisi.','https://picsum.photos/seed/887/600/600',1875.00,1009.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(26,2,6,'Imported Lockman, Willms and Hickle Device','imported-lockman-willms-and-hickle-device-40506','Quia voluptatem quis dolores magni. Earum consequatur voluptas quo unde illum maiores ratione. Nemo cupiditate assumenda voluptas quo animi quod debitis ea.','https://picsum.photos/seed/640/600/600',2088.00,1086.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(27,2,6,'Original Rippin-Carter Product','original-rippin-carter-product-50679','Qui nostrum dolor quod et eius. Et repellat qui rerum consectetur omnis laudantium. Optio sunt et vel eligendi dolorem eaque quo ad quos quo qui omnis provident vitae aut.','https://picsum.photos/seed/6/600/600',2541.00,1617.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(28,2,6,'Imported Kunze-Jaskolski Pack','imported-kunze-jaskolski-pack-7823','Omnis deleniti est et labore numquam amet officia. Nostrum qui temporibus sequi. Qui inventore ducimus ipsum et. Quidem repellendus quis accusamus consequatur error eveniet commodi quos eaque repudiandae autem quasi tenetur.','https://picsum.photos/seed/63/600/600',9863.00,8287.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(29,2,6,'Branded Batz and Sons Kit','branded-batz-and-sons-kit-59960','Recusandae et et et facere. Sit aut eaque dolor animi corporis magni id possimus. Est deserunt incidunt laboriosam tempora ea qui fuga fuga cum eum inventore enim beatae saepe.','https://picsum.photos/seed/733/600/600',410.00,150.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(30,2,6,'Modern Harber, Konopelski and Littel Kit','modern-harber-konopelski-and-littel-kit-20819','Dolorum sed voluptas et aut ratione ipsam. Voluptatem explicabo saepe soluta minima rem saepe sit. Occaecati cumque saepe voluptatum id modi iure sequi magni minus ea debitis quae laudantium architecto ducimus.','https://picsum.photos/seed/218/600/600',3966.00,239.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(31,3,7,'Premium Sipes, O\'Conner and Terry Gadget','premium-sipes-oconner-and-terry-gadget-66619','Dignissimos assumenda ipsum qui temporibus. Omnis molestiae aut molestiae architecto illo et voluptatem. Repudiandae qui nobis et cum fuga quasi magni. Veritatis ipsam suscipit sapiente in est necessitatibus sapiente consequatur id.','https://picsum.photos/seed/741/600/600',3196.00,1886.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(32,3,7,'Imported Herzog-Robel Accessory','imported-herzog-robel-accessory-56928','Omnis praesentium qui minus impedit possimus animi. Expedita perferendis nobis veniam explicabo recusandae exercitationem. Illum soluta vitae quasi quae nisi dolores illo ut dolores impedit tempora reiciendis inventore eum voluptates.','https://picsum.photos/seed/9/600/600',4851.00,2834.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(33,3,7,'Original VonRueden-Goodwin Collection','original-vonrueden-goodwin-collection-9151','Rem harum molestiae sapiente necessitatibus neque ut qui magnam. Est quos ab corrupti iste doloribus recusandae. Rerum quaerat aut cum id sint est nobis cumque debitis repudiandae.','https://picsum.photos/seed/994/600/600',1471.00,507.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(34,3,7,'Genuine Walsh Group Kit','genuine-walsh-group-kit-22089','Quo in odio omnis voluptatum deleniti beatae rerum. Delectus voluptatem est et qui. Odit sit minima cumque et. Sint sint aut ad nam quia ullam suscipit natus ex doloribus placeat earum quia perspiciatis voluptas.','https://picsum.photos/seed/511/600/600',1796.00,1328.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(35,3,7,'Premium Ryan, Collins and Muller Product','premium-ryan-collins-and-muller-product-51730','Optio aspernatur nostrum debitis quas voluptatum molestiae. Dolorum aut est illo ut. Minima necessitatibus ut rerum enim. Eligendi harum qui voluptatem saepe deleniti alias adipisci.','https://picsum.photos/seed/237/600/600',680.00,344.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(36,3,8,'Premium Zieme-Lang Product','premium-zieme-lang-product-1615','Id rerum saepe sed expedita. Itaque accusamus facilis consectetur quis ullam. Et repellat eveniet debitis cupiditate enim numquam reiciendis. Assumenda quisquam maxime inventore quasi dolor autem inventore recusandae dolor quidem.','https://picsum.photos/seed/295/600/600',7112.00,4501.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(37,3,8,'Imported Klocko, Ratke and Stark Pack','imported-klocko-ratke-and-stark-pack-28077','Iure est fuga et suscipit dolor perspiciatis est. Praesentium veritatis non earum ipsa. Quos iste non quia doloremque quas sint modi. Repudiandae dolores nesciunt voluptatibus voluptatem quia aut sunt nihil aut nulla.','https://picsum.photos/seed/345/600/600',3151.00,1448.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(38,3,8,'Genuine Casper Inc Product','genuine-casper-inc-product-19935','Iusto aut nihil possimus autem vero ut aut. Recusandae perferendis quaerat aut magni voluptas quas voluptas. Expedita illo velit impedit corporis aut dolores. Tempore eius hic qui consequatur nihil expedita veritatis sequi qui libero non natus at repellendus ut.','https://picsum.photos/seed/608/600/600',5558.00,2896.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(39,3,8,'Genuine Gibson and Sons Item','genuine-gibson-and-sons-item-20035','Aut vitae sint modi architecto laudantium quod nobis. Esse eaque ut iste ipsa ad provident incidunt. Qui tempore quisquam asperiores est. Velit ex modi autem non fugit soluta facere accusantium architecto ab.','https://picsum.photos/seed/142/600/600',9285.00,8117.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(40,3,8,'Modern Lemke-Heathcote Item','modern-lemke-heathcote-item-69357','Asperiores sit pariatur totam dolore autem hic. Beatae non accusamus veniam. Non eum perspiciatis at pariatur deserunt et doloremque optio consequatur quia.','https://picsum.photos/seed/724/600/600',2537.00,749.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(41,3,9,'Modern Kris, Schiller and Barton Kit','modern-kris-schiller-and-barton-kit-66272','Eius accusantium rerum ipsam aut id sint. Iure architecto in enim tenetur. Fugit qui nisi illum quis iusto ad consequatur dolorum consectetur minus.','https://picsum.photos/seed/721/600/600',3770.00,2832.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(42,3,9,'Professional Erdman-Breitenberg Gadget','professional-erdman-breitenberg-gadget-7113','Sunt libero amet itaque consequatur. Aut qui molestias tenetur ut. Animi provident perspiciatis voluptatem commodi dolore. Velit perferendis eum sed praesentium et omnis dignissimos.','https://picsum.photos/seed/983/600/600',1454.00,673.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(43,3,9,'Local Tillman-Franecki Device','local-tillman-franecki-device-11334','Ad possimus nisi non quos eveniet. Laborum tenetur debitis corrupti illum qui incidunt dolor qui. Deserunt perspiciatis quibusdam magnam ab. Doloribus dicta sed eaque enim est sunt autem sunt quam vitae quas unde nemo.','https://picsum.photos/seed/230/600/600',8086.00,5152.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(44,3,9,'Stylish Hansen and Sons Set','stylish-hansen-and-sons-set-17182','Aut possimus quo impedit dignissimos ipsum sunt. Qui repellat ab maiores assumenda laudantium cum quas molestiae. Dicta qui iusto et mollitia ipsam tenetur optio deleniti dolor.','https://picsum.photos/seed/333/600/600',5381.00,298.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(45,3,9,'Modern Kuhlman, Lowe and Thiel Set','modern-kuhlman-lowe-and-thiel-set-16040','Et eos labore culpa dignissimos optio. Impedit dolor quis quod. Illum ipsam reiciendis possimus non eum adipisci amet doloremque saepe eius expedita inventore libero saepe.','https://picsum.photos/seed/450/600/600',3346.00,1333.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(46,4,10,'Modern Kutch PLC Set','modern-kutch-plc-set-31239','Voluptatum alias molestiae accusamus voluptates labore vel. Reprehenderit et illum occaecati aut quia quia consequatur. Deserunt cum nihil aspernatur hic quo. Beatae culpa placeat eos ipsa sit culpa vel sit.','https://picsum.photos/seed/732/600/600',4180.00,2895.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(47,4,10,'Branded McLaughlin, Moen and Osinski Gadget','branded-mclaughlin-moen-and-osinski-gadget-2211','Quia enim consequuntur eum neque fuga autem. Est neque qui et consequuntur. Voluptatem enim veritatis enim sint cumque ab qui aut maxime.','https://picsum.photos/seed/456/600/600',586.00,425.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(48,4,10,'Genuine O\'Kon-Johnson Bundle','genuine-okon-johnson-bundle-77818','Sapiente culpa dignissimos ipsum ullam id. Et et sit in mollitia voluptatem aperiam. Sapiente assumenda enim exercitationem. Quidem voluptatem molestiae commodi vel est ut tempore deleniti et pariatur culpa quo et omnis facilis.','https://picsum.photos/seed/352/600/600',4742.00,692.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(49,4,10,'Modern Langosh, Huel and Trantow Set','modern-langosh-huel-and-trantow-set-3113','Dolores eveniet reprehenderit provident officia praesentium molestias. Sunt provident porro quasi neque asperiores. Autem maxime voluptatem voluptas iure quisquam recusandae atque. Et similique laboriosam soluta iusto quia qui illum perferendis et.','https://picsum.photos/seed/930/600/600',3402.00,2807.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(50,4,10,'Modern Feeney, Kreiger and Harvey Item','modern-feeney-kreiger-and-harvey-item-31374','Nobis non et ullam qui amet optio. Tempore possimus est voluptatum assumenda a nesciunt quo. A et velit minus sed minima rem deserunt numquam repellat iure quas autem ea non blanditiis.','https://picsum.photos/seed/900/600/600',5314.00,2005.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(51,4,11,'Classic Schulist Ltd Device','classic-schulist-ltd-device-53311','Perspiciatis soluta modi animi facilis omnis vero. Incidunt voluptate omnis aut. Aut in voluptatum inventore. Laboriosam rem ut et ipsum aliquam doloremque dolorum dolorem rerum.','https://picsum.photos/seed/896/600/600',2143.00,1062.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(52,4,11,'Stylish Nader, Hayes and Beier Pack','stylish-nader-hayes-and-beier-pack-76672','Nostrum accusamus consequatur cupiditate nesciunt ut debitis doloribus. Sit numquam quasi consequatur vitae autem. Cum saepe laborum culpa provident et. Vitae ut nemo ut et et ut quo perferendis error neque est.','https://picsum.photos/seed/300/600/600',5684.00,1704.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(53,4,11,'Genuine Zemlak, Goldner and Strosin Gadget','genuine-zemlak-goldner-and-strosin-gadget-98164','Enim aut possimus reprehenderit dicta pariatur alias aut ut. Nemo distinctio odio possimus aperiam tempore odio repellendus aut. Ullam quam saepe officiis non pariatur repudiandae animi laudantium architecto praesentium quidem et ipsa sapiente.','https://picsum.photos/seed/827/600/600',6278.00,5452.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(54,4,11,'Branded Stracke-Ortiz Set','branded-stracke-ortiz-set-33465','Explicabo dicta rerum omnis aut error dolorem unde magnam. Molestiae dolor adipisci voluptatem at. Aut quia iste sunt. Dolorem dolores ad assumenda mollitia ipsum magni placeat atque consequatur in ea sint.','https://picsum.photos/seed/792/600/600',887.00,349.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(55,4,11,'Imported Paucek-Hand Accessory','imported-paucek-hand-accessory-31213','Ut in rerum quis similique voluptatum tempore. Dolores quia voluptate suscipit et porro praesentium eos. Et in ut maxime iusto labore ut error. Numquam est rerum illum fugit quia aut eum suscipit quia enim sit.','https://picsum.photos/seed/760/600/600',7913.00,6200.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(56,4,12,'Professional Huels, DuBuque and Stiedemann Item','professional-huels-dubuque-and-stiedemann-item-37022','Nihil est et ut labore fugiat eveniet culpa consequuntur. Dolor dolor minima accusamus explicabo assumenda nihil. Praesentium officiis id sint aut temporibus et corrupti aliquid neque et.','https://picsum.photos/seed/877/600/600',2676.00,1899.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(57,4,12,'Classic Murazik Ltd Kit','classic-murazik-ltd-kit-3802','Rem incidunt amet fuga aut dicta ea tempore. Provident autem velit minus. Dolorem aut inventore rerum consectetur optio exercitationem est ad et voluptate esse enim.','https://picsum.photos/seed/609/600/600',5193.00,2524.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(58,4,12,'Modern Ondricka PLC Pack','modern-ondricka-plc-pack-67613','Eaque quidem voluptas maxime dicta et vel quia. Et fuga omnis ut. Totam qui aliquam excepturi dolorem et nulla soluta maxime iusto adipisci debitis corrupti est officia possimus ut.','https://picsum.photos/seed/88/600/600',3225.00,458.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(59,4,12,'Professional Sawayn-Bradtke Pack','professional-sawayn-bradtke-pack-35584','Non unde officiis repellat corporis quis. Dolorum et dolorem consequatur. Placeat voluptas culpa aspernatur vel fugiat voluptate rem eius dolores.','https://picsum.photos/seed/430/600/600',1542.00,765.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(60,4,12,'Genuine Rogahn, Pouros and Kozey Item','genuine-rogahn-pouros-and-kozey-item-5397','Rerum voluptatem laudantium non praesentium aliquam quos. Ut aliquam placeat ex dolorem minima ut cumque non. Occaecati ab est aut et. Ab ratione assumenda est consequuntur rem unde alias recusandae ea quos commodi ut possimus debitis.','https://picsum.photos/seed/617/600/600',6175.00,874.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(61,5,13,'Local Barrows Inc Gadget','local-barrows-inc-gadget-88480','Ea voluptas voluptas qui dolorum. Incidunt nemo fugit ut voluptas omnis molestiae voluptatem culpa. Suscipit ut dolor voluptatibus magni. Sint repellat maiores expedita nam fugit inventore cum provident aspernatur vitae.','https://picsum.photos/seed/572/600/600',3897.00,1923.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(62,5,13,'Professional Goyette, Harris and Gerhold Accessory','professional-goyette-harris-and-gerhold-accessory-67073','Qui laboriosam eum perspiciatis sit aliquid nesciunt modi pariatur. Ut cum vitae vel sequi placeat reiciendis. Asperiores nihil maiores molestiae. Odit sint nesciunt nisi nisi unde occaecati nam cupiditate ut tempore fugit rerum beatae.','https://picsum.photos/seed/360/600/600',5919.00,5444.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(63,5,13,'Imported Klocko LLC Kit','imported-klocko-llc-kit-78335','Et quae consequatur eum quod. Ipsum excepturi qui velit dignissimos sapiente sunt quia. Et magni non velit deserunt in ipsa illo. Qui et animi consequuntur suscipit ullam omnis ut quos nulla ut sunt rerum iste.','https://picsum.photos/seed/664/600/600',5164.00,1765.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(64,5,13,'Imported Kunze Ltd Item','imported-kunze-ltd-item-72751','Modi cum occaecati omnis dolore. Et sunt voluptatibus deleniti ullam atque harum et. Quam eum itaque nam nulla consequatur quia reiciendis. Sed quia corporis velit quis qui aut necessitatibus aliquid praesentium sed.','https://picsum.photos/seed/941/600/600',9237.00,1418.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(65,5,13,'Genuine Lakin, Wunsch and Rempel Device','genuine-lakin-wunsch-and-rempel-device-98437','Eius quasi est nam possimus. Velit qui qui eum nulla quaerat incidunt officia et. Quod repellendus nobis quidem voluptas quas. Voluptatem sit voluptatem voluptatem saepe tempore assumenda odit neque nesciunt sequi et molestiae odio velit minima tenetur.','https://picsum.photos/seed/673/600/600',3587.00,2318.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(66,5,14,'Premium Botsford-Cassin Gadget','premium-botsford-cassin-gadget-7715','Libero officiis maiores similique dolorem ratione fugiat. Eligendi nam omnis labore placeat. Eos incidunt quas quaerat eos rerum libero sit. Quos doloribus quibusdam fugiat ullam praesentium aliquid repudiandae nesciunt et consequatur officia tempore et quis.','https://picsum.photos/seed/651/600/600',4901.00,1689.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(67,5,14,'Branded Hodkiewicz, Predovic and Schamberger Bundle','branded-hodkiewicz-predovic-and-schamberger-bundle-6734','Quo aut a est pariatur. Reiciendis laboriosam sed accusantium nihil qui. Sit est suscipit at ab repudiandae provident dolorem ex esse odit.','https://picsum.photos/seed/907/600/600',8748.00,2773.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(68,5,14,'Imported Parker-Mohr Gadget','imported-parker-mohr-gadget-15090','Corrupti eligendi incidunt quis. Perferendis et harum sed a suscipit. Fugit omnis unde consequuntur qui iure eum omnis aut voluptas dolor soluta tenetur.','https://picsum.photos/seed/320/600/600',734.00,250.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(69,5,14,'Modern Koepp, Padberg and Halvorson Product','modern-koepp-padberg-and-halvorson-product-62794','Velit quos totam dolores quidem est. Id ipsum sint ut veniam debitis repellendus. Vel cum odio labore possimus sint quos eius ea molestias culpa distinctio doloremque totam.','https://picsum.photos/seed/631/600/600',9391.00,9126.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(70,5,14,'Premium Prosacco Group Collection','premium-prosacco-group-collection-86164','Veritatis error quia ullam. Odio velit nihil fugiat eum repellendus et. Saepe in non in et laboriosam quasi voluptatum repudiandae magnam repudiandae et.','https://picsum.photos/seed/740/600/600',2923.00,2475.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(71,5,15,'Original Kuhn, Swaniawski and Altenwerth Product','original-kuhn-swaniawski-and-altenwerth-product-88732','Modi ipsa natus sed ut. Nostrum quia ipsa sunt ratione et molestiae. Est et officiis quam aliquid et earum iure laudantium rerum ullam et sed molestias cupiditate quidem.','https://picsum.photos/seed/567/600/600',8730.00,1560.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(72,5,15,'Premium Cronin, Graham and Kutch Item','premium-cronin-graham-and-kutch-item-74030','Porro dolor modi debitis voluptatum veniam itaque dicta. Asperiores iure corporis aspernatur quae doloribus officiis facere. Fugit minima consequatur nihil impedit rerum quisquam sit quae. Esse doloremque fugit quo impedit iste voluptatem ipsum qui.','https://picsum.photos/seed/719/600/600',6041.00,1400.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(73,5,15,'Modern O\'Connell Ltd Bundle','modern-oconnell-ltd-bundle-9083','Tenetur maiores a molestiae voluptas. Adipisci aut debitis voluptas iusto placeat ut enim. Harum ullam id sed. Explicabo fuga harum doloremque eum eos ducimus voluptates sit consequatur tenetur rerum et iusto in.','https://picsum.photos/seed/530/600/600',4716.00,389.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(74,5,15,'Genuine Upton, Green and Cremin Accessory','genuine-upton-green-and-cremin-accessory-39665','Magnam beatae natus quasi ullam. Molestias consectetur nesciunt sunt vero minima incidunt qui. Officia ex ut consequatur ea. Provident sequi voluptatibus nesciunt aperiam natus occaecati exercitationem repellendus nostrum quam quia amet numquam atque.','https://picsum.photos/seed/538/600/600',6570.00,3173.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(75,5,15,'Local Schulist-Berge Device','local-schulist-berge-device-30802','Odio sed totam rerum. Perspiciatis nisi animi ex repudiandae perspiciatis provident est. Laudantium dolorem rerum eum esse at molestiae rem. Ut modi maxime quia mollitia quisquam velit iste harum ut officiis qui deserunt hic debitis.','https://picsum.photos/seed/433/600/600',1452.00,702.00,1,'2026-11-08 06:49:03','2026-11-08 06:49:03');
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
INSERT INTO `subcategories` VALUES (1,1,'Mobile Phones','mobile-phones','Provident ut velit odit incidunt et nihil.',1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(2,1,'Laptops & Computers','laptops-computers','Non harum ipsum aut sit maxime nemo ducimus architecto dolore quaerat cupiditate repellat neque.',1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(3,1,'Cameras & Photography','cameras-photography','Voluptatem corporis dolor ullam qui voluptas error voluptate atque.',1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(4,2,'Audio & Headphones','audio-headphones','Ut nisi quis doloribus quo sit et neque vero illo itaque ab laboriosam.',1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(5,2,'Smart Watches','smart-watches','Reprehenderit veniam commodi quaerat ex eligendi animi qui.',1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(6,2,'Men\'s Clothing','mens-clothing','Occaecati excepturi explicabo exercitationem pariatur ipsum in dolor sit.',1,'2026-11-08 06:49:02','2026-11-08 06:49:02'),(7,3,'Women\'s Clothing','womens-clothing','Dolor nam impedit repellat nemo deserunt sit corrupti ea consequatur officia temporibus quod.',1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(8,3,'Footwear','footwear','Nemo aut sit quasi nihil doloribus ipsam maiores.',1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(9,3,'Bags & Luggage','bags-luggage','Odio enim iusto ut ad consequatur consequuntur eos blanditiis magnam quod reprehenderit ducimus.',1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(10,4,'Jewellery & Accessories','jewellery-accessories','Dignissimos non itaque dolor iusto eligendi est eius laboriosam.',1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(11,4,'Furniture','furniture','Quos sit consequatur culpa laborum omnis facilis est saepe officia ipsa.',1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(12,4,'Kitchen & Dining','kitchen-dining','Assumenda dignissimos sit aliquid tenetur dolorem occaecati voluptas rerum rerum.',1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(13,5,'Bedding & Bath','bedding-bath','Nesciunt laboriosam corporis dolorem inventore dolor totam quia numquam aut.',1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(14,5,'Home D├⌐cor','home-decor','Doloribus eos modi quo autem ut ducimus dolores magni facere.',1,'2026-11-08 06:49:03','2026-11-08 06:49:03'),(15,5,'Lighting','lighting','Ducimus nisi consequuntur aut dolorem qui harum aliquid quos.',1,'2026-11-08 06:49:03','2026-11-08 06:49:03');
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
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
INSERT INTO `users` VALUES (1,'Admin','admin@banglapuzzle.com','2026-11-08 06:49:02','$2y$12$ips1N02vV5ViFHwnjklccOFR9Rtyhjqkeawc96YFDfTIx3NXIELLO',NULL,1,NULL,NULL,NULL,NULL,NULL,'2026-11-08 06:49:02','2026-11-08 06:49:02');
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

-- Dump completed on 2026-11-08 18:52:03
