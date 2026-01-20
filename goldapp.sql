-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2026 at 01:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `goldapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `account_number` varchar(255) NOT NULL,
  `account_holder` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `bank_name`, `account_number`, `account_holder`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'BCA', '0', 'CV BCA', 1, '2026-01-17 01:09:38', '2026-01-17 01:09:38'),
(2, 'BRI', '0', 'CV BRI', 1, '2026-01-17 01:11:38', '2026-01-17 01:11:38'),
(4, 'MANDIRI', '0', 'CV MANDIRI', 1, '2026-01-17 01:12:47', '2026-01-17 01:13:15'),
(5, 'BCA', '0', 'BCA PRIBADI', 1, '2026-01-17 01:13:45', '2026-01-17 01:13:45');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `code`, `name`, `address`, `created_at`, `updated_at`) VALUES
(1, 'psrn', 'pasuruan', NULL, '2026-01-03 22:42:08', '2026-01-03 22:42:08'),
(2, 'psp', 'paserpan', NULL, '2026-01-03 22:42:18', '2026-01-03 22:42:18');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('search_bankAccounts__page_1', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:4:{i:0;O:22:\"App\\Models\\BankAccount\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"bank_accounts\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:5;s:9:\"bank_name\";s:3:\"BCA\";s:14:\"account_number\";s:1:\"0\";s:14:\"account_holder\";s:11:\"BCA PRIBADI\";s:9:\"is_active\";i:1;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:5;s:9:\"bank_name\";s:3:\"BCA\";s:14:\"account_number\";s:1:\"0\";s:14:\"account_holder\";s:11:\"BCA PRIBADI\";s:9:\"is_active\";i:1;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:9:\"bank_name\";i:1;s:14:\"account_number\";i:2;s:14:\"account_holder\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:22:\"App\\Models\\BankAccount\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"bank_accounts\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:4;s:9:\"bank_name\";s:7:\"MANDIRI\";s:14:\"account_number\";s:1:\"0\";s:14:\"account_holder\";s:10:\"CV MANDIRI\";s:9:\"is_active\";i:1;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:4;s:9:\"bank_name\";s:7:\"MANDIRI\";s:14:\"account_number\";s:1:\"0\";s:14:\"account_holder\";s:10:\"CV MANDIRI\";s:9:\"is_active\";i:1;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:9:\"bank_name\";i:1;s:14:\"account_number\";i:2;s:14:\"account_holder\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:22:\"App\\Models\\BankAccount\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"bank_accounts\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:2;s:9:\"bank_name\";s:3:\"BRI\";s:14:\"account_number\";s:1:\"0\";s:14:\"account_holder\";s:6:\"CV BRI\";s:9:\"is_active\";i:1;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:2;s:9:\"bank_name\";s:3:\"BRI\";s:14:\"account_number\";s:1:\"0\";s:14:\"account_holder\";s:6:\"CV BRI\";s:9:\"is_active\";i:1;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:9:\"bank_name\";i:1;s:14:\"account_number\";i:2;s:14:\"account_holder\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:22:\"App\\Models\\BankAccount\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:13:\"bank_accounts\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:1;s:9:\"bank_name\";s:3:\"BCA\";s:14:\"account_number\";s:1:\"0\";s:14:\"account_holder\";s:6:\"CV BCA\";s:9:\"is_active\";i:1;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:1;s:9:\"bank_name\";s:3:\"BCA\";s:14:\"account_number\";s:1:\"0\";s:14:\"account_holder\";s:6:\"CV BCA\";s:9:\"is_active\";i:1;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:9:\"is_active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:9:\"bank_name\";i:1;s:14:\"account_number\";i:2;s:14:\"account_holder\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:4;s:11:\"\0*\0lastPage\";i:1;}', 1768642495),
('search_branches__page_1', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:17:\"App\\Models\\Branch\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"branches\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:4:{s:2:\"id\";i:2;s:4:\"code\";s:3:\"psp\";s:4:\"name\";s:8:\"paserpan\";s:7:\"address\";N;}s:11:\"\0*\0original\";a:4:{s:2:\"id\";i:2;s:4:\"code\";s:3:\"psp\";s:4:\"name\";s:8:\"paserpan\";s:7:\"address\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:7:\"address\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:17:\"App\\Models\\Branch\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"branches\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:4:{s:2:\"id\";i:1;s:4:\"code\";s:4:\"psrn\";s:4:\"name\";s:8:\"pasuruan\";s:7:\"address\";N;}s:11:\"\0*\0original\";a:4:{s:2:\"id\";i:1;s:4:\"code\";s:4:\"psrn\";s:4:\"name\";s:8:\"pasuruan\";s:7:\"address\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"code\";i:1;s:4:\"name\";i:2;s:7:\"address\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:2;s:11:\"\0*\0lastPage\";i:1;}', 1768642573),
('search_goldConversions__page_1', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:42:\"http://127.0.0.1:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:42:\"http://127.0.0.1:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:0;s:11:\"\0*\0lastPage\";i:1;}', 1768759442),
('search_goldMergeConversions__page_1', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:42:\"http://127.0.0.1:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:42:\"http://127.0.0.1:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:0;s:11:\"\0*\0lastPage\";i:1;}', 1768755370),
('search_products__page_1', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:18:\"App\\Models\\Product\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:3:{s:2:\"id\";i:4;s:4:\"code\";s:4:\"emas\";s:4:\"name\";s:4:\"emas\";}s:11:\"\0*\0original\";a:3:{s:2:\"id\";i:4;s:4:\"code\";s:4:\"emas\";s:4:\"name\";s:4:\"emas\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:2:{i:0;s:4:\"code\";i:1;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:18:\"App\\Models\\Product\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:3:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"LIONTIN\";s:4:\"name\";s:7:\"liontin\";}s:11:\"\0*\0original\";a:3:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"LIONTIN\";s:4:\"name\";s:7:\"liontin\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:2:{i:0;s:4:\"code\";i:1;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:2;s:11:\"\0*\0lastPage\";i:1;}', 1768761634),
('search_productVariants__page_1', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:10:{i:0;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:133;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:18:\"LIONTIN-6K-2-SEPUH\";s:7:\"barcode\";s:12:\"OYYFFMQUFJQ9\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:133;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:18:\"LIONTIN-6K-2-SEPUH\";s:7:\"barcode\";s:12:\"OYYFFMQUFJQ9\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";O:18:\"App\\Models\\Product\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"LIONTIN\";s:4:\"name\";s:7:\"liontin\";s:10:\"created_at\";s:19:\"2026-01-04 05:42:38\";s:10:\"updated_at\";s:19:\"2026-01-04 05:42:38\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"LIONTIN\";s:4:\"name\";s:7:\"liontin\";s:10:\"created_at\";s:19:\"2026-01-04 05:42:38\";s:10:\"updated_at\";s:19:\"2026-01-04 05:42:38\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:2:{i:0;s:4:\"code\";i:1;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}s:5:\"karat\";O:16:\"App\\Models\\Karat\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:6:\"karats\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:4:{s:2:\"id\";i:1;s:4:\"name\";s:2:\"6K\";s:10:\"created_at\";s:19:\"2026-01-04 05:42:38\";s:10:\"updated_at\";s:19:\"2026-01-04 05:42:38\";}s:11:\"\0*\0original\";a:4:{s:2:\"id\";i:1;s:4:\"name\";s:2:\"6K\";s:10:\"created_at\";s:19:\"2026-01-04 05:42:38\";s:10:\"updated_at\";s:19:\"2026-01-04 05:42:38\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:1:{i:0;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:128;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:21:\"LIONTIN-6K-2-CUSTOMER\";s:7:\"barcode\";s:12:\"RHYJ3HFDNEXP\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:128;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:21:\"LIONTIN-6K-2-CUSTOMER\";s:7:\"barcode\";s:12:\"RHYJ3HFDNEXP\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:127;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:1;s:4:\"gram\";N;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:16:\"EMAS-6K-CUSTOMER\";s:7:\"barcode\";s:12:\"I7JPN12FZMEE\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:127;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:1;s:4:\"gram\";N;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:16:\"EMAS-6K-CUSTOMER\";s:7:\"barcode\";s:12:\"I7JPN12FZMEE\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";O:18:\"App\\Models\\Product\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:4;s:4:\"code\";s:4:\"emas\";s:4:\"name\";s:4:\"emas\";s:10:\"created_at\";s:19:\"2026-01-04 06:40:10\";s:10:\"updated_at\";s:19:\"2026-01-04 06:40:10\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:4;s:4:\"code\";s:4:\"emas\";s:4:\"name\";s:4:\"emas\";s:10:\"created_at\";s:19:\"2026-01-04 06:40:10\";s:10:\"updated_at\";s:19:\"2026-01-04 06:40:10\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:2:{i:0;s:4:\"code\";i:1;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:126;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:1;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:18:\"LIONTIN-6K-1-SEPUH\";s:7:\"barcode\";s:12:\"AZ7KHI1YKYKT\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:126;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:1;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:18:\"LIONTIN-6K-1-SEPUH\";s:7:\"barcode\";s:12:\"AZ7KHI1YKYKT\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:4;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:125;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:0.4;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:19:\"LIONTIN-6K-0.4SEPUH\";s:7:\"barcode\";s:12:\"BMJZBKGOPSR4\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:125;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:0.4;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:19:\"LIONTIN-6K-0.4SEPUH\";s:7:\"barcode\";s:12:\"BMJZBKGOPSR4\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:5;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:124;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:0.68;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:20:\"LIONTIN-6K-0.68SEPUH\";s:7:\"barcode\";s:12:\"HLKROM5RXH9W\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:124;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:0.68;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:20:\"LIONTIN-6K-0.68SEPUH\";s:7:\"barcode\";s:12:\"HLKROM5RXH9W\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:6;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:123;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:1.44;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:20:\"LIONTIN-6K-1.44SEPUH\";s:7:\"barcode\";s:12:\"NHXNYVKK0VLG\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:123;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:1.44;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:20:\"LIONTIN-6K-1.44SEPUH\";s:7:\"barcode\";s:12:\"NHXNYVKK0VLG\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:7;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:122;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:0.64;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:20:\"LIONTIN-6K-0.64SEPUH\";s:7:\"barcode\";s:12:\"JBZUCWYQKV9Z\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:122;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:0.64;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:20:\"LIONTIN-6K-0.64SEPUH\";s:7:\"barcode\";s:12:\"JBZUCWYQKV9Z\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:8;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:121;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:0.65;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:20:\"LIONTIN-6K-0.65SEPUH\";s:7:\"barcode\";s:12:\"GPJSP7XXTW8F\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:121;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:0.65;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:20:\"LIONTIN-6K-0.65SEPUH\";s:7:\"barcode\";s:12:\"GPJSP7XXTW8F\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:9;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:120;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:0.76;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:20:\"LIONTIN-6K-0.76SEPUH\";s:7:\"barcode\";s:12:\"JJIQAMW10KIG\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:120;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:0.76;s:4:\"type\";s:5:\"sepuh\";s:3:\"sku\";s:20:\"LIONTIN-6K-0.76SEPUH\";s:7:\"barcode\";s:12:\"JJIQAMW10KIG\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:129;s:11:\"\0*\0lastPage\";i:13;}', 1768761638);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('search_productVariants__page_2', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:10:{i:0;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:253;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:1;s:4:\"gram\";d:1.5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:20:\"EMAS-6K-1.5-CUSTOMER\";s:7:\"barcode\";s:12:\"CBTDQIRPNUYP\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:253;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:1;s:4:\"gram\";d:1.5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:20:\"EMAS-6K-1.5-CUSTOMER\";s:7:\"barcode\";s:12:\"CBTDQIRPNUYP\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";O:18:\"App\\Models\\Product\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:4;s:4:\"code\";s:4:\"emas\";s:4:\"name\";s:4:\"emas\";s:10:\"created_at\";s:19:\"2026-01-04 06:40:10\";s:10:\"updated_at\";s:19:\"2026-01-04 06:40:10\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:4;s:4:\"code\";s:4:\"emas\";s:4:\"name\";s:4:\"emas\";s:10:\"created_at\";s:19:\"2026-01-04 06:40:10\";s:10:\"updated_at\";s:19:\"2026-01-04 06:40:10\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:2:{i:0;s:4:\"code\";i:1;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}s:5:\"karat\";O:16:\"App\\Models\\Karat\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:6:\"karats\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:4:{s:2:\"id\";i:1;s:4:\"name\";s:2:\"6K\";s:10:\"created_at\";s:19:\"2026-01-04 05:42:38\";s:10:\"updated_at\";s:19:\"2026-01-04 05:42:38\";}s:11:\"\0*\0original\";a:4:{s:2:\"id\";i:1;s:4:\"name\";s:2:\"6K\";s:10:\"created_at\";s:19:\"2026-01-04 05:42:38\";s:10:\"updated_at\";s:19:\"2026-01-04 05:42:38\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:1:{i:0;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:251;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:1.5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:23:\"LIONTIN-6K-1.5-CUSTOMER\";s:7:\"barcode\";s:12:\"DIROXYHURFK0\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:251;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:1.5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:23:\"LIONTIN-6K-1.5-CUSTOMER\";s:7:\"barcode\";s:12:\"DIROXYHURFK0\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";O:18:\"App\\Models\\Product\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"LIONTIN\";s:4:\"name\";s:7:\"liontin\";s:10:\"created_at\";s:19:\"2026-01-04 05:42:38\";s:10:\"updated_at\";s:19:\"2026-01-04 05:42:38\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"LIONTIN\";s:4:\"name\";s:7:\"liontin\";s:10:\"created_at\";s:19:\"2026-01-04 05:42:38\";s:10:\"updated_at\";s:19:\"2026-01-04 05:42:38\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:2:{i:0;s:4:\"code\";i:1;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:250;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2.5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:20:\"EMAS-6K-2.5-CUSTOMER\";s:7:\"barcode\";s:12:\"FQ17UDAXPPWG\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:250;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2.5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:20:\"EMAS-6K-2.5-CUSTOMER\";s:7:\"barcode\";s:12:\"FQ17UDAXPPWG\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:248;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2.5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:23:\"LIONTIN-6K-2.5-CUSTOMER\";s:7:\"barcode\";s:12:\"AW7JL9COSIJB\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:248;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2.5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:23:\"LIONTIN-6K-2.5-CUSTOMER\";s:7:\"barcode\";s:12:\"AW7JL9COSIJB\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:184;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:4;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:247;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:2;s:4:\"gram\";d:3;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:18:\"EMAS-8K-3-CUSTOMER\";s:7:\"barcode\";s:12:\"XXPLKDIVBQWL\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:247;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:2;s:4:\"gram\";d:3;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:18:\"EMAS-8K-3-CUSTOMER\";s:7:\"barcode\";s:12:\"XXPLKDIVBQWL\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";O:16:\"App\\Models\\Karat\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:6:\"karats\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:4:{s:2:\"id\";i:2;s:4:\"name\";s:2:\"8K\";s:10:\"created_at\";s:19:\"2026-01-04 05:43:10\";s:10:\"updated_at\";s:19:\"2026-01-04 05:43:10\";}s:11:\"\0*\0original\";a:4:{s:2:\"id\";i:2;s:4:\"name\";s:2:\"8K\";s:10:\"created_at\";s:19:\"2026-01-04 05:43:10\";s:10:\"updated_at\";s:19:\"2026-01-04 05:43:10\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:1:{i:0;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:5;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:246;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:3;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:21:\"LIONTIN-8K-3-CUSTOMER\";s:7:\"barcode\";s:12:\"XPSN3LRLOWDF\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:246;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:3;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:21:\"LIONTIN-8K-3-CUSTOMER\";s:7:\"barcode\";s:12:\"XPSN3LRLOWDF\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:184;s:5:\"karat\";r:399;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:6;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:245;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:1;s:4:\"gram\";d:5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:18:\"EMAS-6K-5-CUSTOMER\";s:7:\"barcode\";s:12:\"X0XRY27M1ZVW\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:245;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:1;s:4:\"gram\";d:5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:18:\"EMAS-6K-5-CUSTOMER\";s:7:\"barcode\";s:12:\"X0XRY27M1ZVW\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:7;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:244;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:21:\"LIONTIN-6K-5-CUSTOMER\";s:7:\"barcode\";s:12:\"ETMQ8GDTRI0W\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:244;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:5;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:21:\"LIONTIN-6K-5-CUSTOMER\";s:7:\"barcode\";s:12:\"ETMQ8GDTRI0W\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:184;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:8;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:243;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:18:\"EMAS-6K-2-CUSTOMER\";s:7:\"barcode\";s:12:\"ZCXB8QPUIQ5P\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:243;s:10:\"product_id\";i:4;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:18:\"EMAS-6K-2-CUSTOMER\";s:7:\"barcode\";s:12:\"ZCXB8QPUIQ5P\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:9;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:238;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:21:\"LIONTIN-6K-2-CUSTOMER\";s:7:\"barcode\";s:12:\"Z91RFTGRHL8R\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:238;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:1;s:4:\"gram\";d:2;s:4:\"type\";s:8:\"customer\";s:3:\"sku\";s:21:\"LIONTIN-6K-2-CUSTOMER\";s:7:\"barcode\";s:12:\"Z91RFTGRHL8R\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:184;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:2;s:7:\"\0*\0path\";s:44:\"http://192.168.1.6:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:44:\"http://192.168.1.6:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:250;s:11:\"\0*\0lastPage\";i:25;}', 1768043370),
('search_productVariants__page_3', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:10:{i:0;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:230;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.59;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.59\";s:7:\"barcode\";s:12:\"ERFHDTB8BYDC\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:230;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.59;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.59\";s:7:\"barcode\";s:12:\"ERFHDTB8BYDC\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";O:18:\"App\\Models\\Product\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:8:\"products\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"LIONTIN\";s:4:\"name\";s:7:\"liontin\";s:10:\"created_at\";s:19:\"2026-01-04 05:42:38\";s:10:\"updated_at\";s:19:\"2026-01-04 05:42:38\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:1;s:4:\"code\";s:7:\"LIONTIN\";s:4:\"name\";s:7:\"liontin\";s:10:\"created_at\";s:19:\"2026-01-04 05:42:38\";s:10:\"updated_at\";s:19:\"2026-01-04 05:42:38\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:2:{i:0;s:4:\"code\";i:1;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}s:5:\"karat\";O:16:\"App\\Models\\Karat\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:6:\"karats\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:4:{s:2:\"id\";i:2;s:4:\"name\";s:2:\"8K\";s:10:\"created_at\";s:19:\"2026-01-04 05:43:10\";s:10:\"updated_at\";s:19:\"2026-01-04 05:43:10\";}s:11:\"\0*\0original\";a:4:{s:2:\"id\";i:2;s:4:\"name\";s:2:\"8K\";s:10:\"created_at\";s:19:\"2026-01-04 05:43:10\";s:10:\"updated_at\";s:19:\"2026-01-04 05:43:10\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:1:{i:0;s:4:\"name\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:229;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:1.38;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-1.38\";s:7:\"barcode\";s:12:\"X0NOQM2RRQRI\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:229;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:1.38;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-1.38\";s:7:\"barcode\";s:12:\"X0NOQM2RRQRI\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:228;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.75;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.75\";s:7:\"barcode\";s:12:\"JRUXYZAUM4U8\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:228;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.75;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.75\";s:7:\"barcode\";s:12:\"JRUXYZAUM4U8\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:227;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.74;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.74\";s:7:\"barcode\";s:12:\"79CXU3Z0KC4J\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:227;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.74;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.74\";s:7:\"barcode\";s:12:\"79CXU3Z0KC4J\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:4;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:226;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.46;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.46\";s:7:\"barcode\";s:12:\"U1IIJC5KQKTO\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:226;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.46;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.46\";s:7:\"barcode\";s:12:\"U1IIJC5KQKTO\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:5;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:225;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.56;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.56\";s:7:\"barcode\";s:12:\"L3OIAIIXMUPI\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:225;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.56;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.56\";s:7:\"barcode\";s:12:\"L3OIAIIXMUPI\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:6;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:224;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:1;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:12:\"LIONTIN-8K-1\";s:7:\"barcode\";s:12:\"M1I8EOQKTTGG\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:224;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:1;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:12:\"LIONTIN-8K-1\";s:7:\"barcode\";s:12:\"M1I8EOQKTTGG\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:7;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:223;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.63;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.63\";s:7:\"barcode\";s:12:\"LNXAINDPS7JB\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:223;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.63;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.63\";s:7:\"barcode\";s:12:\"LNXAINDPS7JB\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:8;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:222;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.57;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.57\";s:7:\"barcode\";s:12:\"H8YO3PKIHTEW\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:222;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.57;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:15:\"LIONTIN-8K-0.57\";s:7:\"barcode\";s:12:\"H8YO3PKIHTEW\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:9;O:25:\"App\\Models\\ProductVariant\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:16:\"product_variants\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:221;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.9;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:14:\"LIONTIN-8K-0.9\";s:7:\"barcode\";s:12:\"QCINMPWV4GMK\";s:13:\"default_price\";i:0;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:221;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";d:0.9;s:4:\"type\";s:3:\"new\";s:3:\"sku\";s:14:\"LIONTIN-8K-0.9\";s:7:\"barcode\";s:12:\"QCINMPWV4GMK\";s:13:\"default_price\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:7:\"product\";r:44;s:5:\"karat\";r:88;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:7:{i:0;s:10:\"product_id\";i:1;s:8:\"karat_id\";i:2;s:4:\"gram\";i:3;s:4:\"type\";i:4;s:3:\"sku\";i:5;s:7:\"barcode\";i:6;s:13:\"default_price\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:3;s:7:\"\0*\0path\";s:42:\"http://127.0.0.1:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:42:\"http://127.0.0.1:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:250;s:11:\"\0*\0lastPage\";i:25;}', 1768491406),
('search_sales__page_1', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:22:\"App\\Models\\Transaction\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:12:\"transactions\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:17;s:16:\"transaction_date\";s:10:\"2026-01-17\";s:14:\"invoice_number\";s:10:\"INV-3Z5WKM\";s:5:\"total\";s:10:\"2600000.00\";s:11:\"customer_id\";i:16;s:13:\"supplier_name\";N;s:4:\"note\";N;s:10:\"created_by\";i:1;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:17;s:16:\"transaction_date\";s:10:\"2026-01-17\";s:14:\"invoice_number\";s:10:\"INV-3Z5WKM\";s:5:\"total\";s:10:\"2600000.00\";s:11:\"customer_id\";i:16;s:13:\"supplier_name\";N;s:4:\"note\";N;s:10:\"created_by\";i:1;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:4:\"user\";O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:1;s:8:\"username\";s:20:\"Haidar Ali Al-Jufrie\";s:5:\"email\";s:21:\"haidar17ali@gmail.com\";s:17:\"email_verified_at\";N;s:8:\"password\";s:60:\"$2y$12$AgTYCqylAoDV1gv6bKhmKuAgZy77jt6/3Y7wCCd7UOh1GjVNuaOl2\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;s:14:\"remember_token\";N;s:10:\"created_at\";s:19:\"2026-01-04 05:03:05\";s:10:\"updated_at\";s:19:\"2026-01-04 05:03:05\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:1;s:8:\"username\";s:20:\"Haidar Ali Al-Jufrie\";s:5:\"email\";s:21:\"haidar17ali@gmail.com\";s:17:\"email_verified_at\";N;s:8:\"password\";s:60:\"$2y$12$AgTYCqylAoDV1gv6bKhmKuAgZy77jt6/3Y7wCCd7UOh1GjVNuaOl2\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;s:14:\"remember_token\";N;s:10:\"created_at\";s:19:\"2026-01-04 05:03:05\";s:10:\"updated_at\";s:19:\"2026-01-04 05:03:05\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}s:8:\"customer\";O:27:\"App\\Models\\CustomerSupplier\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"customer_suppliers\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:7:{s:2:\"id\";i:16;s:4:\"name\";s:6:\"haidar\";s:12:\"phone_number\";N;s:7:\"address\";s:9:\"Hasanudin\";s:4:\"type\";N;s:10:\"created_at\";s:19:\"2026-01-04 06:28:21\";s:10:\"updated_at\";s:19:\"2026-01-04 06:28:21\";}s:11:\"\0*\0original\";a:7:{s:2:\"id\";i:16;s:4:\"name\";s:6:\"haidar\";s:12:\"phone_number\";N;s:7:\"address\";s:9:\"Hasanudin\";s:4:\"type\";N;s:10:\"created_at\";s:19:\"2026-01-04 06:28:21\";s:10:\"updated_at\";s:19:\"2026-01-04 06:28:21\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:4:\"name\";i:1;s:12:\"phone_number\";i:2;s:7:\"address\";i:3;s:4:\"type\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:16:{i:0;s:4:\"type\";i:1;s:13:\"purchase_type\";i:2;s:9:\"branch_id\";i:3;s:19:\"storage_location_id\";i:4;s:16:\"transaction_date\";i:5;s:14:\"invoice_number\";i:6;s:5:\"total\";i:7;s:11:\"customer_id\";i:8;s:13:\"supplier_name\";i:9;s:4:\"note\";i:10;s:5:\"photo\";i:11;s:14:\"payment_method\";i:12;s:15:\"bank_account_id\";i:13;s:11:\"cash_amount\";i:14;s:15:\"transfer_amount\";i:15;s:10:\"created_by\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:1;s:11:\"\0*\0lastPage\";i:1;}', 1768644865),
('search_stockAdjustments__page_1', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:26:\"App\\Models\\StockAdjustment\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"stock_adjustments\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:1;s:9:\"branch_id\";i:1;s:19:\"storage_location_id\";i:1;s:15:\"adjustment_date\";s:10:\"2026-01-18\";s:4:\"note\";s:19:\"Import Stock Opname\";s:10:\"created_by\";i:1;s:11:\"approved_by\";N;s:11:\"approved_at\";N;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:1;s:9:\"branch_id\";i:1;s:19:\"storage_location_id\";i:1;s:15:\"adjustment_date\";s:10:\"2026-01-18\";s:4:\"note\";s:19:\"Import Stock Opname\";s:10:\"created_by\";i:1;s:11:\"approved_by\";N;s:11:\"approved_at\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:15:\"adjustment_date\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:8:{i:0;s:9:\"branch_id\";i:1;s:19:\"storage_location_id\";i:2;s:15:\"adjustment_date\";i:3;s:4:\"note\";i:4;s:10:\"created_by\";i:5;s:11:\"approved_by\";i:6;s:11:\"approved_at\";i:7;s:6:\"weight\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:42:\"http://127.0.0.1:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:42:\"http://127.0.0.1:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:1;s:11:\"\0*\0lastPage\";i:1;}', 1768758552);
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('search_storageLocations__page_1', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:26:\"App\\Models\\StorageLocation\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"storage_locations\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:7:\"Brankas\";s:11:\"description\";N;}s:11:\"\0*\0original\";a:3:{s:2:\"id\";i:2;s:4:\"name\";s:7:\"Brankas\";s:11:\"description\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:2:{i:0;s:4:\"name\";i:1;s:11:\"description\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:26:\"App\\Models\\StorageLocation\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"storage_locations\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:7:\"Etalase\";s:11:\"description\";N;}s:11:\"\0*\0original\";a:3:{s:2:\"id\";i:1;s:4:\"name\";s:7:\"Etalase\";s:11:\"description\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:2:{i:0;s:4:\"name\";i:1;s:11:\"description\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:2;s:11:\"\0*\0lastPage\";i:1;}', 1768642578),
('search_transactions__page_1', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:22:\"App\\Models\\Transaction\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:12:\"transactions\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:8:{s:2:\"id\";i:1;s:16:\"transaction_date\";s:10:\"2026-01-18\";s:14:\"invoice_number\";s:10:\"INV-2BJT70\";s:5:\"total\";s:10:\"1900000.00\";s:11:\"customer_id\";i:16;s:13:\"supplier_name\";N;s:4:\"note\";N;s:10:\"created_by\";i:1;}s:11:\"\0*\0original\";a:8:{s:2:\"id\";i:1;s:16:\"transaction_date\";s:10:\"2026-01-18\";s:14:\"invoice_number\";s:10:\"INV-2BJT70\";s:5:\"total\";s:10:\"1900000.00\";s:11:\"customer_id\";i:16;s:13:\"supplier_name\";N;s:4:\"note\";N;s:10:\"created_by\";i:1;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:4:\"user\";O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:10:{s:2:\"id\";i:1;s:8:\"username\";s:20:\"Haidar Ali Al-Jufrie\";s:5:\"email\";s:21:\"haidar17ali@gmail.com\";s:17:\"email_verified_at\";N;s:8:\"password\";s:60:\"$2y$12$AgTYCqylAoDV1gv6bKhmKuAgZy77jt6/3Y7wCCd7UOh1GjVNuaOl2\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;s:14:\"remember_token\";N;s:10:\"created_at\";s:19:\"2026-01-04 05:03:05\";s:10:\"updated_at\";s:19:\"2026-01-04 05:03:05\";}s:11:\"\0*\0original\";a:10:{s:2:\"id\";i:1;s:8:\"username\";s:20:\"Haidar Ali Al-Jufrie\";s:5:\"email\";s:21:\"haidar17ali@gmail.com\";s:17:\"email_verified_at\";N;s:8:\"password\";s:60:\"$2y$12$AgTYCqylAoDV1gv6bKhmKuAgZy77jt6/3Y7wCCd7UOh1GjVNuaOl2\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;s:14:\"remember_token\";N;s:10:\"created_at\";s:19:\"2026-01-04 05:03:05\";s:10:\"updated_at\";s:19:\"2026-01-04 05:03:05\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}s:8:\"customer\";O:27:\"App\\Models\\CustomerSupplier\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:18:\"customer_suppliers\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:7:{s:2:\"id\";i:16;s:4:\"name\";s:6:\"haidar\";s:12:\"phone_number\";N;s:7:\"address\";s:9:\"Hasanudin\";s:4:\"type\";N;s:10:\"created_at\";s:19:\"2026-01-04 06:28:21\";s:10:\"updated_at\";s:19:\"2026-01-04 06:28:21\";}s:11:\"\0*\0original\";a:7:{s:2:\"id\";i:16;s:4:\"name\";s:6:\"haidar\";s:12:\"phone_number\";N;s:7:\"address\";s:9:\"Hasanudin\";s:4:\"type\";N;s:10:\"created_at\";s:19:\"2026-01-04 06:28:21\";s:10:\"updated_at\";s:19:\"2026-01-04 06:28:21\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:4:\"name\";i:1;s:12:\"phone_number\";i:2;s:7:\"address\";i:3;s:4:\"type\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:16:{i:0;s:4:\"type\";i:1;s:13:\"purchase_type\";i:2;s:9:\"branch_id\";i:3;s:19:\"storage_location_id\";i:4;s:16:\"transaction_date\";i:5;s:14:\"invoice_number\";i:6;s:5:\"total\";i:7;s:11:\"customer_id\";i:8;s:13:\"supplier_name\";i:9;s:4:\"note\";i:10;s:5:\"photo\";i:11;s:14:\"payment_method\";i:12;s:15:\"bank_account_id\";i:13;s:11:\"cash_amount\";i:14;s:15:\"transfer_amount\";i:15;s:10:\"created_by\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:42:\"http://127.0.0.1:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:42:\"http://127.0.0.1:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:1;s:11:\"\0*\0lastPage\";i:1;}', 1768758641),
('search_users__page_1', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:10:{i:0;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:14;s:8:\"username\";s:11:\"Shofil Fuad\";s:5:\"email\";s:22:\"fuadshofil14@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:14;s:8:\"username\";s:11:\"Shofil Fuad\";s:5:\"email\";s:22:\"fuadshofil14@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:1;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:13;s:8:\"username\";s:14:\"Eka Nur Azizah\";s:5:\"email\";s:23:\"nurazizahe427@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:13;s:8:\"username\";s:14:\"Eka Nur Azizah\";s:5:\"email\";s:23:\"nurazizahe427@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:2;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:12;s:8:\"username\";s:21:\"Aulya Rizqy Ramadhani\";s:5:\"email\";s:22:\"aulyarizky36@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:12;s:8:\"username\";s:21:\"Aulya Rizqy Ramadhani\";s:5:\"email\";s:22:\"aulyarizky36@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:3;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:11;s:8:\"username\";s:21:\"Adinda Chandrawahyudi\";s:5:\"email\";s:32:\"chandrawahyudiadinda32@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:11;s:8:\"username\";s:21:\"Adinda Chandrawahyudi\";s:5:\"email\";s:32:\"chandrawahyudiadinda32@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:4;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:10;s:8:\"username\";s:17:\"Riza Adafi\'ah A M\";s:5:\"email\";s:21:\"rizaadafiah@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:10;s:8:\"username\";s:17:\"Riza Adafi\'ah A M\";s:5:\"email\";s:21:\"rizaadafiah@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:5;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:9;s:8:\"username\";s:14:\"Safinatun Naza\";s:5:\"email\";s:22:\"safinanaza49@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:9;s:8:\"username\";s:14:\"Safinatun Naza\";s:5:\"email\";s:22:\"safinanaza49@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:6;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:8;s:8:\"username\";s:14:\"Risa Ramadhani\";s:5:\"email\";s:21:\"risaramah18@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:8;s:8:\"username\";s:14:\"Risa Ramadhani\";s:5:\"email\";s:21:\"risaramah18@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:7;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:7;s:8:\"username\";s:17:\"Maimunatun Nafisa\";s:5:\"email\";s:17:\"maimuna@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:7;s:8:\"username\";s:17:\"Maimunatun Nafisa\";s:5:\"email\";s:17:\"maimuna@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:8;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:6;s:8:\"username\";s:19:\"Ahmad osama bamifta\";s:5:\"email\";s:15:\"osama@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:6;s:8:\"username\";s:19:\"Ahmad osama bamifta\";s:5:\"email\";s:15:\"osama@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:9;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:5;s:8:\"username\";s:17:\"Kurniawan perdana\";s:5:\"email\";s:23:\"sangarpojok69@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:5;s:8:\"username\";s:17:\"Kurniawan perdana\";s:5:\"email\";s:23:\"sangarpojok69@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:1;s:7:\"\0*\0path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:14;s:11:\"\0*\0lastPage\";i:2;}', 1768685918),
('search_users__page_2', 'O:42:\"Illuminate\\Pagination\\LengthAwarePaginator\":11:{s:8:\"\0*\0items\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:4:{i:0;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:4;s:8:\"username\";s:12:\"Abdul Rahman\";s:5:\"email\";s:23:\"abd.rahmanatt@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:4;s:8:\"username\";s:12:\"Abdul Rahman\";s:5:\"email\";s:23:\"abd.rahmanatt@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:1;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:3;s:8:\"username\";s:19:\"Amalia Bela Safitri\";s:5:\"email\";s:21:\"amaliabela2@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:3;s:8:\"username\";s:19:\"Amalia Bela Safitri\";s:5:\"email\";s:21:\"amaliabela2@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:2;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:2;s:8:\"username\";s:13:\"Fishal Rayyes\";s:5:\"email\";s:22:\"rayyesfishal@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:2;s:8:\"username\";s:13:\"Fishal Rayyes\";s:5:\"email\";s:22:\"rayyesfishal@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}i:3;O:15:\"App\\Models\\User\":32:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"users\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:1;s:8:\"username\";s:20:\"Haidar Ali Al-Jufrie\";s:5:\"email\";s:21:\"haidar17ali@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:1;s:8:\"username\";s:20:\"Haidar Ali Al-Jufrie\";s:5:\"email\";s:21:\"haidar17ali@gmail.com\";s:9:\"is_active\";i:1;s:11:\"employee_id\";N;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:17:\"email_verified_at\";s:8:\"datetime\";s:8:\"password\";s:6:\"hashed\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:2:{i:0;s:8:\"password\";i:1;s:14:\"remember_token\";}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:4:{i:0;s:8:\"username\";i:1;s:5:\"email\";i:2;s:8:\"password\";i:3;s:9:\"is_active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}s:19:\"\0*\0authPasswordName\";s:8:\"password\";s:20:\"\0*\0rememberTokenName\";s:14:\"remember_token\";}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:10:\"\0*\0perPage\";i:10;s:14:\"\0*\0currentPage\";i:2;s:7:\"\0*\0path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"\0*\0query\";a:0:{}s:11:\"\0*\0fragment\";N;s:11:\"\0*\0pageName\";s:4:\"page\";s:10:\"onEachSide\";i:3;s:10:\"\0*\0options\";a:2:{s:4:\"path\";s:44:\"http://192.168.1.4:8000/gold-app/data/search\";s:8:\"pageName\";s:4:\"page\";}s:8:\"\0*\0total\";i:14;s:11:\"\0*\0lastPage\";i:2;}', 1768685923),
('spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:112:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:16:\"permission.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:19:\"permission.generate\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:10:\"role.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:9:\"role.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:11:\"role.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:9:\"role.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:11:\"role.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:10:\"role.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:14:\"pengguna.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:13:\"pengguna.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:15:\"pengguna.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:13:\"pengguna.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:15:\"pengguna.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:14:\"pengguna.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:12:\"produk.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:11:\"produk.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:13:\"produk.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:11:\"produk.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:13:\"produk.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:12:\"produk.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:11:\"karat.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:21;a:4:{s:1:\"a\";i:22;s:1:\"b\";s:10:\"karat.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:22;a:4:{s:1:\"a\";i:23;s:1:\"b\";s:12:\"karat.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:23;a:4:{s:1:\"a\";i:24;s:1:\"b\";s:10:\"karat.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:12:\"karat.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:11:\"karat.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:17:\"penyimpanan.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:16:\"penyimpanan.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:18:\"penyimpanan.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:16:\"penyimpanan.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:18:\"penyimpanan.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:31;a:4:{s:1:\"a\";i:32;s:1:\"b\";s:17:\"penyimpanan.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:32;a:4:{s:1:\"a\";i:33;s:1:\"b\";s:12:\"cabang.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:33;a:4:{s:1:\"a\";i:34;s:1:\"b\";s:11:\"cabang.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:34;a:4:{s:1:\"a\";i:35;s:1:\"b\";s:13:\"cabang.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:35;a:4:{s:1:\"a\";i:36;s:1:\"b\";s:11:\"cabang.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:36;a:4:{s:1:\"a\";i:37;s:1:\"b\";s:13:\"cabang.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:37;a:4:{s:1:\"a\";i:38;s:1:\"b\";s:12:\"cabang.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:38;a:4:{s:1:\"a\";i:39;s:1:\"b\";s:14:\"rekening.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:39;a:4:{s:1:\"a\";i:40;s:1:\"b\";s:13:\"rekening.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:40;a:4:{s:1:\"a\";i:41;s:1:\"b\";s:15:\"rekening.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:41;a:4:{s:1:\"a\";i:42;s:1:\"b\";s:13:\"rekening.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:42;a:4:{s:1:\"a\";i:43;s:1:\"b\";s:15:\"rekening.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:43;a:4:{s:1:\"a\";i:44;s:1:\"b\";s:14:\"rekening.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:44;a:4:{s:1:\"a\";i:45;s:1:\"b\";s:23:\"customer-supplier.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:45;a:4:{s:1:\"a\";i:46;s:1:\"b\";s:22:\"customer-supplier.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:46;a:4:{s:1:\"a\";i:47;s:1:\"b\";s:24:\"customer-supplier.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:47;a:4:{s:1:\"a\";i:48;s:1:\"b\";s:22:\"customer-supplier.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:48;a:4:{s:1:\"a\";i:49;s:1:\"b\";s:24:\"customer-supplier.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:49;a:4:{s:1:\"a\";i:50;s:1:\"b\";s:23:\"customer-supplier.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:50;a:4:{s:1:\"a\";i:51;s:1:\"b\";s:19:\"varian-produk.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:51;a:4:{s:1:\"a\";i:52;s:1:\"b\";s:18:\"varian-produk.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:52;a:4:{s:1:\"a\";i:53;s:1:\"b\";s:20:\"varian-produk.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:53;a:4:{s:1:\"a\";i:54;s:1:\"b\";s:18:\"varian-produk.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:54;a:4:{s:1:\"a\";i:55;s:1:\"b\";s:20:\"varian-produk.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:55;a:4:{s:1:\"a\";i:56;s:1:\"b\";s:24:\"varian-produk.ubahDetail\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:56;a:4:{s:1:\"a\";i:57;s:1:\"b\";s:26:\"varian-produk.updateDetail\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:57;a:4:{s:1:\"a\";i:58;s:1:\"b\";s:19:\"varian-produk.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:58;a:4:{s:1:\"a\";i:59;s:1:\"b\";s:20:\"varian-produk.import\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:59;a:4:{s:1:\"a\";i:60;s:1:\"b\";s:26:\"varian-produk.barcode-form\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:60;a:4:{s:1:\"a\";i:61;s:1:\"b\";s:27:\"varian-produk.barcode-print\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:61;a:4:{s:1:\"a\";i:62;s:1:\"b\";s:15:\"transaksi.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:62;a:4:{s:1:\"a\";i:63;s:1:\"b\";s:14:\"transaksi.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:63;a:4:{s:1:\"a\";i:64;s:1:\"b\";s:16:\"transaksi.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:64;a:4:{s:1:\"a\";i:65;s:1:\"b\";s:14:\"transaksi.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:65;a:4:{s:1:\"a\";i:66;s:1:\"b\";s:16:\"transaksi.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:66;a:4:{s:1:\"a\";i:67;s:1:\"b\";s:15:\"transaksi.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:67;a:4:{s:1:\"a\";i:68;s:1:\"b\";s:15:\"penjualan.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:68;a:4:{s:1:\"a\";i:69;s:1:\"b\";s:14:\"penjualan.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:69;a:4:{s:1:\"a\";i:70;s:1:\"b\";s:16:\"penjualan.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:70;a:4:{s:1:\"a\";i:71;s:1:\"b\";s:14:\"penjualan.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:71;a:4:{s:1:\"a\";i:72;s:1:\"b\";s:16:\"penjualan.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:72;a:4:{s:1:\"a\";i:73;s:1:\"b\";s:15:\"penjualan.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:73;a:4:{s:1:\"a\";i:74;s:1:\"b\";s:15:\"penjualan.cetak\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:74;a:4:{s:1:\"a\";i:75;s:1:\"b\";s:12:\"opname.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:75;a:4:{s:1:\"a\";i:76;s:1:\"b\";s:11:\"opname.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:76;a:4:{s:1:\"a\";i:77;s:1:\"b\";s:13:\"opname.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:77;a:4:{s:1:\"a\";i:78;s:1:\"b\";s:18:\"opname.import-form\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:78;a:4:{s:1:\"a\";i:79;s:1:\"b\";s:13:\"opname.import\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:79;a:4:{s:1:\"a\";i:80;s:1:\"b\";s:12:\"opname.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:80;a:4:{s:1:\"a\";i:81;s:1:\"b\";s:17:\"opname.dapatStock\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:81;a:4:{s:1:\"a\";i:82;s:1:\"b\";s:22:\"pengelolaan-emas.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:82;a:4:{s:1:\"a\";i:83;s:1:\"b\";s:21:\"pengelolaan-emas.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:83;a:4:{s:1:\"a\";i:84;s:1:\"b\";s:23:\"pengelolaan-emas.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:84;a:4:{s:1:\"a\";i:85;s:1:\"b\";s:21:\"pengelolaan-emas.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:85;a:4:{s:1:\"a\";i:86;s:1:\"b\";s:23:\"pengelolaan-emas.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:86;a:4:{s:1:\"a\";i:87;s:1:\"b\";s:22:\"pengelolaan-emas.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:87;a:4:{s:1:\"a\";i:88;s:1:\"b\";s:19:\"konversi-emas.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:88;a:4:{s:1:\"a\";i:89;s:1:\"b\";s:18:\"konversi-emas.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:89;a:4:{s:1:\"a\";i:90;s:1:\"b\";s:20:\"konversi-emas.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:90;a:4:{s:1:\"a\";i:91;s:1:\"b\";s:18:\"konversi-emas.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:91;a:4:{s:1:\"a\";i:92;s:1:\"b\";s:20:\"konversi-emas.detail\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:92;a:4:{s:1:\"a\";i:93;s:1:\"b\";s:20:\"konversi-emas.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:93;a:4:{s:1:\"a\";i:94;s:1:\"b\";s:19:\"konversi-emas.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:94;a:4:{s:1:\"a\";i:95;s:1:\"b\";s:20:\"keluar-etalase.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:95;a:4:{s:1:\"a\";i:96;s:1:\"b\";s:19:\"keluar-etalase.buat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:96;a:4:{s:1:\"a\";i:97;s:1:\"b\";s:21:\"keluar-etalase.simpan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:97;a:4:{s:1:\"a\";i:98;s:1:\"b\";s:19:\"keluar-etalase.ubah\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:98;a:4:{s:1:\"a\";i:99;s:1:\"b\";s:21:\"keluar-etalase.detail\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:99;a:4:{s:1:\"a\";i:100;s:1:\"b\";s:21:\"keluar-etalase.update\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:100;a:4:{s:1:\"a\";i:101;s:1:\"b\";s:20:\"keluar-etalase.hapus\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:101;a:4:{s:1:\"a\";i:102;s:1:\"b\";s:11:\"stock.index\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:102;a:4:{s:1:\"a\";i:103;s:1:\"b\";s:13:\"stocks.detail\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:103;a:4:{s:1:\"a\";i:104;s:1:\"b\";s:10:\"stock.info\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:104;a:4:{s:1:\"a\";i:105;s:1:\"b\";s:11:\"stock.berat\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:105;a:4:{s:1:\"a\";i:106;s:1:\"b\";s:19:\"utility.ajax-no-rek\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:106;a:4:{s:1:\"a\";i:107;s:1:\"b\";s:15:\"utility.getById\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:107;a:4:{s:1:\"a\";i:108;s:1:\"b\";s:23:\"utility.getMultipleData\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:108;a:4:{s:1:\"a\";i:109;s:1:\"b\";s:20:\"utility.suratJalanId\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:109;a:4:{s:1:\"a\";i:110;s:1:\"b\";s:6:\"search\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:110;a:4:{s:1:\"a\";i:111;s:1:\"b\";s:25:\"utility.cetak-surat-jalan\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:111;a:4:{s:1:\"a\";i:112;s:1:\"b\";s:13:\"backup.export\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:11:\"super-admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:3:\"SPV\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:10:\"Pramuniaga\";s:1:\"c\";s:3:\"web\";}}}', 1768841217);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_suppliers`
--

CREATE TABLE `customer_suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `type` enum('supplier','customer') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_suppliers`
--

INSERT INTO `customer_suppliers` (`id`, `name`, `phone_number`, `address`, `type`, `created_at`, `updated_at`) VALUES
(16, 'haidar', NULL, 'Hasanudin', NULL, '2026-01-03 23:28:21', '2026-01-03 23:28:21');

-- --------------------------------------------------------

--
-- Table structure for table `cuttings`
--

CREATE TABLE `cuttings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `tailor_name` varchar(255) NOT NULL,
  `create_by` int(11) NOT NULL,
  `edit_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cutting_details`
--

CREATE TABLE `cutting_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cutting_id` int(11) NOT NULL,
  `product_variant_id` int(11) NOT NULL,
  `qty` bigint(20) NOT NULL,
  `finish_at` date DEFAULT NULL,
  `status` enum('Pending','Cancel','Finish') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `debts`
--

CREATE TABLE `debts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tailor` varchar(255) NOT NULL,
  `source_id` int(11) NOT NULL,
  `cutting_detail_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `from` enum('cutting','delivery') NOT NULL DEFAULT 'cutting',
  `status` enum('belum','sebagian','lunas') NOT NULL DEFAULT 'belum',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `sender` varchar(255) NOT NULL,
  `arrival_date` datetime DEFAULT NULL,
  `create_by` int(11) NOT NULL,
  `edit_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_details`
--

CREATE TABLE `delivery_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_id` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  `source_type` enum('cutting','hutang') NOT NULL,
  `qty` int(11) NOT NULL,
  `status` enum('pending','datang') NOT NULL DEFAULT 'pending',
  `edit_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fabrics`
--

CREATE TABLE `fabrics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `material` varchar(255) DEFAULT NULL,
  `unit` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gold_conversions`
--

CREATE TABLE `gold_conversions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED NOT NULL,
  `input_weight` decimal(10,2) NOT NULL,
  `note` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gold_conversion_outputs`
--

CREATE TABLE `gold_conversion_outputs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gold_conversion_id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gold_management`
--

CREATE TABLE `gold_management` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `type` enum('sepuh','patri','rosok') NOT NULL,
  `product_id` int(11) NOT NULL,
  `karat_id` int(11) NOT NULL,
  `gram_in` double NOT NULL,
  `gram_out` double NOT NULL,
  `note` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `edited_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gold_merge_conversions`
--

CREATE TABLE `gold_merge_conversions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `note` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `edited_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gold_merge_conversion_inputs`
--

CREATE TABLE `gold_merge_conversion_inputs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gold_merge_conversion_id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `karats`
--

CREATE TABLE `karats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `karats`
--

INSERT INTO `karats` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '6K', '2026-01-03 22:42:38', '2026-01-03 22:42:38'),
(2, '8K', '2026-01-03 22:43:10', '2026-01-03 22:43:10');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_02_04_012716_create_permission_tables', 1),
(5, '2025_09_09_032326_create_products_table', 1),
(6, '2025_09_09_042226_create_types_table', 1),
(7, '2025_09_12_020720_create_karats_table', 1),
(8, '2025_09_16_004331_create_cuttings_table', 1),
(9, '2025_09_16_004906_create_cutting_details_table', 1),
(10, '2025_09_18_013008_create_debts_table', 1),
(11, '2025_09_18_014432_create_deliveries_table', 1),
(12, '2025_09_18_014940_create_delivery_details_table', 1),
(14, '2025_10_03_062650_create_fabrics_table', 1),
(15, '2025_10_20_151421_create_branches_table', 1),
(16, '2025_10_20_151645_create_storage_locations_table', 1),
(21, '2025_10_24_071416_create_bank_accounts_table', 1),
(28, '2025_11_08_040021_create_customer_suppliers_table', 1),
(93, '2025_11_06_064312_create_gold_management_table', 9),
(117, '2025_09_25_070316_create_product_variants_table', 13),
(118, '2025_10_21_073926_create_stocks_table', 13),
(119, '2025_10_21_075454_create_stock_movements_table', 14),
(120, '2025_10_21_083738_create_transactions_table', 14),
(121, '2025_10_21_083747_create_transaction_details_table', 14),
(122, '2025_10_25_184845_create_stock_adjustments_table', 15),
(123, '2025_10_25_184911_create_stock_adjustment_details_table', 16),
(124, '2025_10_25_194542_add_weight_to_stock_adjustment_details_table', 16),
(125, '2025_11_28_013612_create_gold_conversions_table', 16),
(126, '2025_11_28_013705_create_gold_conversion_outputs_table', 17),
(127, '2025_12_28_150119_create_gold_merge_conversions_table', 17),
(128, '2025_12_28_150140_create_gold_merge_conversion_inputs_table', 17);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 6),
(2, 'App\\Models\\User', 7),
(2, 'App\\Models\\User', 8),
(2, 'App\\Models\\User', 9),
(2, 'App\\Models\\User', 10),
(2, 'App\\Models\\User', 11),
(2, 'App\\Models\\User', 12),
(2, 'App\\Models\\User', 13),
(2, 'App\\Models\\User', 14),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'permission.index', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(2, 'permission.generate', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(3, 'role.index', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(4, 'role.buat', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(5, 'role.simpan', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(6, 'role.ubah', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(7, 'role.update', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(8, 'role.hapus', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(9, 'pengguna.index', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(10, 'pengguna.buat', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(11, 'pengguna.simpan', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(12, 'pengguna.ubah', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(13, 'pengguna.update', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(14, 'pengguna.hapus', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(15, 'produk.index', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(16, 'produk.buat', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(17, 'produk.simpan', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(18, 'produk.ubah', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(19, 'produk.update', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(20, 'produk.hapus', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(21, 'karat.index', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(22, 'karat.buat', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(23, 'karat.simpan', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(24, 'karat.ubah', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(25, 'karat.update', 'web', '2026-01-03 22:04:20', '2026-01-03 22:04:20'),
(26, 'karat.hapus', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(27, 'penyimpanan.index', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(28, 'penyimpanan.buat', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(29, 'penyimpanan.simpan', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(30, 'penyimpanan.ubah', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(31, 'penyimpanan.update', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(32, 'penyimpanan.hapus', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(33, 'cabang.index', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(34, 'cabang.buat', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(35, 'cabang.simpan', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(36, 'cabang.ubah', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(37, 'cabang.update', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(38, 'cabang.hapus', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(39, 'rekening.index', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(40, 'rekening.buat', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(41, 'rekening.simpan', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(42, 'rekening.ubah', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(43, 'rekening.update', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(44, 'rekening.hapus', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(45, 'customer-supplier.index', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(46, 'customer-supplier.buat', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(47, 'customer-supplier.simpan', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(48, 'customer-supplier.ubah', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(49, 'customer-supplier.update', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(50, 'customer-supplier.hapus', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(51, 'varian-produk.index', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(52, 'varian-produk.buat', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(53, 'varian-produk.simpan', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(54, 'varian-produk.ubah', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(55, 'varian-produk.update', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(56, 'varian-produk.ubahDetail', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(57, 'varian-produk.updateDetail', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(58, 'varian-produk.hapus', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(59, 'varian-produk.import', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(60, 'varian-produk.barcode-form', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(61, 'varian-produk.barcode-print', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(62, 'transaksi.index', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(63, 'transaksi.buat', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(64, 'transaksi.simpan', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(65, 'transaksi.ubah', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(66, 'transaksi.update', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(67, 'transaksi.hapus', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(68, 'penjualan.index', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(69, 'penjualan.buat', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(70, 'penjualan.simpan', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(71, 'penjualan.ubah', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(72, 'penjualan.update', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(73, 'penjualan.hapus', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(74, 'penjualan.cetak', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(75, 'opname.index', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(76, 'opname.buat', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(77, 'opname.simpan', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(78, 'opname.import-form', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(79, 'opname.import', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(80, 'opname.hapus', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(81, 'opname.dapatStock', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(82, 'pengelolaan-emas.index', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(83, 'pengelolaan-emas.buat', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(84, 'pengelolaan-emas.simpan', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(85, 'pengelolaan-emas.ubah', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(86, 'pengelolaan-emas.update', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(87, 'pengelolaan-emas.hapus', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(88, 'konversi-emas.index', 'web', '2026-01-03 22:04:21', '2026-01-03 22:04:21'),
(89, 'konversi-emas.buat', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(90, 'konversi-emas.simpan', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(91, 'konversi-emas.ubah', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(92, 'konversi-emas.detail', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(93, 'konversi-emas.update', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(94, 'konversi-emas.hapus', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(95, 'keluar-etalase.index', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(96, 'keluar-etalase.buat', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(97, 'keluar-etalase.simpan', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(98, 'keluar-etalase.ubah', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(99, 'keluar-etalase.detail', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(100, 'keluar-etalase.update', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(101, 'keluar-etalase.hapus', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(102, 'stock.index', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(103, 'stocks.detail', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(104, 'stock.info', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(105, 'stock.berat', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(106, 'utility.ajax-no-rek', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(107, 'utility.getById', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(108, 'utility.getMultipleData', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(109, 'utility.suratJalanId', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(110, 'search', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(111, 'utility.cetak-surat-jalan', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(112, 'backup.export', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `code`, `name`, `created_at`, `updated_at`) VALUES
(1, 'LIONTIN', 'liontin', '2026-01-03 22:42:38', '2026-01-03 22:42:38'),
(4, 'emas', 'emas', '2026-01-03 23:40:10', '2026-01-03 23:40:10');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `karat_id` bigint(20) UNSIGNED DEFAULT NULL,
  `gram` double DEFAULT NULL,
  `type` enum('sepuh','new','customer','batangan') NOT NULL DEFAULT 'new',
  `sku` varchar(255) NOT NULL,
  `barcode` varchar(255) DEFAULT NULL,
  `default_price` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'web', '2026-01-03 22:04:22', '2026-01-03 22:04:22'),
(2, 'Pramuniaga', 'web', '2026-01-16 23:52:02', '2026-01-16 23:52:02'),
(3, 'SPV', 'web', '2026-01-17 01:38:37', '2026-01-17 01:38:37');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(15, 3),
(16, 1),
(16, 3),
(17, 1),
(17, 3),
(18, 1),
(18, 3),
(19, 1),
(19, 3),
(20, 1),
(20, 3),
(21, 1),
(21, 3),
(22, 1),
(22, 3),
(23, 1),
(23, 3),
(24, 1),
(24, 3),
(25, 1),
(25, 3),
(26, 1),
(26, 3),
(27, 1),
(27, 3),
(28, 1),
(28, 3),
(29, 1),
(29, 3),
(30, 1),
(30, 3),
(31, 1),
(31, 3),
(32, 1),
(32, 3),
(33, 1),
(33, 3),
(34, 1),
(34, 3),
(35, 1),
(35, 3),
(36, 1),
(36, 3),
(37, 1),
(37, 3),
(38, 1),
(38, 3),
(39, 1),
(39, 3),
(40, 1),
(40, 3),
(41, 1),
(41, 3),
(42, 1),
(42, 3),
(43, 1),
(43, 3),
(44, 1),
(44, 3),
(45, 1),
(45, 3),
(46, 1),
(46, 3),
(47, 1),
(47, 3),
(48, 1),
(48, 3),
(49, 1),
(49, 3),
(50, 1),
(50, 3),
(51, 1),
(51, 3),
(52, 1),
(52, 3),
(53, 1),
(53, 3),
(54, 1),
(54, 3),
(55, 1),
(55, 3),
(56, 1),
(56, 3),
(57, 1),
(57, 3),
(58, 1),
(58, 3),
(59, 1),
(59, 3),
(60, 1),
(60, 3),
(61, 1),
(61, 3),
(62, 1),
(62, 3),
(63, 1),
(63, 3),
(64, 1),
(64, 3),
(65, 1),
(65, 3),
(66, 1),
(66, 3),
(67, 1),
(67, 3),
(68, 1),
(68, 2),
(68, 3),
(69, 1),
(69, 2),
(69, 3),
(70, 1),
(70, 2),
(70, 3),
(71, 1),
(71, 3),
(72, 1),
(72, 3),
(73, 1),
(73, 3),
(74, 1),
(74, 2),
(74, 3),
(75, 1),
(75, 3),
(76, 1),
(76, 3),
(77, 1),
(77, 3),
(78, 1),
(78, 3),
(79, 1),
(79, 3),
(80, 1),
(80, 3),
(81, 1),
(81, 3),
(82, 1),
(82, 3),
(83, 1),
(83, 3),
(84, 1),
(84, 3),
(85, 1),
(85, 3),
(86, 1),
(86, 3),
(87, 1),
(87, 3),
(88, 1),
(88, 3),
(89, 1),
(89, 3),
(90, 1),
(90, 3),
(91, 1),
(91, 3),
(92, 1),
(92, 3),
(93, 1),
(93, 3),
(94, 1),
(94, 3),
(95, 1),
(95, 3),
(96, 1),
(96, 3),
(97, 1),
(97, 3),
(98, 1),
(98, 3),
(99, 1),
(99, 3),
(100, 1),
(100, 3),
(101, 1),
(101, 3),
(102, 1),
(102, 3),
(103, 1),
(103, 3),
(104, 1),
(104, 3),
(105, 1),
(105, 3),
(106, 1),
(106, 2),
(106, 3),
(107, 1),
(107, 2),
(107, 3),
(108, 1),
(108, 2),
(108, 3),
(109, 1),
(109, 2),
(109, 3),
(110, 1),
(110, 2),
(110, 3),
(111, 1),
(111, 2),
(111, 3),
(112, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('kTRSm4XZcjx7YMD4NGs0eiwsD5OJn63AKVVYkMMU', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoib0ZEcEhEaEVKQjdjQ2I3OGVwazBiOFhMdGl3RGV4N1FFRnNLVkVqbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9nb2xkLWFwcC92YXJpYW4tcHJvZHVrL2JhcmNvZGUvMTIxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3Njg3NTQ4MTY7fX0=', 1768763091),
('PUPyOFNQTuKqY8exSwbUk5kxq96rxUA6Xpjoxboi', 1, '192.168.1.2', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Mobile Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMXVqU0Jhd1c5V0o3OXZkcVBNT0JFc0FyamhCZUQ4TXRhSFN2OFZROCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTg6Imh0dHA6Ly8xOTIuMTY4LjEuNDo4MDAwL2dvbGQtYXBwL3Zhcmlhbi1wcm9kdWsvYmFyY29kZS8xMzMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc2ODc2MTMyMzt9fQ==', 1768762332);

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `storage_location_id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('new','customer','sepuh','batangan') NOT NULL DEFAULT 'new',
  `weight` decimal(15,3) DEFAULT NULL,
  `quantity` decimal(15,3) NOT NULL DEFAULT 0.000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustments`
--

CREATE TABLE `stock_adjustments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `storage_location_id` bigint(20) UNSIGNED NOT NULL,
  `adjustment_date` date NOT NULL,
  `note` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_adjustment_details`
--

CREATE TABLE `stock_adjustment_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_adjustment_id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED NOT NULL,
  `system_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `actual_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `difference` decimal(15,3) NOT NULL DEFAULT 0.000,
  `weight` decimal(15,3) NOT NULL DEFAULT 0.000,
  `type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('in','out','adjustment','loan_out','loan_in') NOT NULL,
  `gold_type` enum('new','customer','sepuh','batangan') NOT NULL DEFAULT 'new',
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `storage_location_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `weight` decimal(12,3) DEFAULT NULL,
  `reference_type` varchar(255) DEFAULT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `storage_locations`
--

CREATE TABLE `storage_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `storage_locations`
--

INSERT INTO `storage_locations` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Etalase', NULL, '2026-01-03 22:41:23', '2026-01-03 22:41:33'),
(2, 'Brankas', NULL, '2026-01-03 22:41:44', '2026-01-03 22:41:52');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('purchase','penjualan') NOT NULL,
  `purchase_type` enum('customer','new') DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED NOT NULL,
  `storage_location_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `total` decimal(18,2) NOT NULL DEFAULT 0.00,
  `customer_id` int(11) DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `payment_method` enum('cash','transfer','cash_transfer') DEFAULT NULL,
  `bank_account_id` bigint(20) UNSIGNED DEFAULT NULL,
  `cash_amount` decimal(15,2) DEFAULT NULL,
  `transfer_amount` decimal(15,2) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `product_variant_id` bigint(20) UNSIGNED NOT NULL,
  `unit_price` decimal(18,2) NOT NULL,
  `type` enum('new','customer','sepuh','batangan') NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE `types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `email_verified_at`, `password`, `is_active`, `employee_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Haidar Ali Al-Jufrie', 'haidar17ali@gmail.com', NULL, '$2y$12$AgTYCqylAoDV1gv6bKhmKuAgZy77jt6/3Y7wCCd7UOh1GjVNuaOl2', 1, NULL, NULL, '2026-01-03 22:03:05', '2026-01-03 22:03:05'),
(2, 'Fishal Rayyes', 'rayyesfishal@gmail.com', NULL, '$2y$12$AY8h1iZjcFSpj/JPNhUzWOjB0QJZmcVYsUTAzDHdgShZREmzz6fdO', 1, NULL, NULL, '2026-01-16 23:49:01', '2026-01-16 23:49:01'),
(3, 'Amalia Bela Safitri', 'amaliabela2@gmail.com', NULL, '$2y$12$YCPPuGQ06jEePkwKJe24Ze7guzfm09wOXupumXiXldlgg46lT0JdG', 1, NULL, NULL, '2026-01-17 00:06:30', '2026-01-17 00:06:44'),
(4, 'Abdul Rahman', 'abd.rahmanatt@gmail.com', NULL, '$2y$12$7QAGhrDeI0YzOZLwj1BoouJ6MsFXQrQ2AVR.gftX0zD6lxKU3uajy', 1, NULL, NULL, '2026-01-17 14:25:56', '2026-01-17 14:25:56'),
(5, 'Kurniawan perdana', 'sangarpojok69@gmail.com', NULL, '$2y$12$4bhmtkzHfBM//4Ugr6doVenIYUaqJ1Gomh4/bEZuJvdeRuWgh0O/W', 1, NULL, NULL, '2026-01-17 14:27:18', '2026-01-17 14:27:18'),
(6, 'Ahmad osama bamifta', 'osama@gmail.com', NULL, '$2y$12$uy8Cse7d9TT.4VTrjk10U.AcoSLZpNZ5JwyKA1wIqkDCeq.7PGzBy', 1, NULL, NULL, '2026-01-17 14:27:58', '2026-01-17 14:27:58'),
(7, 'Maimunatun Nafisa', 'maimuna@gmail.com', NULL, '$2y$12$mFxvsO/UCpDQ9aQzIQO7MedsUnlFA0Jng5ZHjKClrrXeveESvI.Ny', 1, NULL, NULL, '2026-01-17 14:28:39', '2026-01-17 14:28:39'),
(8, 'Risa Ramadhani', 'risaramah18@gmail.com', NULL, '$2y$12$w/U.B.Xp.LE5yLivKf2WN.Qrm5Vhi/QlK1nKd8b6cZOxnKZraQShK', 1, NULL, NULL, '2026-01-17 14:29:21', '2026-01-17 14:29:21'),
(9, 'Safinatun Naza', 'safinanaza49@gmail.com', NULL, '$2y$12$ZOQK1Git/peC/MY2utNqwupFgrWocYpNxQ4o1zaey.H/ZNrdp6ctS', 1, NULL, NULL, '2026-01-17 14:29:59', '2026-01-17 14:29:59'),
(10, 'Riza Adafi\'ah A M', 'rizaadafiah@gmail.com', NULL, '$2y$12$VISPwes2tGDkvvdXs8A9eeWtV432HErpaMLS6IiMQktfJaDcHtiya', 1, NULL, NULL, '2026-01-17 14:30:39', '2026-01-17 14:30:39'),
(11, 'Adinda Chandrawahyudi', 'chandrawahyudiadinda32@gmail.com', NULL, '$2y$12$Oq1m6Ob3Qijuca3iIbOwauCxpv5imcbPo01lqeOZWxIjjoWtYWz9W', 1, NULL, NULL, '2026-01-17 14:31:22', '2026-01-17 14:31:22'),
(12, 'Aulya Rizqy Ramadhani', 'aulyarizky36@gmail.com', NULL, '$2y$12$5JMtFv5s1x.LiMCifOQxdeSZR0QiEquke9poX1lBAKS8ItueZTUyK', 1, NULL, NULL, '2026-01-17 14:32:13', '2026-01-17 14:32:13'),
(13, 'Eka Nur Azizah', 'nurazizahe427@gmail.com', NULL, '$2y$12$Q9N4lg5VZ5zbPJdIApNlCeG0CahLwLBF1BfAeUcpVpJ5ia8dAmn7C', 1, NULL, NULL, '2026-01-17 14:32:46', '2026-01-17 14:32:46'),
(14, 'Shofil Fuad', 'fuadshofil14@gmail.com', NULL, '$2y$12$fnWVY5HNyhCcl5IyAisljOtfR7TNZRSs0UzG/j8usRVguFuSbCoL.', 1, NULL, NULL, '2026-01-17 14:33:37', '2026-01-17 14:33:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branches_code_unique` (`code`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `customer_suppliers`
--
ALTER TABLE `customer_suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cuttings`
--
ALTER TABLE `cuttings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cutting_details`
--
ALTER TABLE `cutting_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `debts`
--
ALTER TABLE `debts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_details`
--
ALTER TABLE `delivery_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fabrics`
--
ALTER TABLE `fabrics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gold_conversions`
--
ALTER TABLE `gold_conversions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gold_conversions_stock_id_foreign` (`stock_id`),
  ADD KEY `gold_conversions_created_by_foreign` (`created_by`),
  ADD KEY `gold_conversions_edited_by_foreign` (`edited_by`);

--
-- Indexes for table `gold_conversion_outputs`
--
ALTER TABLE `gold_conversion_outputs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gold_conversion_outputs_gold_conversion_id_foreign` (`gold_conversion_id`),
  ADD KEY `gold_conversion_outputs_product_variant_id_foreign` (`product_variant_id`);

--
-- Indexes for table `gold_management`
--
ALTER TABLE `gold_management`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gold_merge_conversions`
--
ALTER TABLE `gold_merge_conversions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gold_merge_conversion_inputs`
--
ALTER TABLE `gold_merge_conversion_inputs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karats`
--
ALTER TABLE `karats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_code_unique` (`code`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_variants_sku_unique` (`sku`),
  ADD UNIQUE KEY `product_variants_product_id_karat_id_gram_type_unique` (`product_id`,`karat_id`,`gram`,`type`),
  ADD UNIQUE KEY `product_variants_barcode_unique` (`barcode`),
  ADD KEY `product_variants_karat_id_foreign` (`karat_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_stock_per_location` (`branch_id`,`storage_location_id`,`product_variant_id`,`type`),
  ADD KEY `stocks_storage_location_id_foreign` (`storage_location_id`),
  ADD KEY `stocks_product_variant_id_foreign` (`product_variant_id`);

--
-- Indexes for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_adjustments_branch_id_foreign` (`branch_id`),
  ADD KEY `stock_adjustments_storage_location_id_foreign` (`storage_location_id`);

--
-- Indexes for table `stock_adjustment_details`
--
ALTER TABLE `stock_adjustment_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_adjustment_details_stock_adjustment_id_foreign` (`stock_adjustment_id`),
  ADD KEY `stock_adjustment_details_product_variant_id_foreign` (`product_variant_id`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_product_variant_id_foreign` (`product_variant_id`),
  ADD KEY `stock_movements_branch_id_foreign` (`branch_id`),
  ADD KEY `stock_movements_storage_location_id_foreign` (`storage_location_id`),
  ADD KEY `stock_movements_created_by_foreign` (`created_by`);

--
-- Indexes for table `storage_locations`
--
ALTER TABLE `storage_locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transactions_invoice_number_unique` (`invoice_number`),
  ADD KEY `transactions_branch_id_foreign` (`branch_id`),
  ADD KEY `transactions_storage_location_id_foreign` (`storage_location_id`),
  ADD KEY `transactions_bank_account_id_foreign` (`bank_account_id`),
  ADD KEY `transactions_created_by_foreign` (`created_by`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_details_transaction_id_foreign` (`transaction_id`),
  ADD KEY `transaction_details_product_variant_id_foreign` (`product_variant_id`);

--
-- Indexes for table `types`
--
ALTER TABLE `types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer_suppliers`
--
ALTER TABLE `customer_suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `cuttings`
--
ALTER TABLE `cuttings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cutting_details`
--
ALTER TABLE `cutting_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `debts`
--
ALTER TABLE `debts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_details`
--
ALTER TABLE `delivery_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fabrics`
--
ALTER TABLE `fabrics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gold_conversions`
--
ALTER TABLE `gold_conversions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gold_conversion_outputs`
--
ALTER TABLE `gold_conversion_outputs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gold_management`
--
ALTER TABLE `gold_management`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gold_merge_conversions`
--
ALTER TABLE `gold_merge_conversions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gold_merge_conversion_inputs`
--
ALTER TABLE `gold_merge_conversion_inputs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `karats`
--
ALTER TABLE `karats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_adjustment_details`
--
ALTER TABLE `stock_adjustment_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `storage_locations`
--
ALTER TABLE `storage_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `types`
--
ALTER TABLE `types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `gold_conversions`
--
ALTER TABLE `gold_conversions`
  ADD CONSTRAINT `gold_conversions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gold_conversions_edited_by_foreign` FOREIGN KEY (`edited_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gold_conversions_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gold_conversion_outputs`
--
ALTER TABLE `gold_conversion_outputs`
  ADD CONSTRAINT `gold_conversion_outputs_gold_conversion_id_foreign` FOREIGN KEY (`gold_conversion_id`) REFERENCES `gold_conversions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gold_conversion_outputs_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`);

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_karat_id_foreign` FOREIGN KEY (`karat_id`) REFERENCES `karats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stocks`
--
ALTER TABLE `stocks`
  ADD CONSTRAINT `stocks_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stocks_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stocks_storage_location_id_foreign` FOREIGN KEY (`storage_location_id`) REFERENCES `storage_locations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_adjustments`
--
ALTER TABLE `stock_adjustments`
  ADD CONSTRAINT `stock_adjustments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustments_storage_location_id_foreign` FOREIGN KEY (`storage_location_id`) REFERENCES `storage_locations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_adjustment_details`
--
ALTER TABLE `stock_adjustment_details`
  ADD CONSTRAINT `stock_adjustment_details_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_adjustment_details_stock_adjustment_id_foreign` FOREIGN KEY (`stock_adjustment_id`) REFERENCES `stock_adjustments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `stock_movements_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_storage_location_id_foreign` FOREIGN KEY (`storage_location_id`) REFERENCES `storage_locations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `transactions_storage_location_id_foreign` FOREIGN KEY (`storage_location_id`) REFERENCES `storage_locations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_product_variant_id_foreign` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_details_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
