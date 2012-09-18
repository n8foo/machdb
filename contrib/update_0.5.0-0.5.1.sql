# disk and filesystem size gets bigger
ALTER TABLE `disk` CHANGE `size` `size` bigint UNSIGNED NOT NULL;
ALTER TABLE `filesystem` CHANGE `size` `size` bigint UNSIGNED NOT NULL;
ALTER TABLE `archive_filesystem` CHANGE `size` `size` bigint UNSIGNED NOT NULL;

# cpu gets unified_cache filed
ALTER TABLE `cpu` ADD `unified_cache` int(1) DEFAULT NULL  AFTER `cache`;

# packages add 'release'
ALTER TABLE `pkg` ADD `release` varchar(30) DEFAULT NULL  AFTER `version`;
ALTER TABLE `pkg` ADD INDEX  (`release`);

# os name gets bigger
ALTER TABLE `os` CHANGE `name` `name` varchar(100) NOT NULL DEFAULT '' ;
