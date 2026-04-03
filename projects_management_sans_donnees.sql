-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.31 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Listage de la structure de la table crec_rh_system_db. pro_app_notifications
CREATE TABLE IF NOT EXISTS `pro_app_notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `data` json DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT 'bi-bell',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `app_notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_app_settings
CREATE TABLE IF NOT EXISTS `pro_app_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('text','date','number','textarea','email','url') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_archiving_documents
CREATE TABLE IF NOT EXISTS `pro_archiving_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'archive',
  `physical_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `archive_date` date DEFAULT NULL,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pro_archiving_documents_project_id_foreign` (`project_id`),
  KEY `pro_archiving_documents_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `pro_archiving_documents_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `pro_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pro_archiving_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_amendment_deviation_inspections
CREATE TABLE IF NOT EXISTS `pro_cl_amendment_deviation_inspections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint unsigned NOT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `document_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deviation_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amendment_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pro_cl_amendment_deviation_inspections_inspection_id_unique` (`inspection_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_cdc_bottle_coating
CREATE TABLE IF NOT EXISTS `pro_cl_cdc_bottle_coating` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_cdc_bottle_test
CREATE TABLE IF NOT EXISTS `pro_cl_cdc_bottle_test` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_cone_irs_bl_test
CREATE TABLE IF NOT EXISTS `pro_cl_cone_irs_bl_test` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_cone_irs_bl_treat
CREATE TABLE IF NOT EXISTS `pro_cl_cone_irs_bl_treat` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_cone_irs_walls
CREATE TABLE IF NOT EXISTS `pro_cl_cone_irs_walls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_cone_llin
CREATE TABLE IF NOT EXISTS `pro_cl_cone_llin` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_cylinder_bioassay
CREATE TABLE IF NOT EXISTS `pro_cl_cylinder_bioassay` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_data_quality_inspections
CREATE TABLE IF NOT EXISTS `pro_cl_data_quality_inspections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint unsigned NOT NULL,
  `sections_done` json DEFAULT NULL,
  `aspects_inspected` json DEFAULT NULL,
  `study_start_date` date DEFAULT NULL,
  `study_end_date` date DEFAULT NULL,
  `study_director_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_inspector_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_inspector_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `personnel_involved` json DEFAULT NULL,
  `a_answers` json DEFAULT NULL,
  `a_date_performed` date DEFAULT NULL,
  `a_qa_personnel_id` bigint unsigned DEFAULT NULL,
  `a_comments` text COLLATE utf8mb4_unicode_ci,
  `a_is_conforming` tinyint(1) DEFAULT NULL,
  `b_answers` json DEFAULT NULL,
  `b_date_performed` date DEFAULT NULL,
  `b_qa_personnel_id` bigint unsigned DEFAULT NULL,
  `b_comments` text COLLATE utf8mb4_unicode_ci,
  `b_is_conforming` tinyint(1) DEFAULT NULL,
  `c_v1_answers` json DEFAULT NULL,
  `c_v1_date_performed` date DEFAULT NULL,
  `c_v1_qa_personnel_id` bigint unsigned DEFAULT NULL,
  `c_v2_answers` json DEFAULT NULL,
  `c_v2_date_performed` date DEFAULT NULL,
  `c_v2_qa_personnel_id` bigint unsigned DEFAULT NULL,
  `c_comments` text COLLATE utf8mb4_unicode_ci,
  `c_is_conforming` tinyint(1) DEFAULT NULL,
  `d_v1_answers` json DEFAULT NULL,
  `d_v1_date_performed` date DEFAULT NULL,
  `d_v1_qa_personnel_id` bigint unsigned DEFAULT NULL,
  `d_v2_answers` json DEFAULT NULL,
  `d_v2_date_performed` date DEFAULT NULL,
  `d_v2_qa_personnel_id` bigint unsigned DEFAULT NULL,
  `d_comments` text COLLATE utf8mb4_unicode_ci,
  `d_is_conforming` tinyint(1) DEFAULT NULL,
  `e_answers` json DEFAULT NULL,
  `e_date_performed` date DEFAULT NULL,
  `e_qa_personnel_id` bigint unsigned DEFAULT NULL,
  `e_comments` text COLLATE utf8mb4_unicode_ci,
  `e_is_conforming` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pro_cl_data_quality_inspections_inspection_id_foreign` (`inspection_id`),
  CONSTRAINT `pro_cl_data_quality_inspections_inspection_id_foreign` FOREIGN KEY (`inspection_id`) REFERENCES `pro_qa_inspections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_facility_inspection
CREATE TABLE IF NOT EXISTS `pro_cl_facility_inspection` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint unsigned NOT NULL,
  `sections_done` json DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `project_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `a_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q17` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q18` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q19` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q20` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q21` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q22` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q23` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q24` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q25` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q26` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_comments` text COLLATE utf8mb4_unicode_ci,
  `a_is_conforming` tinyint(1) DEFAULT NULL,
  `b_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_comments` text COLLATE utf8mb4_unicode_ci,
  `b_is_conforming` tinyint(1) DEFAULT NULL,
  `c_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_comments` text COLLATE utf8mb4_unicode_ci,
  `c_is_conforming` tinyint(1) DEFAULT NULL,
  `d_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_comments` text COLLATE utf8mb4_unicode_ci,
  `d_is_conforming` tinyint(1) DEFAULT NULL,
  `e_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_comments` text COLLATE utf8mb4_unicode_ci,
  `e_is_conforming` tinyint(1) DEFAULT NULL,
  `f_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q17` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q18` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q19` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_comments` text COLLATE utf8mb4_unicode_ci,
  `f_is_conforming` tinyint(1) DEFAULT NULL,
  `g_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_comments` text COLLATE utf8mb4_unicode_ci,
  `g_is_conforming` tinyint(1) DEFAULT NULL,
  `h_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_comments` text COLLATE utf8mb4_unicode_ci,
  `h_is_conforming` tinyint(1) DEFAULT NULL,
  `i_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_comments` text COLLATE utf8mb4_unicode_ci,
  `i_is_conforming` tinyint(1) DEFAULT NULL,
  `j_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `j_comments` text COLLATE utf8mb4_unicode_ci,
  `j_is_conforming` tinyint(1) DEFAULT NULL,
  `k_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `k_comments` text COLLATE utf8mb4_unicode_ci,
  `k_is_conforming` tinyint(1) DEFAULT NULL,
  `l_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q17` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q18` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q19` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q20` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q21` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q22` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q23` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q24` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_q25` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_comments` text COLLATE utf8mb4_unicode_ci,
  `l_is_conforming` tinyint(1) DEFAULT NULL,
  `m_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `m_comments` text COLLATE utf8mb4_unicode_ci,
  `m_is_conforming` tinyint(1) DEFAULT NULL,
  `n_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q17` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q18` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q19` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q20` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q21` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q22` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q23` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q24` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_q25` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_comments` text COLLATE utf8mb4_unicode_ci,
  `n_is_conforming` tinyint(1) DEFAULT NULL,
  `o_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `o_comments` text COLLATE utf8mb4_unicode_ci,
  `o_is_conforming` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pro_cl_facility_inspection_inspection_id_index` (`inspection_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_facility_inspection_cove
CREATE TABLE IF NOT EXISTS `pro_cl_facility_inspection_cove` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint unsigned NOT NULL,
  `sections_done` json DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `a_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_comments` text COLLATE utf8mb4_unicode_ci,
  `a_is_conforming` tinyint(1) DEFAULT NULL,
  `b_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q17` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q18` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q19` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q20` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q21` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q22` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q23` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q24` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_comments` text COLLATE utf8mb4_unicode_ci,
  `b_is_conforming` tinyint(1) DEFAULT NULL,
  `c_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q17` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q18` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q19` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q20` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q21` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q22` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_comments` text COLLATE utf8mb4_unicode_ci,
  `c_is_conforming` tinyint(1) DEFAULT NULL,
  `d_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_comments` text COLLATE utf8mb4_unicode_ci,
  `d_is_conforming` tinyint(1) DEFAULT NULL,
  `e_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_comments` text COLLATE utf8mb4_unicode_ci,
  `e_is_conforming` tinyint(1) DEFAULT NULL,
  `f_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_comments` text COLLATE utf8mb4_unicode_ci,
  `f_is_conforming` tinyint(1) DEFAULT NULL,
  `g_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g_comments` text COLLATE utf8mb4_unicode_ci,
  `g_is_conforming` tinyint(1) DEFAULT NULL,
  `h_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q17` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q18` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q19` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q20` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q21` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q22` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q23` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q24` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_q25` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `h_comments` text COLLATE utf8mb4_unicode_ci,
  `h_is_conforming` tinyint(1) DEFAULT NULL,
  `i_q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `i_comments` text COLLATE utf8mb4_unicode_ci,
  `i_is_conforming` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_irs_treatment
CREATE TABLE IF NOT EXISTS `pro_cl_irs_treatment` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_irs_trial
CREATE TABLE IF NOT EXISTS `pro_cl_irs_trial` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_llin_exp_huts
CREATE TABLE IF NOT EXISTS `pro_cl_llin_exp_huts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q17` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q18` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q19` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q20` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_llin_washing
CREATE TABLE IF NOT EXISTS `pro_cl_llin_washing` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_process_inspection
CREATE TABLE IF NOT EXISTS `pro_cl_process_inspection` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` int DEFAULT NULL,
  `filled_by` int DEFAULT NULL,
  `a_q1` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q2` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q3` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q4` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q5` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q6` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q7` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q8` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q9` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q10` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q11` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q12` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q13` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q14` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q15` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q16` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q17` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q18` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q19` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q20` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q21` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q22` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q23` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q24` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_comments` text COLLATE utf8mb4_unicode_ci,
  `a_is_conforming` tinyint(1) DEFAULT NULL,
  `b_q1` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q2` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q3` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q4` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q5` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q6` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q7` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q8` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q9` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q10` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q11` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q12` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q13` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q14` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q15` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q16` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q17` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q18` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q19` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q20` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_comments` text COLLATE utf8mb4_unicode_ci,
  `b_is_conforming` tinyint(1) DEFAULT NULL,
  `c_q1` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q2` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q3` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q4` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q5` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q6` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q7` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q8` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q9` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q10` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q11` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q12` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q13` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q14` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q15` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q16` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q17` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q18` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q19` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q20` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q21` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q22` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q23` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q24` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q25` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q26` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q27` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q28` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q29` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q30` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q31` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_comments` text COLLATE utf8mb4_unicode_ci,
  `c_is_conforming` tinyint(1) DEFAULT NULL,
  `d_q1` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q2` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q3` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q4` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q5` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q6` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q7` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q8` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q9` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q10` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q11` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q12` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q13` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q14` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q15` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q16` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q17` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q18` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q19` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q20` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q21` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q22` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q23` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q24` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q25` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_comments` text COLLATE utf8mb4_unicode_ci,
  `d_is_conforming` tinyint(1) DEFAULT NULL,
  `e_q1` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q2` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q3` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q4` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q5` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q6` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q7` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q8` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q9` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q10` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q11` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_comments` text COLLATE utf8mb4_unicode_ci,
  `e_is_conforming` tinyint(1) DEFAULT NULL,
  `sections_done` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_questions
CREATE TABLE IF NOT EXISTS `pro_cl_questions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `section_id` bigint unsigned NOT NULL,
  `item_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `response_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes_no_na',
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `copied_from_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `first_used_at` timestamp NULL DEFAULT NULL,
  `usage_count` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cl_questions_section_item_unique` (`section_id`,`item_number`),
  KEY `cl_questions_copied_from_id_foreign` (`copied_from_id`),
  CONSTRAINT `cl_questions_copied_from_id_foreign` FOREIGN KEY (`copied_from_id`) REFERENCES `pro_cl_questions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cl_questions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `pro_cl_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=822 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_sections
CREATE TABLE IF NOT EXISTS `pro_cl_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `template_id` bigint unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `letter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_style` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `form_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cl_sections_template_code_unique` (`template_id`,`code`),
  CONSTRAINT `cl_sections_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `pro_cl_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_spatial_repellents
CREATE TABLE IF NOT EXISTS `pro_cl_spatial_repellents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q17` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q18` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q19` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q20` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_study_protocol_inspections
CREATE TABLE IF NOT EXISTS `pro_cl_study_protocol_inspections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint unsigned NOT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `sections_done` json DEFAULT NULL,
  `a_q1` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q2` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q3` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q4` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q5` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q6` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q7` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q8` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q9` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q10` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q11` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q12` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q13` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q14` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q15` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q16` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q17` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q18` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q19` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q20` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_comments` text COLLATE utf8mb4_unicode_ci,
  `a_is_conforming` tinyint(1) DEFAULT NULL,
  `b_q1` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q2` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q3` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q4` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_comments` text COLLATE utf8mb4_unicode_ci,
  `b_is_conforming` tinyint(1) DEFAULT NULL,
  `c_q1` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q2` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q3` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q4` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q5` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q6` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_comments` text COLLATE utf8mb4_unicode_ci,
  `c_is_conforming` tinyint(1) DEFAULT NULL,
  `d_q1` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q2` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_comments` text COLLATE utf8mb4_unicode_ci,
  `d_is_conforming` tinyint(1) DEFAULT NULL,
  `e_q1` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q2` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q3` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q4` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_comments` text COLLATE utf8mb4_unicode_ci,
  `e_is_conforming` tinyint(1) DEFAULT NULL,
  `f_q1` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_1_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_1_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_1_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_2_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_2_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_2_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_3_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_3_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_3_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_4_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_4_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_4_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_5_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_5_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_5_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_6_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_6_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_6_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_7_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_7_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_7_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_8_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_8_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_8_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_9_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_9_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_9_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_10_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_10_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_10_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_11_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_11_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_11_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_12_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_12_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_12_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_13_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_13_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_13_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_14_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_14_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_14_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_15_result` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_15_level` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_staff_15_remarks` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f_comments` text COLLATE utf8mb4_unicode_ci,
  `f_is_conforming` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pro_cl_study_protocol_inspections_inspection_id_foreign` (`inspection_id`),
  CONSTRAINT `pro_cl_study_protocol_inspections_inspection_id_foreign` FOREIGN KEY (`inspection_id`) REFERENCES `pro_qa_inspections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_study_report_inspections
CREATE TABLE IF NOT EXISTS `pro_cl_study_report_inspections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint unsigned NOT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `sections_done` json DEFAULT NULL,
  `a_q1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q5` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q6` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q7` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q8` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q9` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q10` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q11` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q12` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q13` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q14` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q15` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q16` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q17` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q18` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q19` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q20` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q21` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q22` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q23` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q24` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q25` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q26` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q27` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_q28` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_comments` text COLLATE utf8mb4_unicode_ci,
  `a_is_conforming` tinyint(1) DEFAULT NULL,
  `b_q1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_q3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_comments` text COLLATE utf8mb4_unicode_ci,
  `b_is_conforming` tinyint(1) DEFAULT NULL,
  `c_q1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_q2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_comments` text COLLATE utf8mb4_unicode_ci,
  `c_is_conforming` tinyint(1) DEFAULT NULL,
  `d_q1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_q4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_comments` text COLLATE utf8mb4_unicode_ci,
  `d_is_conforming` tinyint(1) DEFAULT NULL,
  `e_q1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_q4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_comments` text COLLATE utf8mb4_unicode_ci,
  `e_is_conforming` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pro_cl_study_report_inspections_inspection_id_unique` (`inspection_id`),
  CONSTRAINT `pro_cl_study_report_inspections_inspection_id_foreign` FOREIGN KEY (`inspection_id`) REFERENCES `pro_qa_inspections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_templates
CREATE TABLE IF NOT EXISTS `pro_cl_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cl_templates_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cl_tunnel_test
CREATE TABLE IF NOT EXISTS `pro_cl_tunnel_test` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspection_id` bigint unsigned NOT NULL,
  `q1` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q2` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q3` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q4` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q5` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q6` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q7` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q8` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q9` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q10` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q11` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q12` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q13` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q14` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q15` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `q16` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `filled_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cpia_assessments
CREATE TABLE IF NOT EXISTS `pro_cpia_assessments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `project_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `study_director_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `study_title` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `completed_at` timestamp NULL DEFAULT NULL,
  `completed_by` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpia_assessments_project_id_unique` (`project_id`),
  CONSTRAINT `cpia_assessments_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `pro_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cpia_items
CREATE TABLE IF NOT EXISTS `pro_cpia_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `section_id` bigint unsigned NOT NULL,
  `item_number` smallint unsigned NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `copied_from_id` bigint unsigned DEFAULT NULL,
  `usage_count` int unsigned NOT NULL DEFAULT '0',
  `first_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpia_items_section_id_item_number_unique` (`section_id`,`item_number`),
  KEY `cpia_items_copied_from_id_foreign` (`copied_from_id`),
  CONSTRAINT `cpia_items_copied_from_id_foreign` FOREIGN KEY (`copied_from_id`) REFERENCES `pro_cpia_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cpia_items_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `pro_cpia_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cpia_responses
CREATE TABLE IF NOT EXISTS `pro_cpia_responses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `assessment_id` bigint unsigned NOT NULL,
  `section_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `impact_score` tinyint unsigned DEFAULT NULL,
  `is_selected` tinyint(1) NOT NULL DEFAULT '0',
  `item_text_snapshot` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpia_responses_assessment_id_item_id_unique` (`assessment_id`,`item_id`),
  KEY `cpia_responses_section_id_foreign` (`section_id`),
  KEY `cpia_responses_item_id_foreign` (`item_id`),
  KEY `cpia_responses_assessment_id_section_id_index` (`assessment_id`,`section_id`),
  CONSTRAINT `cpia_responses_assessment_id_foreign` FOREIGN KEY (`assessment_id`) REFERENCES `pro_cpia_assessments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cpia_responses_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `pro_cpia_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cpia_responses_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `pro_cpia_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_cpia_sections
CREATE TABLE IF NOT EXISTS `pro_cpia_sections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `letter` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` smallint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpia_sections_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_document_signatures
CREATE TABLE IF NOT EXISTS `pro_document_signatures` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `signer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_id` bigint unsigned NOT NULL,
  `role_in_document` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `signature_data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signed_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_signatures_user_id_foreign` (`user_id`),
  KEY `document_signatures_document_type_document_id_index` (`document_type`,`document_id`),
  CONSTRAINT `document_signatures_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_inspection_question_snapshots
CREATE TABLE IF NOT EXISTS `pro_inspection_question_snapshots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint unsigned NOT NULL,
  `cl_question_id` bigint unsigned DEFAULT NULL,
  `template_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_letter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `section_subtitle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section_display_style` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `section_form_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `section_sort_order` int NOT NULL DEFAULT '0',
  `url_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `response_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes_no_na',
  `sort_order` int NOT NULL DEFAULT '0',
  `snapshotted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inspection_question_snapshots_cl_question_id_foreign` (`cl_question_id`),
  KEY `inspection_question_snapshots_inspection_id_url_slug_index` (`inspection_id`,`url_slug`),
  KEY `inspection_question_snapshots_inspection_id_section_code_index` (`inspection_id`,`section_code`),
  CONSTRAINT `inspection_question_snapshots_cl_question_id_foreign` FOREIGN KEY (`cl_question_id`) REFERENCES `pro_cl_questions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inspection_question_snapshots_inspection_id_foreign` FOREIGN KEY (`inspection_id`) REFERENCES `pro_qa_inspections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_inspection_responses
CREATE TABLE IF NOT EXISTS `pro_inspection_responses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint unsigned NOT NULL,
  `snapshot_id` bigint unsigned DEFAULT NULL,
  `section_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `yes_no_na` enum('yes','no','na') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text_response` text COLLATE utf8mb4_unicode_ci,
  `is_checked` tinyint(1) DEFAULT NULL,
  `date_value` date DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `is_conforming` tinyint(1) DEFAULT NULL,
  `ca_completed` tinyint(1) DEFAULT NULL,
  `ca_date` date DEFAULT NULL,
  `corrective_actions` text COLLATE utf8mb4_unicode_ci,
  `extra_data` json DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `insp_resp_unique` (`inspection_id`,`section_code`,`item_number`),
  KEY `inspection_responses_snapshot_id_foreign` (`snapshot_id`),
  KEY `inspection_responses_inspection_id_section_code_index` (`inspection_id`,`section_code`),
  CONSTRAINT `inspection_responses_inspection_id_foreign` FOREIGN KEY (`inspection_id`) REFERENCES `pro_qa_inspections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inspection_responses_snapshot_id_foreign` FOREIGN KEY (`snapshot_id`) REFERENCES `pro_inspection_question_snapshots` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=219 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_key_facility_personnels
CREATE TABLE IF NOT EXISTS `pro_key_facility_personnels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `personnel_id` bigint unsigned NOT NULL,
  `staff_role` enum('Facility Manager','Quality Assurance','Archivist','SOP Manager','Data Manager') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_lab_tests
CREATE TABLE IF NOT EXISTS `pro_lab_tests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lab_test_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_test` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_other_basic_documents
CREATE TABLE IF NOT EXISTS `pro_other_basic_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `titre_document` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_document` text COLLATE utf8mb4_unicode_ci,
  `document_file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `upload_date` date DEFAULT NULL,
  `uploaded_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_products_types
CREATE TABLE IF NOT EXISTS `pro_products_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_type_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_product` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_projects
CREATE TABLE IF NOT EXISTS `pro_projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_glp` tinyint NOT NULL DEFAULT (0),
  `project_nature` enum('Evaluation_Phase_1','Evaluation_Phase_2','Evaluation_Phase_1_et_2','Community_Study') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `test_system` enum('lab_mosquitoes','field_mosquitoes','lab_and_field_mosquitoes') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `protocol_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `study_director` int DEFAULT NULL,
  `project_manager` int DEFAULT NULL,
  `date_debut_previsionnelle` date DEFAULT NULL,
  `date_debut_effective` date DEFAULT NULL,
  `date_fin_previsionnelle` date DEFAULT NULL,
  `date_fin_effective` date DEFAULT NULL,
  `description_project` text COLLATE utf8mb4_unicode_ci,
  `project_stage` enum('not_started','in progress','suspended','completed','archived','NA') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_started',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `phases_completed` json DEFAULT NULL,
  `archive_checklist` json DEFAULT NULL,
  `archived_by` bigint unsigned DEFAULT NULL,
  `archive_submission_date` date DEFAULT NULL,
  `archive_deposition_form_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pro_projects_study_director_index` (`study_director`),
  KEY `pro_projects_archived_by_foreign` (`archived_by`),
  CONSTRAINT `pro_projects_archived_by_foreign` FOREIGN KEY (`archived_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_projects_phases_activities_owners
CREATE TABLE IF NOT EXISTS `pro_projects_phases_activities_owners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `activity_id` int NOT NULL,
  `task_owner_id` int NOT NULL,
  `date_execution_prevue` date DEFAULT NULL,
  `date_execution_effective` date DEFAULT NULL,
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_projects_phases_relationship
CREATE TABLE IF NOT EXISTS `pro_projects_phases_relationship` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `phase_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_projects_related_lab_tests
CREATE TABLE IF NOT EXISTS `pro_projects_related_lab_tests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `lab_test_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_projects_related_product_types
CREATE TABLE IF NOT EXISTS `pro_projects_related_product_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `product_type_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_projects_related_study_types
CREATE TABLE IF NOT EXISTS `pro_projects_related_study_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `study_type_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_projects_study_phases_completed
CREATE TABLE IF NOT EXISTS `pro_projects_study_phases_completed` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int unsigned NOT NULL,
  `project_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_phase_id` int unsigned NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `evidence1_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_evidence1_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evidence2_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url_evidence2_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_update_start` date DEFAULT NULL,
  `date_update_end` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pro_projects_study_phases_completed_project_id_index` (`project_id`),
  KEY `pro_projects_study_phases_completed_study_phase_id_index` (`study_phase_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_projects_team
CREATE TABLE IF NOT EXISTS `pro_projects_team` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `staff_id` int NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_joined` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_project_critical_phases_identified
CREATE TABLE IF NOT EXISTS `pro_project_critical_phases_identified` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `activity_id` bigint unsigned NOT NULL,
  `inspection_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_protocols_devs_activities
CREATE TABLE IF NOT EXISTS `pro_protocols_devs_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nom_activite` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_activite` text COLLATE utf8mb4_unicode_ci,
  `level_activite` int NOT NULL,
  `staff_role_perform` enum('Study Director','Facility Manager','Quality Assurance','Project Manager','Other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alternative_staff_role_perform` enum('Study Director','Facility Manager','Quality Assurance','Project Manager','Other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `multipicite` enum('une_fois','plusieurs_fois') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'une_fois',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_protocols_devs_activities_projects
CREATE TABLE IF NOT EXISTS `pro_protocols_devs_activities_projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `protocol_dev_activity_id` bigint unsigned NOT NULL,
  `date_performed` date DEFAULT NULL,
  `real_date_performed` date DEFAULT NULL,
  `date_upload` date DEFAULT NULL,
  `qa_inspection_id` bigint unsigned DEFAULT NULL,
  `due_date_performed` date DEFAULT NULL,
  `staff_id_performed` bigint unsigned DEFAULT NULL,
  `staff_id_assigned` bigint unsigned DEFAULT NULL,
  `staff_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `applicable` tinyint(1) NOT NULL DEFAULT '1',
  `level_activite` int DEFAULT NULL,
  `complete` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pro_protocols_devs_activities_projects_qa_inspection_id_foreign` (`qa_inspection_id`),
  CONSTRAINT `pro_protocols_devs_activities_projects_qa_inspection_id_foreign` FOREIGN KEY (`qa_inspection_id`) REFERENCES `pro_qa_inspections` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_protocol_dev_documents
CREATE TABLE IF NOT EXISTS `pro_protocol_dev_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_project_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned NOT NULL,
  `document_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_performed` date DEFAULT NULL,
  `date_upload` date DEFAULT NULL,
  `staff_id_performed` bigint unsigned DEFAULT NULL,
  `qa_inspection_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pro_protocol_dev_documents_activity_project_id_foreign` (`activity_project_id`),
  KEY `pro_protocol_dev_documents_qa_inspection_id_foreign` (`qa_inspection_id`),
  CONSTRAINT `pro_protocol_dev_documents_activity_project_id_foreign` FOREIGN KEY (`activity_project_id`) REFERENCES `pro_protocols_devs_activities_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pro_protocol_dev_documents_qa_inspection_id_foreign` FOREIGN KEY (`qa_inspection_id`) REFERENCES `pro_qa_inspections` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_qa_activities_checklists
CREATE TABLE IF NOT EXISTS `pro_qa_activities_checklists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `item_number` tinyint unsigned NOT NULL,
  `date_performed` date DEFAULT NULL,
  `means_of_verification` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_checked` tinyint(1) NOT NULL DEFAULT '0',
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pro_qa_activities_checklists_project_id_item_number_unique` (`project_id`,`item_number`),
  CONSTRAINT `pro_qa_activities_checklists_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `pro_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_qa_inspections
CREATE TABLE IF NOT EXISTS `pro_qa_inspections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `facility_location` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qa_inspector_id` bigint unsigned DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `activity_id` bigint unsigned DEFAULT NULL,
  `checklist_slug` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_scheduled` date DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `date_report_fm` date DEFAULT NULL,
  `date_report_sd` date DEFAULT NULL,
  `date_performed` date DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `type_inspection` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_qa_inspections_findings
CREATE TABLE IF NOT EXISTS `pro_qa_inspections_findings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint unsigned NOT NULL,
  `facility_section` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `finding_text` text COLLATE utf8mb4_unicode_ci,
  `is_conformity` tinyint(1) NOT NULL DEFAULT '0',
  `action_point` text COLLATE utf8mb4_unicode_ci,
  `means_of_verification` text COLLATE utf8mb4_unicode_ci,
  `resolved_by_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deadline_date` date DEFAULT NULL,
  `deadline_text` text COLLATE utf8mb4_unicode_ci,
  `meeting_date` date DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `status` enum('pending','complete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `parent_finding_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_qa_review_custom_items
CREATE TABLE IF NOT EXISTS `pro_qa_review_custom_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint unsigned NOT NULL,
  `sort_order` tinyint unsigned NOT NULL DEFAULT '1',
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `yes_no` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `corrective_actions` text COLLATE utf8mb4_unicode_ci,
  `ca_completed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qa_review_custom_items_inspection_id_foreign` (`inspection_id`),
  CONSTRAINT `qa_review_custom_items_inspection_id_foreign` FOREIGN KEY (`inspection_id`) REFERENCES `pro_qa_review_inspections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_qa_review_inspections
CREATE TABLE IF NOT EXISTS `pro_qa_review_inspections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `scheduled_date` date DEFAULT NULL,
  `review_date` date DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `reviewer_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_signed` date DEFAULT NULL,
  `meeting_date` date DEFAULT NULL,
  `meeting_participants` text COLLATE utf8mb4_unicode_ci,
  `meeting_notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qa_review_inspections_created_by_foreign` (`created_by`),
  CONSTRAINT `qa_review_inspections_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_qa_review_responses
CREATE TABLE IF NOT EXISTS `pro_qa_review_responses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_id` bigint unsigned NOT NULL,
  `section_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_number` tinyint unsigned NOT NULL,
  `yes_no` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `corrective_actions` text COLLATE utf8mb4_unicode_ci,
  `ca_completed` tinyint(1) NOT NULL DEFAULT '0',
  `ca_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qa_rev_resp_unique` (`inspection_id`,`section_code`,`item_number`),
  CONSTRAINT `qa_review_responses_inspection_id_foreign` FOREIGN KEY (`inspection_id`) REFERENCES `pro_qa_review_inspections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_qa_statements
CREATE TABLE IF NOT EXISTS `pro_qa_statements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `status` enum('draft','final') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `date_signed` date DEFAULT NULL,
  `qa_manager_id` bigint unsigned DEFAULT NULL,
  `intro_text` text COLLATE utf8mb4_unicode_ci,
  `report_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doc_ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'QA-PR-L-001/09',
  `doc_issue_date` date DEFAULT NULL,
  `doc_next_review` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pro_qa_statements_project_id_foreign` (`project_id`),
  CONSTRAINT `pro_qa_statements_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `pro_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_report_phase_documents
CREATE TABLE IF NOT EXISTS `pro_report_phase_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `document_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `submission_date` date DEFAULT NULL,
  `signature_date` date DEFAULT NULL,
  `qa_inspection_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `submitted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pro_report_phase_documents_project_id_foreign` (`project_id`),
  KEY `pro_report_phase_documents_submitted_by_foreign` (`submitted_by`),
  KEY `pro_report_phase_documents_qa_inspection_id_foreign` (`qa_inspection_id`),
  CONSTRAINT `pro_report_phase_documents_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `pro_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pro_report_phase_documents_qa_inspection_id_foreign` FOREIGN KEY (`qa_inspection_id`) REFERENCES `pro_qa_inspections` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pro_report_phase_documents_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_studies_activities
CREATE TABLE IF NOT EXISTS `pro_studies_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `study_activity_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_activity_id` int DEFAULT NULL,
  `study_type_id` int DEFAULT NULL,
  `activity_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `study_sub_category_id` bigint unsigned DEFAULT NULL,
  `project_id` bigint unsigned NOT NULL,
  `estimated_activity_date` date DEFAULT NULL,
  `estimated_activity_end_date` date DEFAULT NULL,
  `actual_activity_date` date DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `should_be_performed_by` bigint unsigned DEFAULT NULL,
  `performed_by` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'pending, in_progress, completed, delayed, cancelled',
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `phase_critique` tinyint DEFAULT '0',
  `meeting_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_studies_initiation_meetings
CREATE TABLE IF NOT EXISTS `pro_studies_initiation_meetings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `organizer_id` bigint unsigned NOT NULL,
  `date_scheduled` date DEFAULT NULL,
  `time_scheduled` time DEFAULT NULL,
  `date_performed` date DEFAULT NULL,
  `status` enum('complete','pending','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `meeting_type` enum('study_initiation_meeting','other_meeting') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'study_initiation_meeting',
  `study_initiation_meeting_report` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `breve_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `meeting_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_studies_initiation_meetings_participants
CREATE TABLE IF NOT EXISTS `pro_studies_initiation_meetings_participants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `initiation_meeting_id` bigint unsigned NOT NULL,
  `participant_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_studies_types
CREATE TABLE IF NOT EXISTS `pro_studies_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `study_type_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_type` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_studies_types_sub_categories
CREATE TABLE IF NOT EXISTS `pro_studies_types_sub_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `study_sub_category_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_sub_category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `study_type_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_study_activities
CREATE TABLE IF NOT EXISTS `pro_study_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phase_id` int NOT NULL,
  `nb_days_min` int NOT NULL DEFAULT '0' COMMENT 'Le nombre de jour au minimum après le début du projet pour que cette tâche soit accomplie',
  `nb_days_max` int NOT NULL DEFAULT '0' COMMENT 'Le nombre de jour au maximum après le début du projet pour que cette tâche soit accomplie',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_study_directors
CREATE TABLE IF NOT EXISTS `pro_study_directors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `personnel_id` int NOT NULL,
  `date_promotion` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_study_director_appointment_forms
CREATE TABLE IF NOT EXISTS `pro_study_director_appointment_forms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `study_director` bigint unsigned NOT NULL,
  `project_manager` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sd_appointment_date` date DEFAULT NULL,
  `estimated_start_date` date DEFAULT NULL,
  `estimated_end_date` date DEFAULT NULL,
  `study_director_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quality_assurance_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fm_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `sd_appointment_file` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `replacement_date` date DEFAULT NULL,
  `replacement_reason` text COLLATE utf8mb4_unicode_ci,
  `active` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table crec_rh_system_db. pro_study_phases
CREATE TABLE IF NOT EXISTS `pro_study_phases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phase_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `evidence1` text COLLATE utf8mb4_unicode_ci,
  `evidence2` text COLLATE utf8mb4_unicode_ci,
  `level` int NOT NULL DEFAULT (0),
  `class_couleur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
