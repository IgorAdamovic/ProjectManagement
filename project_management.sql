# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: (MySQL 5.5.5-10.1.47-MariaDB-0ubuntu0.18.04.1)
# Database: recognite_project_manager
# Generation Time: 2021-02-05 12:33:11 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table client_list
# ------------------------------------------------------------

DROP TABLE IF EXISTS `client_list`;

CREATE TABLE `client_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client` varchar(100) NOT NULL,
  `contact_details` varchar(100) NOT NULL,
  `invoice_contact` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table hardware_list
# ------------------------------------------------------------

DROP TABLE IF EXISTS `hardware_list`;

CREATE TABLE `hardware_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `development_name` varchar(100) NOT NULL,
  `client` varchar(100) NOT NULL,
  `hardware_installed` varchar(1000) NOT NULL DEFAULT '',
  `development_address` varchar(100) NOT NULL,
  `contact` varchar(200) NOT NULL,
  `date_installed` date DEFAULT '1000-01-01',
  `extra_info` varchar(1000) DEFAULT NULL,
  `last_change` varchar(50) DEFAULT NULL,
  `comments` varchar(1000) DEFAULT NULL,
  `time_stamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table project_list
# ------------------------------------------------------------

DROP TABLE IF EXISTS `project_list`;

CREATE TABLE `project_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `title` varchar(250) NOT NULL DEFAULT '',
  `client` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL DEFAULT '',
  `start_date` date DEFAULT NULL,
  `deadline` date DEFAULT '1000-01-01',
  `contact` varchar(200) DEFAULT NULL,
  `project_handler` varchar(50) NOT NULL,
  `quoted` varchar(50) NOT NULL,
  `estimated_completion` date DEFAULT '1000-01-01',
  `status` varchar(50) NOT NULL,
  `last_change` varchar(50) DEFAULT NULL,
  `comments` varchar(1000) DEFAULT NULL,
  `time_stamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table updates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `updates`;

CREATE TABLE `updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `development` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL DEFAULT '',
  `date_entered` date DEFAULT NULL,
  `level_of_urgency` varchar(50) NOT NULL,
  `estimated_completion` date DEFAULT '1000-01-01',
  `status` varchar(50) NOT NULL,
  `last_change` varchar(50) DEFAULT NULL,
  `comments` varchar(1000) DEFAULT NULL,
  `time_stamp` timestamp NULL DEFAULT NULL,
  `solutions` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `idUsers` int(11) NOT NULL AUTO_INCREMENT,
  `uidUsers` tinytext NOT NULL,
  `emailUsers` tinytext NOT NULL,
  `pwdUsers` longtext NOT NULL,
  `role` varchar(10) NOT NULL,
  `projects_state` text,
  `updates_state` text,
  `hardware_state` text,
  `clients_state` text,
  PRIMARY KEY (`idUsers`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`idUsers`, `uidUsers`, `emailUsers`, `pwdUsers`, `role`, `projects_state`, `updates_state`, `hardware_state`, `clients_state`)
VALUES
	(1,'Igor','igor@recognite.co.uk','$2y$10$pHJprIGUU5dT4H2Aq5hQa.UV6cG8UUxSVR5MLxiOpVxSVj9yBQxAO','Admin',NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
