ALTER TABLE `#__vq_emails` ADD `title` VARCHAR( 255 ) NULL AFTER `id` ;
ALTER TABLE `#__vq_authors` ADD `user_id` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `published` ;