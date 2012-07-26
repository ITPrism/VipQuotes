SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `tmp_#__vq_quotes`;

RENAME TABLE `#__vq_quotes` TO `tmp_#__vq_quotes`;

DROP TABLE IF EXISTS `#__vq_votes`;

DROP TABLE IF EXISTS `#__vq_history`;

DROP TABLE IF EXISTS `#__vq_categories`;

CREATE TABLE IF NOT EXISTS `#__vq_quotes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `quote` varchar(5120) NOT NULL,
  `author` varchar(128) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published` tinyint(1) UNSIGNED NOT NULL,
  `catid` smallint(6) UNSIGNED NOT NULL,
  `ordering` smallint(6) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY(`id`),
  INDEX `idx_itpvq_user_id`(`user_id`),
  CONSTRAINT `FK_itpvq_user_id` FOREIGN KEY (`user_id`)
    REFERENCES `#__users`(`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT
)
ENGINE=INNODB
CHARACTER SET utf8 
COLLATE utf8_general_ci ;

INSERT INTO `#__vq_quotes` (`id`,`quote`,`author`,`date`,`published`,`catid`,`ordering`,`user_id`) SELECT `id`,`quote`,`author`,`date`,`published`,`catid`,`ordering`,`user_id` FROM `tmp_#__vq_quotes`;

SET FOREIGN_KEY_CHECKS=1;