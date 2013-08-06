ALTER TABLE `#__vq_authors` ADD `copyright` VARCHAR( 1024 ) NULL DEFAULT NULL AFTER `thumb`;
ALTER TABLE `#__vq_authors` CHANGE `alias` `alias` VARCHAR( 64 ) NOT NULL AFTER name;
ALTER TABLE `#__vq_authors` CHANGE `image` `image` VARCHAR( 32 ) NULL DEFAULT NULL;
ALTER TABLE `#__vq_authors` CHANGE `thumb` `thumb` VARCHAR( 32 ) NULL DEFAULT NULL;
ALTER TABLE `#__vq_authors` CHANGE `published` `published` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';
