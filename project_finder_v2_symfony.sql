-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 01, 2022 at 09:36 AM
-- Server version: 5.7.36
-- PHP Version: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project_finder_v2_symfony`
--
CREATE DATABASE IF NOT EXISTS `project_finder_v2_symfony` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `project_finder_v2_symfony`;

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20220928020038', '2022-09-28 02:03:16', 623),
('DoctrineMigrations\\Version20220929013301', '2022-09-29 01:35:11', 651);

-- --------------------------------------------------------

--
-- Table structure for table `git_hub_projects_request_manager`
--

DROP TABLE IF EXISTS `git_hub_projects_request_manager`;
CREATE TABLE IF NOT EXISTS `git_hub_projects_request_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_running` tinyint(1) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `error_msg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `git_hub_projects_request_manager`
--

INSERT INTO `git_hub_projects_request_manager` (`id`, `is_running`, `start_time`, `end_time`, `error_msg`) VALUES
(1, 0, '2022-10-01 09:34:22', '2022-10-01 09:35:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `git_hub_repository_record`
--

DROP TABLE IF EXISTS `git_hub_repository_record`;
CREATE TABLE IF NOT EXISTS `git_hub_repository_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `repository_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `html_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stargazers_count` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `pushed_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
