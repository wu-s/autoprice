
CREATE TABLE `inquiry_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inquiry_time` datetime NOT NULL,
  `state` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `zip` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `utility_provider` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `price` decimal(12,2) not null default 0,
  `type` int(11) NOT NULL default 1,
  `insert_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  `url` text,
  `params` text,
  `response` text,
  PRIMARY KEY (`id`),
  KEY `inquiry_time` (`inquiry_time`,`state`),
  KEY `state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;