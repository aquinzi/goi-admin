-- MySQL dump 10.19  Distrib 10.3.29-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: goi_admin
-- ------------------------------------------------------
-- Server version	10.3.29-MariaDB-0ubuntu0.20.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `v_WordHighestPriority`
--

DROP TABLE IF EXISTS `v_WordHighestPriority`;
/*!50001 DROP VIEW IF EXISTS `v_WordHighestPriority`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_WordHighestPriority` (
  `word_id` tinyint NOT NULL,
  `highest_priority` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_WordInfo`
--

DROP TABLE IF EXISTS `v_WordInfo`;
/*!50001 DROP VIEW IF EXISTS `v_WordInfo`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_WordInfo` (
  `wid` tinyint NOT NULL,
  `word` tinyint NOT NULL,
  `reading` tinyint NOT NULL,
  `definition` tinyint NOT NULL,
  `in_anki` tinyint NOT NULL,
  `deleted` tinyint NOT NULL,
  `priority_history` tinyint NOT NULL,
  `word_count` tinyint NOT NULL,
  `word_tags` tinyint NOT NULL,
  `highest_priority` tinyint NOT NULL,
  `log_tags_all` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `v_WordLatestPriority`
--

DROP TABLE IF EXISTS `v_WordLatestPriority`;
/*!50001 DROP VIEW IF EXISTS `v_WordLatestPriority`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `v_WordLatestPriority` (
  `word_id` tinyint NOT NULL,
  `priority` tinyint NOT NULL,
  `created` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `word_history`
--

DROP TABLE IF EXISTS `word_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `word_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `priority` int(11) NOT NULL,
  `source` varchar(100) DEFAULT NULL,
  `examples` text DEFAULT NULL,
  `source_tag` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `word_history_FK` (`word_id`),
  CONSTRAINT `word_history_FK` FOREIGN KEY (`word_id`) REFERENCES `words` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6158 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `words`
--

DROP TABLE IF EXISTS `words`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `words` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(100) NOT NULL,
  `reading` varchar(100) NOT NULL,
  `definition` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `anki_noteid` bigint(20) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `words_unique` (`word`,`reading`)
) ENGINE=InnoDB AUTO_INCREMENT=3905 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `words_tags`
--

DROP TABLE IF EXISTS `words_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `words_tags` (
  `word_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  UNIQUE KEY `words_tags_UN` (`word_id`,`tag_id`),
  KEY `words_tags_FK` (`tag_id`),
  CONSTRAINT `words_tags_FK` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`),
  CONSTRAINT `words_tags_FK_1` FOREIGN KEY (`word_id`) REFERENCES `words` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'goi_admin'
--

--
-- Final view structure for view `v_WordHighestPriority`
--

/*!50001 DROP TABLE IF EXISTS `v_WordHighestPriority`*/;
/*!50001 DROP VIEW IF EXISTS `v_WordHighestPriority`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_WordHighestPriority` AS select `word_history`.`word_id` AS `word_id`,min(`word_history`.`priority`) AS `highest_priority` from `word_history` group by `word_history`.`word_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_WordInfo`
--

/*!50001 DROP TABLE IF EXISTS `v_WordInfo`*/;
/*!50001 DROP VIEW IF EXISTS `v_WordInfo`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_WordInfo` AS select `tbl_words`.`id` AS `wid`,`tbl_words`.`word` AS `word`,`tbl_words`.`reading` AS `reading`,`tbl_words`.`definition` AS `definition`,`tbl_words`.`anki_noteid` AS `in_anki`,`tbl_words`.`deleted` AS `deleted`,`wh`.`priority_history` AS `priority_history`,`tbl_word_count`.`log_word_count` AS `word_count`,`tbl_wordtags`.`word_tags` AS `word_tags`,`tbl_highest_priority`.`highest_priority` AS `highest_priority`,`tbl_log_tag`.`log_tags_all` AS `log_tags_all` from (((((`goi_admin`.`words` `tbl_words` left join (select `goi_admin`.`word_history`.`word_id` AS `word_id`,group_concat(`goi_admin`.`word_history`.`priority` separator ',') AS `priority_history` from `goi_admin`.`word_history` group by `goi_admin`.`word_history`.`word_id`) `wh` on(`wh`.`word_id` = `tbl_words`.`id`)) left join (select `goi_admin`.`word_history`.`word_id` AS `log_word_id`,count(`goi_admin`.`word_history`.`word_id`) AS `log_word_count` from `goi_admin`.`word_history` group by `goi_admin`.`word_history`.`word_id`) `tbl_word_count` on(`tbl_word_count`.`log_word_id` = `tbl_words`.`id`)) left join (select `wh`.`word_id` AS `word_id`,group_concat(distinct `wt`.`name` separator ', ') AS `word_tags` from (`goi_admin`.`word_history` `wh` left join `goi_admin`.`tags` `wt` on(`wt`.`id` = `wh`.`source_tag`)) group by `wh`.`word_id`) `tbl_wordtags` on(`tbl_wordtags`.`word_id` = `tbl_words`.`id`)) left join (select `goi_admin`.`word_history`.`word_id` AS `log_word_id2`,group_concat(`goi_admin`.`word_history`.`source_tag` separator ',') AS `log_tags_all` from `goi_admin`.`word_history` group by `goi_admin`.`word_history`.`word_id`) `tbl_log_tag` on(`tbl_log_tag`.`log_word_id2` = `tbl_words`.`id`)) join `goi_admin`.`v_WordHighestPriority` `tbl_highest_priority` on(`tbl_highest_priority`.`word_id` = `tbl_words`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_WordLatestPriority`
--

/*!50001 DROP TABLE IF EXISTS `v_WordLatestPriority`*/;
/*!50001 DROP VIEW IF EXISTS `v_WordLatestPriority`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_WordLatestPriority` AS select `b1`.`word_id` AS `word_id`,`b1`.`priority` AS `priority`,`b1`.`created` AS `created` from (`goi_admin`.`word_history` `b1` join (select max(`goi_admin`.`word_history`.`created`) AS `created`,`goi_admin`.`word_history`.`word_id` AS `tmp_wid` from `goi_admin`.`word_history` group by `goi_admin`.`word_history`.`word_id`) `b2` on(`b1`.`created` = `b2`.`created` and `b1`.`word_id` = `b2`.`tmp_wid`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-05-27 11:07:45
