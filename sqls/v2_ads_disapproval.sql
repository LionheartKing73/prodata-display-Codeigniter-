-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 09, 2017 at 08:59 PM
-- Server version: 5.7.17-0ubuntu0.16.04.2
-- PHP Version: 5.6.30-7+deb.sury.org~xenial+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prodata`
--

-- --------------------------------------------------------

--
-- Table structure for table `v2_ads_disapproval`
--

CREATE TABLE IF NOT EXISTS `v2_ads_disapproval` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `io` varchar(20) NOT NULL,
  `ad_id` bigint(20) NOT NULL,
  `status` enum('APPROVED','DISAPPROVED','PENDING','UNKNOWN') NOT NULL DEFAULT 'PENDING',
  `disapproval_reasons` text,
  `network` varchar(45) NOT NULL,
  `date_creative` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `campaign_io` (`io`),
  KEY `ad_id` (`ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
