CREATE TABLE IF NOT EXISTS `#__vq_emails` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `sender_name` varchar(255) DEFAULT NULL,
  `sender_email` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;