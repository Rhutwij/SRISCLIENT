-- MySQL dump 10.13  Distrib 5.1.73, for apple-darwin10.3.0 (i386)
--
-- Host: localhost    Database: SRIS_SYSTEM
-- ------------------------------------------------------
-- Server version	5.1.73

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
-- Table structure for table `Colleges`
--

DROP TABLE IF EXISTS `Colleges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Colleges` (
  `CollegeId` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Type` varchar(45) NOT NULL,
  PRIMARY KEY (`CollegeId`),
  UNIQUE KEY `Name_UNIQUE` (`Name`),
  UNIQUE KEY `Type_UNIQUE` (`Type`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Roles`
--

DROP TABLE IF EXISTS `Roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Roles` (
  `RoleId` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  PRIMARY KEY (`RoleId`),
  UNIQUE KEY `RoleName_UNIQUE` (`Name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Subjects`
--

DROP TABLE IF EXISTS `Subjects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Subjects` (
  `SubjectId` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `CollegeId` int(11) NOT NULL,
  PRIMARY KEY (`SubjectId`),
  UNIQUE KEY `Name_UNIQUE` (`Name`),
  KEY `CollegeId` (`CollegeId`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Subjects_Taken`
--

DROP TABLE IF EXISTS `Subjects_Taken`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Subjects_Taken` (
  `UserId` int(11) NOT NULL,
  `SubjectId` int(11) NOT NULL,
  KEY `UserId` (`UserId`),
  KEY `SubjectId` (`SubjectId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `DocumentId` int(11) DEFAULT NULL,
  `Comment` varchar(300) NOT NULL,
  `Date` timestamp NULL DEFAULT NULL,
  `UserId` int(11) NOT NULL,
  `Hide` tinyint(1) NOT NULL DEFAULT '0',
  `commentId` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`commentId`),
  KEY `DocumentId` (`DocumentId`),
  KEY `UserId` (`UserId`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `documents` (
  `DocumentId` int(11) NOT NULL AUTO_INCREMENT,
  `BucketId` int(11) NOT NULL,
  `Size` int(11) NOT NULL,
  `Date` timestamp NULL DEFAULT NULL,
  `UserId` int(11) NOT NULL,
  `Title` varchar(100) NOT NULL,
  `Rating` int(11) NOT NULL DEFAULT '0',
  `Hide` tinyint(1) NOT NULL DEFAULT '0',
  `Desc` varchar(300) NOT NULL,
  `keyname` varchar(550) NOT NULL,
  PRIMARY KEY (`DocumentId`)
) ENGINE=MyISAM AUTO_INCREMENT=112 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `index_table`
--

DROP TABLE IF EXISTS `index_table`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `index_table` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(45) DEFAULT NULL,
  `Rating` int(11) NOT NULL DEFAULT '0',
  `Description` varchar(150) DEFAULT NULL,
  `Date` timestamp NULL DEFAULT NULL,
  `Url` varchar(400) NOT NULL,
  `UserId` int(11) NOT NULL,
  `rawtext` varchar(150) DEFAULT NULL,
  `Subject` varchar(45) DEFAULT NULL,
  `CollegeId` int(11) DEFAULT NULL,
  `BucketId` int(11) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reported_comments`
--

DROP TABLE IF EXISTS `reported_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reported_comments` (
  `commentId` int(11) NOT NULL DEFAULT '0',
  `reporter` int(11) NOT NULL DEFAULT '0',
  `reason` varchar(60) DEFAULT NULL,
  UNIQUE KEY `commentId_2` (`commentId`,`reporter`),
  KEY `reporter` (`reporter`),
  KEY `commentId` (`commentId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reported_documents`
--

DROP TABLE IF EXISTS `reported_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reported_documents` (
  `documentId` int(11) NOT NULL DEFAULT '0',
  `reporter` int(11) NOT NULL DEFAULT '0',
  `reason` varchar(60) DEFAULT NULL,
  UNIQUE KEY `documentId_2` (`documentId`,`reporter`),
  KEY `documentId` (`documentId`),
  KEY `reporter` (`reporter`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `s3_buckets`
--

DROP TABLE IF EXISTS `s3_buckets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `s3_buckets` (
  `ThreadId` int(11) NOT NULL,
  `BucketId` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(45) NOT NULL,
  `Date` timestamp NULL DEFAULT NULL,
  `Block` tinyint(1) NOT NULL DEFAULT '0',
  `Owner` varchar(45) NOT NULL,
  PRIMARY KEY (`BucketId`),
  UNIQUE KEY `Name_UNIQUE` (`Name`),
  KEY `ThreadId` (`ThreadId`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `threads`
--

DROP TABLE IF EXISTS `threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `threads` (
  `ThreadId` int(11) NOT NULL AUTO_INCREMENT,
  `SubjectId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `CreationDate` timestamp NULL DEFAULT NULL,
  `TotalDocs` int(11) DEFAULT '0',
  `Name` varchar(45) NOT NULL DEFAULT 'NEW THREAD',
  PRIMARY KEY (`ThreadId`),
  KEY `SubjectId` (`SubjectId`),
  KEY `UserId` (`UserId`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `UserId` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(45) NOT NULL,
  `Password` varchar(45) NOT NULL,
  `RoleId` int(11) NOT NULL,
  `RegDate` timestamp NULL DEFAULT NULL,
  `LastLogin` timestamp NULL DEFAULT NULL,
  `Ban` tinyint(1) NOT NULL DEFAULT '0',
  `CollegeId` int(11) NOT NULL,
  `Rating` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`UserId`,`Username`),
  UNIQUE KEY `Username_UNIQUE` (`Username`),
  UNIQUE KEY `Password_UNIQUE` (`Password`),
  KEY `CollegeId` (`CollegeId`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-02-04  7:14:41
