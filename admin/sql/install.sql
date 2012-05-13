CREATE TABLE IF NOT EXISTS `#__vq_categories` (
  `id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(32) NOT NULL,
  `desc` text,
  `url` varchar(64) NOT NULL DEFAULT '0000-00-00',
  `published` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vq_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `quote` varchar(5120) NOT NULL,
  `author` varchar(128) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `votes` smallint(6) unsigned NOT NULL,
  `rating` float(10,2) unsigned NOT NULL,
  `likes` smallint(6) unsigned NOT NULL DEFAULT '0',
  `catid` tinyint(4) unsigned NOT NULL,
  `published` tinyint(1) unsigned DEFAULT '1',
  `ordering` smallint(6) unsigned NOT NULL,
  `quote_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vq_quotes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `quote` varchar(5120) NOT NULL,
  `author` varchar(128) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `votes` smallint(6) unsigned NOT NULL DEFAULT '0',
  `rating` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `likes` smallint(6) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL,
  `catid` smallint(6) unsigned NOT NULL,
  `ordering` smallint(6) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vq_votes` (
  `user_id` int(11) unsigned NOT NULL,
  `quote_id` int(11) unsigned NOT NULL,
  `ip` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;