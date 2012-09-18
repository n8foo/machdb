-- MySQL dump 10.10
--
-- Host: localhost    Database: machdb
-- ------------------------------------------------------
-- Server version	5.0.27

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
-- Table structure for table `archive_filesystem`
--

DROP TABLE IF EXISTS `archive_filesystem`;
CREATE TABLE `archive_filesystem` (
  `id` int(11) default NULL,
  `host_id` int(11) default NULL,
  `device` varchar(50) default NULL,
  `mountpoint` varchar(50) default NULL,
  `size` bigint(20) unsigned NOT NULL,
  `type` varchar(10) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `archive_host`
--

DROP TABLE IF EXISTS `archive_host`;
CREATE TABLE `archive_host` (
  `id` int(11) NOT NULL,
  `hostname` varchar(30) NOT NULL default '',
  `domain` varchar(30) NOT NULL,
  `hwid` varchar(100) NOT NULL default '',
  `os_id` int(11) NOT NULL,
  `cpu_id` int(11) NOT NULL,
  `cpu_count` int(3) NOT NULL,
  `swaptotal` int(11) NOT NULL,
  `memtotal` int(11) default NULL,
  `location_id` int(11) default NULL,
  `status` varchar(10) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

--
-- Table structure for table `archive_host_bios`
--

DROP TABLE IF EXISTS `archive_host_bios`;
CREATE TABLE `archive_host_bios` (
  `id` int(11) NOT NULL,
  `host_id` int(11) NOT NULL,
  `bios_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `archive_host_chassis`
--

DROP TABLE IF EXISTS `archive_host_chassis`;
CREATE TABLE `archive_host_chassis` (
  `id` int(11) NOT NULL,
  `host_id` int(11) NOT NULL,
  `chassis_id` int(11) NOT NULL,
  `serial` varchar(50) default NULL,
  `asset_tag` varchar(50) default NULL,
  `timestamp` timestamp NOT NULL default '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `archive_host_disk`
--

DROP TABLE IF EXISTS `archive_host_disk`;
CREATE TABLE `archive_host_disk` (
  `id` int(11) default NULL,
  `host_id` int(11) default NULL,
  `disk_id` int(11) default NULL,
  `device` varchar(20) default NULL,
  `timestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  `status` int(1) default NULL
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Table structure for table `archive_host_mb`
--

DROP TABLE IF EXISTS `archive_host_mb`;
CREATE TABLE `archive_host_mb` (
  `id` int(11) NOT NULL,
  `host_id` int(11) NOT NULL,
  `mb_id` int(11) NOT NULL,
  `serial` varchar(100) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `archive_host_pkg`
--

DROP TABLE IF EXISTS `archive_host_pkg`;
CREATE TABLE `archive_host_pkg` (
  `id` int(11) NOT NULL,
  `host_id` int(11) NOT NULL,
  `pkg_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `archive_host_system`
--

DROP TABLE IF EXISTS `archive_host_system`;
CREATE TABLE `archive_host_system` (
  `id` int(11) NOT NULL,
  `host_id` int(11) NOT NULL,
  `system_id` int(11) NOT NULL,
  `serial` varchar(100) default NULL,
  `uuid` varchar(100) default NULL,
  `timestamp` timestamp NOT NULL default '0000-00-00 00:00:00'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `archive_nic`
--

DROP TABLE IF EXISTS `archive_nic`;
CREATE TABLE `archive_nic` (
  `id` int(11) default NULL,
  `host_id` int(11) default NULL,
  `macaddr` varchar(18) default NULL,
  `interface` varchar(10) default NULL,
  `netmask` varchar(16) default NULL,
  `ipaddr` varchar(16) default NULL,
  `broadcast` varchar(16) default NULL,
  `timestamp` timestamp NOT NULL default '0000-00-00 00:00:00',
  `status` int(1) NOT NULL default '1'
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;

--
-- Table structure for table `bios`
--

DROP TABLE IF EXISTS `bios`;
CREATE TABLE `bios` (
  `id` int(11) NOT NULL auto_increment,
  `vendor` varchar(30) default NULL,
  `version` varchar(30) default NULL,
  `date` varchar(30) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Table structure for table `chassis`
--

DROP TABLE IF EXISTS `chassis`;
CREATE TABLE `chassis` (
  `id` int(11) NOT NULL auto_increment,
  `vendor` varchar(30) default NULL,
  `type` varchar(30) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Table structure for table `cpu`
--

DROP TABLE IF EXISTS `cpu`;
CREATE TABLE `cpu` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `shortname` varchar(32) default NULL,
  `speed` int(6) NOT NULL,
  `cache` int(6) default NULL,
  `unified_cache` int(1) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

--
-- Table structure for table `disk`
--

DROP TABLE IF EXISTS `disk`;
CREATE TABLE `disk` (
  `id` int(11) NOT NULL auto_increment,
  `model` varchar(100) NOT NULL default '',
  `size` bigint(20) unsigned NOT NULL,
  `type` varchar(30) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;

--
-- Table structure for table `filesystem`
--

DROP TABLE IF EXISTS `filesystem`;
CREATE TABLE `filesystem` (
  `id` int(11) NOT NULL auto_increment,
  `host_id` int(11) default NULL,
  `device` varchar(50) default NULL,
  `mountpoint` varchar(50) default NULL,
  `size` bigint(20) unsigned NOT NULL,
  `type` varchar(10) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=77 DEFAULT CHARSET=latin1;

--
-- Table structure for table `host`
--

DROP TABLE IF EXISTS `host`;
CREATE TABLE `host` (
  `id` int(11) NOT NULL auto_increment,
  `hostname` varchar(30) NOT NULL default '',
  `domain` varchar(30) NOT NULL,
  `hwid` varchar(100) NOT NULL default '',
  `os_id` int(11) NOT NULL,
  `cpu_id` int(11) NOT NULL,
  `cpu_count` int(3) NOT NULL,
  `swaptotal` int(11) NOT NULL,
  `memtotal` int(11) default NULL,
  `location_id` int(11) default NULL,
  `status` varchar(10) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  FULLTEXT KEY `hostname` (`hostname`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;

--
-- Table structure for table `host_bios`
--

DROP TABLE IF EXISTS `host_bios`;
CREATE TABLE `host_bios` (
  `id` int(11) NOT NULL auto_increment,
  `host_id` int(11) NOT NULL,
  `bios_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Table structure for table `host_chassis`
--

DROP TABLE IF EXISTS `host_chassis`;
CREATE TABLE `host_chassis` (
  `id` int(11) NOT NULL auto_increment,
  `host_id` int(11) NOT NULL,
  `chassis_id` int(11) NOT NULL,
  `serial` varchar(50) default NULL,
  `asset_tag` varchar(50) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Table structure for table `host_disk`
--

DROP TABLE IF EXISTS `host_disk`;
CREATE TABLE `host_disk` (
  `id` int(11) NOT NULL auto_increment,
  `host_id` int(11) default NULL,
  `disk_id` int(11) default NULL,
  `device` varchar(20) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `host_id` (`host_id`),
  KEY `disk_id` (`disk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=104 DEFAULT CHARSET=latin1;

--
-- Table structure for table `host_mb`
--

DROP TABLE IF EXISTS `host_mb`;
CREATE TABLE `host_mb` (
  `id` int(11) NOT NULL auto_increment,
  `host_id` int(11) NOT NULL,
  `mb_id` int(11) NOT NULL,
  `serial` varchar(100) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Table structure for table `host_pkg`
--

DROP TABLE IF EXISTS `host_pkg`;
CREATE TABLE `host_pkg` (
  `id` int(11) NOT NULL auto_increment,
  `host_id` int(11) NOT NULL,
  `pkg_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `host_id` (`host_id`),
  KEY `pkg_id` (`pkg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7951 DEFAULT CHARSET=latin1;

--
-- Table structure for table `host_system`
--

DROP TABLE IF EXISTS `host_system`;
CREATE TABLE `host_system` (
  `id` int(11) NOT NULL auto_increment,
  `host_id` int(11) NOT NULL,
  `system_id` int(11) NOT NULL,
  `serial` varchar(100) default NULL,
  `uuid` varchar(100) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(11) NOT NULL auto_increment,
  `datacenter` varchar(30) default NULL,
  `rack` int(11) default NULL,
  `area` int(11) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Table structure for table `mb`
--

DROP TABLE IF EXISTS `mb`;
CREATE TABLE `mb` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `vendor` varchar(30) default NULL,
  `version` varchar(30) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Table structure for table `nic`
--

DROP TABLE IF EXISTS `nic`;
CREATE TABLE `nic` (
  `id` int(11) NOT NULL auto_increment,
  `host_id` int(11) default NULL,
  `macaddr` varchar(18) NOT NULL,
  `interface` varchar(10) NOT NULL,
  `netmask` varchar(16) NOT NULL default '',
  `ipaddr` varchar(16) default NULL,
  `broadcast` varchar(16) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `status` int(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `host_id` (`host_id`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=latin1;

--
-- Table structure for table `os`
--

DROP TABLE IF EXISTS `os`;
CREATE TABLE `os` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `kernel` varchar(30) NOT NULL,
  `arch` varchar(30) NOT NULL,
  `basearch` varchar(30) NOT NULL default '',
  `vendor` varchar(30) default NULL,
  `release_date` date default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Table structure for table `pkg`
--

DROP TABLE IF EXISTS `pkg`;
CREATE TABLE `pkg` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `arch` varchar(30) NOT NULL,
  `version` varchar(30) NOT NULL,
  `release` varchar(30) default '',
  `description` varchar(255) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `arch` (`arch`),
  KEY `version` (`version`),
  KEY `release` (`release`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=4050 DEFAULT CHARSET=latin1;

--
-- Table structure for table `system`
--

DROP TABLE IF EXISTS `system`;
CREATE TABLE `system` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `vendor` varchar(100) default NULL,
  `version` varchar(30) default NULL,
  `description` varchar(255) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
  `host_id` int(11) NOT NULL,
  `type` varchar(30) NOT NULL default '',
  `tag` varchar(30) NOT NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(11) default NULL,
  `comment` varchar(255) default NULL,
  `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2007-12-19 12:52:37
