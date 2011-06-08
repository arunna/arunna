-- phpMyAdmin SQL Dump
-- version 3.3.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 19, 2011 at 06:27 PM
-- Server version: 5.1.41
-- PHP Version: 5.3.2-1ubuntu4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `arunna_alpha_spod`
--

-- --------------------------------------------------------

--
-- Table structure for table `lumonata_additional_fields`
--

CREATE TABLE IF NOT EXISTS `lumonata_additional_fields` (
  `lapp_id` bigint(20) NOT NULL,
  `lkey` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lvalue` text CHARACTER SET utf8 NOT NULL,
  `lapp_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`lapp_id`,`lkey`),
  KEY `key` (`lkey`),
  KEY `app_name` (`lapp_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `lumonata_additional_fields`
--

INSERT INTO `lumonata_additional_fields` (`lapp_id`, `lkey`, `lvalue`, `lapp_name`) VALUES
(2, 'meta_title', '', 'pages'),
(2, 'meta_description', '', 'pages'),
(2, 'meta_keywords', '', 'pages'),
(3, 'meta_title', '', 'articles'),
(3, 'meta_description', '', 'articles'),
(3, 'meta_keywords', '', 'articles'),
(1, 'first_name', 'Raden', 'user'),
(1, 'last_name', 'Yudistira', 'user'),
(1, 'website', 'http://', 'user'),
(1, 'bio', '', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `lumonata_articles`
--

CREATE TABLE IF NOT EXISTS `lumonata_articles` (
  `larticle_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `larticle_title` text CHARACTER SET utf8 NOT NULL,
  `larticle_brief` text CHARACTER SET utf8 NOT NULL,
  `larticle_content` longtext CHARACTER SET utf8 NOT NULL,
  `larticle_status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `larticle_type` varchar(20) CHARACTER SET utf8 NOT NULL,
  `lcomment_status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `lcomment_count` bigint(20) NOT NULL,
  `lcount_like` bigint(20) NOT NULL,
  `lsef` text CHARACTER SET utf8 NOT NULL,
  `lorder` bigint(20) NOT NULL DEFAULT '1',
  `lpost_by` bigint(20) NOT NULL,
  `lpost_date` datetime NOT NULL,
  `lupdated_by` bigint(20) NOT NULL,
  `ldlu` datetime NOT NULL,
  `lshare_to` bigint(20) NOT NULL,
  PRIMARY KEY (`larticle_id`),
  KEY `article_title` (`larticle_title`(255)),
  KEY `type_status_date_by` (`larticle_type`,`larticle_status`,`lpost_date`,`lpost_by`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `lumonata_articles`
--

INSERT INTO `lumonata_articles` (`larticle_id`, `larticle_title`, `larticle_brief`, `larticle_content`, `larticle_status`, `larticle_type`, `lcomment_status`, `lcomment_count`, `lcount_like`, `lsef`, `lorder`, `lpost_by`, `lpost_date`, `lupdated_by`, `ldlu`, `lshare_to`) VALUES
(1, 'My First Status', '', '', 'publish', 'status', 'allowed', 0, 0, 'my-first-status', 3, 1, '2011-03-18 21:46:22', 1, '2011-03-18 21:46:22', 0),
(2, 'ABOUT ARUNNA', '', '<p>Arunna is a solution that creates a place where social media, networking  and community collaborate. You and your customers can have the option  to connect and share content online. Our platform gives the opportunity  for both hosted community and white label self hosted sites to take the  advantage to express themselves. With arunna, small organization to  large enterprises can build brand awareness, engage their community and  much more.</p>\r\n<p>That''s what we call connecting the clouds...</p>', 'publish', 'pages', 'allowed', 0, 0, 'about-arunna', 2, 1, '2011-03-18 21:50:33', 1, '2011-03-18 21:50:33', 0),
(3, 'Welcome to Arunna', '', '<p>Hello Friends,</p>\r\n<p>Let''s share to make a better world</p>', 'publish', 'articles', 'allowed', 1, 0, 'welcome-to-arunna', 1, 1, '2011-03-18 21:51:45', 1, '2011-03-18 21:51:45', 0);

-- --------------------------------------------------------

--
-- Table structure for table `lumonata_attachment`
--

CREATE TABLE IF NOT EXISTS `lumonata_attachment` (
  `lattach_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `larticle_id` bigint(20) NOT NULL,
  `lattach_loc` text CHARACTER SET utf8 NOT NULL,
  `lattach_loc_thumb` text CHARACTER SET utf8 NOT NULL,
  `lattach_loc_medium` text CHARACTER SET utf8 NOT NULL,
  `lattach_loc_large` text CHARACTER SET utf8 NOT NULL,
  `ltitle` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lalt_text` text CHARACTER SET utf8 NOT NULL,
  `lcaption` varchar(200) CHARACTER SET utf8 NOT NULL,
  `mime_type` varchar(50) CHARACTER SET utf8 NOT NULL,
  `lorder` bigint(20) NOT NULL DEFAULT '0',
  `upload_date` datetime NOT NULL,
  `date_last_update` datetime NOT NULL,
  PRIMARY KEY (`lattach_id`),
  KEY `article_id` (`larticle_id`),
  KEY `attachment_title` (`ltitle`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lumonata_attachment`
--


-- --------------------------------------------------------

--
-- Table structure for table `lumonata_comments`
--

CREATE TABLE IF NOT EXISTS `lumonata_comments` (
  `lcomment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lcomment_parent` bigint(20) NOT NULL,
  `larticle_id` bigint(20) NOT NULL,
  `lcomentator_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lcomentator_email` varchar(100) CHARACTER SET utf8 NOT NULL,
  `lcomentator_url` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lcomentator_ip` varchar(100) CHARACTER SET utf8 NOT NULL,
  `lcomment_date` datetime NOT NULL,
  `lcomment` text CHARACTER SET utf8 NOT NULL,
  `lcomment_status` varchar(20) CHARACTER SET utf8 NOT NULL,
  `lcomment_like` bigint(20) NOT NULL,
  `luser_id` bigint(20) NOT NULL,
  `lcomment_type` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT 'like,comment,like_comment',
  PRIMARY KEY (`lcomment_id`),
  KEY `lcomment_status` (`lcomment_status`),
  KEY `lcomment_userid` (`luser_id`),
  KEY `lcomment_type` (`lcomment_type`),
  KEY `larticle_id` (`larticle_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `lumonata_comments`
--

INSERT INTO `lumonata_comments` (`lcomment_id`, `lcomment_parent`, `larticle_id`, `lcomentator_name`, `lcomentator_email`, `lcomentator_url`, `lcomentator_ip`, `lcomment_date`, `lcomment`, `lcomment_status`, `lcomment_like`, `luser_id`, `lcomment_type`) VALUES
(1, 0, 3, 'Wahya Biantara', 'request@arunna.com', 'http://localhost/arunna-repo/?user=admin', '127.0.0.1', '2011-03-18 21:52:25', 'Hello comment....', 'approved', 0, 1, 'comment');

-- --------------------------------------------------------

--
-- Table structure for table `lumonata_friendship`
--

CREATE TABLE IF NOT EXISTS `lumonata_friendship` (
  `lfriendship_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `luser_id` bigint(20) NOT NULL,
  `lfriend_id` bigint(20) NOT NULL,
  `lstatus` varchar(20) NOT NULL COMMENT 'connected, onrequest, pending, unfollow',
  PRIMARY KEY (`lfriendship_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `lumonata_friendship`
--


-- --------------------------------------------------------

--
-- Table structure for table `lumonata_friends_list`
--

CREATE TABLE IF NOT EXISTS `lumonata_friends_list` (
  `lfriends_list_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `luser_id` bigint(20) NOT NULL,
  `llist_name` varchar(300) CHARACTER SET utf8 NOT NULL,
  `lorder` bigint(20) NOT NULL,
  PRIMARY KEY (`lfriends_list_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `lumonata_friends_list`
--

INSERT INTO `lumonata_friends_list` (`lfriends_list_id`, `luser_id`, `llist_name`, `lorder`) VALUES
(1, 1, 'Work', 1),
(2, 1, 'School', 2),
(3, 1, 'Familiy', 3);

-- --------------------------------------------------------

--
-- Table structure for table `lumonata_friends_list_rel`
--

CREATE TABLE IF NOT EXISTS `lumonata_friends_list_rel` (
  `lfriendship_id` bigint(20) NOT NULL,
  `lfriends_list_id` bigint(20) NOT NULL,
  PRIMARY KEY (`lfriendship_id`,`lfriends_list_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lumonata_friends_list_rel`
--


-- --------------------------------------------------------

--
-- Table structure for table `lumonata_meta_data`
--

CREATE TABLE IF NOT EXISTS `lumonata_meta_data` (
  `lmeta_id` int(11) NOT NULL AUTO_INCREMENT,
  `lmeta_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lmeta_value` longtext CHARACTER SET utf8 NOT NULL,
  `lapp_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lapp_id` int(11) NOT NULL,
  PRIMARY KEY (`lmeta_id`),
  KEY `meta_name` (`lmeta_name`),
  KEY `app_name` (`lapp_name`),
  KEY `app_id` (`lapp_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=54 ;

--
-- Dumping data for table `lumonata_meta_data`
--

INSERT INTO `lumonata_meta_data` (`lmeta_id`, `lmeta_name`, `lmeta_value`, `lapp_name`, `lapp_id`) VALUES
(1, 'front_theme', 'yudistira', 'themes', 0),
(2, 'time_zone', 'Asia/Singapore', 'global_setting', 0),
(3, 'site_url', 'localhost/arunna', 'global_setting', 0),
(4, 'web_title', 'Arunna - Alpha        ', 'global_setting', 0),
(5, 'smtp_server', '10.10.10.43', 'global_setting', 0),
(6, 'admin_theme', 'default', 'themes', 0),
(7, 'smtp', 'mail.email.com', 'global_setting', 0),
(8, 'email', 'you@email.com', 'global_setting', 0),
(9, 'web_tagline', 'Connecting the clouds', 'global_setting', 0),
(10, 'invitation_limit', '10', 'global_setting', 0),
(11, 'date_format', 'F j, Y', 'global_setting', 0),
(12, 'time_format', 'H:i', 'global_setting', 0),
(13, 'post_viewed', '10', 'global_setting', 0),
(14, 'rss_viewed', '15', 'global_setting', 0),
(15, 'rss_view_format', 'full_text', 'global_setting', 0),
(16, 'list_viewed', '30', 'global_setting', 0),
(17, 'email_format', 'plain_text', 'global_setting', 0),
(18, 'text_editor', 'tiny_mce', 'global_setting', 0),
(19, 'thumbnail_image_size', '150:150', 'global_setting', 0),
(20, 'large_image_size', '1024:1024', 'global_setting', 0),
(21, 'medium_image_size', '300:300', 'global_setting', 0),
(22, 'is_allow_comment', '1', 'global_setting', 0),
(23, 'is_login_to_comment', '1', 'global_setting', 0),
(24, 'is_auto_close_comment', '0', 'global_setting', 0),
(25, 'days_auto_close_comment', '15', 'global_setting', 0),
(26, 'is_break_comment', '1', 'global_setting', 0),
(27, 'comment_page_displayed', 'last', 'global_setting', 0),
(28, 'comment_per_page', '3', 'global_setting', 0),
(29, 'active_plugins', '{"lumonata-meta-data":"\\/metadata\\/metadata.php"}', 'plugins', 0),
(30, 'save_changes', 'Save Changes', 'global_setting', 0),
(31, 'is_rewrite', 'no', 'global_setting', 0),
(32, 'is_allow_post_like', '1', 'global_setting', 0),
(33, 'is_allow_comment_like', '1', 'global_setting', 0),
(34, 'alert_on_register', '1', 'global_setting', 0),
(35, 'alert_on_comment', '1', 'global_setting', 0),
(36, 'alert_on_comment_reply', '1', 'global_setting', 0),
(37, 'alert_on_liked_post', '1', 'global_setting', 0),
(38, 'alert_on_liked_comment', '1', 'global_setting', 0),
(39, 'web_name', 'Arunna           ', 'global_setting', 0),
(40, 'meta_description', '', 'global_setting', 0),
(41, 'meta_keywords', '', 'global_setting', 0),
(42, 'meta_title', 'Arunna - Alpha', 'global_setting', 0),
(43, 'custome_bg_color', 'c4c4c4', 'themes', 0),
(44, 'status_viewed', '30', 'global_setting', 0),
(45, 'update', 'true', 'global_setting', 0),
(46, 'the_date_format', 'F j, Y', 'global_setting', 0),
(47, 'the_time_format', 'H:i', 'global_setting', 0),
(48, 'thumbnail_image_width', '150', 'global_setting', 0),
(49, 'thumbnail_image_height', '150', 'global_setting', 0),
(50, 'medium_image_width', '300', 'global_setting', 0),
(51, 'medium_image_height', '300', 'global_setting', 0),
(52, 'large_image_width', '1024', 'global_setting', 0),
(53, 'large_image_height', '1024', 'global_setting', 0);

-- --------------------------------------------------------

--
-- Table structure for table `lumonata_rules`
--

DROP TABLE IF EXISTS `lumonata_rules`;
CREATE TABLE IF NOT EXISTS `lumonata_rules` (
  `lrule_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lparent` bigint(20) NOT NULL,
  `lname` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lsef` varchar(200) CHARACTER SET utf8 NOT NULL,
  `ldescription` text CHARACTER SET utf8 NOT NULL,
  `lrule` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lgroup` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lcount` bigint(20) NOT NULL DEFAULT '0',
  `lorder` bigint(20) NOT NULL DEFAULT '1',
  `lsubsite` varchar(100) COLLATE latin1_general_ci NOT NULL DEFAULT 'arunna',
  PRIMARY KEY (`lrule_id`),
  KEY `rules_name` (`lname`),
  KEY `sef` (`lsef`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=8 ;

--
-- Dumping data for table `lumonata_rules`
--

INSERT INTO `lumonata_rules` (`lrule_id`, `lparent`, `lname`, `lsef`, `ldescription`, `lrule`, `lgroup`, `lcount`, `lorder`, `lsubsite`) VALUES
(1, 0, 'Uncategorized', 'uncategorized', '', 'categories', 'default', 1, 113, 'arunna'),
(2, 0, 'Designer', 'designer', '', 'categories', 'global_settings', 0, 6, 'arunna'),
(3, 0, 'Entepreneurs', 'entepreneurs', '', 'categories', 'global_settings', 1, 5, 'arunna'),
(4, 0, 'Photographer', 'photographer', '', 'categories', 'global_settings', 0, 4, 'arunna'),
(5, 0, 'Programmer', 'programmer', '', 'categories', 'global_settings', 1, 3, 'arunna'),
(6, 0, 'CEO', 'ceo', '', 'tags', 'profile', 1, 2, 'arunna'),
(7, 0, 'Cooking', 'cooking', '', 'skills', 'profile', 1, 1, 'arunna');

-- --------------------------------------------------------

--
-- Table structure for table `lumonata_rule_relationship`
--

CREATE TABLE IF NOT EXISTS `lumonata_rule_relationship` (
  `lapp_id` bigint(20) NOT NULL,
  `lrule_id` bigint(20) NOT NULL,
  `lorder_id` bigint(20) NOT NULL,
  PRIMARY KEY (`lapp_id`,`lrule_id`),
  KEY `taxonomy_id` (`lrule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

--
-- Dumping data for table `lumonata_rule_relationship`
--

INSERT INTO `lumonata_rule_relationship` (`lapp_id`, `lrule_id`, `lorder_id`) VALUES
(3, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `lumonata_users`
--

CREATE TABLE IF NOT EXISTS `lumonata_users` (
  `luser_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lusername` varchar(200) CHARACTER SET utf8 NOT NULL,
  `ldisplay_name` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lpassword` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lemail` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lregistration_date` datetime NOT NULL,
  `luser_type` varchar(50) CHARACTER SET utf8 NOT NULL,
  `lactivation_key` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lavatar` varchar(200) CHARACTER SET utf8 NOT NULL,
  `lsex` int(11) NOT NULL COMMENT '1=male,2=female',
  `lbirthday` date NOT NULL,
  `lstatus` int(11) NOT NULL COMMENT '0=pendding activation, 1=active,2=blocked',
  `ldlu` datetime NOT NULL,
  PRIMARY KEY (`luser_id`),
  KEY `username` (`lusername`),
  KEY `display_name` (`ldisplay_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `lumonata_users`
--

INSERT INTO `lumonata_users` (`luser_id`, `lusername`, `ldisplay_name`, `lpassword`, `lemail`, `lregistration_date`, `luser_type`, `lactivation_key`, `lavatar`, `lsex`, `lbirthday`, `lstatus`, `ldlu`) VALUES
(1, 'admin', 'Raden Yudistira', 'fcea920f7412b5da7be0cf42b8c93759', 'request@arunna.com', '0000-00-00 00:00:00', 'administrator', '', 'admin-1.jpg|admin-2.jpg|admin-3.jpg', 1, '2011-03-19', 1, '2011-03-18 21:57:25');


--
-- Table structure for table `lumonata_notifications`
--

CREATE TABLE IF NOT EXISTS `lumonata_notifications` (
  `lnotification_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lpost_id` bigint(20) NOT NULL,
  `lpost_owner` bigint(20) NOT NULL,
  `luser_id` bigint(20) NOT NULL,
  `laffected_user` bigint(20) NOT NULL,
  `laction_name` varchar(50) NOT NULL,
  `laction_date` date NOT NULL,
  `lstatus` varchar(10) NOT NULL,
  `lshare_to` bigint(20) NOT NULL,
  PRIMARY KEY (`lnotification_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;