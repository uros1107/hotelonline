/*
SQLyog Professional v13.1.1 (64 bit)
MySQL - 10.4.6-MariaDB : Database - hotel-manager
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`hotel-manager` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `hotel-manager`;

/*Table structure for table `pm_activity` */

DROP TABLE IF EXISTS `pm_activity`;

CREATE TABLE `pm_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `hotels` varchar(250) DEFAULT NULL,
  `users` text DEFAULT NULL,
  `max_children` int(11) DEFAULT 1,
  `max_adults` int(11) DEFAULT 1,
  `max_people` int(11) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `descr` longtext DEFAULT NULL,
  `duration` float DEFAULT 0,
  `duration_unit` varchar(50) DEFAULT NULL,
  `price` double DEFAULT 0,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`lang`),
  KEY `activity_lang_fkey` (`lang`),
  CONSTRAINT `activity_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_activity` */

/*Table structure for table `pm_activity_file` */

DROP TABLE IF EXISTS `pm_activity_file`;

CREATE TABLE `pm_activity_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 1,
  `rank` int(11) DEFAULT 0,
  `file` varchar(250) DEFAULT NULL,
  `label` varchar(250) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `activity_file_fkey` (`id_item`,`lang`),
  KEY `activity_file_lang_fkey` (`lang`),
  CONSTRAINT `activity_file_fkey` FOREIGN KEY (`id_item`, `lang`) REFERENCES `pm_activity` (`id`, `lang`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `activity_file_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_activity_file` */

/*Table structure for table `pm_activity_session` */

DROP TABLE IF EXISTS `pm_activity_session`;

CREATE TABLE `pm_activity_session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_activity` int(11) NOT NULL,
  `days` varchar(20) DEFAULT NULL,
  `start_date` int(11) DEFAULT NULL,
  `end_date` int(11) DEFAULT NULL,
  `users` text DEFAULT NULL,
  `price` double DEFAULT 0,
  `price_child` double DEFAULT 0,
  `discount` double DEFAULT 0,
  `discount_type` varchar(10) DEFAULT NULL,
  `id_tax` int(11) DEFAULT NULL,
  `taxes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_session_fkey` (`id_activity`),
  CONSTRAINT `activity_session_fkey` FOREIGN KEY (`id_activity`) REFERENCES `pm_activity` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_activity_session` */

/*Table structure for table `pm_activity_session_hour` */

DROP TABLE IF EXISTS `pm_activity_session_hour`;

CREATE TABLE `pm_activity_session_hour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_activity_session` int(11) NOT NULL,
  `start_h` int(11) DEFAULT NULL,
  `start_m` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_session_hour_fkey` (`id_activity_session`),
  CONSTRAINT `activity_session_hour_fkey` FOREIGN KEY (`id_activity_session`) REFERENCES `pm_activity_session` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_activity_session_hour` */

/*Table structure for table `pm_article` */

DROP TABLE IF EXISTS `pm_article`;

CREATE TABLE `pm_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `title` varchar(250) DEFAULT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `text` longtext DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `tags` varchar(250) DEFAULT NULL,
  `id_page` int(11) DEFAULT NULL,
  `users` text DEFAULT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  `add_date` int(11) DEFAULT NULL,
  `edit_date` int(11) DEFAULT NULL,
  `publish_date` int(11) DEFAULT NULL,
  `unpublish_date` int(11) DEFAULT NULL,
  `comment` int(11) DEFAULT 0,
  `rating` int(11) DEFAULT 0,
  `show_langs` text DEFAULT NULL,
  `hide_langs` text DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `article_lang_fkey` (`lang`),
  KEY `article_page_fkey` (`id_page`,`lang`),
  CONSTRAINT `article_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `article_page_fkey` FOREIGN KEY (`id_page`, `lang`) REFERENCES `pm_page` (`id`, `lang`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `pm_article` */

insert  into `pm_article`(`id`,`lang`,`title`,`subtitle`,`alias`,`text`,`url`,`tags`,`id_page`,`users`,`home`,`checked`,`rank`,`add_date`,`edit_date`,`publish_date`,`unpublish_date`,`comment`,`rating`,`show_langs`,`hide_langs`) values 
(1,1,'Plongez dans des eaux inconnues !','','plongee','','','',5,'1',1,1,1,0,0,NULL,NULL,1,0,NULL,NULL),
(1,2,'Dive into unknown waters!','','scuba-diving','<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. Phasellus eget est quis est faucibus condimentum. Morbi tellus turpis, posuere vel tincidunt non, varius ac ante. Suspendisse in sem neque. Donec et faucibus justo. Nulla vitae nisl lacus. Fusce tincidunt quam nec vestibulum vestibulum. Vivamus vulputate, nunc non ullamcorper mattis, nunc orci imperdiet nulla, at laoreet ipsum nisl non leo. Aenean dapibus aliquet sem, ut lacinia magna mattis in.</p>\r\n\r\n<p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Curabitur tempor arcu eu sapien ullamcorper sodales. Aenean eu massa in ante commodo scelerisque vitae sed sapien. Aenean eu dictum arcu. Mauris ultricies dolor eu molestie egestas.<br />\r\nProin feugiat, nunc at pellentesque fringilla, ex purus efficitur dolor, ac pretium odio lacus id leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse eu ipsum viverra dolor tempus vehicula eu eu risus. Praesent rutrum dapibus odio, nec accumsan justo fermentum in. Ut quis neque a ante facilisis bibendum.</p>\r\n','','',5,'1',1,1,1,0,0,NULL,NULL,1,0,NULL,NULL),
(1,3,'Dive into unknown waters!','','scuba-diving','','','',5,'1',1,1,1,0,0,NULL,NULL,1,0,NULL,NULL),
(1,4,'Dive into unknown waters!','','scuba-diving','<p>Lorem ipsum dolor sit amet consectetur adipiscing elit. Nullam molestie, nunc eu consequat varius, nisi metus iaculis nulla, nec ornare odio leo quis eros. Donec gravida eget velit eget pulvinar. Phasellus eget est quis est faucibus condimentum. Morbi tellus turpis, posuere vel tincidunt non, varius ac ante. Suspendisse in sem neque. Donec et faucibus justo. Nulla vitae nisl lacus. Fusce tincidunt quam nec vestibulum vestibulum. Vivamus vulputate, nunc non ullamcorper mattis, nunc orci imperdiet nulla, at laoreet ipsum nisl non leo. Aenean dapibus aliquet sem, ut lacinia magna mattis in.</p>\r\n\r\n<p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Curabitur tempor arcu eu sapien ullamcorper sodales. Aenean eu massa in ante commodo scelerisque vitae sed sapien. Aenean eu dictum arcu. Mauris ultricies dolor eu molestie egestas.<br />\r\nProin feugiat, nunc at pellentesque fringilla, ex purus efficitur dolor, ac pretium odio lacus id leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Suspendisse eu ipsum viverra dolor tempus vehicula eu eu risus. Praesent rutrum dapibus odio, nec accumsan justo fermentum in. Ut quis neque a ante facilisis bibendum.</p>\r\n','','',5,'1',1,1,1,0,0,NULL,NULL,1,0,NULL,NULL),
(4,1,'Première gallery','','premiere-gallery','','','',7,'1',0,1,4,0,0,NULL,NULL,0,0,NULL,NULL),
(4,2,'First gallery','','first-gallery','','','',7,'1',0,1,4,0,0,NULL,NULL,0,0,NULL,NULL),
(4,3,'First gallery','','first-gallery','','','',7,'1',0,1,4,0,0,NULL,NULL,0,0,NULL,NULL),
(4,4,'First gallery','','first-gallery','','','',7,'1',0,1,4,0,0,NULL,NULL,0,0,NULL,NULL);

/*Table structure for table `pm_article_file` */

DROP TABLE IF EXISTS `pm_article_file`;

CREATE TABLE `pm_article_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 1,
  `rank` int(11) DEFAULT 0,
  `file` varchar(250) DEFAULT NULL,
  `label` varchar(250) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `article_file_fkey` (`id_item`,`lang`),
  KEY `article_file_lang_fkey` (`lang`),
  CONSTRAINT `article_file_fkey` FOREIGN KEY (`id_item`, `lang`) REFERENCES `pm_article` (`id`, `lang`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `article_file_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `pm_article_file` */

insert  into `pm_article_file`(`id`,`lang`,`id_item`,`home`,`checked`,`rank`,`file`,`label`,`type`) values 
(4,1,4,0,1,4,'sample4.jpg','','image'),
(4,2,4,0,1,4,'sample4.jpg','','image'),
(4,3,4,0,1,4,'sample4.jpg','','image'),
(4,4,4,0,1,4,'sample4.jpg','','image'),
(5,1,1,0,1,5,'diving.jpg','','image'),
(5,2,1,0,1,5,'diving.jpg','','image'),
(5,3,1,0,1,5,'diving.jpg','','image'),
(5,4,1,0,1,5,'diving.jpg','','image');

/*Table structure for table `pm_booking` */

DROP TABLE IF EXISTS `pm_booking`;

CREATE TABLE `pm_booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_hotel` int(11) DEFAULT NULL,
  `add_date` int(11) DEFAULT NULL,
  `edit_date` int(11) DEFAULT NULL,
  `from_date` int(11) DEFAULT NULL,
  `to_date` int(11) DEFAULT NULL,
  `nights` int(11) DEFAULT 0,
  `adults` int(11) DEFAULT 1,
  `children` int(11) DEFAULT 1,
  `amount` float DEFAULT NULL,
  `tourist_tax` float DEFAULT NULL,
  `discount` float DEFAULT NULL,
  `ex_tax` float DEFAULT NULL,
  `tax_amount` float DEFAULT NULL,
  `total` float DEFAULT NULL,
  `down_payment` float DEFAULT NULL,
  `paid` float DEFAULT NULL,
  `balance` float DEFAULT NULL,
  `extra_services` text DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `company` varchar(50) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `country` varchar(100) NOT NULL,
  `comments` text DEFAULT NULL,
  `status` int(11) DEFAULT 1,
  `trans` varchar(50) DEFAULT NULL,
  `payment_date` int(11) DEFAULT NULL,
  `payment_option` varchar(250) DEFAULT NULL,
  `users` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_booking` */

/*Table structure for table `pm_booking_activity` */

DROP TABLE IF EXISTS `pm_booking_activity`;

CREATE TABLE `pm_booking_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_booking` int(11) NOT NULL,
  `id_activity` int(11) NOT NULL,
  `title` varchar(250) DEFAULT NULL,
  `children` int(11) DEFAULT 0,
  `adults` int(11) DEFAULT 0,
  `duration` varchar(50) DEFAULT NULL,
  `amount` double DEFAULT 0,
  `ex_tax` double DEFAULT 0,
  `tax_rate` double DEFAULT 0,
  `date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_activity_fkey` (`id_booking`),
  CONSTRAINT `booking_activity_fkey` FOREIGN KEY (`id_booking`) REFERENCES `pm_booking` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_booking_activity` */

/*Table structure for table `pm_booking_payment` */

DROP TABLE IF EXISTS `pm_booking_payment`;

CREATE TABLE `pm_booking_payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_booking` int(11) NOT NULL,
  `descr` varchar(100) DEFAULT NULL,
  `method` varchar(100) DEFAULT NULL,
  `amount` double DEFAULT 0,
  `trans` varchar(100) DEFAULT NULL,
  `date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `booking_payment_fkey` (`id_booking`),
  CONSTRAINT `booking_payment_fkey` FOREIGN KEY (`id_booking`) REFERENCES `pm_booking` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_booking_payment` */

/*Table structure for table `pm_booking_room` */

DROP TABLE IF EXISTS `pm_booking_room`;

CREATE TABLE `pm_booking_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_booking` int(11) NOT NULL,
  `id_room` int(11) DEFAULT NULL,
  `id_hotel` int(11) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `num` varchar(10) DEFAULT NULL,
  `children` int(11) DEFAULT 0,
  `adults` int(11) DEFAULT 0,
  `amount` double DEFAULT 0,
  `ex_tax` double DEFAULT 0,
  `tax_rate` double DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `booking_room_fkey` (`id_booking`),
  CONSTRAINT `booking_room_fkey` FOREIGN KEY (`id_booking`) REFERENCES `pm_booking` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_booking_room` */

/*Table structure for table `pm_booking_service` */

DROP TABLE IF EXISTS `pm_booking_service`;

CREATE TABLE `pm_booking_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_booking` int(11) NOT NULL,
  `id_service` int(11) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `qty` int(11) DEFAULT 0,
  `amount` double DEFAULT 0,
  `ex_tax` double DEFAULT 0,
  `tax_rate` double DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `booking_service_fkey` (`id_booking`),
  CONSTRAINT `booking_service_fkey` FOREIGN KEY (`id_booking`) REFERENCES `pm_booking` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_booking_service` */

/*Table structure for table `pm_booking_tax` */

DROP TABLE IF EXISTS `pm_booking_tax`;

CREATE TABLE `pm_booking_tax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_booking` int(11) NOT NULL,
  `id_tax` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `amount` double DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `booking_tax_fkey` (`id_booking`),
  CONSTRAINT `booking_tax_fkey` FOREIGN KEY (`id_booking`) REFERENCES `pm_booking` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_booking_tax` */

/*Table structure for table `pm_comment` */

DROP TABLE IF EXISTS `pm_comment`;

CREATE TABLE `pm_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_type` varchar(30) DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `checked` int(11) DEFAULT 0,
  `add_date` int(11) DEFAULT NULL,
  `edit_date` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `msg` longtext DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_comment` */

/*Table structure for table `pm_country` */

DROP TABLE IF EXISTS `pm_country`;

CREATE TABLE `pm_country` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `code` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8;

/*Data for the table `pm_country` */

insert  into `pm_country`(`id`,`name`,`code`) values 
(1,'Afghanistan','AF'),
(2,'Åland','AX'),
(3,'Albania','AL'),
(4,'Algeria','DZ'),
(5,'American Samoa','AS'),
(6,'Andorra','AD'),
(7,'Angola','AO'),
(8,'Anguilla','AI'),
(9,'Antarctica','AQ'),
(10,'Antigua and Barbuda','AG'),
(11,'Argentina','AR'),
(12,'Armenia','AM'),
(13,'Aruba','AW'),
(14,'Australia','AU'),
(15,'Austria','AT'),
(16,'Azerbaijan','AZ'),
(17,'Bahamas','BS'),
(18,'Bahrain','BH'),
(19,'Bangladesh','BD'),
(20,'Barbados','BB'),
(21,'Belarus','BY'),
(22,'Belgium','BE'),
(23,'Belize','BZ'),
(24,'Benin','BJ'),
(25,'Bermuda','BM'),
(26,'Bhutan','BT'),
(27,'Bolivia','BO'),
(28,'Bonaire','BQ'),
(29,'Bosnia and Herzegovina','BA'),
(30,'Botswana','BW'),
(31,'Bouvet Island','BV'),
(32,'Brazil','BR'),
(33,'British Indian Ocean Territory','IO'),
(34,'British Virgin Islands','VG'),
(35,'Brunei','BN'),
(36,'Bulgaria','BG'),
(37,'Burkina Faso','BF'),
(38,'Burundi','BI'),
(39,'Cambodia','KH'),
(40,'Cameroon','CM'),
(41,'Canada','CA'),
(42,'Cape Verde','CV'),
(43,'Cayman Islands','KY'),
(44,'Central African Republic','CF'),
(45,'Chad','TD'),
(46,'Chile','CL'),
(47,'China','CN'),
(48,'Christmas Island','CX'),
(49,'Cocos [Keeling] Islands','CC'),
(50,'Colombia','CO'),
(51,'Comoros','KM'),
(52,'Cook Islands','CK'),
(53,'Costa Rica','CR'),
(54,'Croatia','HR'),
(55,'Cuba','CU'),
(56,'Curacao','CW'),
(57,'Cyprus','CY'),
(58,'Czech Republic','CZ'),
(59,'Democratic Republic of the Congo','CD'),
(60,'Denmark','DK'),
(61,'Djibouti','DJ'),
(62,'Dominica','DM'),
(63,'Dominican Republic','DO'),
(64,'East Timor','TL'),
(65,'Ecuador','EC'),
(66,'Egypt','EG'),
(67,'El Salvador','SV'),
(68,'Equatorial Guinea','GQ'),
(69,'Eritrea','ER'),
(70,'Estonia','EE'),
(71,'Ethiopia','ET'),
(72,'Falkland Islands','FK'),
(73,'Faroe Islands','FO'),
(74,'Fiji','FJ'),
(75,'Finland','FI'),
(76,'France','FR'),
(77,'French Guiana','GF'),
(78,'French Polynesia','PF'),
(79,'French Southern Territories','TF'),
(80,'Gabon','GA'),
(81,'Gambia','GM'),
(82,'Georgia','GE'),
(83,'Germany','DE'),
(84,'Ghana','GH'),
(85,'Gibraltar','GI'),
(86,'Greece','GR'),
(87,'Greenland','GL'),
(88,'Grenada','GD'),
(89,'Guadeloupe','GP'),
(90,'Guam','GU'),
(91,'Guatemala','GT'),
(92,'Guernsey','GG'),
(93,'Guinea','GN'),
(94,'Guinea-Bissau','GW'),
(95,'Guyana','GY'),
(96,'Haiti','HT'),
(97,'Heard Island and McDonald Islands','HM'),
(98,'Honduras','HN'),
(99,'Hong Kong','HK'),
(100,'Hungary','HU'),
(101,'Iceland','IS'),
(102,'India','IN'),
(103,'Indonesia','ID'),
(104,'Iran','IR'),
(105,'Iraq','IQ'),
(106,'Ireland','IE'),
(107,'Isle of Man','IM'),
(108,'Israel','IL'),
(109,'Italy','IT'),
(110,'Ivory Coast','CI'),
(111,'Jamaica','JM'),
(112,'Japan','JP'),
(113,'Jersey','JE'),
(114,'Jordan','JO'),
(115,'Kazakhstan','KZ'),
(116,'Kenya','KE'),
(117,'Kiribati','KI'),
(118,'Kosovo','XK'),
(119,'Kuwait','KW'),
(120,'Kyrgyzstan','KG'),
(121,'Laos','LA'),
(122,'Latvia','LV'),
(123,'Lebanon','LB'),
(124,'Lesotho','LS'),
(125,'Liberia','LR'),
(126,'Libya','LY'),
(127,'Liechtenstein','LI'),
(128,'Lithuania','LT'),
(129,'Luxembourg','LU'),
(130,'Macao','MO'),
(131,'Macedonia','MK'),
(132,'Madagascar','MG'),
(133,'Malawi','MW'),
(134,'Malaysia','MY'),
(135,'Maldives','MV'),
(136,'Mali','ML'),
(137,'Malta','MT'),
(138,'Marshall Islands','MH'),
(139,'Martinique','MQ'),
(140,'Mauritania','MR'),
(141,'Mauritius','MU'),
(142,'Mayotte','YT'),
(143,'Mexico','MX'),
(144,'Micronesia','FM'),
(145,'Moldova','MD'),
(146,'Monaco','MC'),
(147,'Mongolia','MN'),
(148,'Montenegro','ME'),
(149,'Montserrat','MS'),
(150,'Morocco','MA'),
(151,'Mozambique','MZ'),
(152,'Myanmar [Burma]','MM'),
(153,'Namibia','NA'),
(154,'Nauru','NR'),
(155,'Nepal','NP'),
(156,'Netherlands','NL'),
(157,'New Caledonia','NC'),
(158,'New Zealand','NZ'),
(159,'Nicaragua','NI'),
(160,'Niger','NE'),
(161,'Nigeria','NG'),
(162,'Niue','NU'),
(163,'Norfolk Island','NF'),
(164,'North Korea','KP'),
(165,'Northern Mariana Islands','MP'),
(166,'Norway','NO'),
(167,'Oman','OM'),
(168,'Pakistan','PK'),
(169,'Palau','PW'),
(170,'Palestine','PS'),
(171,'Panama','PA'),
(172,'Papua New Guinea','PG'),
(173,'Paraguay','PY'),
(174,'Peru','PE'),
(175,'Philippines','PH'),
(176,'Pitcairn Islands','PN'),
(177,'Poland','PL'),
(178,'Portugal','PT'),
(179,'Puerto Rico','PR'),
(180,'Qatar','QA'),
(181,'Republic of the Congo','CG'),
(182,'Réunion','RE'),
(183,'Romania','RO'),
(184,'Russia','RU'),
(185,'Rwanda','RW'),
(186,'Saint Barthélemy','BL'),
(187,'Saint Helena','SH'),
(188,'Saint Kitts and Nevis','KN'),
(189,'Saint Lucia','LC'),
(190,'Saint Martin','MF'),
(191,'Saint Pierre and Miquelon','PM'),
(192,'Saint Vincent and the Grenadines','VC'),
(193,'Samoa','WS'),
(194,'San Marino','SM'),
(195,'São Tomé and Príncipe','ST'),
(196,'Saudi Arabia','SA'),
(197,'Senegal','SN'),
(198,'Serbia','RS'),
(199,'Seychelles','SC'),
(200,'Sierra Leone','SL'),
(201,'Singapore','SG'),
(202,'Sint Maarten','SX'),
(203,'Slovakia','SK'),
(204,'Slovenia','SI'),
(205,'Solomon Islands','SB'),
(206,'Somalia','SO'),
(207,'South Africa','ZA'),
(208,'South Georgia and the South Sandwich Islands','GS'),
(209,'South Korea','KR'),
(210,'South Sudan','SS'),
(211,'Spain','ES'),
(212,'Sri Lanka','LK'),
(213,'Sudan','SD'),
(214,'Suriname','SR'),
(215,'Svalbard and Jan Mayen','SJ'),
(216,'Swaziland','SZ'),
(217,'Sweden','SE'),
(218,'Switzerland','CH'),
(219,'Syria','SY'),
(220,'Taiwan','TW'),
(221,'Tajikistan','TJ'),
(222,'Tanzania','TZ'),
(223,'Thailand','TH'),
(224,'Togo','TG'),
(225,'Tokelau','TK'),
(226,'Tonga','TO'),
(227,'Trinidad and Tobago','TT'),
(228,'Tunisia','TN'),
(229,'Turkey','TR'),
(230,'Turkmenistan','TM'),
(231,'Turks and Caicos Islands','TC'),
(232,'Tuvalu','TV'),
(233,'U.S. Minor Outlying Islands','UM'),
(234,'U.S. Virgin Islands','VI'),
(235,'Uganda','UG'),
(236,'Ukraine','UA'),
(237,'United Arab Emirates','AE'),
(238,'United Kingdom','GB'),
(239,'United States','US'),
(240,'Uruguay','UY'),
(241,'Uzbekistan','UZ'),
(242,'Vanuatu','VU'),
(243,'Vatican City','VA'),
(244,'Venezuela','VE'),
(245,'Vietnam','VN'),
(246,'Wallis and Futuna','WF'),
(247,'Western Sahara','EH'),
(248,'Yemen','YE'),
(249,'Zambia','ZM'),
(250,'Zimbabwe','ZW');

/*Table structure for table `pm_coupon` */

DROP TABLE IF EXISTS `pm_coupon`;

CREATE TABLE `pm_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `discount` double DEFAULT 0,
  `discount_type` varchar(10) DEFAULT NULL,
  `rooms` text DEFAULT NULL,
  `once` int(11) DEFAULT NULL,
  `checked` int(11) DEFAULT 0,
  `publish_date` int(11) DEFAULT NULL,
  `unpublish_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_coupon` */

/*Table structure for table `pm_coupon_user` */

DROP TABLE IF EXISTS `pm_coupon_user`;

CREATE TABLE `pm_coupon_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_coupon` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coupon_user_fkey` (`id_coupon`),
  CONSTRAINT `coupon_user_fkey` FOREIGN KEY (`id_coupon`) REFERENCES `pm_coupon` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_coupon_user` */

/*Table structure for table `pm_currency` */

DROP TABLE IF EXISTS `pm_currency`;

CREATE TABLE `pm_currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(5) DEFAULT NULL,
  `sign` varchar(5) DEFAULT NULL,
  `main` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `pm_currency` */

insert  into `pm_currency`(`id`,`code`,`sign`,`main`,`rank`) values 
(1,'USD','$',1,1),
(2,'EUR','€',0,2),
(3,'GBP','£',0,3),
(4,'INR','₹',0,4),
(5,'AUD','A$',0,5),
(6,'CAD','C$',0,6),
(7,'CNY','¥',0,7),
(8,'TRY','₺',0,8);

/*Table structure for table `pm_destination` */

DROP TABLE IF EXISTS `pm_destination`;

CREATE TABLE `pm_destination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `title_tag` varchar(250) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `descr` text DEFAULT NULL,
  `text` longtext DEFAULT NULL,
  `video` text DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`lang`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `pm_destination` */

insert  into `pm_destination`(`id`,`lang`,`name`,`title`,`subtitle`,`title_tag`,`alias`,`descr`,`text`,`video`,`lat`,`lng`,`home`,`checked`,`rank`) values 
(1,1,'','','','','','','','',36.393073,25.461696,1,1,1),
(1,2,'Santorini, Greece','Find Hotels in  Santorini, Greece','Visitors Love: Scenery, Sunsets, and Relaxation','Travel in  Santorini, Greece','hotels-santorini','','','',36.393073,25.461696,1,1,1),
(1,3,'','','','','','','','',36.393073,25.461696,1,1,1),
(1,4,'Santorini, Greece','Find Hotels in  Santorini, Greece','Visitors Love: Scenery, Sunsets, and Relaxation','Travel in  Santorini, Greece','hotels-santorini','','','',36.393073,25.461696,1,1,1);

/*Table structure for table `pm_destination_file` */

DROP TABLE IF EXISTS `pm_destination_file`;

CREATE TABLE `pm_destination_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 1,
  `rank` int(11) DEFAULT 0,
  `file` varchar(250) DEFAULT NULL,
  `label` varchar(250) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `destination_file_fkey` (`id_item`,`lang`),
  KEY `destination_file_lang_fkey` (`lang`),
  CONSTRAINT `destination_file_fkey` FOREIGN KEY (`id_item`, `lang`) REFERENCES `pm_destination` (`id`, `lang`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `destination_file_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `pm_destination_file` */

insert  into `pm_destination_file`(`id`,`lang`,`id_item`,`home`,`checked`,`rank`,`file`,`label`,`type`) values 
(1,1,1,NULL,1,1,'izghud7.gif',NULL,'image'),
(1,2,1,NULL,1,1,'izghud7.gif',NULL,'image'),
(1,3,1,NULL,1,1,'izghud7.gif',NULL,'image'),
(1,4,1,NULL,1,1,'izghud7.gif',NULL,'image');

/*Table structure for table `pm_email_content` */

DROP TABLE IF EXISTS `pm_email_content`;

CREATE TABLE `pm_email_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `subject` varchar(250) DEFAULT NULL,
  `content` text DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `email_content_lang_fkey` (`lang`),
  CONSTRAINT `email_content_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `pm_email_content` */

insert  into `pm_email_content`(`id`,`lang`,`name`,`subject`,`content`) values 
(1,1,'CONTACT','Contact','<b>Nom:</b> {name}<b>Adresse:</b> {address}<b>Téléphone:</b> {phone}<b>E-mail:</b> {email}<b>Message:</b>{msg}'),
(1,2,'CONTACT','Contact','<b>Name:</b> {name}<br><b>Address:</b> {address}<br><b>Phone:</b> {phone}<br><b>E-mail:</b> {email}<br><b>Message:</b><br>{msg}'),
(1,3,'CONTACT','Contact','<b>Name:</b> {name}<b>Address:</b> {address}<b>Phone:</b> {phone}<b>E-mail:</b> {email}<b>Message:</b>{msg}'),
(1,4,'CONTACT','Contact','<b>Name:</b> {name}<br><b>Address:</b> {address}<br><b>Phone:</b> {phone}<br><b>E-mail:</b> {email}<br><b>Message:</b><br>{msg}'),
(2,1,'BOOKING_REQUEST','Demande de réservation','<p><b>Adresse de facturation</b><br />\r\n{firstname} {lastname}<br />\r\n{address}<br />\r\n{postcode} {city}<br />\r\nSociété : {company}<br />\r\nTéléphone : {phone}<br />\r\nMobile : {mobile}<br />\r\nEmail : {email}</p>\r\n\r\n<p><strong>Détails de la réservation</strong><br />\r\nArrivée : <b>{Check_in}</b><br />\r\nDépart : <b>{Check_out}</b><br />\r\n<b>{num_nights}</b> nuit(s)<br />\r\n<b>{num_guests}</b> personne(s) - Adulte(s) : <b>{num_adults}</b> / Enfant(s) : <b>{num_children}</b></p>\r\n\r\n<p><b>Chambres</b></p>\r\n\r\n<p>{rooms}</p>\r\n\r\n<p><b>Services supplémentaires</b></p>\r\n\r\n<p>{extra_services}</p>\r\n\r\n<p><b>Activités</b></p>\r\n\r\n<p>{activities}</p>\r\n\r\n<p><b>Commentaires</b><br />\r\n{comments}</p>\r\n'),
(2,2,'BOOKING_REQUEST','Booking request','<p><b>Billing address</b><br />\r\n{firstname} {lastname}<br />\r\n{address}<br />\r\n{postcode} {city}<br />\r\nCompany: {company}<br />\r\nPhone: {phone}<br />\r\nMobile: {mobile}<br />\r\nEmail: {email}</p>\r\n\r\n<p><strong>Booking details</strong><br />\r\nCheck in <b>{Check_in}</b><br />\r\nCheck out <b>{Check_out}</b><br />\r\n<b>{num_nights}</b> nights<br />\r\n<b>{num_guests}</b> persons - Adults: <b>{num_adults}</b> / Children: <b>{num_children}</b></p>\r\n\r\n<p><strong>Rooms</strong></p>\r\n\r\n<p>{rooms}</p>\r\n\r\n<p><b>Extra services</b></p>\r\n\r\n<p>{extra_services}</p>\r\n\r\n<p><b>Activities</b></p>\r\n\r\n<p>{activities}</p>\r\n\r\n<p><b>Comments</b><br />\r\n{comments}</p>\r\n'),
(2,3,'BOOKING_REQUEST','Booking request','<p><b>Billing address</b><br />\r\n{firstname} {lastname}<br />\r\n{address}<br />\r\n{postcode} {city}<br />\r\nCompany: {company}<br />\r\nPhone: {phone}<br />\r\nMobile: {mobile}<br />\r\nEmail: {email}</p>\r\n\r\n<p><strong>Booking details</strong><br />\r\nCheck in <b>{Check_in}</b><br />\r\nCheck out <b>{Check_out}</b><br />\r\n<b>{num_nights}</b> nights<br />\r\n<b>{num_guests}</b> persons - Adults: <b>{num_adults}</b> / Children: <b>{num_children}</b></p>\r\n\r\n<p><strong>Rooms</strong></p>\r\n\r\n<p>{rooms}</p>\r\n\r\n<p><b>Extra services</b></p>\r\n\r\n<p>{extra_services}</p>\r\n\r\n<p><b>Activities</b></p>\r\n\r\n<p>{activities}</p>\r\n\r\n<p><b>Comments</b><br />\r\n{comments}</p>\r\n'),
(2,4,'BOOKING_REQUEST','Booking request','<p><b>Billing address</b><br />\r\n{firstname} {lastname}<br />\r\n{address}<br />\r\n{postcode} {city}<br />\r\nCompany: {company}<br />\r\nPhone: {phone}<br />\r\nMobile: {mobile}<br />\r\nEmail: {email}</p>\r\n\r\n<p><strong>Booking details</strong><br />\r\nCheck in <b>{Check_in}</b><br />\r\nCheck out <b>{Check_out}</b><br />\r\n<b>{num_nights}</b> nights<br />\r\n<b>{num_guests}</b> persons - Adults: <b>{num_adults}</b> / Children: <b>{num_children}</b></p>\r\n\r\n<p><strong>Rooms</strong></p>\r\n\r\n<p>{rooms}</p>\r\n\r\n<p><b>Extra services</b></p>\r\n\r\n<p>{extra_services}</p>\r\n\r\n<p><b>Activities</b></p>\r\n\r\n<p>{activities}</p>\r\n\r\n<p><b>Comments</b><br />\r\n{comments}</p>\r\n'),
(3,1,'BOOKING_CONFIRMATION','Confirmation de réservation','<p><b>Adresse de facturation</b><br />\r\n{firstname} {lastname}<br />\r\n{address}<br />\r\n{postcode} {city}<br />\r\nSociété : {company}<br />\r\nTéléphone : {phone}<br />\r\nMobile : {mobile}<br />\r\nEmail : {email}</p>\r\n\r\n<p><strong>Détails de la réservation</strong><br />\r\nArrivée : <b>{Check_in}</b><br />\r\nDépart : <b>{Check_out}</b><br />\r\n<b>{num_nights}</b> nuit(s)<br />\r\n<b>{num_guests}</b> personne(s) - Adulte(s) : <b>{num_adults}</b> / Enfant(s) : <b>{num_children}</b></p>\r\n\r\n<p><b>Chambres</b></p>\r\n\r\n<p>{rooms}</p>\r\n\r\n<p><b>Services supplémentaires</b></p>\r\n\r\n<p>{extra_services}</p>\r\n\r\n<p><b>Activités</b></p>\r\n\r\n<p>{activities}</p>\r\n\r\n<p>Taxe de séjour : {tourist_tax}<br />\r\nRéduction: {discount}<br />\r\n{taxes}<br />\r\nTotal : <strong>{total} TTC</strong></p>\r\n\r\n<p>Acompte : <strong>{down_payment} TTC</strong></p>\r\n\r\n<p><b>Commentaires</b><br />\r\n{comments}</p>\r\n\r\n<p>{payment_notice}</p>\r\n'),
(3,2,'BOOKING_CONFIRMATION','Booking confirmation','<p><b>Billing address</b><br />\r\n{firstname} {lastname}<br />\r\n{address}<br />\r\n{postcode} {city}<br />\r\nCompany: {company}<br />\r\nPhone: {phone}<br />\r\nMobile: {mobile}<br />\r\nEmail: {email}</p>\r\n\r\n<p><strong>Booking details</strong><br />\r\nCheck in <b>{Check_in}</b><br />\r\nCheck out <b>{Check_out}</b><br />\r\n<b>{num_nights}</b> nights<br />\r\n<b>{num_guests}</b> persons - Adults: <b>{num_adults}</b> / Children: <b>{num_children}</b></p>\r\n\r\n<p><strong>Rooms</strong></p>\r\n\r\n<p>{rooms}</p>\r\n\r\n<p><b>Extra services</b></p>\r\n\r\n<p>{extra_services}</p>\r\n\r\n<p><b>Activities</b></p>\r\n\r\n<p>{activities}</p>\r\n\r\n<p>Tourist tax: {tourist_tax}<br />\r\nDiscount: {discount}<br />\r\n{taxes}<br />\r\nTotal: <strong>{total} incl. VAT</strong></p>\r\n\r\n<p>Down payment: <strong>{down_payment} incl. VAT</strong></p>\r\n\r\n<p><b>Comments</b><br />\r\n{comments}</p>\r\n\r\n<p>{payment_notice}</p>\r\n'),
(3,3,'BOOKING_CONFIRMATION','Booking confirmation','<p><b>Billing address</b><br />\r\n{firstname} {lastname}<br />\r\n{address}<br />\r\n{postcode} {city}<br />\r\nCompany: {company}<br />\r\nPhone: {phone}<br />\r\nMobile: {mobile}<br />\r\nEmail: {email}</p>\r\n\r\n<p><strong>Booking details</strong><br />\r\nCheck in <b>{Check_in}</b><br />\r\nCheck out <b>{Check_out}</b><br />\r\n<b>{num_nights}</b> nights<br />\r\n<b>{num_guests}</b> persons - Adults: <b>{num_adults}</b> / Children: <b>{num_children}</b></p>\r\n\r\n<p><strong>Rooms</strong></p>\r\n\r\n<p>{rooms}</p>\r\n\r\n<p><b>Extra services</b></p>\r\n\r\n<p>{extra_services}</p>\r\n\r\n<p><b>Activities</b></p>\r\n\r\n<p>{activities}</p>\r\n\r\n<p>Tourist tax: {tourist_tax}<br />\r\nDiscount: {discount}<br />\r\n{taxes}<br />\r\nTotal: <strong>{total} incl. VAT</strong></p>\r\n\r\n<p>Down payment: <strong>{down_payment} incl. VAT</strong></p>\r\n\r\n<p><b>Comments</b><br />\r\n{comments}</p>\r\n\r\n<p>{payment_notice}</p>\r\n'),
(3,4,'BOOKING_CONFIRMATION','Booking confirmation','<p><b>Billing address</b><br />\r\n{firstname} {lastname}<br />\r\n{address}<br />\r\n{postcode} {city}<br />\r\nCompany: {company}<br />\r\nPhone: {phone}<br />\r\nMobile: {mobile}<br />\r\nEmail: {email}</p>\r\n\r\n<p><strong>Booking details</strong><br />\r\nCheck in <b>{Check_in}</b><br />\r\nCheck out <b>{Check_out}</b><br />\r\n<b>{num_nights}</b> nights<br />\r\n<b>{num_guests}</b> persons - Adults: <b>{num_adults}</b> / Children: <b>{num_children}</b></p>\r\n\r\n<p><strong>Rooms</strong></p>\r\n\r\n<p>{rooms}</p>\r\n\r\n<p><b>Extra services</b></p>\r\n\r\n<p>{extra_services}</p>\r\n\r\n<p><b>Activities</b></p>\r\n\r\n<p>{activities}</p>\r\n\r\n<p>Tourist tax: {tourist_tax}<br />\r\nDiscount: {discount}<br />\r\n{taxes}<br />\r\nTotal: <strong>{total} incl. VAT</strong></p>\r\n\r\n<p>Down payment: <strong>{down_payment} incl. VAT</strong></p>\r\n\r\n<p><b>Comments</b><br />\r\n{comments}</p>\r\n\r\n<p>{payment_notice}</p>\r\n'),
(4,1,'ACCOUNT_CONFIRMATION','Confirmation du compte','<p>Bonjour,<br />\r\nVous avez cr&eacute;&eacute; un nouveau compte.<br />\r\nCliquez sur le lien ci-dessous pour valider votre compte:<br />\r\n<a href=\"{link}\">Valider mon compte</a></p>\r\n'),
(4,2,'ACCOUNT_CONFIRMATION','Validate your account','<p>Hi,<br />\r\nYou created a new account.<br />\r\nClick on the link bellow to validate your account:<br />\r\n<a href=\"{link}\">Validate my new account</a></p>\r\n'),
(4,3,'ACCOUNT_CONFIRMATION','Validate your account','<p>Hi,<br />\r\nYou created a new account.<br />\r\nClick on the link bellow to validate your account:<br />\r\n<a href=\"{link}\">Validate my new account</a></p>\r\n'),
(4,4,'ACCOUNT_CONFIRMATION','Validate your account','<p>Hi,<br />\r\nYou created a new account.<br />\r\nClick on the link bellow to validate your account:<br />\r\n<a href=\"{link}\">Validate my new account</a></p>\r\n');

/*Table structure for table `pm_facility` */

DROP TABLE IF EXISTS `pm_facility`;

CREATE TABLE `pm_facility` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`lang`),
  KEY `facility_lang_fkey` (`lang`),
  CONSTRAINT `facility_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

/*Data for the table `pm_facility` */

insert  into `pm_facility`(`id`,`lang`,`name`,`rank`) values 
(1,1,'Climatisation',1),
(1,2,'Air conditioning',1),
(1,4,'Air conditioning',1),
(2,1,'Lit bébé',2),
(2,2,'Baby cot',2),
(2,4,'Baby cot',2),
(3,1,'Balcon',3),
(3,2,'Balcony',3),
(3,4,'Balcony',3),
(4,1,'Barbecue',4),
(4,2,'Barbecue',4),
(4,4,'Barbecue',4),
(5,1,'Salle de bain',5),
(5,2,'Bathroom',5),
(5,4,'Bathroom',5),
(6,1,'Cafetière',6),
(6,2,'Coffeemaker',6),
(6,4,'Coffeemaker',6),
(7,1,'Plaque de cuisson',7),
(7,2,'Cooktop',7),
(7,4,'Cooktop',7),
(8,1,'Bureau',8),
(8,2,'Desk',8),
(8,4,'Desk',8),
(9,1,'Lave vaisselle',9),
(9,2,'Dishwasher',9),
(9,4,'Dishwasher',9),
(10,1,'Ventilateur',10),
(10,2,'Fan',10),
(10,4,'Fan',10),
(11,1,'Parking gratuit',11),
(11,2,'Free parking',11),
(11,4,'Free parking',11),
(12,1,'Réfrigérateur',12),
(12,2,'Fridge',12),
(12,4,'Fridge',12),
(13,1,'Sèche-cheveux',13),
(13,2,'Hairdryer',13),
(13,4,'Hairdryer',13),
(14,1,'Internet',14),
(14,2,'Internet',14),
(14,4,'Internet',14),
(15,1,'Fer à repasser',15),
(15,2,'Iron',15),
(15,4,'Iron',15),
(16,1,'Micro-ondes',16),
(16,2,'Microwave',16),
(16,4,'Microwave',16),
(17,1,'Mini-bar',17),
(17,2,'Mini-bar',17),
(17,4,'Mini-bar',17),
(18,1,'Non-fumeurs',18),
(18,2,'Non-smoking',18),
(18,4,'Non-smoking',18),
(19,1,'Parking payant',19),
(19,2,'Paid parking',19),
(19,4,'Paid parking',19),
(20,1,'Animaux acceptés',20),
(20,2,'Pets allowed',20),
(20,4,'Pets allowed',20),
(21,1,'Animaux interdits',21),
(21,2,'Pets not allowed',21),
(21,4,'Pets not allowed',21),
(22,1,'Radio',22),
(22,2,'Radio',22),
(22,4,'Radio',22),
(23,1,'Coffre-fort',23),
(23,2,'Safe',23),
(23,4,'Safe',23),
(24,1,'Chaines satellite',24),
(24,2,'Satellite chanels',24),
(24,4,'Satellite chanels',24),
(25,1,'Salle d\'eau',25),
(25,2,'Shower-room',25),
(25,4,'Shower-room',25),
(26,1,'Coin salon',26),
(26,2,'Small lounge',26),
(26,4,'Small lounge',26),
(27,1,'Telephone',27),
(27,2,'Telephone',27),
(27,4,'Telephone',27),
(28,1,'Téléviseur',28),
(28,2,'Television',28),
(28,4,'Television',28),
(29,1,'Terrasse',29),
(29,2,'Terrasse',29),
(29,4,'Terrasse',29),
(30,1,'Machine à laver',30),
(30,2,'Washing machine',30),
(30,4,'Washing machine',30),
(31,1,'Accès handicapés',31),
(31,2,'Wheelchair accessible',31),
(31,4,'Wheelchair accessible',31),
(32,1,'Wi-Fi',31),
(32,2,'WiFi',31),
(32,4,'WiFi',31),
(33,1,'Chaine hifi',32),
(33,2,'Hi-fi system',32),
(33,4,'Hi-fi system',32),
(34,1,'Lecteur DVD',33),
(34,2,'DVD player',33),
(34,4,'DVD player',33),
(35,1,'Ascenceur',34),
(35,2,'Elevator',34),
(35,4,'Elevator',34),
(36,1,'Coin salon',35),
(36,2,'Lounge',35),
(36,4,'Lounge',35),
(37,1,'Restaurant',36),
(37,2,'Restaurant',36),
(37,4,'Restaurant',36),
(38,1,'Service de chambre',37),
(38,2,'Room service',37),
(38,4,'Room service',37),
(39,1,'Vestiaire',38),
(39,2,'Cloakroom',38),
(39,4,'Cloakroom',38);

/*Table structure for table `pm_facility_file` */

DROP TABLE IF EXISTS `pm_facility_file`;

CREATE TABLE `pm_facility_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 1,
  `rank` int(11) DEFAULT 0,
  `file` varchar(250) DEFAULT NULL,
  `label` varchar(250) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `facility_file_fkey` (`id_item`,`lang`),
  KEY `facility_file_lang_fkey` (`lang`),
  CONSTRAINT `facility_file_fkey` FOREIGN KEY (`id_item`, `lang`) REFERENCES `pm_facility` (`id`, `lang`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `facility_file_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

/*Data for the table `pm_facility_file` */

insert  into `pm_facility_file`(`id`,`lang`,`id_item`,`home`,`checked`,`rank`,`file`,`label`,`type`) values 
(1,2,31,0,1,1,'wheelchair.png','','image'),
(1,4,31,0,1,1,'wheelchair.png','','image'),
(2,2,20,0,1,2,'pet-allowed.png','','image'),
(2,4,20,0,1,2,'pet-allowed.png','','image'),
(3,2,21,0,1,3,'pet-not-allowed.png','','image'),
(3,4,21,0,1,3,'pet-not-allowed.png','','image'),
(4,2,3,0,1,4,'balcony.png','','image'),
(4,4,3,0,1,4,'balcony.png','','image'),
(5,2,4,0,1,5,'barbecue.png','','image'),
(5,4,4,0,1,5,'barbecue.png','','image'),
(6,2,8,0,1,6,'desk.png','','image'),
(6,4,8,0,1,6,'desk.png','','image'),
(7,2,6,0,1,7,'coffee.png','','image'),
(7,4,6,0,1,7,'coffee.png','','image'),
(8,2,24,0,1,8,'satellite.png','','image'),
(8,4,24,0,1,8,'satellite.png','','image'),
(9,2,1,0,1,1,'air-conditioning.png','','image'),
(9,4,1,0,1,1,'air-conditioning.png','','image'),
(10,2,23,0,1,10,'safe.png','','image'),
(10,4,23,0,1,10,'safe.png','','image'),
(11,2,26,0,1,11,'lounge.png','','image'),
(11,4,26,0,1,11,'lounge.png','','image'),
(12,2,15,0,1,12,'iron.png','','image'),
(12,4,15,0,1,12,'iron.png','','image'),
(13,2,14,0,1,13,'adsl.png','','image'),
(13,4,14,0,1,13,'adsl.png','','image'),
(14,2,9,0,1,14,'dishwasher.png','','image'),
(14,4,9,0,1,14,'dishwasher.png','','image'),
(15,2,2,0,1,15,'baby-cot.png','','image'),
(15,4,2,0,1,15,'baby-cot.png','','image'),
(16,2,30,0,1,16,'washing-machine.png','','image'),
(16,4,30,0,1,16,'washing-machine.png','','image'),
(17,2,16,0,1,17,'microwaves.png','','image'),
(17,4,16,0,1,17,'microwaves.png','','image'),
(18,2,17,0,1,18,'mini-bar.png','','image'),
(18,4,17,0,1,18,'mini-bar.png','','image'),
(19,2,18,0,1,19,'non-smoking.png','','image'),
(19,4,18,0,1,19,'non-smoking.png','','image'),
(20,2,11,0,1,20,'free-parking.png','','image'),
(20,4,11,0,1,20,'free-parking.png','','image'),
(21,2,19,0,1,21,'paid-parking.png','','image'),
(21,4,19,0,1,21,'paid-parking.png','','image'),
(22,2,7,0,1,22,'cooktop.png','','image'),
(22,4,7,0,1,22,'cooktop.png','','image'),
(23,2,22,0,1,23,'radio.png','','image'),
(23,4,22,0,1,23,'radio.png','','image'),
(24,2,12,0,1,24,'fridge.png','','image'),
(24,4,12,0,1,24,'fridge.png','','image'),
(25,2,25,0,1,25,'shower.png','','image'),
(25,4,25,0,1,25,'shower.png','','image'),
(26,2,5,0,1,26,'bath.png','','image'),
(26,4,5,0,1,26,'bath.png','','image'),
(27,2,13,0,1,27,'hairdryer.png','','image'),
(27,4,13,0,1,27,'hairdryer.png','','image'),
(28,2,27,0,1,28,'phone.png','','image'),
(28,4,27,0,1,28,'phone.png','','image'),
(29,2,28,0,1,29,'tv.png','','image'),
(29,4,28,0,1,29,'tv.png','','image'),
(30,2,29,0,1,30,'terrasse.png','','image'),
(30,4,29,0,1,30,'terrasse.png','','image'),
(31,2,10,0,1,31,'fan.png','','image'),
(31,4,10,0,1,31,'fan.png','','image'),
(32,2,32,0,1,32,'wifi.png','','image'),
(32,4,32,0,1,32,'wifi.png','','image'),
(33,2,33,0,1,33,'hifi.png','','image'),
(33,4,33,0,1,33,'hifi.png','','image'),
(34,2,34,0,1,34,'dvd.png','','image'),
(34,4,34,0,1,34,'dvd.png','','image'),
(35,2,33,0,1,33,'elevator.png','','image'),
(35,4,33,0,1,33,'elevator.png','','image'),
(36,2,33,0,1,33,'lounge.png','','image'),
(36,4,33,0,1,33,'lounge.png','','image'),
(37,2,33,0,1,33,'restaurant.png','','image'),
(37,4,33,0,1,33,'restaurant.png','','image'),
(38,2,33,0,1,33,'room-service.png','','image'),
(38,4,33,0,1,33,'room-service.png','','image'),
(39,2,33,0,1,33,'cloakroom.png','','image'),
(39,4,33,0,1,33,'cloakroom.png','','image');

/*Table structure for table `pm_hotel` */

DROP TABLE IF EXISTS `pm_hotel`;

CREATE TABLE `pm_hotel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `users` text DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `class` int(11) DEFAULT 0,
  `address` varchar(250) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `web` varchar(250) DEFAULT NULL,
  `descr` longtext DEFAULT NULL,
  `facilities` varchar(250) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `id_destination` int(11) DEFAULT NULL,
  `paypal_email` varchar(250) DEFAULT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`lang`),
  KEY `hotel_lang_fkey` (`lang`),
  CONSTRAINT `hotel_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `pm_hotel` */

insert  into `pm_hotel`(`id`,`lang`,`users`,`title`,`subtitle`,`alias`,`class`,`address`,`lat`,`lng`,`email`,`phone`,`web`,`descr`,`facilities`,`tags`,`id_destination`,`paypal_email`,`home`,`checked`,`rank`) values 
(1,1,'1','Royal Hotel','Hôtel luxueux avec vue sur la mer','royal-hotel',4,'',4.455734,73.718185,'contact@pandao.eu','+30 1 0xxx xxxx','','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean aliquet felis massa, sed condimentum ligula feugiat et. Etiam facilisis euismod dignissim. Vivamus facilisis lorem ut purus pellentesque, nec sollicitudin lorem suscipit. Fusce sed enim ultricies, venenatis nunc ut, pharetra nunc. Quisque sollicitudin egestas varius. Nulla aliquet magna sapien, id malesuada felis lobortis id. Vivamus vulputate sed enim sit amet eleifend. Vivamus sit amet felis id urna vulputate maximus. Nullam fringilla sed turpis non volutpat. Cras ultrices diam velit, ac volutpat odio semper at. Sed pulvinar turpis imperdiet sapien hendrerit pulvinar.</p>\r\n','1,11,20,37,32',NULL,NULL,NULL,1,1,1),
(1,2,'1','Royal Hotel','Luxury hotel overlooking the sea','royal-hotel',4,'',4.455734,73.718185,'contact@pandao.eu','+30 1 0xxx xxxx','','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean aliquet felis massa, sed condimentum ligula feugiat et. Etiam facilisis euismod dignissim. Vivamus facilisis lorem ut purus pellentesque, nec sollicitudin lorem suscipit. Fusce sed enim ultricies, venenatis nunc ut, pharetra nunc. Quisque sollicitudin egestas varius. Nulla aliquet magna sapien, id malesuada felis lobortis id. Vivamus vulputate sed enim sit amet eleifend. Vivamus sit amet felis id urna vulputate maximus. Nullam fringilla sed turpis non volutpat. Cras ultrices diam velit, ac volutpat odio semper at. Sed pulvinar turpis imperdiet sapien hendrerit pulvinar.</p>\r\n','1,11,20,37,32',NULL,NULL,NULL,1,1,1),
(1,3,'1','Royal Hotel','فندق فخم يطل على البحر','royal-hotel',4,'',4.455734,73.718185,'contact@pandao.eu','+30 1 0xxx xxxx','','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean aliquet felis massa, sed condimentum ligula feugiat et. Etiam facilisis euismod dignissim. Vivamus facilisis lorem ut purus pellentesque, nec sollicitudin lorem suscipit. Fusce sed enim ultricies, venenatis nunc ut, pharetra nunc. Quisque sollicitudin egestas varius. Nulla aliquet magna sapien, id malesuada felis lobortis id. Vivamus vulputate sed enim sit amet eleifend. Vivamus sit amet felis id urna vulputate maximus. Nullam fringilla sed turpis non volutpat. Cras ultrices diam velit, ac volutpat odio semper at. Sed pulvinar turpis imperdiet sapien hendrerit pulvinar.</p>\r\n','1,11,20,37,32',NULL,NULL,NULL,1,1,1),
(1,4,'1','Royal Hotel','Luxury hotel overlooking the sea','royal-hotel',4,'',4.455734,73.718185,'contact@pandao.eu','+30 1 0xxx xxxx','','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean aliquet felis massa, sed condimentum ligula feugiat et. Etiam facilisis euismod dignissim. Vivamus facilisis lorem ut purus pellentesque, nec sollicitudin lorem suscipit. Fusce sed enim ultricies, venenatis nunc ut, pharetra nunc. Quisque sollicitudin egestas varius. Nulla aliquet magna sapien, id malesuada felis lobortis id. Vivamus vulputate sed enim sit amet eleifend. Vivamus sit amet felis id urna vulputate maximus. Nullam fringilla sed turpis non volutpat. Cras ultrices diam velit, ac volutpat odio semper at. Sed pulvinar turpis imperdiet sapien hendrerit pulvinar.</p>\r\n','1,11,20,37,32',NULL,NULL,NULL,1,1,1),
(2,1,'1','','','',0,'A-30-1,twins tower,damansara height',56,87,'amar.chan9655@gmail.com','111111111','aaa','','1,5,39,8,37,25','',NULL,'amar.chan9655@gmail.com',1,1,2),
(2,2,'1','Excellent','hotel ','hotel',0,'A-30-1,twins tower,damansara height',56,87,'amar.chan9655@gmail.com','111111111','aaa','','1,5,39,8,37,25','',NULL,'amar.chan9655@gmail.com',1,1,2),
(2,3,'1','','','',0,'A-30-1,twins tower,damansara height',56,87,'amar.chan9655@gmail.com','111111111','aaa','','1,5,39,8,37,25','',NULL,'amar.chan9655@gmail.com',1,1,2),
(2,4,'1','Excellent','hotel ','hotel',0,'A-30-1,twins tower,damansara height',56,87,'amar.chan9655@gmail.com','111111111','aaa','','1,5,39,8,37,25','',NULL,'amar.chan9655@gmail.com',1,1,2);

/*Table structure for table `pm_hotel_file` */

DROP TABLE IF EXISTS `pm_hotel_file`;

CREATE TABLE `pm_hotel_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 1,
  `rank` int(11) DEFAULT 0,
  `file` varchar(250) DEFAULT NULL,
  `label` varchar(250) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `hotel_file_fkey` (`id_item`,`lang`),
  KEY `hotel_file_lang_fkey` (`lang`),
  CONSTRAINT `hotel_file_fkey` FOREIGN KEY (`id_item`, `lang`) REFERENCES `pm_hotel` (`id`, `lang`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `hotel_file_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `pm_hotel_file` */

insert  into `pm_hotel_file`(`id`,`lang`,`id_item`,`home`,`checked`,`rank`,`file`,`label`,`type`) values 
(1,1,1,0,1,1,'5555048217-1389b680d6-o.jpg','','image'),
(1,2,1,0,1,1,'5555048217-1389b680d6-o.jpg','','image'),
(1,3,1,0,1,1,'5555048217-1389b680d6-o.jpg','','image'),
(1,4,1,0,1,1,'5555048217-1389b680d6-o.jpg','','image'),
(2,1,2,NULL,1,2,'31-048-sp1.jpg','','image'),
(2,2,2,NULL,1,2,'31-048-sp1.jpg','','image'),
(2,3,2,NULL,1,2,'31-048-sp1.jpg','','image'),
(2,4,2,NULL,1,2,'31-048-sp1.jpg','','image');

/*Table structure for table `pm_ical_event` */

DROP TABLE IF EXISTS `pm_ical_event`;

CREATE TABLE `pm_ical_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_room` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `sync_date` int(11) DEFAULT NULL,
  `from_date` int(11) DEFAULT NULL,
  `to_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ical_event_fkey` (`id_room`),
  CONSTRAINT `ical_event_fkey` FOREIGN KEY (`id_room`) REFERENCES `pm_room` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_ical_event` */

/*Table structure for table `pm_lang` */

DROP TABLE IF EXISTS `pm_lang`;

CREATE TABLE `pm_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `locale` varchar(20) DEFAULT NULL,
  `main` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  `tag` varchar(20) DEFAULT NULL,
  `rtl` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `pm_lang` */

insert  into `pm_lang`(`id`,`title`,`locale`,`main`,`checked`,`rank`,`tag`,`rtl`) values 
(1,'Français','fr_FR',0,1,2,'fr',0),
(2,'English','en_GB',1,1,1,'en',0),
(3,'عربي','ar_MA',0,1,3,'ar',1),
(4,'española','es_es',0,1,4,'es',0);

/*Table structure for table `pm_lang_file` */

DROP TABLE IF EXISTS `pm_lang_file`;

CREATE TABLE `pm_lang_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 1,
  `rank` int(11) DEFAULT 0,
  `file` varchar(250) DEFAULT NULL,
  `label` varchar(250) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lang_file_fkey` (`id_item`),
  CONSTRAINT `lang_file_fkey` FOREIGN KEY (`id_item`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `pm_lang_file` */

insert  into `pm_lang_file`(`id`,`id_item`,`home`,`checked`,`rank`,`file`,`label`,`type`) values 
(1,1,0,1,2,'fr.png','','image'),
(2,2,0,1,1,'gb.png','','image'),
(3,3,0,1,3,'ar.png','','image'),
(4,4,NULL,1,4,'flag.png',NULL,'image');

/*Table structure for table `pm_location` */

DROP TABLE IF EXISTS `pm_location`;

CREATE TABLE `pm_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL,
  `checked` int(11) DEFAULT 0,
  `pages` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `pm_location` */

insert  into `pm_location`(`id`,`name`,`address`,`lat`,`lng`,`checked`,`pages`) values 
(1,'Panda Multi Resorts','Maldives Mint, Neeloafaru Magu 20014, Maldives',4.174411,73.517851,1,'2');

/*Table structure for table `pm_media` */

DROP TABLE IF EXISTS `pm_media`;

CREATE TABLE `pm_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_media` */

/*Table structure for table `pm_media_file` */

DROP TABLE IF EXISTS `pm_media_file`;

CREATE TABLE `pm_media_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 1,
  `rank` int(11) DEFAULT 0,
  `file` varchar(250) DEFAULT NULL,
  `label` varchar(250) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_file_fkey` (`id_item`),
  CONSTRAINT `media_file_fkey` FOREIGN KEY (`id_item`) REFERENCES `pm_media` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_media_file` */

/*Table structure for table `pm_menu` */

DROP TABLE IF EXISTS `pm_menu`;

CREATE TABLE `pm_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `id_parent` int(11) DEFAULT NULL,
  `item_type` varchar(30) DEFAULT NULL,
  `id_item` int(11) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `main` int(11) DEFAULT 1,
  `footer` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`lang`),
  KEY `menu_lang_fkey` (`lang`),
  CONSTRAINT `menu_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `pm_menu` */

insert  into `pm_menu`(`id`,`lang`,`name`,`title`,`id_parent`,`item_type`,`id_item`,`url`,`main`,`footer`,`checked`,`rank`) values 
(1,1,'Accueil','Lorem ipsum dolor sit amet',NULL,'page',1,NULL,1,0,1,1),
(1,2,'Home','Panda Multi Resorts, Luxury Hotels',NULL,'page',1,NULL,1,0,1,1),
(1,3,'ترحيب','هو سقطت الساحلية ذات, أن.',NULL,'page',1,NULL,1,0,1,1),
(1,4,'Home','Panda Multi Resorts, Luxury Hotels',NULL,'page',1,NULL,1,0,1,1),
(2,1,'Contact','Contact',NULL,'page',2,NULL,1,1,1,9),
(2,2,'Contact','Contact',NULL,'page',2,NULL,1,1,1,9),
(2,3,'جهة الاتصال','جهة الاتصال',NULL,'page',2,NULL,1,1,1,9),
(2,4,'Contact','Contact',NULL,'page',2,NULL,1,1,1,9),
(3,1,'Mentions légales','Mentions légales',NULL,'page',3,NULL,0,1,1,10),
(3,2,'Legal notices','Legal notices',NULL,'page',3,NULL,0,1,1,10),
(3,3,'يذكر القانونية','يذكر القانونية',NULL,'page',3,NULL,0,1,1,10),
(3,4,'Legal notices','Legal notices',NULL,'page',3,NULL,0,1,1,10),
(4,1,'Plan du site','Plan du site',NULL,'page',4,NULL,0,1,1,11),
(4,2,'Sitemap','Sitemap',NULL,'page',4,NULL,0,1,1,11),
(4,3,'خريطة الموقع','خريطة الموقع',NULL,'page',4,NULL,0,1,1,11),
(4,4,'Sitemap','Sitemap',NULL,'page',4,NULL,0,1,1,11),
(5,1,'Qui sommes-nous ?','Qui sommes-nous ?',NULL,'page',5,NULL,1,0,1,2),
(5,2,'About us','About us',NULL,'page',5,NULL,1,0,1,2),
(5,3,'معلومات عنا','معلومات عنا',NULL,'page',5,NULL,1,0,1,2),
(5,4,'About us','About us',NULL,'page',5,NULL,1,0,1,2),
(6,1,'Galerie','Galerie',NULL,'page',7,NULL,1,0,1,4),
(6,2,'Gallery','Gallery',NULL,'page',7,NULL,1,0,1,4),
(6,3,'صور معرض','صور معرض',NULL,'page',7,NULL,1,0,1,4),
(6,4,'Gallery','Gallery',NULL,'page',7,NULL,1,0,1,4),
(7,1,'Hôtels','Hôtels',NULL,'page',9,NULL,1,0,1,3),
(7,2,'Hotels','Hotels',NULL,'page',9,NULL,1,0,1,3),
(7,3,'الفنادق','الفنادق',NULL,'page',9,NULL,1,0,1,3),
(7,4,'Hotels','Hotels',NULL,'page',9,NULL,1,0,1,3),
(8,1,'Réserver','Réserver',NULL,'page',10,NULL,1,0,1,5),
(8,2,'Booking','Booking',NULL,'page',10,NULL,1,0,1,5),
(8,3,'الحجز','الحجز',NULL,'page',10,NULL,1,0,1,5),
(8,4,'Booking','Booking',NULL,'page',10,NULL,1,0,1,5),
(9,1,'Activités','Activités',NULL,'page',16,NULL,1,0,1,4),
(9,2,'Activities','Activities',NULL,'page',16,NULL,1,0,1,4),
(9,3,'Activities','Activities',NULL,'page',16,NULL,1,0,1,4),
(9,4,'Activities','Activities',NULL,'page',16,NULL,1,0,1,4),
(10,1,'Destinations','',NULL,'page',18,'',1,0,1,4),
(10,2,'Destinations','',NULL,'page',18,'',1,0,1,4),
(10,3,'وجهات','',NULL,'page',18,'',1,0,1,4),
(10,4,'Destinations','',NULL,'page',18,'',1,0,1,4);

/*Table structure for table `pm_message` */

DROP TABLE IF EXISTS `pm_message`;

CREATE TABLE `pm_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `add_date` int(11) DEFAULT NULL,
  `edit_date` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `subject` varchar(250) DEFAULT NULL,
  `msg` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_message` */

/*Table structure for table `pm_package` */

DROP TABLE IF EXISTS `pm_package`;

CREATE TABLE `pm_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users` text DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `days` text DEFAULT NULL,
  `min_nights` int(11) DEFAULT NULL,
  `max_nights` int(11) DEFAULT NULL,
  `day_start` int(11) DEFAULT NULL,
  `day_end` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `pm_package` */

insert  into `pm_package`(`id`,`users`,`name`,`days`,`min_nights`,`max_nights`,`day_start`,`day_end`) values 
(1,'1','Week-end','5,6,7',0,0,NULL,NULL),
(2,'1','Night','1,2,3,4,5,6,7',0,0,NULL,NULL),
(3,'1','Mid-week','1,2,3,4,5',3,4,NULL,NULL),
(4,'1','2 nights','1,2,3,4',2,2,NULL,NULL),
(6,'1','Week','1,2,3,4,5,6,7',7,0,NULL,NULL);

/*Table structure for table `pm_page` */

DROP TABLE IF EXISTS `pm_page`;

CREATE TABLE `pm_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `title_tag` varchar(250) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `descr` longtext DEFAULT NULL,
  `robots` varchar(20) DEFAULT NULL,
  `keywords` varchar(250) DEFAULT NULL,
  `intro` longtext DEFAULT NULL,
  `text` longtext DEFAULT NULL,
  `id_parent` int(11) DEFAULT NULL,
  `page_model` varchar(50) DEFAULT NULL,
  `article_model` varchar(50) DEFAULT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  `add_date` int(11) DEFAULT NULL,
  `edit_date` int(11) DEFAULT NULL,
  `comment` int(11) DEFAULT 0,
  `rating` int(11) DEFAULT 0,
  `system` int(11) DEFAULT 0,
  `show_langs` text DEFAULT NULL,
  `hide_langs` text DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `page_lang_fkey` (`lang`),
  CONSTRAINT `page_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Data for the table `pm_page` */

insert  into `pm_page`(`id`,`lang`,`name`,`title`,`subtitle`,`title_tag`,`alias`,`descr`,`robots`,`keywords`,`intro`,`text`,`id_parent`,`page_model`,`article_model`,`home`,`checked`,`rank`,`add_date`,`edit_date`,`comment`,`rating`,`system`,`show_langs`,`hide_langs`) values 
(1,1,'Accueil','Lorem ipsum dolor sit amet','Consectetur adipiscing elit','Accueil','','','index,follow','','','',NULL,'home','',1,1,1,1596295561,1596295644,0,0,0,'',''),
(1,2,'Home','Panda Multi Resorts, Luxury Hotels','','Panda Multi Resorts, web software to create and manage multi hotels platforms','','','index,follow','','','<blockquote class=\"text-center\">\r\n<p>A man travels the world over in search of what he needs and returns home to find it.</p>\r\n</blockquote>\r\n\r\n<p class=\"text-muted\" style=\"text-align: center;\">- George A. Moore -</p>\r\n',NULL,'home','',1,1,1,1596295561,1596295644,0,0,0,'',''),
(1,3,'ترحيب','هو سقطت الساحلية ذات, أن.','غير بمعارضة وهولندا، الإقتصادية قد, فقد الفرنسي المعاهدات قد من.','ترحيب','','','index,follow','','','',NULL,'home','',1,1,1,1596295561,1596295644,0,0,0,'',''),
(1,4,'Home','Panda Multi Resorts, Luxury Hotels','','Panda Multi Resorts, web software to create and manage multi hotels platforms','','','index,follow','','','<blockquote class=\"text-center\">\r\n<p>A man travels the world over in search of what he needs and returns home to find it.</p>\r\n</blockquote>\r\n\r\n<p class=\"text-muted\" style=\"text-align: center;\">- George A. Moore -</p>\r\n',NULL,'home','',1,1,1,1596295561,1596295644,0,0,0,'',''),
(2,1,'Contact','Contact','','Contact','contact','','index,follow','','','',NULL,'contact','',0,1,11,0,0,0,0,0,NULL,NULL),
(2,2,'Contact','Contact','','Contact','contact','','index,follow','','','',NULL,'contact','',0,1,11,0,0,0,0,0,NULL,NULL),
(2,3,'جهة الاتصال','جهة الاتصال','','جهة الاتصال','contact','','index,follow','','','',NULL,'contact','',0,1,11,0,0,0,0,0,NULL,NULL),
(2,4,'Contact','Contact','','Contact','contact','','index,follow','','','',NULL,'contact','',0,1,11,0,0,0,0,0,NULL,NULL),
(3,1,'Mentions légales','Mentions légales','','Mentions légales','mentions-legales','','index,follow','','','',NULL,'page','',0,1,12,0,0,0,0,0,NULL,NULL),
(3,2,'Legal notices','Legal notices','','Legal notices','legal-notices','','index,follow','','','',NULL,'page','',0,1,12,0,0,0,0,0,NULL,NULL),
(3,3,'يذكر القانونية','يذكر القانونية','','يذكر القانونية','legal-notices','','index,follow','','','',NULL,'page','',0,1,12,0,0,0,0,0,NULL,NULL),
(3,4,'Legal notices','Legal notices','','Legal notices','legal-notices','','index,follow','','','',NULL,'page','',0,1,12,0,0,0,0,0,NULL,NULL),
(4,1,'Plan du site','Plan du site','','Plan du site','plan-site','','index,follow','','','',NULL,'sitemap','',0,1,13,0,0,0,0,0,NULL,NULL),
(4,2,'Sitemap','Sitemap','','Sitemap','sitemap','','index,follow','','','',NULL,'sitemap','',0,1,13,0,0,0,0,0,NULL,NULL),
(4,3,'خريطة الموقع','خريطة الموقع','','خريطة الموقع','sitemap','','index,follow','','','',NULL,'sitemap','',0,1,13,0,0,0,0,0,NULL,NULL),
(4,4,'Sitemap','Sitemap','','Sitemap','sitemap','','index,follow','','','',NULL,'sitemap','',0,1,13,0,0,0,0,0,NULL,NULL),
(5,1,'Qui sommes-nous ?','Qui sommes-nous ?','','Qui sommes-nous ?','qui-sommes-nous','','index,follow','','','',NULL,'page','article',0,1,2,0,0,0,0,0,NULL,NULL),
(5,2,'About us','About us','','About us','about-us','','index,follow','','','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque fringilla vel est at rhoncus. Cras porttitor ligula vel magna vehicula accumsan. Mauris eget elit et sem commodo interdum. Aenean dolor sem, tincidunt ac neque tempus, hendrerit blandit lacus. Vivamus placerat nulla in mi tristique, fringilla fermentum nisl vehicula. Nullam quis eros non magna tincidunt interdum ac eu eros. Morbi malesuada pulvinar ultrices. Etiam bibendum efficitur risus, sit amet venenatis urna ullamcorper non. Proin fermentum malesuada tortor, vitae mattis sem scelerisque in. Curabitur rutrum leo at mi efficitur suscipit. Vivamus tristique lorem eros, sit amet malesuada augue sodales sed.</p>\r\n',NULL,'page','article',0,1,2,0,0,0,0,0,NULL,NULL),
(5,3,'معلومات عنا','معلومات عنا','','معلومات عنا','about us','','index,follow','','','',NULL,'page','article',0,1,2,0,0,0,0,0,NULL,NULL),
(5,4,'About us','About us','','About us','about-us','','index,follow','','','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque fringilla vel est at rhoncus. Cras porttitor ligula vel magna vehicula accumsan. Mauris eget elit et sem commodo interdum. Aenean dolor sem, tincidunt ac neque tempus, hendrerit blandit lacus. Vivamus placerat nulla in mi tristique, fringilla fermentum nisl vehicula. Nullam quis eros non magna tincidunt interdum ac eu eros. Morbi malesuada pulvinar ultrices. Etiam bibendum efficitur risus, sit amet venenatis urna ullamcorper non. Proin fermentum malesuada tortor, vitae mattis sem scelerisque in. Curabitur rutrum leo at mi efficitur suscipit. Vivamus tristique lorem eros, sit amet malesuada augue sodales sed.</p>\r\n',NULL,'page','article',0,1,2,0,0,0,0,0,NULL,NULL),
(6,1,'Recherche','Recherche','','Recherche','search','','noindex,nofollow','','','',NULL,'search','',0,1,14,0,0,0,0,1,NULL,NULL),
(6,2,'Search','Search','','Search','search','','noindex,nofollow','','','',NULL,'search','',0,1,14,0,0,0,0,1,NULL,NULL),
(6,3,'بحث','بحث','','بحث','search','','noindex,nofollow','','','',NULL,'search','',0,1,14,0,0,0,0,1,NULL,NULL),
(6,4,'Search','Search','','Search','search','','noindex,nofollow','','','',NULL,'search','',0,1,14,0,0,0,0,1,NULL,NULL),
(7,1,'Galerie','Galerie','','Galerie','galerie','','index,follow','','','',NULL,'page','gallery',0,1,5,0,0,0,0,0,NULL,NULL),
(7,2,'Gallery','Gallery','','Gallery','gallery','','index,follow','','','',NULL,'page','gallery',0,1,5,0,0,0,0,0,NULL,NULL),
(7,3,'صور معرض','صور معرض','','صور معرض','gallery','','index,follow','','','',NULL,'page','gallery',0,1,5,0,0,0,0,0,NULL,NULL),
(7,4,'Gallery','Gallery','','Gallery','gallery','','index,follow','','','',NULL,'page','gallery',0,1,5,0,0,0,0,0,NULL,NULL),
(8,1,'404','Erreur 404 : Page introuvable !','','404 Page introuvable','404','','noindex,nofollow','','','<p>L\'URL demandée n\'a pas été trouvée sur ce serveur.<br />\r\nLa page que vous voulez afficher n\'existe pas, ou est temporairement indisponible.</p>\r\n\r\n<p>Merci d\'essayer les actions suivantes :</p>\r\n\r\n<ul>\r\n    <li>Assurez-vous que l\'URL dans la barre d\'adresse de votre navigateur est correctement orthographiée et formatée.</li>\r\n    <li>Si vous avez atteint cette page en cliquant sur un lien ou si vous pensez que cela concerne une erreur du serveur, contactez l\'administrateur pour l\'alerter.</li>\r\n</ul>\r\n',NULL,'404','',0,1,15,0,0,0,0,1,NULL,NULL),
(8,2,'404','404 Error: Page not found!','','404 Not Found','404','','noindex,nofollow','','','<p>The wanted URL was not found on this server.<br />\r\nThe page you wish to display does not exist, or is temporarily unavailable.</p>\r\n\r\n<p>Thank you for trying the following actions :</p>\r\n\r\n<ul>\r\n    <li>Be sure the URL in the address bar of your browser is correctly spelt and formated.</li>\r\n    <li>If you reached this page by clicking a link or if you think that it is about an error of the server, contact the administrator to alert him.</li>\r\n</ul>\r\n',NULL,'404','',0,1,15,0,0,0,0,1,NULL,NULL),
(8,3,'404','404 Error: Page not found!','','404 Not Found','404','','noindex,nofollow','','','',NULL,'404','',0,1,15,0,0,0,0,1,NULL,NULL),
(8,4,'404','404 Error: Page not found!','','404 Not Found','404','','noindex,nofollow','','','<p>The wanted URL was not found on this server.<br />\r\nThe page you wish to display does not exist, or is temporarily unavailable.</p>\r\n\r\n<p>Thank you for trying the following actions :</p>\r\n\r\n<ul>\r\n    <li>Be sure the URL in the address bar of your browser is correctly spelt and formated.</li>\r\n    <li>If you reached this page by clicking a link or if you think that it is about an error of the server, contact the administrator to alert him.</li>\r\n</ul>\r\n',NULL,'404','',0,1,15,0,0,0,0,1,NULL,NULL),
(9,1,'Hôtels','Hôtels','','Hôtels','hotels','','index,follow','','','',NULL,'hotels','hotel',0,1,3,0,0,0,0,1,NULL,NULL),
(9,2,'Hotels','Hotels','','Hotels','hotels','','index,follow','','','',NULL,'hotels','hotel',0,1,3,0,0,0,0,1,NULL,NULL),
(9,3,'الفنادق','الفنادق','','الفنادق','hotels','','index,follow','','','',NULL,'hotels','hotel',0,1,3,0,0,0,0,1,NULL,NULL),
(9,4,'Hotels','Hotels','','Hotels','hotels','','index,follow','','','',NULL,'hotels','hotel',0,1,3,0,0,0,0,1,NULL,NULL),
(10,1,'Réserver','Réserver','','Réserver','reserver','','index,nofollow','','','',NULL,'booking','booking',0,1,6,0,0,0,0,1,NULL,NULL),
(10,2,'Booking','Booking','','Booking','booking','','index,nofollow','','','',NULL,'booking','booking',0,1,6,0,0,0,0,1,NULL,NULL),
(10,3,'الحجز','الحجز','','الحجز','booking','','index,nofollow','','','',NULL,'booking','booking',0,1,6,0,0,0,0,1,NULL,NULL),
(10,4,'Booking','Booking','','Booking','booking','','index,nofollow','','','',NULL,'booking','booking',0,1,6,0,0,0,0,1,NULL,NULL),
(11,1,'Coordonnées','Coordonnées','','Coordonnées','coordonnees','','noindex,nofollow','','','',10,'details','',0,1,8,0,0,0,0,1,NULL,NULL),
(11,2,'Details','Booking details','','Booking details','booking-details','','noindex,nofollow','','','',10,'details','',0,1,8,0,0,0,0,1,NULL,NULL),
(11,3,'تفاصيل الحجز','تفاصيل الحجز','','تفاصيل الحجز','booking-details','','noindex,nofollow','','','',10,'details','',0,1,8,0,0,0,0,1,NULL,NULL),
(11,4,'Details','Booking details','','Booking details','booking-details','','noindex,nofollow','','','',10,'details','',0,1,8,0,0,0,0,1,NULL,NULL),
(12,1,'Paiement','Paiement','','Paiement','paiement','','noindex,nofollow','','','',13,'payment','',0,1,10,0,0,0,0,1,NULL,NULL),
(12,2,'Payment','Payment','','Payment','payment','','noindex,nofollow','','','',13,'payment','',0,1,10,0,0,0,0,1,NULL,NULL),
(12,3,'دفع','دفع','','دفع','payment','','noindex,nofollow','','','',13,'payment','',0,1,10,0,0,0,0,1,NULL,NULL),
(12,4,'Payment','Payment','','Payment','payment','','noindex,nofollow','','','',13,'payment','',0,1,10,0,0,0,0,1,NULL,NULL),
(13,1,'Résumé de la réservation','Résumé de la réservation','','Résumé de la réservation','resume-reservation','','noindex,nofollow','','','',11,'summary','',0,1,9,0,0,0,0,1,NULL,NULL),
(13,2,'Summary','Booking summary','','Booking summary','booking-summary','','noindex,nofollow','','','',11,'summary','',0,1,9,0,0,0,0,1,NULL,NULL),
(13,3,'ملخص الحجز','ملخص الحجز','','ملخص الحجز','booking-summary','','noindex,nofollow','','','',11,'summary','',0,1,9,0,0,0,0,1,NULL,NULL),
(13,4,'Summary','Booking summary','','Booking summary','booking-summary','','noindex,nofollow','','','',11,'summary','',0,1,9,0,0,0,0,1,NULL,NULL),
(14,1,'Espace client','Espace client','','Espace client','espace-client','','noindex,nofollow','','','',NULL,'account','',0,1,16,0,0,0,0,1,NULL,NULL),
(14,2,'Account','Account','','Account','account','','noindex,nofollow','','','',NULL,'account','',0,1,16,0,0,0,0,1,NULL,NULL),
(14,3,'Account','Account','','Account','account','','noindex,nofollow','','','',NULL,'account','',0,1,16,0,0,0,0,1,NULL,NULL),
(14,4,'Account','Account','','Account','account','','noindex,nofollow','','','',NULL,'account','',0,1,16,0,0,0,0,1,NULL,NULL),
(15,1,'Activités','Activités','','Activités','reservation-activitees','','noindex,nofollow','','','',10,'booking-activities','',0,1,7,0,0,0,0,1,NULL,NULL),
(15,2,'Activities','Activities','','Activities','booking-activities','','noindex,nofollow','','','',10,'booking-activities','',0,1,7,0,0,0,0,1,NULL,NULL),
(15,3,'Activities','Activities','','Activities','booking-activities','','noindex,nofollow','','','',10,'booking-activities','',0,1,7,0,0,0,0,1,NULL,NULL),
(15,4,'Activities','Activities','','Activities','booking-activities','','noindex,nofollow','','','',10,'booking-activities','',0,1,7,0,0,0,0,1,NULL,NULL),
(16,1,'Activités','Activités','','Activités','activitees','','index,follow','','','',NULL,'activities','activity',0,1,4,0,0,0,0,1,NULL,NULL),
(16,2,'Activities','Activities','','Activities','activities','','index,follow','','','',NULL,'activities','activity',0,1,4,0,0,0,0,1,NULL,NULL),
(16,3,'Activities','Activities','','Activities','activities','','index,follow','','','',NULL,'activities','activity',0,1,4,0,0,0,0,1,NULL,NULL),
(16,4,'Activities','Activities','','Activities','activities','','index,follow','','','',NULL,'activities','activity',0,1,4,0,0,0,0,1,NULL,NULL),
(17,1,'Blog','Blog','','Blog','blog','','index,follow','','','',NULL,'blog','article-blog',0,1,12,0,0,0,0,0,NULL,NULL),
(17,2,'Blog','Blog','','Blog','blog','','index,follow','','','',NULL,'blog','article-blog',0,1,12,0,0,0,0,0,NULL,NULL),
(17,3,'مدونة','مدونة','','مدونة','blog','','index,follow','','','',NULL,'blog','article-blog',0,1,12,0,0,0,0,0,NULL,NULL),
(17,4,'Blog','Blog','','Blog','blog','','index,follow','','','',NULL,'blog','article-blog',0,1,12,0,0,0,0,0,NULL,NULL),
(18,1,'Destinations','Destinations','','Destinations','destinations','','index,follow','','','',NULL,'destinations','',0,1,7,0,0,0,0,1,NULL,NULL),
(18,2,'Destinations','Destinations','','Destinations','destinations','','index,follow','','','',NULL,'destinations','',0,1,7,0,0,0,0,1,NULL,NULL),
(18,3,'وجهات','وجهات','','وجهات','destinations','','index,follow','','','',NULL,'destinations','',0,1,7,0,0,0,0,1,NULL,NULL),
(18,4,'Destinations','Destinations','','Destinations','destinations','','index,follow','','','',NULL,'destinations','',0,1,7,0,0,0,0,1,NULL,NULL);

/*Table structure for table `pm_page_file` */

DROP TABLE IF EXISTS `pm_page_file`;

CREATE TABLE `pm_page_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 1,
  `rank` int(11) DEFAULT 0,
  `file` varchar(250) DEFAULT NULL,
  `label` varchar(250) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `page_file_fkey` (`id_item`,`lang`),
  KEY `page_file_lang_fkey` (`lang`),
  CONSTRAINT `page_file_fkey` FOREIGN KEY (`id_item`, `lang`) REFERENCES `pm_page` (`id`, `lang`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `page_file_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_page_file` */

/*Table structure for table `pm_popup` */

DROP TABLE IF EXISTS `pm_popup`;

CREATE TABLE `pm_popup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `title` varchar(250) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `allpages` text DEFAULT NULL,
  `pages` text DEFAULT NULL,
  `background` varchar(20) DEFAULT NULL,
  `checked` int(11) DEFAULT 0,
  `publish_date` int(11) DEFAULT NULL,
  `unpublish_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `popup_lang_fkey` (`lang`),
  CONSTRAINT `popup_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `pm_popup` */

insert  into `pm_popup`(`id`,`lang`,`title`,`content`,`allpages`,`pages`,`background`,`checked`,`publish_date`,`unpublish_date`) values 
(1,1,'','','0','1','',1,1596363960,NULL),
(1,2,'COVID','<p><span style=\"font-size:9px;\"><img alt=\"Kenilworth Hotel, Kolkata, India - Booking.com\" data-noaft=\"1\" jsaction=\"load:XAeZkd,gvK6lb;\" jsname=\"HiaYvf\" src=\"https://q-cf.bstatic.com/images/hotel/max1024x768/214/214429380.jpg\" /></span></p>\r\n','0','1','',1,1596363960,NULL),
(1,3,'','','0','1','',1,1596363960,NULL),
(1,4,'','','0','1','',1,1596363960,NULL);

/*Table structure for table `pm_rate` */

DROP TABLE IF EXISTS `pm_rate`;

CREATE TABLE `pm_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_room` int(11) NOT NULL,
  `id_hotel` int(11) DEFAULT NULL,
  `id_package` int(11) NOT NULL,
  `users` text DEFAULT NULL,
  `start_date` int(11) DEFAULT NULL,
  `end_date` int(11) DEFAULT NULL,
  `price` double DEFAULT 0,
  `child_price` double DEFAULT 0,
  `discount` double DEFAULT 0,
  `discount_type` varchar(10) DEFAULT 'rate',
  `people` int(11) DEFAULT NULL,
  `price_sup` double DEFAULT NULL,
  `fixed_sup` double DEFAULT NULL,
  `id_tax` int(11) DEFAULT NULL,
  `taxes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rate_room_fkey` (`id_room`),
  CONSTRAINT `rate_room_fkey` FOREIGN KEY (`id_room`) REFERENCES `pm_room` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_rate` */

/*Table structure for table `pm_rate_child` */

DROP TABLE IF EXISTS `pm_rate_child`;

CREATE TABLE `pm_rate_child` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_rate` int(11) NOT NULL,
  `min_age` int(11) DEFAULT NULL,
  `max_age` int(11) DEFAULT NULL,
  `price` double DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `rate_child_fkey` (`id_rate`),
  CONSTRAINT `rate_child_fkey` FOREIGN KEY (`id_rate`) REFERENCES `pm_rate` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_rate_child` */

/*Table structure for table `pm_room` */

DROP TABLE IF EXISTS `pm_room`;

CREATE TABLE `pm_room` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `id_hotel` int(11) NOT NULL,
  `users` text DEFAULT NULL,
  `max_children` int(11) DEFAULT 1,
  `max_adults` int(11) DEFAULT 1,
  `max_people` int(11) DEFAULT NULL,
  `min_people` int(11) DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `subtitle` varchar(250) DEFAULT NULL,
  `alias` varchar(100) DEFAULT NULL,
  `descr` longtext DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `stock` int(11) DEFAULT 1,
  `price` double DEFAULT 0,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  `start_lock` int(11) DEFAULT NULL,
  `end_lock` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `room_lang_fkey` (`lang`),
  KEY `room_hotel_fkey` (`id_hotel`,`lang`),
  CONSTRAINT `room_hotel_fkey` FOREIGN KEY (`id_hotel`, `lang`) REFERENCES `pm_hotel` (`id`, `lang`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `room_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `pm_room` */

insert  into `pm_room`(`id`,`lang`,`id_hotel`,`users`,`max_children`,`max_adults`,`max_people`,`min_people`,`title`,`subtitle`,`alias`,`descr`,`facilities`,`stock`,`price`,`home`,`checked`,`rank`,`start_lock`,`end_lock`) values 
(1,1,1,'1',2,2,2,1,'Chambre Double Deluxe','Petit-déjeuner inclus','chambre-double-deluxe','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut eleifend diam. Etiam molestie quam at nunc tempus, ac porttitor ante rutrum. Donec ipsum orci, molestie sit amet nibh a, accumsan varius nisl. Suspendisse blandit efficitur interdum. Nulla auctor tortor eu volutpat imperdiet. Nam at tempus sapien, sit amet porttitor neque. Nam lacinia ex libero, vel egestas ante vehicula nec.</p>\r\n\r\n<p>Sed sed dignissim est. Donec egestas nisl eu congue rhoncus. Nulla finibus malesuada mauris, et pellentesque diam scelerisque non. Duis auctor dapibus augue sed malesuada. Nam placerat at libero quis aliquam. Phasellus quam orci, dapibus sit amet finibus a, convallis volutpat arcu. Nullam condimentum quam id urna scelerisque varius. Duis a metus metus.</p>\r\n','1,5,11,13,17,18,21,23,24,25,27,28,29,32',4,145,1,1,1,NULL,NULL),
(1,2,1,'1',2,2,2,1,'Deluxe Double Bedroom','Breakfast included','deluxe-double-bedroom','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut eleifend diam. Etiam molestie quam at nunc tempus, ac porttitor ante rutrum. Donec ipsum orci, molestie sit amet nibh a, accumsan varius nisl. Suspendisse blandit efficitur interdum. Nulla auctor tortor eu volutpat imperdiet. Nam at tempus sapien, sit amet porttitor neque. Nam lacinia ex libero, vel egestas ante vehicula nec.</p>\r\n\r\n<p>Sed sed dignissim est. Donec egestas nisl eu congue rhoncus. Nulla finibus malesuada mauris, et pellentesque diam scelerisque non. Duis auctor dapibus augue sed malesuada. Nam placerat at libero quis aliquam. Phasellus quam orci, dapibus sit amet finibus a, convallis volutpat arcu. Nullam condimentum quam id urna scelerisque varius. Duis a metus metus.</p>\r\n','1,5,11,13,17,18,21,23,24,25,27,28,29,32',4,145,1,1,1,NULL,NULL),
(1,3,1,'1',2,2,2,1,'Deluxe Double Bedroom','Breakfast included','deluxe-double-bedroom','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut eleifend diam. Etiam molestie quam at nunc tempus, ac porttitor ante rutrum. Donec ipsum orci, molestie sit amet nibh a, accumsan varius nisl. Suspendisse blandit efficitur interdum. Nulla auctor tortor eu volutpat imperdiet. Nam at tempus sapien, sit amet porttitor neque. Nam lacinia ex libero, vel egestas ante vehicula nec.</p>\r\n\r\n<p>Sed sed dignissim est. Donec egestas nisl eu congue rhoncus. Nulla finibus malesuada mauris, et pellentesque diam scelerisque non. Duis auctor dapibus augue sed malesuada. Nam placerat at libero quis aliquam. Phasellus quam orci, dapibus sit amet finibus a, convallis volutpat arcu. Nullam condimentum quam id urna scelerisque varius. Duis a metus metus.</p>\r\n','1,5,11,13,17,18,21,23,24,25,27,28,29,32',4,145,1,1,1,NULL,NULL),
(1,4,1,'1',2,2,2,1,'Deluxe Double Bedroom','Breakfast included','deluxe-double-bedroom','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ut eleifend diam. Etiam molestie quam at nunc tempus, ac porttitor ante rutrum. Donec ipsum orci, molestie sit amet nibh a, accumsan varius nisl. Suspendisse blandit efficitur interdum. Nulla auctor tortor eu volutpat imperdiet. Nam at tempus sapien, sit amet porttitor neque. Nam lacinia ex libero, vel egestas ante vehicula nec.</p>\r\n\r\n<p>Sed sed dignissim est. Donec egestas nisl eu congue rhoncus. Nulla finibus malesuada mauris, et pellentesque diam scelerisque non. Duis auctor dapibus augue sed malesuada. Nam placerat at libero quis aliquam. Phasellus quam orci, dapibus sit amet finibus a, convallis volutpat arcu. Nullam condimentum quam id urna scelerisque varius. Duis a metus metus.</p>\r\n','1,5,11,13,17,18,21,23,24,25,27,28,29,32',4,145,1,1,1,NULL,NULL);

/*Table structure for table `pm_room_calendar` */

DROP TABLE IF EXISTS `pm_room_calendar`;

CREATE TABLE `pm_room_calendar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_room` int(11) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `latest_sync` int(11) DEFAULT NULL,
  `url` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `room_calendar_fkey` (`id_room`),
  CONSTRAINT `room_calendar_fkey` FOREIGN KEY (`id_room`) REFERENCES `pm_room` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_room_calendar` */

/*Table structure for table `pm_room_closing` */

DROP TABLE IF EXISTS `pm_room_closing`;

CREATE TABLE `pm_room_closing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_room` int(11) NOT NULL,
  `from_date` int(11) DEFAULT NULL,
  `to_date` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `room_closing_fkey` (`id_room`),
  CONSTRAINT `room_closing_fkey` FOREIGN KEY (`id_room`) REFERENCES `pm_room` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `pm_room_closing` */

/*Table structure for table `pm_room_file` */

DROP TABLE IF EXISTS `pm_room_file`;

CREATE TABLE `pm_room_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 1,
  `rank` int(11) DEFAULT 0,
  `file` varchar(250) DEFAULT NULL,
  `label` varchar(250) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `room_file_fkey` (`id_item`,`lang`),
  KEY `room_file_lang_fkey` (`lang`),
  CONSTRAINT `room_file_fkey` FOREIGN KEY (`id_item`, `lang`) REFERENCES `pm_room` (`id`, `lang`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `room_file_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `pm_room_file` */

insert  into `pm_room_file`(`id`,`lang`,`id_item`,`home`,`checked`,`rank`,`file`,`label`,`type`) values 
(1,1,1,0,1,1,'deluxe-double-room.jpg','','image'),
(1,2,1,0,1,1,'deluxe-double-room.jpg','','image'),
(1,3,1,0,1,1,'deluxe-double-room.jpg','','image'),
(1,4,1,0,1,1,'deluxe-double-room.jpg','','image');

/*Table structure for table `pm_room_lock` */

DROP TABLE IF EXISTS `pm_room_lock`;

CREATE TABLE `pm_room_lock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_room` int(11) DEFAULT NULL,
  `from_date` int(11) DEFAULT NULL,
  `to_date` int(11) DEFAULT NULL,
  `add_date` int(11) DEFAULT NULL,
  `sessid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `room_lock_fkey` (`id_room`),
  CONSTRAINT `room_lock_fkey` FOREIGN KEY (`id_room`) REFERENCES `pm_room` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_room_lock` */

/*Table structure for table `pm_service` */

DROP TABLE IF EXISTS `pm_service`;

CREATE TABLE `pm_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `users` text DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `descr` text DEFAULT NULL,
  `long_descr` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `rooms` varchar(250) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `id_tax` int(11) DEFAULT NULL,
  `taxes` text DEFAULT NULL,
  `mandatory` int(11) DEFAULT 0,
  `start_date` int(11) DEFAULT NULL,
  `end_date` int(11) DEFAULT NULL,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`lang`),
  KEY `service_lang_fkey` (`lang`),
  CONSTRAINT `service_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `pm_service` */

insert  into `pm_service`(`id`,`lang`,`users`,`title`,`descr`,`long_descr`,`type`,`rooms`,`price`,`id_tax`,`taxes`,`mandatory`,`start_date`,`end_date`,`checked`,`rank`) values 
(1,1,'1','Set de toilette','1 serviette de toilette, 1 drap de douche, 1 tapis','','qty-night','4,1,3,2',7,1,'',0,NULL,NULL,1,1),
(1,2,'1','Rent of towel (kit)','1 hand towel, 1 bath towel, 1 bath mat','','qty-night','4,1,3,2',7,1,'',0,NULL,NULL,1,1),
(1,3,'1','Rent of towel (kit)','1 hand towel, 1 bath towel, 1 bath mat','','qty-night','4,1,3,2',7,1,'',0,NULL,NULL,1,1),
(1,4,'1','Rent of towel (kit)','1 hand towel, 1 bath towel, 1 bath mat','','qty-night','4,1,3,2',7,1,'',0,NULL,NULL,1,1),
(2,1,'1','Ménage','','','package','1,3,2',50,1,'',0,NULL,NULL,1,2),
(2,2,'1','Housework','','','package','1,3,2',50,1,'',0,NULL,NULL,1,2),
(2,3,'1','Housework','','','package','1,3,2',50,1,'',0,NULL,NULL,1,2),
(2,4,'1','Housework','','','package','1,3,2',50,1,'',0,NULL,NULL,1,2),
(3,1,'1','Chauffage','','','night','1,3,2',8,1,'',0,NULL,NULL,1,3),
(3,2,'1','Heating','','','night','1,3,2',8,1,'',0,NULL,NULL,1,3),
(3,3,'1','Heating','','','night','1,3,2',8,1,'',0,NULL,NULL,1,3),
(3,4,'1','Heating','','','night','1,3,2',8,1,'',0,NULL,NULL,1,3),
(4,1,'1','Animal domestique','Précisez la race ci-dessous','','qty-night','4,1,3,2',5,1,'',0,NULL,NULL,1,4),
(4,2,'1','Pet','Specify the breed below','','qty-night','4,1,3,2',5,1,'',0,NULL,NULL,1,4),
(4,3,'1','Pet','Specify the breed below','','qty-night','4,1,3,2',5,1,'',0,NULL,NULL,1,4),
(4,4,'1','Pet','Specify the breed below','','qty-night','4,1,3,2',5,1,'',0,NULL,NULL,1,4);

/*Table structure for table `pm_slide` */

DROP TABLE IF EXISTS `pm_slide`;

CREATE TABLE `pm_slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `legend` text DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `id_page` int(11) DEFAULT NULL,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`lang`),
  KEY `slide_lang_fkey` (`lang`),
  KEY `slide_page_fkey` (`id_page`,`lang`),
  CONSTRAINT `slide_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `slide_page_fkey` FOREIGN KEY (`id_page`, `lang`) REFERENCES `pm_page` (`id`, `lang`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `pm_slide` */

insert  into `pm_slide`(`id`,`lang`,`legend`,`url`,`id_page`,`checked`,`rank`) values 
(1,1,'','',1,1,2),
(1,2,'<h1>Book your holydays with Panda Multi Resorts</h1>\r\n\r\n<h2>Fast, Easy and Powerfull</h2>\r\n','',1,1,2),
(1,3,'','',1,1,2),
(1,4,'<h1>Book your holydays with Panda Multi Resorts</h1>\r\n\r\n<h2>Fast, Easy and Powerfull</h2>\r\n','',1,1,2),
(2,1,'','',1,1,3),
(2,2,'<h1>A dream stay at the best price</h1>\r\n\r\n<h2>Best price guarantee</h2>\r\n','',1,1,3),
(2,3,'','',1,1,3),
(2,4,'<h1>A dream stay at the best price</h1>\r\n\r\n<h2>Best price guarantee</h2>\r\n','',1,1,3),
(3,1,'','',1,1,1),
(3,2,'<h1>Find Hotels, Activities and Tours</h1>\r\n\r\n<h2>Your whole vacation starts here</h2>\r\n','',1,1,1),
(3,3,'','',1,1,1),
(3,4,'<h1>Find Hotels, Activities and Tours</h1>\r\n\r\n<h2>Your whole vacation starts here</h2>\r\n','',1,1,1);

/*Table structure for table `pm_slide_file` */

DROP TABLE IF EXISTS `pm_slide_file`;

CREATE TABLE `pm_slide_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `home` int(11) DEFAULT 0,
  `checked` int(11) DEFAULT 1,
  `rank` int(11) DEFAULT 0,
  `file` varchar(250) DEFAULT NULL,
  `label` varchar(250) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `slide_file_fkey` (`id_item`,`lang`),
  KEY `slide_file_lang_fkey` (`lang`),
  CONSTRAINT `slide_file_fkey` FOREIGN KEY (`id_item`, `lang`) REFERENCES `pm_slide` (`id`, `lang`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `slide_file_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `pm_slide_file` */

insert  into `pm_slide_file`(`id`,`lang`,`id_item`,`home`,`checked`,`rank`,`file`,`label`,`type`) values 
(3,1,1,0,1,2,'slide1.jpg','','image'),
(3,2,1,0,1,2,'slide1.jpg','','image'),
(3,3,1,0,1,2,'slide1.jpg','','image'),
(3,4,1,0,1,2,'slide1.jpg','','image'),
(4,1,2,0,1,3,'slide2.jpg','','image'),
(4,2,2,0,1,3,'slide2.jpg','','image'),
(4,3,2,0,1,3,'slide2.jpg','','image'),
(4,4,2,0,1,3,'slide2.jpg','','image'),
(6,1,3,0,1,4,'slide3.jpg','','image'),
(6,2,3,0,1,4,'slide3.jpg','','image'),
(6,3,3,0,1,4,'slide3.jpg','','image'),
(6,4,3,0,1,4,'slide3.jpg','','image');

/*Table structure for table `pm_social` */

DROP TABLE IF EXISTS `pm_social`;

CREATE TABLE `pm_social` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `checked` int(11) DEFAULT 1,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_social` */

/*Table structure for table `pm_tag` */

DROP TABLE IF EXISTS `pm_tag`;

CREATE TABLE `pm_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `value` varchar(250) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `pages` varchar(250) DEFAULT NULL,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`lang`),
  KEY `tag_lang_fkey` (`lang`),
  CONSTRAINT `tag_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pm_tag` */

/*Table structure for table `pm_tax` */

DROP TABLE IF EXISTS `pm_tax`;

CREATE TABLE `pm_tax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `value` double DEFAULT 0,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`lang`),
  KEY `tax_lang_fkey` (`lang`),
  CONSTRAINT `tax_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `pm_tax` */

insert  into `pm_tax`(`id`,`lang`,`name`,`value`,`checked`,`rank`) values 
(1,1,'TVA',10,1,1),
(1,2,'VAT',10,1,1),
(1,3,'VAT',10,1,1),
(1,4,'VAT',10,1,1);

/*Table structure for table `pm_text` */

DROP TABLE IF EXISTS `pm_text`;

CREATE TABLE `pm_text` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`,`lang`),
  KEY `text_lang_fkey` (`lang`),
  CONSTRAINT `text_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8;

/*Data for the table `pm_text` */

insert  into `pm_text`(`id`,`lang`,`name`,`value`) values 
(1,1,'CREATION','Création'),
(1,2,'CREATION','Creation'),
(1,3,'CREATION','إنشاء'),
(1,4,'CREATION','Creation'),
(2,1,'MESSAGE','Message'),
(2,2,'MESSAGE','Message'),
(2,3,'MESSAGE','رسالة'),
(2,4,'MESSAGE','Message'),
(3,1,'EMAIL','E-mail'),
(3,2,'EMAIL','E-mail'),
(3,3,'EMAIL','بَرِيدٌ إلِكْترونيّ'),
(3,4,'EMAIL','E-mail'),
(4,1,'PHONE','Tél.'),
(4,2,'PHONE','Phone'),
(4,3,'PHONE','رقم هاتف'),
(4,4,'PHONE','Phone'),
(5,1,'FAX','Fax'),
(5,2,'FAX','Fax'),
(5,3,'FAX','فاكس'),
(5,4,'FAX','Fax'),
(6,1,'COMPANY','Société'),
(6,2,'COMPANY','Company'),
(6,3,'COMPANY','مشروع'),
(6,4,'COMPANY','Company'),
(7,1,'COPY_CODE','Recopiez le code'),
(7,2,'COPY_CODE','Copy the code'),
(7,3,'COPY_CODE','رمز الأمان'),
(7,4,'COPY_CODE','Copy the code'),
(8,1,'SUBJECT','Sujet'),
(8,2,'SUBJECT','Subject'),
(8,3,'SUBJECT','موضوع'),
(8,4,'SUBJECT','Subject'),
(9,1,'REQUIRED_FIELD','Champ requis'),
(9,2,'REQUIRED_FIELD','Required field'),
(9,3,'REQUIRED_FIELD','الحقل المطلوب'),
(9,4,'REQUIRED_FIELD','Required field'),
(10,1,'INVALID_CAPTCHA_CODE','Le code de sécurité saisi est incorrect'),
(10,2,'INVALID_CAPTCHA_CODE','Invalid security code'),
(10,3,'INVALID_CAPTCHA_CODE','رمز الحماية أدخلته غير صحيح'),
(10,4,'INVALID_CAPTCHA_CODE','Invalid security code'),
(11,1,'INVALID_EMAIL','Adresse e-mail invalide'),
(11,2,'INVALID_EMAIL','Invalid email address'),
(11,3,'INVALID_EMAIL','بريد إلكتروني خاطئ'),
(11,4,'INVALID_EMAIL','Invalid email address'),
(12,1,'FIRSTNAME','Prénom'),
(12,2,'FIRSTNAME','Firstname'),
(12,3,'FIRSTNAME','الاسم الأول'),
(12,4,'FIRSTNAME','Firstname'),
(13,1,'LASTNAME','Nom'),
(13,2,'LASTNAME','Lastname'),
(13,3,'LASTNAME','اسم العائلة'),
(13,4,'LASTNAME','Lastname'),
(14,1,'ADDRESS','Adresse'),
(14,2,'ADDRESS','Address'),
(14,3,'ADDRESS','عنوان الشارع'),
(14,4,'ADDRESS','Address'),
(15,1,'POSTCODE','Code postal'),
(15,2,'POSTCODE','Post code'),
(15,3,'POSTCODE','الرمز البريدي'),
(15,4,'POSTCODE','Post code'),
(16,1,'CITY','Ville'),
(16,2,'CITY','City'),
(16,3,'CITY','مدينة'),
(16,4,'CITY','City'),
(17,1,'MOBILE','Portable'),
(17,2,'MOBILE','Mobile'),
(17,3,'MOBILE','هاتف'),
(17,4,'MOBILE','Mobile'),
(18,1,'ADD','Ajouter'),
(18,2,'ADD','Add'),
(18,3,'ADD','إضافة على'),
(18,4,'ADD','Add'),
(19,1,'EDIT','Modifier'),
(19,2,'EDIT','Edit'),
(19,3,'EDIT','تغيير'),
(19,4,'EDIT','Edit'),
(20,1,'INVALID_INPUT','Saisie invalide'),
(20,2,'INVALID_INPUT','Invalid input'),
(20,3,'INVALID_INPUT','إدخال غير صالح'),
(20,4,'INVALID_INPUT','Invalid input'),
(21,1,'MAIL_DELIVERY_FAILURE','Echec lors de l\'envoi du message.'),
(21,2,'MAIL_DELIVERY_FAILURE','A failure occurred during the delivery of this message.'),
(21,3,'MAIL_DELIVERY_FAILURE','حدث فشل أثناء تسليم هذه الرسالة.'),
(21,4,'MAIL_DELIVERY_FAILURE','A failure occurred during the delivery of this message.'),
(22,1,'MAIL_DELIVERY_SUCCESS','Merci de votre intérêt, votre message a bien été envoyé.\nNous vous contacterons dans les plus brefs délais.'),
(22,2,'MAIL_DELIVERY_SUCCESS','Thank you for your interest, your message has been sent.\nWe will contact you as soon as possible.'),
(22,3,'MAIL_DELIVERY_SUCCESS','خزان لاهتمامك ، تم إرسال رسالتك . سوف نتصل بك في أقرب وقت ممكن .'),
(22,4,'MAIL_DELIVERY_SUCCESS','Thank you for your interest, your message has been sent.\nWe will contact you as soon as possible.'),
(23,1,'SEND','Envoyer'),
(23,2,'SEND','Send'),
(23,3,'SEND','ارسل انت'),
(23,4,'SEND','Send'),
(24,1,'FORM_ERRORS','Le formulaire comporte des erreurs.'),
(24,2,'FORM_ERRORS','The following form contains some errors.'),
(24,3,'FORM_ERRORS','النموذج التالي يحتوي على بعض الأخطاء.'),
(24,4,'FORM_ERRORS','The following form contains some errors.'),
(25,1,'FROM_DATE','Du'),
(25,2,'FROM_DATE','From'),
(25,3,'FROM_DATE','من'),
(25,4,'FROM_DATE','From'),
(26,1,'TO_DATE','au'),
(26,2,'TO_DATE','till'),
(26,3,'TO_DATE','حتى'),
(26,4,'TO_DATE','till'),
(27,1,'FROM','De'),
(27,2,'FROM','From'),
(27,3,'FROM','من'),
(27,4,'FROM','From'),
(28,1,'TO','à'),
(28,2,'TO','to'),
(28,3,'TO','إلى'),
(28,4,'TO','to'),
(29,1,'BOOK','Réserver'),
(29,2,'BOOK','Book'),
(29,3,'BOOK','للحجز'),
(29,4,'BOOK','Book'),
(30,1,'READMORE','Lire la suite'),
(30,2,'READMORE','Read more'),
(30,3,'READMORE','اقرأ المزيد'),
(30,4,'READMORE','Read more'),
(31,1,'BACK','Retour'),
(31,2,'BACK','Back'),
(31,3,'BACK','عودة'),
(31,4,'BACK','Back'),
(32,1,'DISCOVER','Découvrir'),
(32,2,'DISCOVER','Discover'),
(32,3,'DISCOVER','اكتشف'),
(32,4,'DISCOVER','Discover'),
(33,1,'ALL','Tous'),
(33,2,'ALL','All'),
(33,3,'ALL','كل'),
(33,4,'ALL','All'),
(34,1,'ALL_RIGHTS_RESERVED','Tous droits réservés'),
(34,2,'ALL_RIGHTS_RESERVED','All rights reserved'),
(34,3,'ALL_RIGHTS_RESERVED','جميع الحقوق محفوظه'),
(34,4,'ALL_RIGHTS_RESERVED','All rights reserved'),
(35,1,'FORGOTTEN_PASSWORD','Mot de passe oublié ?'),
(35,2,'FORGOTTEN_PASSWORD','Forgotten password?'),
(35,3,'FORGOTTEN_PASSWORD','هل نسيت كلمة المرور؟'),
(35,4,'FORGOTTEN_PASSWORD','Forgotten password?'),
(36,1,'LOG_IN','Connexion'),
(36,2,'LOG_IN','Log in'),
(36,3,'LOG_IN','تسجيل الدخول'),
(36,4,'LOG_IN','Log in'),
(37,1,'SIGN_UP','Inscription'),
(37,2,'SIGN_UP','Sign up'),
(37,3,'SIGN_UP','تسجيل'),
(37,4,'SIGN_UP','Sign up'),
(38,1,'LOG_OUT','Déconnexion'),
(38,2,'LOG_OUT','Log out'),
(38,3,'LOG_OUT','تسجيل الخروج'),
(38,4,'LOG_OUT','Log out'),
(39,1,'SEARCH','Rechercher'),
(39,2,'SEARCH','Search'),
(39,3,'SEARCH','ابحث عن'),
(39,4,'SEARCH','Search'),
(40,1,'RESET_PASS_SUCCESS','Votre nouveau mot de passe vous a été envoyé sur votre adresse e-mail.'),
(40,2,'RESET_PASS_SUCCESS','Your new password was sent to you on your e-mail.'),
(40,3,'RESET_PASS_SUCCESS','تم إرسال كلمة المرور الجديدة إلى عنوان البريد الإلكتروني الخاص بك'),
(40,4,'RESET_PASS_SUCCESS','Your new password was sent to you on your e-mail.'),
(41,1,'PASS_TOO_SHORT','Le mot de passe doit contenir 6 caractères au minimum'),
(41,2,'PASS_TOO_SHORT','The password must contain 6 characters at least'),
(41,3,'PASS_TOO_SHORT','يجب أن يحتوي على كلمة المرور ستة أحرف على الأقل'),
(41,4,'PASS_TOO_SHORT','The password must contain 6 characters at least'),
(42,1,'PASS_DONT_MATCH','Les mots de passe doivent correspondre'),
(42,2,'PASS_DONT_MATCH','The passwords don\'t match'),
(42,3,'PASS_DONT_MATCH','يجب أن تتطابق كلمات المرور'),
(42,4,'PASS_DONT_MATCH','The passwords don\'t match'),
(43,1,'ACCOUNT_EXISTS','Un compte existe déjà avec cette adresse e-mail'),
(43,2,'ACCOUNT_EXISTS','An account already exists with this e-mail'),
(43,3,'ACCOUNT_EXISTS','حساب موجود بالفعل مع هذا عنوان البريد الإلكتروني'),
(43,4,'ACCOUNT_EXISTS','An account already exists with this e-mail'),
(44,1,'ACCOUNT_CREATED','Votre compte a bien été créé. Vous allez recevoir un email de confirmation. Cliquez sur le lien de cet e-mail pour confirmer votre compte avant de continuer.'),
(44,2,'ACCOUNT_CREATED','Your account has been created. You will receive a confirmation email. Click on the link in this email to confirm your account before continuing.'),
(44,3,'ACCOUNT_CREATED','Your account has been created. You will receive a confirmation email. Click on the link in this email to confirm your account before continuing.'),
(44,4,'ACCOUNT_CREATED','Your account has been created. You will receive a confirmation email. Click on the link in this email to confirm your account before continuing.'),
(45,1,'INCORRECT_LOGIN','Les informations de connexion sont incorrectes.'),
(45,2,'INCORRECT_LOGIN','Incorrect login information.'),
(45,3,'INCORRECT_LOGIN','معلومات تسجيل الدخول غير صحيحة.'),
(45,4,'INCORRECT_LOGIN','Incorrect login information.'),
(46,1,'I_SIGN_UP','Je m\'inscris'),
(46,2,'I_SIGN_UP','I sign up'),
(46,3,'I_SIGN_UP','يمكنني الاشتراك'),
(46,4,'I_SIGN_UP','I sign up'),
(47,1,'ALREADY_HAVE_ACCOUNT','J\'ai déjà un compte'),
(47,2,'ALREADY_HAVE_ACCOUNT','I already have an account'),
(47,3,'ALREADY_HAVE_ACCOUNT','لدي بالفعل حساب'),
(47,4,'ALREADY_HAVE_ACCOUNT','I already have an account'),
(48,1,'MY_ACCOUNT','Mon compte'),
(48,2,'MY_ACCOUNT','My account'),
(48,3,'MY_ACCOUNT','حسابي'),
(48,4,'MY_ACCOUNT','My account'),
(49,1,'COMMENTS','Commentaires'),
(49,2,'COMMENTS','Comments'),
(49,3,'COMMENTS','تعليقات'),
(49,4,'COMMENTS','Comments'),
(50,1,'LET_US_KNOW','Faîtes-nous savoir ce que vous pensez'),
(50,2,'LET_US_KNOW','Let us know what you think'),
(50,3,'LET_US_KNOW','ماذا عن رايك؟'),
(50,4,'LET_US_KNOW','Let us know what you think'),
(51,1,'COMMENT_SUCCESS','Merci de votre intérêt, votre commentaire va être soumis à validation.'),
(51,2,'COMMENT_SUCCESS','Thank you for your interest, your comment will be checked.'),
(51,3,'COMMENT_SUCCESS','شكرا ل اهتمامك، و سيتم التحقق من صحة للتعليق.'),
(51,4,'COMMENT_SUCCESS','Thank you for your interest, your comment will be checked.'),
(52,1,'NO_SEARCH_RESULT','Aucun résultat. Vérifiez l\'orthographe des termes de recherche (> 3 caractères) ou essayez d\'autres mots.'),
(52,2,'NO_SEARCH_RESULT','No result. Check the spelling terms of search (> 3 characters) or try other words.'),
(52,3,'NO_SEARCH_RESULT','لا نتيجة. التدقيق الإملائي للكلمات (أكثر من ثلاثة أحرف ) أو محاولة بعبارة أخرى .'),
(52,4,'NO_SEARCH_RESULT','No result. Check the spelling terms of search (> 3 characters) or try other words.'),
(53,1,'SEARCH_EXCEEDED','Nombre de recherches dépassé.'),
(53,2,'SEARCH_EXCEEDED','Number of researches exceeded.'),
(53,3,'SEARCH_EXCEEDED','عدد من الأبحاث السابقة .'),
(53,4,'SEARCH_EXCEEDED','Number of researches exceeded.'),
(54,1,'SECONDS','secondes'),
(54,2,'SECONDS','seconds'),
(54,3,'SECONDS','ثواني'),
(54,4,'SECONDS','seconds'),
(55,1,'FOR_A_TOTAL_OF','sur un total de'),
(55,2,'FOR_A_TOTAL_OF','for a total of'),
(55,3,'FOR_A_TOTAL_OF','من الكل'),
(55,4,'FOR_A_TOTAL_OF','for a total of'),
(56,1,'COMMENT','Commentaire'),
(56,2,'COMMENT','Comment'),
(56,3,'COMMENT','تعقيب'),
(56,4,'COMMENT','Comment'),
(57,1,'VIEW','Visionner'),
(57,2,'VIEW','View'),
(57,3,'VIEW','ل عرض'),
(57,4,'VIEW','View'),
(58,1,'RECENT_ARTICLES','Articles récents'),
(58,2,'RECENT_ARTICLES','Recent articles'),
(58,3,'RECENT_ARTICLES','المقالات الأخيرة'),
(58,4,'RECENT_ARTICLES','Recent articles'),
(59,1,'RSS_FEED','Flux RSS'),
(59,2,'RSS_FEED','RSS feed'),
(59,3,'RSS_FEED','تغذية RSS'),
(59,4,'RSS_FEED','RSS feed'),
(60,1,'COUNTRY','Pays'),
(60,2,'COUNTRY','Country'),
(60,3,'COUNTRY','Country'),
(60,4,'COUNTRY','Country'),
(61,1,'ROOM','Chambre'),
(61,2,'ROOM','Room'),
(61,3,'ROOM','Room'),
(61,4,'ROOM','Room'),
(62,1,'INCL_VAT','TTC'),
(62,2,'INCL_VAT','incl. VAT'),
(62,3,'INCL_VAT','incl. VAT'),
(62,4,'INCL_VAT','incl. VAT'),
(63,1,'NIGHTS','nuits'),
(63,2,'NIGHTS','nights'),
(63,3,'NIGHTS','nights'),
(63,4,'NIGHTS','nights'),
(64,1,'ADULTS','Adultes'),
(64,2,'ADULTS','Adults'),
(64,3,'ADULTS','Adults'),
(64,4,'ADULTS','Adults'),
(65,1,'CHILDREN','Enfants'),
(65,2,'CHILDREN','Children'),
(65,3,'CHILDREN','Children'),
(65,4,'CHILDREN','Children'),
(66,1,'PERSONS','personnes'),
(66,2,'PERSONS','persons'),
(66,3,'PERSONS','persons'),
(66,4,'PERSONS','persons'),
(67,1,'CONTACT_DETAILS','Coordonnées'),
(67,2,'CONTACT_DETAILS','Contact details'),
(67,3,'CONTACT_DETAILS','Contact details'),
(67,4,'CONTACT_DETAILS','Contact details'),
(68,1,'NO_AVAILABILITY','Aucune disponibilité'),
(68,2,'NO_AVAILABILITY','No availability'),
(68,3,'NO_AVAILABILITY','No availability'),
(68,4,'NO_AVAILABILITY','No availability'),
(69,1,'AVAILABILITIES','Disponibilités'),
(69,2,'AVAILABILITIES','Availabilities'),
(69,3,'AVAILABILITIES','Availabilities'),
(69,4,'AVAILABILITIES','Availabilities'),
(70,1,'CHECK','Vérifier'),
(70,2,'CHECK','Check'),
(70,3,'CHECK','Check'),
(70,4,'CHECK','Check'),
(71,1,'BOOKING_DETAILS','Détails de la réservation'),
(71,2,'BOOKING_DETAILS','Booking details'),
(71,3,'BOOKING_DETAILS','Booking details'),
(71,4,'BOOKING_DETAILS','Booking details'),
(72,1,'SPECIAL_REQUESTS','Demandes spéciales'),
(72,2,'SPECIAL_REQUESTS','Special requests'),
(72,3,'SPECIAL_REQUESTS','Special requests'),
(72,4,'SPECIAL_REQUESTS','Special requests'),
(73,1,'PREVIOUS_STEP','Étape précédente'),
(73,2,'PREVIOUS_STEP','Previous step'),
(73,3,'PREVIOUS_STEP','Previous step'),
(73,4,'PREVIOUS_STEP','Previous step'),
(74,1,'CONFIRM_BOOKING','Confirmer la réservation'),
(74,2,'CONFIRM_BOOKING','Confirm the booking'),
(74,3,'CONFIRM_BOOKING','Confirm the booking'),
(74,4,'CONFIRM_BOOKING','Confirm the booking'),
(75,1,'ALSO_DISCOVER','Découvrez aussi'),
(75,2,'ALSO_DISCOVER','Also discover'),
(75,3,'ALSO_DISCOVER','Also discover'),
(75,4,'ALSO_DISCOVER','Also discover'),
(76,1,'CHECK_PEOPLE','Merci de vérifier le nombre de personnes pour l\'hébergement choisi.'),
(76,2,'CHECK_PEOPLE','Please verify the number of people for the chosen accommodation'),
(76,3,'CHECK_PEOPLE','Please verify the number of people for the chosen accommodation'),
(76,4,'CHECK_PEOPLE','Please verify the number of people for the chosen accommodation'),
(77,1,'BOOKING','Réservation'),
(77,2,'BOOKING','Booking'),
(77,3,'BOOKING','Booking'),
(77,4,'BOOKING','Booking'),
(78,1,'NIGHT','nuit'),
(78,2,'NIGHT','night'),
(78,3,'NIGHT','night'),
(78,4,'NIGHT','night'),
(79,1,'WEEK','semaine'),
(79,2,'WEEK','week'),
(79,3,'WEEK','week'),
(79,4,'WEEK','week'),
(80,1,'EXTRA_SERVICES','Services supplémentaires'),
(80,2,'EXTRA_SERVICES','Extra services'),
(80,3,'EXTRA_SERVICES','Extra services'),
(80,4,'EXTRA_SERVICES','Extra services'),
(81,1,'BOOKING_TERMS',''),
(81,2,'BOOKING_TERMS',''),
(81,3,'BOOKING_TERMS',''),
(81,4,'BOOKING_TERMS',''),
(82,1,'NEXT_STEP','Étape suivante'),
(82,2,'NEXT_STEP','Next step'),
(82,3,'NEXT_STEP','Next step'),
(82,4,'NEXT_STEP','Next step'),
(83,1,'TOURIST_TAX','Taxe de séjour'),
(83,2,'TOURIST_TAX','Tourist tax'),
(83,3,'TOURIST_TAX','Tourist tax'),
(83,4,'TOURIST_TAX','Tourist tax'),
(84,1,'CHECK_IN','Arrivée'),
(84,2,'CHECK_IN','Check in'),
(84,3,'CHECK_IN','Check in'),
(84,4,'CHECK_IN','Check in'),
(85,1,'CHECK_OUT','Départ'),
(85,2,'CHECK_OUT','Check out'),
(85,3,'CHECK_OUT','Check out'),
(85,4,'CHECK_OUT','Check out'),
(86,1,'TOTAL','Total'),
(86,2,'TOTAL','Total'),
(86,3,'TOTAL','Total'),
(86,4,'TOTAL','Total'),
(87,1,'CAPACITY','Capacité'),
(87,2,'CAPACITY','Capacity'),
(87,3,'CAPACITY','Capacity'),
(87,4,'CAPACITY','Capacity'),
(88,1,'FACILITIES','Équipements'),
(88,2,'FACILITIES','Facilities'),
(88,3,'FACILITIES','Facilities'),
(88,4,'FACILITIES','Facilities'),
(89,1,'PRICE','Prix'),
(89,2,'PRICE','Price'),
(89,3,'PRICE','Price'),
(89,4,'PRICE','Price'),
(90,1,'MORE_DETAILS','Plus d\'infos'),
(90,2,'MORE_DETAILS','More details'),
(90,3,'MORE_DETAILS','More details'),
(90,4,'MORE_DETAILS','More details'),
(91,1,'FROM_PRICE','À partir de'),
(91,2,'FROM_PRICE','From'),
(91,3,'FROM_PRICE','From'),
(91,4,'FROM_PRICE','From'),
(92,1,'AMOUNT','Montant'),
(92,2,'AMOUNT','Amount'),
(92,3,'AMOUNT','Amount'),
(92,4,'AMOUNT','Amount'),
(93,1,'PAY','Payer'),
(93,2,'PAY','Check out'),
(93,3,'PAY','Check out'),
(93,4,'PAY','Check out'),
(94,1,'PAYMENT_PAYPAL_NOTICE','Cliquez sur \"Payer\" ci-dessous, vous allez être redirigé vers le site sécurisé de PayPal'),
(94,2,'PAYMENT_PAYPAL_NOTICE','Click on \"Check Out\" below, you will be redirected towards the secure site of PayPal'),
(94,3,'PAYMENT_PAYPAL_NOTICE','Click on \"Check Out\" below, you will be redirected towards the secure site of PayPal'),
(94,4,'PAYMENT_PAYPAL_NOTICE','Click on \"Check Out\" below, you will be redirected towards the secure site of PayPal'),
(95,1,'PAYMENT_CANCEL_NOTICE','Le paiement a été annulé.<br>Merci de votre visite et à bientôt.'),
(95,2,'PAYMENT_CANCEL_NOTICE','The payment has been cancelled.<br>Thank you for your visit and see you soon.'),
(95,3,'PAYMENT_CANCEL_NOTICE','The payment has been cancelled.<br>Thank you for your visit and see you soon.'),
(95,4,'PAYMENT_CANCEL_NOTICE','The payment has been cancelled.<br>Thank you for your visit and see you soon.'),
(96,1,'PAYMENT_SUCCESS_NOTICE','Le paiement a été réalisé avec succès.<br>Merci de votre visite et à bientôt !'),
(96,2,'PAYMENT_SUCCESS_NOTICE','Your payment has been successfully processed.<br>Thank you for your visit and see you soon!'),
(96,3,'PAYMENT_SUCCESS_NOTICE','Your payment has been successfully processed.<br>Thank you for your visit and see you soon!'),
(96,4,'PAYMENT_SUCCESS_NOTICE','Your payment has been successfully processed.<br>Thank you for your visit and see you soon!'),
(97,1,'BILLING_ADDRESS','Adresse de facturation'),
(97,2,'BILLING_ADDRESS','Billing address'),
(97,3,'BILLING_ADDRESS','Billing address'),
(97,4,'BILLING_ADDRESS','Billing address'),
(98,1,'DOWN_PAYMENT','Acompte'),
(98,2,'DOWN_PAYMENT','Down payment'),
(98,3,'DOWN_PAYMENT','Down payment'),
(98,4,'DOWN_PAYMENT','Down payment'),
(99,1,'PAYMENT_CHECK_NOTICE','Merci d\'envoyer un chèque à \"Panda Multi Resorts, Neeloafaru Magu, Maldives\" d\'un montant de {amount}.<br>Votre réservation sera validée à réception du paiement.<br>Merci de votre visite et à bientôt !'),
(99,2,'PAYMENT_CHECK_NOTICE','Thank you for sending a check of {amount} to \"Panda Multi Resorts, Neeloafaru Magu, Maldives\".<br>Your reservation will be confirmed upon receipt of the payment.<br>Thank you for your visit and see you soon!'),
(99,3,'PAYMENT_CHECK_NOTICE','Thank you for sending a check of {amount} to \"Panda Multi Resorts, Neeloafaru Magu, Maldives\".<br>Your reservation will be confirmed upon receipt of the payment.<br>Thank you for your visit and see you soon!'),
(99,4,'PAYMENT_CHECK_NOTICE','Thank you for sending a check of {amount} to \"Panda Multi Resorts, Neeloafaru Magu, Maldives\".<br>Your reservation will be confirmed upon receipt of the payment.<br>Thank you for your visit and see you soon!'),
(100,1,'PAYMENT_ARRIVAL_NOTICE','Veuillez régler le solde de votre réservation d\'un montant de {amount} à votre arrivée.<br>Merci de votre visite et à bientôt !'),
(100,2,'PAYMENT_ARRIVAL_NOTICE','Thank you for paying the balance of {amount} for your booking on your arrival.<br>Thank you for your visit and see you soon!'),
(100,3,'PAYMENT_ARRIVAL_NOTICE','Thank you for paying the balance of {amount} for your booking on your arrival.<br>Thank you for your visit and see you soon!'),
(100,4,'PAYMENT_ARRIVAL_NOTICE','Thank you for paying the balance of {amount} for your booking on your arrival.<br>Thank you for your visit and see you soon!'),
(101,1,'MAX_PEOPLE','Pers. max'),
(101,2,'MAX_PEOPLE','Max people'),
(101,3,'MAX_PEOPLE','Max people'),
(101,4,'MAX_PEOPLE','Max people'),
(102,1,'VAT_AMOUNT','Dont TVA'),
(102,2,'VAT_AMOUNT','VAT amount'),
(102,3,'VAT_AMOUNT','VAT amount'),
(102,4,'VAT_AMOUNT','VAT amount'),
(103,1,'MIN_NIGHTS','Nuits min'),
(103,2,'MIN_NIGHTS','Min nights'),
(103,3,'MIN_NIGHTS','Min nights'),
(103,4,'MIN_NIGHTS','Min nights'),
(104,1,'ROOMS','Chambres'),
(104,2,'ROOMS','Rooms'),
(104,3,'ROOMS','Rooms'),
(104,4,'ROOMS','Rooms'),
(105,1,'RATINGS','Note(s)'),
(105,2,'RATINGS','Rating(s)'),
(105,3,'RATINGS','Rating(s)'),
(105,4,'RATINGS','Rating(s)'),
(106,1,'MIN_PEOPLE','Pers. min'),
(106,2,'MIN_PEOPLE','Min people'),
(106,3,'MIN_PEOPLE','Min people'),
(106,4,'MIN_PEOPLE','Min people'),
(107,1,'HOTEL','Hôtel'),
(107,2,'HOTEL','Hotel'),
(107,3,'HOTEL','Hotel'),
(107,4,'HOTEL','Hotel'),
(108,1,'MAKE_A_REQUEST','Faire une demande'),
(108,2,'MAKE_A_REQUEST','Make a request'),
(108,3,'MAKE_A_REQUEST','Make a request'),
(108,4,'MAKE_A_REQUEST','Make a request'),
(109,1,'FULLNAME','Nom complet'),
(109,2,'FULLNAME','Full Name'),
(109,3,'FULLNAME','Full Name'),
(109,4,'FULLNAME','Full Name'),
(110,1,'PASSWORD','Mot de passe'),
(110,2,'PASSWORD','Password'),
(110,3,'PASSWORD','Password'),
(110,4,'PASSWORD','Password'),
(111,1,'LOG_IN_WITH_FACEBOOK','Enregistrez-vous avec Facebook'),
(111,2,'LOG_IN_WITH_FACEBOOK','Log in with Facebook'),
(111,3,'LOG_IN_WITH_FACEBOOK','Log in with Facebook'),
(111,4,'LOG_IN_WITH_FACEBOOK','Log in with Facebook'),
(112,1,'OR','Ou'),
(112,2,'OR','Or'),
(112,3,'OR','Or'),
(112,4,'OR','Or'),
(113,1,'NEW_PASSWORD','Nouveau mot de passe'),
(113,2,'NEW_PASSWORD','New password'),
(113,3,'NEW_PASSWORD','New password'),
(113,4,'NEW_PASSWORD','New password'),
(114,1,'NEW_PASSWORD_NOTICE','Merci d\'entrer l\'adresse e-mail correspondant à votre compte. Un nouveau mot de passe vous sera envoyé par e-mail.'),
(114,2,'NEW_PASSWORD_NOTICE','Please enter your e-mail address corresponding to your account. A new password will be sent to you by e-mail.'),
(114,3,'NEW_PASSWORD_NOTICE','Please enter your e-mail address corresponding to your account. A new password will be sent to you by e-mail.'),
(114,4,'NEW_PASSWORD_NOTICE','Please enter your e-mail address corresponding to your account. A new password will be sent to you by e-mail.'),
(115,1,'USERNAME','Utilisateur'),
(115,2,'USERNAME','Username'),
(115,3,'USERNAME','Username'),
(115,4,'USERNAME','Username'),
(116,1,'PASSWORD_CONFIRM','Confirmer mot de passe'),
(116,2,'PASSWORD_CONFIRM','Confirm password'),
(116,3,'PASSWORD_CONFIRM','Confirm password'),
(116,4,'PASSWORD_CONFIRM','Confirm password'),
(117,1,'USERNAME_EXISTS','Un compte existe déjà avec ce nom d\'utilisateur'),
(117,2,'USERNAME_EXISTS','An account already exists with this username'),
(117,3,'USERNAME_EXISTS','An account already exists with this username'),
(117,4,'USERNAME_EXISTS','An account already exists with this username'),
(118,1,'ACCOUNT_EDIT_SUCCESS','Les informations de votre compte ont bien été modifiées.'),
(118,2,'ACCOUNT_EDIT_SUCCESS','Your account information have been changed.'),
(118,3,'ACCOUNT_EDIT_SUCCESS','Your account information have been changed.'),
(118,4,'ACCOUNT_EDIT_SUCCESS','Your account information have been changed.'),
(119,1,'ACCOUNT_EDIT_FAILURE','Echec de la modification des informations de compte.'),
(119,2,'ACCOUNT_EDIT_FAILURE','An error occured during the modification of the account information.'),
(119,3,'ACCOUNT_EDIT_FAILURE','An error occured during the modification of the account information.'),
(119,4,'ACCOUNT_EDIT_FAILURE','An error occured during the modification of the account information.'),
(120,1,'ACCOUNT_CREATE_FAILURE','Echec de la création du compte.'),
(120,2,'ACCOUNT_CREATE_FAILURE','Failed to create account.'),
(120,3,'ACCOUNT_CREATE_FAILURE','Failed to create account.'),
(120,4,'ACCOUNT_CREATE_FAILURE','Failed to create account.'),
(121,1,'PAYMENT_CHECK','Par chèque'),
(121,2,'PAYMENT_CHECK','By check'),
(121,3,'PAYMENT_CHECK','By check'),
(121,4,'PAYMENT_CHECK','By check'),
(122,1,'PAYMENT_ARRIVAL','A l\'arrivée'),
(122,2,'PAYMENT_ARRIVAL','On arrival'),
(122,3,'PAYMENT_ARRIVAL','On arrival'),
(122,4,'PAYMENT_ARRIVAL','On arrival'),
(123,1,'CHOOSE_PAYMENT','Choisissez un moyen de paiement'),
(123,2,'CHOOSE_PAYMENT','Choose a method of payment'),
(123,3,'CHOOSE_PAYMENT','Choose a method of payment'),
(123,4,'CHOOSE_PAYMENT','Choose a method of payment'),
(124,1,'PAYMENT_CREDIT_CARDS','Cartes de credit'),
(124,2,'PAYMENT_CREDIT_CARDS','Credit cards'),
(124,3,'PAYMENT_CREDIT_CARDS','Credit cards'),
(124,4,'PAYMENT_CREDIT_CARDS','Credit cards'),
(125,1,'MAX_ADULTS','Adultes max'),
(125,2,'MAX_ADULTS','Max adults'),
(125,3,'MAX_ADULTS','Max adults'),
(125,4,'MAX_ADULTS','Max adults'),
(126,1,'MAX_CHILDREN','Enfants max'),
(126,2,'MAX_CHILDREN','Max children'),
(126,3,'MAX_CHILDREN','Max children'),
(126,4,'MAX_CHILDREN','Max children'),
(127,1,'PAYMENT_2CHECKOUT_NOTICE','Cliquez sur \"Payer\" ci-dessous, vous allez être redirigé vers le site sécurisé de 2Checkout.com'),
(127,2,'PAYMENT_2CHECKOUT_NOTICE','Click on \"Check Out\" below, you will be redirected towards the secure site of 2Checkout.com'),
(127,3,'PAYMENT_2CHECKOUT_NOTICE','Click on \"Check Out\" below, you will be redirected towards the secure site of 2Checkout.com'),
(127,4,'PAYMENT_2CHECKOUT_NOTICE','Click on \"Check Out\" below, you will be redirected towards the secure site of 2Checkout.com'),
(128,1,'COOKIES_NOTICE','Les cookies nous aident à fournir une meilleure expérience utilisateur. En utilisant notre site, vous acceptez l\'utilisation de cookies.'),
(128,2,'COOKIES_NOTICE','Cookies help us provide better user experience. By using our website, you agree to the use of cookies.'),
(128,3,'COOKIES_NOTICE','Cookies help us provide better user experience. By using our website, you agree to the use of cookies.'),
(128,4,'COOKIES_NOTICE','Cookies help us provide better user experience. By using our website, you agree to the use of cookies.'),
(129,1,'DURATION','Durée'),
(129,2,'DURATION','Duration'),
(129,3,'DURATION','Duration'),
(129,4,'DURATION','Duration'),
(130,1,'PERSON','Personne'),
(130,2,'PERSON','Person'),
(130,3,'PERSON','Person'),
(130,4,'PERSON','Person'),
(131,1,'CHOOSE_A_DATE','Choisissez une date'),
(131,2,'CHOOSE_A_DATE','Choose a date'),
(131,3,'CHOOSE_A_DATE','Choose a date'),
(131,4,'CHOOSE_A_DATE','Choose a date'),
(132,1,'TIMESLOT','Horaire'),
(132,2,'TIMESLOT','Time slot'),
(132,3,'TIMESLOT','Time slot'),
(132,4,'TIMESLOT','Time slot'),
(133,1,'ACTIVITIES','Activités'),
(133,2,'ACTIVITIES','Activities'),
(133,3,'ACTIVITIES','Activities'),
(133,4,'ACTIVITIES','Activities'),
(134,1,'DESTINATION','Destination'),
(134,2,'DESTINATION','Destination'),
(134,3,'DESTINATION','Destination'),
(134,4,'DESTINATION','Destination'),
(135,1,'NO_HOTEL_FOUND','Aucun hotel trouvé'),
(135,2,'NO_HOTEL_FOUND','No hotel found'),
(135,3,'NO_HOTEL_FOUND','No hotel found'),
(135,4,'NO_HOTEL_FOUND','No hotel found'),
(136,1,'FOR','pour'),
(136,2,'FOR','for'),
(136,3,'FOR','for'),
(136,4,'FOR','for'),
(137,1,'FIND_ACTIVITIES_AND_TOURS','Découvrez nos activités et excursions'),
(137,2,'FIND_ACTIVITIES_AND_TOURS','Find out our activities and tours'),
(137,3,'FIND_ACTIVITIES_AND_TOURS','Find out our activities and tours'),
(137,4,'FIND_ACTIVITIES_AND_TOURS','Find out our activities and tours'),
(138,1,'MINUTES','minute(s)'),
(138,2,'MINUTES','minute(s)'),
(138,3,'MINUTES','minute(s)'),
(138,4,'MINUTES','minute(s)'),
(139,1,'HOURS','heure(s)'),
(139,2,'HOURS','hour(s)'),
(139,3,'HOURS','hour(s)'),
(139,4,'HOURS','hour(s)'),
(140,1,'DAYS','jour(s)'),
(140,2,'DAYS','day(s)'),
(140,3,'DAYS','day(s)'),
(140,4,'DAYS','day(s)'),
(141,1,'WEEKS','semaine(s)'),
(141,2,'WEEKS','week(s)'),
(141,3,'WEEKS','week(s)'),
(141,4,'WEEKS','week(s)'),
(142,1,'RESULTS','Résultats'),
(142,2,'RESULTS','Results'),
(142,3,'RESULTS','Results'),
(142,4,'RESULTS','Results'),
(143,1,'BOOKING_HISTORY','Historique des réservations'),
(143,2,'BOOKING_HISTORY','Booking history'),
(143,3,'BOOKING_HISTORY','Booking history'),
(143,4,'BOOKING_HISTORY','Booking history'),
(144,1,'BOOKING_SUMMARY','Résumé de la réservation'),
(144,2,'BOOKING_SUMMARY','Booking summary'),
(144,3,'BOOKING_SUMMARY','Booking summary'),
(144,4,'BOOKING_SUMMARY','Booking summary'),
(145,1,'BOOKING_DATE','Date de la réservations'),
(145,2,'BOOKING_DATE','Booking date'),
(145,3,'BOOKING_DATE','Booking date'),
(145,4,'BOOKING_DATE','Booking date'),
(146,1,'NO_BOOKING_YET','Pas encore de réservation...'),
(146,2,'NO_BOOKING_YET','No booking yet...'),
(146,3,'NO_BOOKING_YET','No booking yet...'),
(146,4,'NO_BOOKING_YET','No booking yet...'),
(147,1,'PAYMENT','Paiement'),
(147,2,'PAYMENT','Payment'),
(147,3,'PAYMENT','Payment'),
(147,4,'PAYMENT','Payment'),
(148,1,'PAYMENT_DATE','Date du paiement'),
(148,2,'PAYMENT_DATE','Payment date'),
(148,3,'PAYMENT_DATE','Payment date'),
(148,4,'PAYMENT_DATE','Payment date'),
(149,1,'PAYMENT_METHOD','Méthode de paiement'),
(149,2,'PAYMENT_METHOD','Payment method'),
(149,3,'PAYMENT_METHOD','Payment method'),
(149,4,'PAYMENT_METHOD','Payment method'),
(150,1,'NUM_TRANSACTION','N°transaction'),
(150,2,'NUM_TRANSACTION','Num. transaction'),
(150,3,'NUM_TRANSACTION','Num. transaction'),
(150,4,'NUM_TRANSACTION','Num. transaction'),
(151,1,'STATUS','Statut'),
(151,2,'STATUS','Status'),
(151,3,'STATUS','Status'),
(151,4,'STATUS','Status'),
(152,1,'AWAITING','En attente'),
(152,2,'AWAITING','Awaiting'),
(152,3,'AWAITING','Awaiting'),
(152,4,'AWAITING','Awaiting'),
(153,1,'CANCELLED','Annulé'),
(153,2,'CANCELLED','Cancelled'),
(153,3,'CANCELLED','Cancelled'),
(153,4,'CANCELLED','Cancelled'),
(154,1,'REJECTED_PAYMENT','Paiement rejeté'),
(154,2,'REJECTED_PAYMENT','Rejected payment'),
(154,3,'REJECTED_PAYMENT','Rejected payment'),
(154,4,'REJECTED_PAYMENT','Rejected payment'),
(155,1,'PAYED','Payé'),
(155,2,'PAYED','Payed'),
(155,3,'PAYED','Payed'),
(155,4,'PAYED','Payed'),
(156,1,'INCL_TAX','TTC'),
(156,2,'INCL_TAX','incl. tax'),
(156,3,'INCL_TAX','incl. tax'),
(156,4,'INCL_TAX','incl. tax'),
(157,1,'TAGS','Tags'),
(157,2,'TAGS','Tags'),
(157,3,'TAGS','Tags'),
(157,4,'TAGS','Tags'),
(158,1,'ARCHIVES','Archives'),
(158,2,'ARCHIVES','Archives'),
(158,3,'ARCHIVES','Archives'),
(158,4,'ARCHIVES','Archives'),
(159,1,'STARS','Étoiles'),
(159,2,'STARS','Stars'),
(159,3,'STARS','Stars'),
(159,4,'STARS','Stars'),
(160,1,'HOTEL_CLASS','Catégorie d\'Hôtel'),
(160,2,'HOTEL_CLASS','Hotel Class'),
(160,3,'HOTEL_CLASS','Hotel Class'),
(160,4,'HOTEL_CLASS','Hotel Class'),
(161,1,'YOUR_BUDGET','Votre Budget'),
(161,2,'YOUR_BUDGET','Your Budget'),
(161,3,'YOUR_BUDGET','Your Budget'),
(161,4,'YOUR_BUDGET','Your Budget'),
(162,1,'LOAD_MORE','Voir plus'),
(162,2,'LOAD_MORE','Load more'),
(162,3,'LOAD_MORE','Load more'),
(162,4,'LOAD_MORE','Load more'),
(163,1,'DO_YOU_HAVE_A_COUPON','Avez-vous un code promo ?'),
(163,2,'DO_YOU_HAVE_A_COUPON','Do you have a coupon?'),
(163,3,'DO_YOU_HAVE_A_COUPON','Do you have a coupon?'),
(163,4,'DO_YOU_HAVE_A_COUPON','Do you have a coupon?'),
(164,1,'DISCOUNT','Réduction'),
(164,2,'DISCOUNT','Discount'),
(164,3,'DISCOUNT','Discount'),
(164,4,'DISCOUNT','Discount'),
(165,1,'COUPON_CODE_SUCCESS','Félicitations ! Le code promo a été ajouté avec succès.'),
(165,2,'COUPON_CODE_SUCCESS','Congratulations! The coupon code has been successfully added.'),
(165,3,'COUPON_CODE_SUCCESS','Congratulations! The coupon code has been successfully added.'),
(165,4,'COUPON_CODE_SUCCESS','Congratulations! The coupon code has been successfully added.'),
(166,1,'ROOMS','chambres'),
(166,2,'ROOMS','rooms'),
(166,3,'ROOMS','rooms'),
(166,4,'ROOMS','rooms'),
(167,1,'ADULT','adulte'),
(167,2,'ADULT','adult'),
(167,3,'ADULT','adult'),
(167,4,'ADULT','adult'),
(168,1,'CHILD','enfant'),
(168,2,'CHILD','child'),
(168,3,'CHILD','child'),
(168,4,'CHILD','child'),
(169,1,'ACTIVITY','Activité'),
(169,2,'ACTIVITY','Activity'),
(169,3,'ACTIVITY','Activity'),
(169,4,'ACTIVITY','Activity'),
(170,1,'DATE','Date'),
(170,2,'DATE','Date'),
(170,3,'DATE','Date'),
(170,4,'DATE','Date'),
(171,1,'QUANTITY','Quantité'),
(171,2,'QUANTITY','Quantity'),
(171,3,'QUANTITY','Quantity'),
(171,4,'QUANTITY','Quantity'),
(172,1,'SERVICE','Service'),
(172,2,'SERVICE','Service'),
(172,3,'SERVICE','Service'),
(172,4,'SERVICE','Service'),
(173,1,'BOOKING_NOTICE','<h2>Réservez sur notre site</h2><p class=\"lead mb0\">Dépêchez-vous ! Sélectionnez vos chambres, complétez votre réservation et profitez de nos packages et offres spéciales ! <br><b>Meilleur prix garanti !</b></p>'),
(173,2,'BOOKING_NOTICE','<h2>Book on our website</h2><p class=\"lead mb0\">Hurry up! Select the your rooms, complete your booking and take advantage of our special offers and packages!<br><b>Best price guarantee!</b></p>'),
(173,3,'BOOKING_NOTICE','<h2>Book on our website</h2><p class=\"lead mb0\">Hurry up! Select the your rooms, complete your booking and take advantage of our special offers and packages!<br><b>Best price guarantee!</b></p>'),
(173,4,'BOOKING_NOTICE','<h2>Book on our website</h2><p class=\"lead mb0\">Hurry up! Select the your rooms, complete your booking and take advantage of our special offers and packages!<br><b>Best price guarantee!</b></p>'),
(174,1,'NUM_ROOMS','Nb chambres'),
(174,2,'NUM_ROOMS','Num rooms'),
(174,3,'NUM_ROOMS','Num rooms'),
(174,4,'NUM_ROOMS','Num rooms'),
(175,1,'TOP_DESTINATIONS','Top Destinations'),
(175,2,'TOP_DESTINATIONS','Top Destinations'),
(175,3,'TOP_DESTINATIONS','Top Destinations'),
(175,4,'TOP_DESTINATIONS','Top Destinations'),
(176,1,'BEST_RATES_SUBTITLE','Meilleurs tarifs à partir de seulement {min_price}'),
(176,2,'BEST_RATES_SUBTITLE','Best rates starting at just {min_price}'),
(176,3,'BEST_RATES_SUBTITLE','Best rates starting at just {min_price}'),
(176,4,'BEST_RATES_SUBTITLE','Best rates starting at just {min_price}'),
(177,1,'CONTINUE_AS_GUEST','Continuer sans m\'enregistrer'),
(177,2,'CONTINUE_AS_GUEST','Continue as guest'),
(177,3,'CONTINUE_AS_GUEST','Continue as guest'),
(177,4,'CONTINUE_AS_GUEST','Continue as guest'),
(178,1,'PRIVACY_POLICY_AGREEMENT','<small>J\'accepte que les informations recueillies par ce formulaire soient stockées dans un fichier informatisé afin de traiter ma demande.<br>Conformément au \"Réglement Général sur la Protection des Données\", vous pouvez exercer votre droit d\'accès aux données vous concernant et les faire rectifier via le formulaire de contact.</small>'),
(178,2,'PRIVACY_POLICY_AGREEMENT','<small>I agree that the information collected by this form will be stored in a database in order to process my request.<br>In accordance with the \"General Data Protection Regulation\", you can exercise your right to access to your data and make them rectified via the contact form.</small>'),
(178,3,'PRIVACY_POLICY_AGREEMENT','<small>I agree that the information collected by this form will be stored in a database in order to process my request.<br>In accordance with the \"General Data Protection Regulation\", you can exercise your right to access to your data and make them rectified via the contact form.</small>'),
(178,4,'PRIVACY_POLICY_AGREEMENT','<small>I agree that the information collected by this form will be stored in a database in order to process my request.<br>In accordance with the \"General Data Protection Regulation\", you can exercise your right to access to your data and make them rectified via the contact form.</small>'),
(179,1,'COMPLETE_YOUR_BOOKING','Terminez votre réservation !'),
(179,2,'COMPLETE_YOUR_BOOKING','Complete your booking!'),
(179,3,'COMPLETE_YOUR_BOOKING','Complete your booking!'),
(179,4,'COMPLETE_YOUR_BOOKING','Complete your booking!'),
(180,1,'CHILDREN_AGE','Age des enfants'),
(180,2,'CHILDREN_AGE','Age of children'),
(180,3,'CHILDREN_AGE','Age of children'),
(180,4,'CHILDREN_AGE','Age of children'),
(181,1,'I_AM_HOTEL_OWNER','Je suis propriétaire'),
(181,2,'I_AM_HOTEL_OWNER','I am a hotel owner'),
(181,3,'I_AM_HOTEL_OWNER','I am a hotel owner'),
(181,4,'I_AM_HOTEL_OWNER','I am a hotel owner'),
(182,1,'I_AM_TRAVELER','Je suis vacancier'),
(182,2,'I_AM_TRAVELER','I am a traveler'),
(182,3,'I_AM_TRAVELER','I am a traveler'),
(182,4,'I_AM_TRAVELER','I am a traveler'),
(183,1,'BOOK_NOW','Réserver maintenant'),
(183,2,'BOOK_NOW','Book now'),
(183,3,'BOOK_NOW','Book now'),
(183,4,'BOOK_NOW','Book now'),
(184,1,'LOCATION','Emplacement'),
(184,2,'LOCATION','Location'),
(184,3,'LOCATION','Location'),
(184,4,'LOCATION','Location'),
(185,1,'DISCOVER_ALSO','Découvrez aussi'),
(185,2,'DISCOVER_ALSO','Discover also'),
(185,3,'DISCOVER_ALSO','Discover also'),
(185,4,'DISCOVER_ALSO','Discover also'),
(186,1,'PAYMENT_BRAINTREE_NOTICE','Remplissez le formulaire ci-dessous avec les informations de votre carte de crédit, puis cliquez sur \"Payer\".'),
(186,2,'PAYMENT_BRAINTREE_NOTICE','Fill in the form bellow with your credit card information, then click on \"Check Out\".'),
(186,3,'PAYMENT_BRAINTREE_NOTICE','Fill in the form bellow with your credit card information, then click on \"Check Out\".'),
(186,4,'PAYMENT_BRAINTREE_NOTICE','Fill in the form bellow with your credit card information, then click on \"Check Out\".'),
(187,1,'COUPON_CODE_FAILURE','Erreur : ce code est invalide ou a déjà été utilisé'),
(187,2,'COUPON_CODE_FAILURE','Error: this code is invalid or already used'),
(187,3,'COUPON_CODE_FAILURE','Error: this code is invalid or already used'),
(187,4,'COUPON_CODE_FAILURE','Error: this code is invalid or already used'),
(188,1,'PAYMENT_RAZORPAY_NOTICE','Cliquez sur \"Payer\", puis remplissez le formulaire avec les informations de votre carte de crédit.'),
(188,2,'PAYMENT_RAZORPAY_NOTICE','Click on \"Check Out\", then fill in the form with your credit card information.'),
(188,3,'PAYMENT_RAZORPAY_NOTICE','Click on \"Check Out\", then fill in the form with your credit card information.'),
(188,4,'PAYMENT_RAZORPAY_NOTICE','Click on \"Check Out\", then fill in the form with your credit card information.'),
(189,1,'YO','y.o.'),
(189,2,'YO','ans'),
(189,3,'YO','y.o.'),
(189,4,'YO','ans');

/*Table structure for table `pm_user` */

DROP TABLE IF EXISTS `pm_user`;

CREATE TABLE `pm_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `login` varchar(50) DEFAULT NULL,
  `pass` varchar(100) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `add_date` int(11) DEFAULT NULL,
  `edit_date` int(11) DEFAULT NULL,
  `checked` int(11) DEFAULT 0,
  `fb_id` varchar(50) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `token` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `pm_user` */

insert  into `pm_user`(`id`,`firstname`,`lastname`,`email`,`login`,`pass`,`type`,`add_date`,`edit_date`,`checked`,`fb_id`,`address`,`postcode`,`city`,`company`,`country`,`mobile`,`phone`,`token`) values 
(1,'amar','chan','amar.chan9655@gmail.com','amar','46793e81d4332d0fa15b76f826d27470','administrator',1596357583,1596378426,1,'','','','','','','','',''),
(2,'olexsandr','hudenets','gudenets2121@gmail.com','olex','718df9618446fcdf8cff4e6f17613b31','manager',1596358189,1596358189,1,NULL,'','','','','','','',NULL),
(3,'somchai','saeueng','somchai.saeueng418@gmail.com','somchai','3a6190b2707282ce11ded4f410861de4','editor',1596358453,1596358453,1,NULL,'','','','','','','',NULL),
(4,'alyosha','karamazov','alyosha.karamazov227@gmail.com','alyosha','16fbc14913d34b937e8ae7813a9c44e0','hotel',1596358612,1596362461,1,NULL,'','','','','','','','2928a99a860857dc8c66fe3132c246f1'),
(5,'danil','gregory','danil.gregory@gmail.com','danil','e2cbf507237ab3baba97afc96eea8553','registered',1596360061,1596360061,1,NULL,'','','','','','','',NULL),
(6,'mikhail','popov','mikhail@gmail.com','mikhail','66538dfdef5db5349744ae774e19a39a','registered',1596360433,1596362507,1,NULL,'','','','','','','','269b184d6fa9e4c14a33193c48b047cb');

/*Table structure for table `pm_widget` */

DROP TABLE IF EXISTS `pm_widget`;

CREATE TABLE `pm_widget` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` int(11) NOT NULL,
  `title` varchar(250) DEFAULT NULL,
  `showtitle` int(11) DEFAULT NULL,
  `pos` varchar(20) DEFAULT NULL,
  `allpages` int(11) DEFAULT NULL,
  `pages` varchar(250) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `class` varchar(200) DEFAULT NULL,
  `checked` int(11) DEFAULT 0,
  `rank` int(11) DEFAULT 0,
  PRIMARY KEY (`id`,`lang`),
  KEY `widget_lang_fkey` (`lang`),
  CONSTRAINT `widget_lang_fkey` FOREIGN KEY (`lang`) REFERENCES `pm_lang` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `pm_widget` */

insert  into `pm_widget`(`id`,`lang`,`title`,`showtitle`,`pos`,`allpages`,`pages`,`type`,`content`,`class`,`checked`,`rank`) values 
(1,1,'Qui sommes-nous ?',1,'footer_col_1',1,'','','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget auctor ipsum. Mauris pharetra neque a mauris commodo, at aliquam leo malesuada. Maecenas eget elit eu ligula rhoncus dapibus at non erat. In sed velit eget eros gravida consectetur varius imperdiet lectus.</p>\r\n',NULL,1,1),
(1,2,'About us',1,'footer_col_1',1,'','','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget auctor ipsum. Mauris pharetra neque a mauris commodo, at aliquam leo malesuada. Maecenas eget elit eu ligula rhoncus dapibus at non erat. In sed velit eget eros gravida consectetur varius imperdiet lectus.</p>\r\n',NULL,1,1),
(1,3,'عنا',1,'footer_col_1',1,'','','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget auctor ipsum. Mauris pharetra neque a mauris commodo, at aliquam leo malesuada. Maecenas eget elit eu ligula rhoncus dapibus at non erat. In sed velit eget eros gravida consectetur varius imperdiet lectus.</p>\r\n',NULL,1,1),
(1,4,'About us',1,'footer_col_1',1,'','','<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget auctor ipsum. Mauris pharetra neque a mauris commodo, at aliquam leo malesuada. Maecenas eget elit eu ligula rhoncus dapibus at non erat. In sed velit eget eros gravida consectetur varius imperdiet lectus.</p>\r\n',NULL,1,1),
(3,1,'Derniers articles',1,'footer_col_2',1,'','latest_articles','','',1,2),
(3,2,'Latest articles',1,'footer_col_2',1,'','latest_articles','','',1,2),
(3,3,'المقالات الأخيرة',1,'footer_col_2',1,'','latest_articles','','',1,2),
(3,4,'Latest articles',1,'footer_col_2',1,'','latest_articles','','',1,2),
(4,1,'Contactez-nous',0,'footer_col_3',1,'','contact_informations','','',1,3),
(4,2,'Contact us',0,'footer_col_3',1,'','contact_informations','','',1,3),
(4,3,'اتصل بنا',0,'footer_col_3',1,'','contact_informations','','',1,3),
(4,4,'Contact us',0,'footer_col_3',1,'','contact_informations','','',1,3),
(5,1,'Footer form',0,'footer_col_3',1,'','footer_form','','footer-form mt10',2,4),
(5,2,'Footer form',0,'footer_col_3',1,'','footer_form','','footer-form mt10',2,4),
(5,3,'Footer form',0,'footer_col_3',1,'','footer_form','','footer-form mt10',2,4),
(5,4,'Footer form',0,'footer_col_3',1,'','footer_form','','footer-form mt10',2,4),
(6,1,'Blog side',0,'right',0,'17','blog_side','','',1,5),
(6,2,'Blog side',0,'right',0,'17','blog_side','','',1,5),
(6,3,'Blog side',0,'right',0,'17','blog_side','','',1,5),
(6,4,'Blog side',0,'right',0,'17','blog_side','','',1,5);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
