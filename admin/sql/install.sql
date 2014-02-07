CREATE TABLE IF NOT EXISTS `#__vq_authors` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `alias` varchar(64) NOT NULL,
  `bio` text,
  `image` varchar(32) DEFAULT NULL,
  `thumb` varchar(32) DEFAULT NULL,
  `copyright` varchar(1024) DEFAULT NULL,
  `hits` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ordering` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vq_emails` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `sender_email` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vq_pages` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` bigint(20) NOT NULL DEFAULT '0',
  `title` varchar(512) NOT NULL,
  `page_url` varchar(255) NOT NULL DEFAULT '',
  `pic` varchar(255) NOT NULL DEFAULT '',
  `pic_square` varchar(255) NOT NULL DEFAULT '',
  `fans` int(11) unsigned NOT NULL DEFAULT '0',
  `type` varchar(32) NOT NULL DEFAULT '',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Facebook pages';

CREATE TABLE IF NOT EXISTS `#__vq_quotes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `quote` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` smallint(5) unsigned NOT NULL DEFAULT '0',
  `hits` smallint(5) unsigned NOT NULL DEFAULT '0',
  `author_id` smallint(6) DEFAULT '0',
  `catid` int(11) DEFAULT '0',
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_itpvq_quser_id` (`user_id`),
  KEY `idx_itpvq_qcat_id` (`catid`),
  KEY `idx_itpvq_qauthor_id` (`author_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__vq_tabs` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `app_id` bigint(20) NOT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `page_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;