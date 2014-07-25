-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2014 at 06:35 PM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `seed_library`
--
CREATE DATABASE IF NOT EXISTS `seed_library` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `seed_library`;

-- --------------------------------------------------------

--
-- Table structure for table `accessions`
--

CREATE TABLE IF NOT EXISTS `accessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accession_number` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `item_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `type` enum('DONATION','RETURN') COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `accession_number_UNIQUE` (`accession_number`),
  KEY `donor_returner_idx` (`user_id`),
  KEY `item_idx` (`item_id`),
  KEY `parent_idx` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE IF NOT EXISTS `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` enum('EDIBLE','HERB','ORNAMENTAL') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `family` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `species` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `variety` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `description` tinytext CHARACTER SET latin1 COLLATE latin1_general_ci,
  `seed_sav_level` enum('EASY','MODERATE','MASTER') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT 'EASY',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `packets`
--

CREATE TABLE IF NOT EXISTS `packets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `accession_id` int(10) unsigned NOT NULL,
  `date_harvest` date DEFAULT NULL,
  `grow_location` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `germination_ratio` float DEFAULT NULL,
  `physical_location` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `grams` int(11) DEFAULT NULL,
  `borrower_id` int(10) unsigned DEFAULT NULL,
  `checked_out_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `accession_idx` (`accession_id`),
  KEY `donor_returner_packet_idx` (`borrower_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `assumption_risk` tinyint(1) DEFAULT '0',
  `email` varchar(75) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `address` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `city` varchar(40) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `province` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `postal_code` varchar(7) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `home_phone` varchar(14) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `work_phone` varchar(14) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `cell_phone` varchar(14) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `mentor` tinyint(1) DEFAULT '0',
  `volunteer` tinyint(1) DEFAULT '0',
  `donor` tinyint(1) DEFAULT '0',
  `gardening_exp` enum('NONE','SOME','LOTS') CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT 'NONE',
  `seedsaving_exp` enum('NONE','SOME','LOTS') CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT 'NONE',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accessions`
--
ALTER TABLE `accessions`
  ADD CONSTRAINT `donor_returner_accession` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `item` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `parent` FOREIGN KEY (`parent_id`) REFERENCES `packets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `packets`
--
ALTER TABLE `packets`
  ADD CONSTRAINT `accession` FOREIGN KEY (`accession_id`) REFERENCES `accessions` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `donor_returner_packet` FOREIGN KEY (`borrower_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
