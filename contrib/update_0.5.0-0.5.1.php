<?php
include 'include/config.php';
include 'include/common.php';

# connect to DB
$db = connect_db($mysqlserver,$mysqluser,$mysqlpassword,$mysqldatabase);


# disk and filesystem size gets bigger
print("ALTER TABLE `disk` CHANGE `size` `size` bigint UNSIGNED NOT NULL;\n");
query_db("ALTER TABLE `disk` CHANGE `size` `size` bigint UNSIGNED NOT NULL;");
print("ALTER TABLE `filesystem` CHANGE `size` `size` bigint UNSIGNED NOT NULL;\n");
query_db("ALTER TABLE `filesystem` CHANGE `size` `size` bigint UNSIGNED NOT NULL;");
print("ALTER TABLE `archive_filesystem` CHANGE `size` `size` bigint UNSIGNED NOT NULL;\n");
query_db("ALTER TABLE `archive_filesystem` CHANGE `size` `size` bigint UNSIGNED NOT NULL;");

# OS name can be over 30 characters, change to 100
print("ALTER TABLE `os` CHANGE `name` `name` varchar(100) NOT NULL DEFAULT '' ;\n");
query_db("ALTER TABLE `os` CHANGE `name` `name` varchar(100) NOT NULL DEFAULT '' ;");

# cpu gets unified_cache filed
print("ALTER TABLE `cpu` ADD `unified_cache` int(1) DEFAULT NULL  AFTER `cache`;\n");
query_db("ALTER TABLE `cpu` ADD `unified_cache` int(1) DEFAULT NULL  AFTER `cache`;");

# packages add 'release'
print("ALTER TABLE `pkg` ADD `release` varchar(30) DEFAULT NULL AFTER `version`;\n");
query_db("ALTER TABLE `pkg` ADD `release` varchar(30) DEFAULT NULL AFTER `version`;");
print("ALTER TABLE `pkg` ADD INDEX  (`release`);\n");
query_db("ALTER TABLE `pkg` ADD INDEX  (`release`);");


// If you want to use the 'release' field, uncomment the following block.

/*
# put all the pkg from db in an array
$result = query_db("SELECT * FROM `pkg` where `release` is NULL");
while ($pkg = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $id = $pkg["id"];
        $name = $pkg["name"];
        $db_version = $pkg["version"];
        list($version,$release) = split("-", $db_version);
	query_db("UPDATE `pkg` SET `version` = '$version', `release` = '$release' WHERE `id` = '$id'");
	#print("UPDATE `pkg` SET `version` = '$version', `release` = '$release' WHERE `id` = '$id'\n");
	print("$name: $version  - $release\n");
}

*/

close_db($db);

echo "Done\n";
?>
