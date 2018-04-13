CREATE TABLE `inquiry_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inquiry_time` datetime NOT NULL,
  `state` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `zip` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `code` int(11) DEFAULT NULL,
  `success` int(11) DEFAULT 0,
  `utility_provider` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `type` int(11) NOT NULL DEFAULT '1',
  `insert_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `url` text COLLATE utf8_unicode_ci,
  `params` text COLLATE utf8_unicode_ci,
  `response` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `inquiry_time` (`inquiry_time`,`state`),
  KEY `state` (`state`)
) ENGINE=InnoDB AUTO_INCREMENT=5138 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;