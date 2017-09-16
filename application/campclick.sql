-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: clickcamps
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `agency`
--

DROP TABLE IF EXISTS `agency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agency` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campaigns`
--

DROP TABLE IF EXISTS `campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campaigns` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `agency_id` bigint(20) NOT NULL DEFAULT '0',
  `io_number` varchar(16) DEFAULT NULL,
  `date_test` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_deploy` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `subject` varchar(64) DEFAULT NULL,
  `from_name` varchar(64) DEFAULT NULL,
  `from_email` varchar(64) DEFAULT NULL,
  `seed_emails` text,
  `special_instructions` text,
  `body_html` mediumtext,
  `body_text` mediumtext,
  `user_id` bigint(20) NOT NULL DEFAULT '0',
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `list_id` bigint(20) NOT NULL DEFAULT '0',
  `source_url` varchar(255) DEFAULT NULL,
  `is_approved` enum('Y','N') NOT NULL DEFAULT 'N',
  `approved_by` bigint(20) NOT NULL DEFAULT '0',
  `status` enum('Y','N','S','P','U') NOT NULL DEFAULT 'U',
  `risk_level` enum('HIGH','MEDIUM','LOW','OPTIN') NOT NULL DEFAULT 'MEDIUM',
  `is_random_recipients` enum('Y','N') NOT NULL DEFAULT 'Y',
  `max_recipients` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `agency_id` (`agency_id`),
  KEY `user_id` (`user_id`),
  KEY `list_id` (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campclick_campaigns`
--

DROP TABLE IF EXISTS `campclick_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campclick_campaigns` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `io` varchar(16) NOT NULL,
  `message` text,
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'N',
  `conversion_tracking` enum('Y','N') NOT NULL DEFAULT 'N',
  `max_clicks` int(11) DEFAULT '9999999',
  `review_on` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `io` (`io`),
  KEY `is_active` (`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campclick_clicks`
--

DROP TABLE IF EXISTS `campclick_clicks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campclick_clicks` (
  `ip_address` varchar(16) DEFAULT NULL,
  `user_agent` varchar(64) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_mobile` enum('Y','N') NOT NULL DEFAULT 'N',
  `web_browser` varchar(64) DEFAULT NULL,
  `mobile_device` varchar(64) DEFAULT NULL,
  `platform` varchar(32) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `link_id` bigint(20) NOT NULL DEFAULT '0',
  `io` varchar(16) NOT NULL DEFAULT '0',
  `conversion_value` double(12,2) DEFAULT '0.00',
  `is_geo` enum('Y','N') NOT NULL DEFAULT 'N',
  `geo_country` varchar(2) DEFAULT '',
  `geo_region` varchar(16) DEFAULT '',
  `is_fraud` enum('Y','N') NOT NULL DEFAULT 'N',
  KEY `io` (`io`),
  KEY `link_id` (`link_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `campclick_links`
--

DROP TABLE IF EXISTS `campclick_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `campclick_links` (
  `dest_url` varchar(255) DEFAULT NULL,
  `io` varchar(16) NOT NULL,
  `counter` int(11) NOT NULL DEFAULT '0',
  `link_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `max_clicks` int(11) DEFAULT '9999999',
  `is_fulfilled` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`link_id`),
  KEY `io_cntr` (`io`,`counter`)
) ENGINE=InnoDB AUTO_INCREMENT=4580 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `custom_fields`
--

DROP TABLE IF EXISTS `custom_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `custom_fields` (
  `recipient_id` bigint(20) NOT NULL DEFAULT '0',
  `custom1` varchar(128) DEFAULT NULL,
  `custom2` varchar(128) DEFAULT NULL,
  `custom3` varchar(128) DEFAULT NULL,
  `custom4` varchar(128) DEFAULT NULL,
  `custom5` varchar(128) DEFAULT NULL,
  `custom6` varchar(128) DEFAULT NULL,
  `custom7` varchar(128) DEFAULT NULL,
  `custom8` varchar(128) DEFAULT NULL,
  `custom9` varchar(128) DEFAULT NULL,
  `custom10` varchar(128) DEFAULT NULL,
  `list_id` bigint(20) DEFAULT '0',
  KEY `list_recipient` (`list_id`,`recipient_id`),
  KEY `recipient_id` (`recipient_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eapi_attachments`
--

DROP TABLE IF EXISTS `eapi_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eapi_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `CampaignID` varchar(64) NOT NULL,
  `FileName` varchar(256) NOT NULL,
  `FileNOriginal` varchar(256) NOT NULL,
  `Attach_date` datetime NOT NULL,
  `is_downloaded` enum('Y','N') NOT NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniqueIndex` (`CampaignID`,`FileNOriginal`)
) ENGINE=MyISAM AUTO_INCREMENT=312 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eapi_auth`
--

DROP TABLE IF EXISTS `eapi_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eapi_auth` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `AuthUsername` varchar(64) NOT NULL,
  `AuthPassword` varchar(64) NOT NULL,
  `AuthKey` varchar(64) NOT NULL,
  `q_limit` int(11) NOT NULL,
  `q_counter` int(11) NOT NULL DEFAULT '0',
  `q_last_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reply_to_email` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eapi_campaigns`
--

DROP TABLE IF EXISTS `eapi_campaigns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eapi_campaigns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `GMRCampaignID` varchar(64) NOT NULL,
  `CampaignID` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `CampaignIDUniqueIndex` (`CampaignID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eapi_emails`
--

DROP TABLE IF EXISTS `eapi_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eapi_emails` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `CampaignID` varchar(64) NOT NULL,
  `UserDefinedID` varchar(64) NOT NULL,
  `Subject` varchar(128) NOT NULL,
  `MessageType` varchar(6) NOT NULL,
  `FromEmail` varchar(128) NOT NULL,
  `FromName` varchar(128) NOT NULL,
  `ToName` varchar(128) NOT NULL,
  `ToEmail` varchar(128) NOT NULL,
  `Body` mediumtext NOT NULL,
  `send_date` datetime NOT NULL,
  `complete_campaign` tinyint(1) NOT NULL,
  `HB` tinyint(1) NOT NULL,
  `SB` tinyint(1) NOT NULL,
  `EV` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UserDefinedIDUnique` (`UserDefinedID`)
) ENGINE=MyISAM AUTO_INCREMENT=6089 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `help_topics`
--

DROP TABLE IF EXISTS `help_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `help_topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `help_topic_content` text,
  `help_topic_heading` varchar(128) DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `list_recipient_map`
--

DROP TABLE IF EXISTS `list_recipient_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `list_recipient_map` (
  `list_id` bigint(20) NOT NULL DEFAULT '0',
  `recipient_id` bigint(20) NOT NULL DEFAULT '0',
  `is_sent` enum('Y','N') NOT NULL DEFAULT 'N',
  `smtp_server_id` bigint(20) NOT NULL DEFAULT '0',
  `campaign_id` bigint(20) NOT NULL DEFAULT '0',
  KEY `list_recipient` (`list_id`,`recipient_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `list_id_recipient_id` (`recipient_id`,`list_id`),
  KEY `smtp_server_id` (`smtp_server_id`),
  KEY `list_id` (`list_id`),
  KEY `campaign_id` (`campaign_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lists`
--

DROP TABLE IF EXISTS `lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lists` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `is_active` enum('Y','N') NOT NULL DEFAULT 'N',
  `source_url` varchar(255) NOT NULL,
  `is_processing` enum('Y','N','P','E') NOT NULL DEFAULT 'P' COMMENT 'Yes, No, Pending, Error',
  `is_source_list` enum('Y','N') NOT NULL DEFAULT 'Y',
  `list_type` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `list_type` (`list_type`),
  KEY `is_source_list` (`is_source_list`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `recipients`
--

DROP TABLE IF EXISTS `recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recipients` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email` varchar(128) DEFAULT NULL,
  `postalcode` varchar(16) DEFAULT NULL,
  `is_unsubscribe` enum('Y','N') NOT NULL DEFAULT 'N',
  `is_bounce` enum('H','S','N') NOT NULL DEFAULT 'N',
  `vertical` varchar(32) DEFAULT NULL,
  `source_list_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `postalcode` (`postalcode`),
  KEY `is_unsubscribe` (`is_unsubscribe`),
  KEY `is_bounce` (`is_bounce`),
  KEY `email` (`email`),
  KEY `source_list_id` (`source_list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3540018 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report` (
  `campaign_id` bigint(20) NOT NULL DEFAULT '0',
  `bounce_hard` bigint(20) NOT NULL DEFAULT '0',
  `bounce_soft` bigint(20) NOT NULL DEFAULT '0',
  `clicks` bigint(20) NOT NULL DEFAULT '0',
  `unsubscribes` bigint(20) NOT NULL DEFAULT '0',
  `opens` bigint(20) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `total_sent` bigint(20) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `report_audit`
--

DROP TABLE IF EXISTS `report_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_audit` (
  `campaign_id` bigint(20) NOT NULL DEFAULT '0',
  `recipient_id` bigint(20) NOT NULL DEFAULT '0',
  `bounce` enum('HB','SB','') NOT NULL DEFAULT '',
  `clicks` bigint(20) NOT NULL DEFAULT '0',
  `unsubscribe` enum('Y','N') NOT NULL DEFAULT 'N',
  `opens` bigint(20) NOT NULL DEFAULT '0',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_address` varchar(16) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `smtp_servers`
--

DROP TABLE IF EXISTS `smtp_servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `smtp_servers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `risk` enum('HIGH','MEDIUM','LOW','OPTIN') NOT NULL DEFAULT 'MEDIUM',
  `hostname` varchar(64) DEFAULT NULL,
  `maxdaily` int(11) NOT NULL DEFAULT '8000',
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `states` (
  `code` char(2) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `country` varchar(32) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transport`
--

DROP TABLE IF EXISTS `transport`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transport` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  `class` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(80) NOT NULL,
  `salt` varchar(40) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_groups`
--

DROP TABLE IF EXISTS `users_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`),
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `zipcodes`
--

DROP TABLE IF EXISTS `zipcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `zipcodes` (
  `zip` varchar(16) DEFAULT NULL,
  `lat` varchar(16) DEFAULT NULL,
  `lon` varchar(16) DEFAULT NULL,
  `city` varchar(32) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `county` varchar(32) DEFAULT NULL,
  KEY `zip` (`zip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-06-02 21:31:32
