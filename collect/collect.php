<?php
# This file is part of MachDB.  For license info, read LICENSE

include '../include/config.php';
include '../include/common.php';

#print_r($_POST['xml']);

$xmlstr = $_POST['xml'];
$xmlstr = stripslashes($xmlstr);
$xml = new SimpleXMLElement($xmlstr);

echo "hostname:", (string) ($xml->hostname), "\n";
echo "hwid:", (string) ($xml->hwid), "\n";



#############
# MAIN
#############

# quick error checks.  hwid must exist.  also, keep a log of hosts connecting
if ($xml->hwid) {
   if ($loglevel > 1) {
      error_log ("Host " . $xml->hostname . " seen at " . date(DATE_ATOM, mktime(0, 0, 0, 7, 1, 2000)), 3, $logfile);
   }
} else {
   error_log ("Host " . $xml->hostname ." has no hwid, bailing out!", 3, $logfile);
   exit;
}

$hostname  = (string) ($xml->hostname);
$domain    = (string) ($xml->domain);
$hwid      = (string) ($xml->hwid);
$cpu_count = (int) ($xml->cpu->count);
$swaptotal = (int) ($xml->memory->swaptotal);
$memtotal  = (int) ($xml->memory->memtotal);
$os_name   = (string) quotemeta($xml->osname);
$os_arch   = (string) quotemeta($xml->arch);
$os_basearch   = (string) quotemeta($xml->basearch);
$os_kernel = (string) quotemeta($xml->kernel);
$cpu_cache = (int) ($xml->cpu->cache);
$cpu_name  = (string) quotemeta($xml->cpu->name);
$cpu_speed = (int) ($xml->cpu->speed);

# connect to DB
$db = connect_db($mysqlserver,$mysqluser,$mysqlpassword,$mysqldatabase);

#print_result(query_db("SELECT host.id,host.hostname,network.macaddr FROM host,network WHERE host.hostname = '$xml->hostname' AND network.macaddr = '$xml->macaddr' AND host.id = network.host_id"));

# search for the hardware id to see if it's in the DB
$host = mysql_fetch_assoc(query_db("SELECT * FROM host WHERE host.hwid = '$hwid'"));
$os_id = insert_os($os_name,$os_arch,$os_basearch,$os_kernel); 
if ($xml->cpu->unified_cache) {
  $cpu_unified_cache = (bool) ($xml->cpu->unified_cache);
} else {
  $cpu_unified_cache = false;
}
$cpu_id = insert_cpu($cpu_cache,$cpu_name,$cpu_speed,$cpu_unified_cache);
if ($host["hwid"]) {
	echo "this host exists with hwid of : $host[hwid]\n";
	$host_id = update_host($hostname,$domain,$hwid,$os_id,$cpu_id,$cpu_count,$swaptotal,$memtotal);
} else {
	echo "this is new hardware\n";
	$host_id = insert_host($hostname,$domain,$hwid,$os_id,$cpu_id,$cpu_count,$swaptotal,$memtotal);
}
if ($xml->nic) { insert_network($host_id,$xml->nic); }
if ($xml->disk) { machdb_disk($host_id,$xml->disk); }
if ($xml->filesystem) { machdb_filesystem($host_id,$xml->filesystem); }
if ($xml->package) { machdb_package($host_id,$xml->package); }
if ($xml->bios) { machdb_bios($host_id,$xml->bios); }
if ($xml->system) { machdb_system($host_id,$xml->system); }
if ($xml->chassis) { machdb_chassis($host_id,$xml->chassis); }
if ($xml->motherboard) { machdb_mb($host_id,$xml->motherboard); }


################
# insert/update filesystem function
################

function machdb_filesystem($host_id,$filesystem){

	if (!$host_id){echo "NO HOST ID\n"; die;};
#$array = array("device","mountpoint,","size","type");
foreach($filesystem as $param) {
	$device = (string) quotemeta($param->device);
	$mountpoint = (string) quotemeta($param->mountpoint);
	$size = (int) quotemeta($param->size);
	$type = (string) quotemeta($param->type);

	$filesystem_db = mysql_fetch_assoc(query_db("SELECT * FROM filesystem WHERE filesystem.host_id = '$host_id' AND filesystem.device='$device'"));
	$filesystem_xml = array(	"device" => (string) $param->device,
					"mountpoint" => (string) $param->mountpoint,
					"size" => (string) $param->size,
					"status" => "1",
					"type" => (string) $param->type );
	#echo "filesystem_db: "; print_r($filesystem_db); echo "filesystem_xml: "; print_r($filesystem_xml);
	if ($filesystem_db) {
	        $updated_filesystem_db = (array_merge($filesystem_db,$filesystem_xml));
        	$filesystem_diff = (array_diff_assoc($updated_filesystem_db,$filesystem_db));
		if ($filesystem_diff){
			#echo "$mountpoint diff:\n"; print_r($filesystem_diff);
        		echo "$mountpoint has changed, archiving...";
        		query_db("INSERT INTO archive_filesystem SELECT * FROM filesystem WHERE id = '$filesystem_db[id]'");
			echo "updating...";
        		array_walk($filesystem_diff,'update_filesystem_value',$filesystem_db["id"]);
			echo "done.\n";
		}
	} else {
		echo "inserting new mountpoint $mountpoint...";
	        query_db("INSERT INTO filesystem (`id`,`host_id`,`device`,`mountpoint`,`size`,`type`,`timestamp`) VALUES (NULL,'$host_id','$device','$mountpoint','$size','$type',NULL)");
		echo "done.\n";
	}
}
# process again to disable inactive mounts
# put all the filesystems from XML in an array
foreach($filesystem as $param) {
	$mountpoint = (string) $param->mountpoint;
	$cleanup_xml_filesystem[] = $mountpoint;
}

# put all the filesystem from db in an array
$result = query_db("SELECT mountpoint FROM filesystem where host_id = '$host_id' AND status = '1'");
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    foreach ($line as $col_value) {
        $cleanup_db_filesystem[] = $col_value;
	}
}
if ($cleanup_db_filesystem) {	
	# compare the 2 newly created arrays, set status, record history 
	#echo "xml:"; print_r($cleanup_xml_filesystem); echo "db:"; print_r($cleanup_db_filesystem);
	$filesystem_diff = (array_diff($cleanup_db_filesystem,$cleanup_xml_filesystem));
	#print_r($filesystem_diff);
	foreach($filesystem_diff as $col_value) {
		$mountpoint = $col_value;
		echo "$mountpoint no longer used, archiving...";
	       	query_db("INSERT INTO archive_filesystem SELECT * FROM filesystem WHERE host_id = '$host_id' AND mountpoint = '$mountpoint'");
		echo "setting inactive status...";
		query_db("UPDATE filesystem SET status=0,timestamp=NOW() WHERE host_id = '$host_id' AND mountpoint = '$mountpoint'");
		echo "done\n";
	}
}

}
#############

################
# insert/update disks
################

function machdb_disk($host_id,$disk){

if (!$host_id){echo "NO HOST ID\n"; die;};
# set up an array with all the right info
#$array = array("device","model,","size","type");
foreach($disk as $param) {
	$device = (string) quotemeta($param->device);
	$model = (string) quotemeta($param->model);
	$size = (int) ($param->size);
	$type = (string) quotemeta($param->type);
	# lookup the disk info based on type, model and size
	$disk_db = mysql_fetch_assoc(query_db("SELECT * FROM disk WHERE disk.type = '$type' AND disk.model='$model' AND disk.size = '$size'"));
	if ($disk_db) {
		# if it exists, just set the disk_id so we know what to use
		$disk_id = $disk_db["id"];
	} else {
		# if it doesn't exist, insert a new entry into the disk table and set disk_id
		echo "NEW disk $model, adding...\n";
		query_db("INSERT INTO disk (`id`,`model`,`size`,`type`) VALUES (NULL,'$model','$size','$type')");
		$disk_id = mysql_insert_id();
		echo "done.\n";
	}

	# populate an array with the info from this disk that is in the host_disk map table, if it exists
	$host_disk_db = mysql_fetch_assoc(query_db("SELECT * FROM host_disk WHERE host_id = '$host_id' AND device = '$device'"));

	if ($host_disk_db) {
		# if it's already in the db, run thru and check for updates and record archive
		$host_disk_xml = array(	"device" => (string) $param->device,
					"disk_id" => $disk_id,
					"status" => "1",
					"host_id" => (string) $host_id);
		$updated_host_disk_db = (array_merge($host_disk_db,$host_disk_xml));
		$host_disk_diff = (array_diff_assoc($updated_host_disk_db,$host_disk_db));
		#echo "$host_disk_db[device] host_disk_db: "; print_r($host_disk_db); echo "host_disk_xml: "; print_r($host_disk_xml); echo "diff:"; print_r($host_disk_diff);
		# if there is a difference, call the update function and record archive
		if ($host_disk_diff){
			#echo $device . "\n"; print_r ($host_disk_diff);
        		echo "$device has changed, archiving...";
			query_db("INSERT INTO archive_host_disk SELECT * FROM host_disk WHERE host_disk.id = '$host_disk_db[id]'");
			echo "updating...";
			array_walk($host_disk_diff,'update_host_disk_value',$host_disk_db["id"]);
			echo "done\n";
		}
	} else {
		# if it's not in the DB, insert it
		echo "NEW $device, adding...";
		query_db("INSERT INTO host_disk (`id`,`host_id`,`disk_id`,`device`,`timestamp`) VALUES (NULL,'$host_id','$disk_id','$device',NULL)");
		echo "done.\n";
	}
	
}

# process again to disable inactive disks
# put all the disks from XML in an array
foreach($disk as $param) {
	$xml_device = (string) $param->device;
	$cleanup_xml_disk[] = $xml_device;
}

# put all the disk from db in an array
$result = query_db("SELECT device FROM host_disk where host_id = '$host_id' AND status = '1'");
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    foreach ($line as $col_value) {
        $cleanup_db_disk[] = $col_value;
	}
}
# compare the 2 newly created arrays, set status, record history 
$disk_diff = (array_diff($cleanup_db_disk,$cleanup_xml_disk));
#echo "xml:"; print_r($cleanup_xml_disk); echo "db:"; print_r($cleanup_db_disk); echo "diff:"; print_r($disk_diff);
foreach($disk_diff as $col_value) {
	$device = $col_value;
	echo "$device no longer used, archiving...";
       	query_db("INSERT INTO archive_host_disk SELECT * FROM host_disk WHERE host_id = '$host_id' AND device = '$device'");
	echo "setting inactive status...";
	query_db("UPDATE host_disk SET status=0,timestamp=NOW() WHERE host_id = '$host_id' AND device = '$device'");
	echo "done\n";
}

}
#############


################
# insert/update network function
################

function insert_network($host_id,$nic){

# shit out if no host_id is found
if (!$host_id){echo "NO HOST ID\n"; die;};
# set up the array for nic and loop thru each interface
#$array = array("interface","bcast,","macaddr","ipaddr");
foreach($nic as $param) {
	$interface = (string) quotemeta($param->interface);
	$macaddr = (string) quotemeta($param->macaddr);
	$netmask = (string) quotemeta($param->netmask);
	$broadcast = (string) quotemeta($param->broadcast);
	$ipaddr = (string) quotemeta($param->ipaddr);
	# get nic info from database, if it exists
	$nic_db = mysql_fetch_assoc(query_db("SELECT * FROM nic WHERE nic.host_id = '$host_id' AND nic.interface='$interface' AND nic.macaddr='$macaddr'"));
	$nic_xml = array(	"interface" => (string) $param->interface,
				"macaddr" => (string) $param->macaddr,
				"netmask" => (string) $param->netmask,
				"broadcast" => (string) $param->broadcast,
				"host_id" => (string) $host_id,
				"status" => "1",
				"ipaddr" => (string) $param->ipaddr );
	# echo "nic_db: "; print_r($nic_db); echo "nic_xml: "; print_r($nic_xml);
	# if it exists, merge and compare the XML and DB arrays
	if ($nic_db) {
	        $updated_nic_db = (array_merge($nic_db,$nic_xml));
        	$nic_diff = (array_diff_assoc($updated_nic_db,$nic_db));
		if ($nic_diff){
        		echo "$interface has changed, archiving...";
        		query_db("INSERT INTO archive_nic SELECT * FROM nic WHERE nic.id = '$nic_db[id]'");
			echo "updating...";
        		array_walk($nic_diff,'update_nic_value',$nic_db["id"]);
			echo "done.\n";
		}
	} else {
		# if it doesn't exist, insert new entry in table
		echo "NEW NIC $interface, inserting...";
	        query_db("INSERT INTO nic (`id`,`host_id`,`macaddr`,`interface`,`netmask`,`ipaddr`,`broadcast`,`timestamp`) VALUES (NULL,'$host_id','$macaddr','$interface','$netmask','$ipaddr','$broadcast',NULL)");
		echo "done.\n";
	}
}
# process again to disable inactive nics
# put all the nics from XML in an array
foreach($nic as $param) {
	$interface = (string) $param->interface;
	$cleanup_xml_nic[] = $interface;
}

# put all the nics from db in an array
$result = query_db("SELECT interface FROM nic where nic.host_id = '$host_id' AND status = '1'");
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    foreach ($line as $col_value) {
        $cleanup_db_nic[] = $col_value;
	}
}
# compare the 2 newly created arrays, set status, record history 
#echo "xml:"; print_r($cleanup_xml_nic); echo "db:"; print_r($cleanup_db_nic);
$nic_diff = (array_diff($cleanup_db_nic,$cleanup_xml_nic));
foreach($nic_diff as $col_value) {
	$interface = $col_value;
	echo "$interface no longer used, archiving...";
       	query_db("INSERT INTO archive_nic SELECT * FROM nic WHERE host_id = '$host_id' AND interface = '$interface'");
	echo "setting inactive status...";
	query_db("UPDATE nic SET status=0,timestamp=NOW() WHERE host_id = '$host_id' AND interface = '$interface'");
	echo "done\n";
}

}
#############

################
# insert/update bios
################

function machdb_bios($host_id,$bios){
	echo "BIOS: ";
	if (!$host_id){echo "NO HOST ID\n"; die;};
	# set up an array with all the right info
	$vendor = (string) quotemeta($bios->vendor);
	$version = (string) quotemeta($bios->version);
	$date = (string) quotemeta($bios->date);
	# lookup the package info based on type, model and size
	$bios_db = mysql_fetch_assoc(query_db("SELECT * FROM bios WHERE vendor='$vendor' AND version='$version' AND date='$date'"));
	if ($bios_db) {
		# if it exists, just set the disk_id so we know what to use
		$bios_id = $bios_db["id"];
	} else {
		# if it doesn't exist, insert a new entry into the bios table and set bios_id
		query_db("INSERT INTO bios (`id`,`vendor`,`version`,`date`) VALUES (NULL,'$vendor','$version','$date')");
		echo "NEW : $vendor $version $date ...";
		$bios_id = mysql_insert_id();
	}
	
	# populate an array with the info from this bios that is in the host_bios map table, if it exists
	$host_bios_db = mysql_fetch_assoc(query_db("SELECT * FROM host_bios WHERE host_id = '$host_id'"));
	if ($host_bios_db) {
		$host_bios_id = $host_bios_db["id"];
	} else {
		# if it's not in the DB, insert it
		query_db("INSERT INTO host_bios (`id`,`host_id`,`bios_id`) VALUES (NULL,'$host_id','$bios_id')");
		$host_bios_id = mysql_insert_id();
	}
	
	# process again to disable inactive bioss
	# put the bios info from XML in an array
	$xml_host_bios = array ( 	"host_id" => $host_id,
					"bios_id" => $bios_id);

	# put the bios info from db in an array
	$result = query_db("SELECT host_bios.host_id,host_bios.bios_id FROM host_bios,bios WHERE bios.id = host_bios.bios_id AND host_bios.host_id = '$host_id'");
	$db_host_bios = mysql_fetch_array($result, MYSQL_ASSOC);

	# compare the 2 newly created arrays
	$host_bios_diff = (array_diff($xml_host_bios,$db_host_bios));
	#echo "XML:\n"; print_r($xml_host_bios); echo "DB:\n"; print_r($db_host_bios); echo "diff:\n"; print_r($host_bios_diff);
	if (($host_bios_diff)) {	
		# if there is a diff, archive and update
		echo "Chassis has changed, updating...archiving...";
		query_db("INSERT INTO archive_host_bios SELECT * FROM host_bios WHERE id = '$host_bios_id'");
		query_db("UPDATE host_bios SET host_id='$host_id',bios_id='$chassis_id',timestamp=NOW() WHERE host_chassis.id = '$host_bios_id'");
	}	
	echo "Done\n";
}
#############

################
# insert/update chassis
################

function machdb_chassis($host_id,$chassis){
	echo "Chassis: ";
	if (!$host_id){echo "NO HOST ID\n"; die;};
	# set up an array with all the right info
	$type = (string) quotemeta($chassis->type);
	$asset_tag = (string) quotemeta($chassis->asset_tag);
	$vendor = (string) quotemeta($chassis->vendor);
	$serial = (string) quotemeta($chassis->serial);
	# lookup the package info based on type, model and size
	$chassis_db = mysql_fetch_assoc(query_db("SELECT * FROM chassis WHERE type='$type' AND vendor='$vendor'"));
	if ($chassis_db) {
		# if it exists, just set the disk_id so we know what to use
		$chassis_id = $chassis_db["id"];
	} else {
		# if it doesn't exist, insert a new entry into the chassis table and set chassis_id
		query_db("INSERT INTO chassis (`id`,`type`,`vendor`) VALUES (NULL,'$type','$vendor')");
		echo "NEW : $vendor $type ...";
		$chassis_id = mysql_insert_id();
	}
	
	# populate an array with the info from this chassis that is in the host_chassis map table, if it exists
	$host_chassis_db = mysql_fetch_assoc(query_db("SELECT * FROM host_chassis WHERE host_id = '$host_id'"));
	if ($host_chassis_db) {
		$host_chassis_id = $host_chassis_db["id"];
	} else {
		# if it's not in the DB, insert it
		query_db("INSERT INTO host_chassis (`id`,`host_id`,`chassis_id`,`serial`,`asset_tag`) VALUES (NULL,'$host_id','$chassis_id','$serial','$asset_tag')");
		$host_chassis_id = mysql_insert_id();
	}
	
	# process again to disable inactive chassiss
	# put the chassis info from XML in an array
	$xml_host_chassis = array ( 	"host_id" => $host_id,
					"chassis_id" => $chassis_id,
					"serial" => $serial,
					"asset_tag" => $asset_tag);

	# put the chassis info from db in an array
	$result = query_db("SELECT host_chassis.host_id,host_chassis.chassis_id,host_chassis.serial,host_chassis.asset_tag FROM host_chassis,chassis WHERE chassis.id = host_chassis.chassis_id AND host_chassis.host_id = '$host_id'");
	$db_host_chassis = mysql_fetch_array($result, MYSQL_ASSOC);

	# compare the 2 newly created arrays
	$host_chassis_diff = (array_diff($xml_host_chassis,$db_host_chassis));
	#echo "XML:\n"; print_r($xml_host_chassis); echo "DB:\n"; print_r($db_host_chassis); echo "diff:\n"; print_r($host_chassis_diff);
	if (($host_chassis_diff)) {	
		# if there is a diff, archive and update
		echo "Chassis has changed, updating...archiving...";
		query_db("INSERT INTO archive_host_chassis SELECT * FROM host_chassis WHERE id = '$host_chassis_id'");
		query_db("UPDATE host_chassis SET host_id='$host_id',chassis_id='$chassis_id',serial='$serial',asset_tag='$asset_tag',timestamp=NOW() WHERE host_chassis.id = '$host_chassis_id'");
	}	
	echo "Done\n";
}

################

################
# insert/update motherboard
################

function machdb_mb($host_id,$mb){
	if (!$host_id){echo "NO HOST ID\n"; die;};
	echo "Motherboard: ";
	# set up an array with all the right info
	#$array = array("device","model,","size","type");
	$name = (string) quotemeta($mb->name);
	$version = (string) quotemeta($mb->version);
	$vendor = (string) quotemeta($mb->vendor);
	$serial = (string) ($mb->serial);
	# lookup the package info based on type, model and size
	$mb_db = mysql_fetch_assoc(query_db("SELECT * FROM mb WHERE name = '$name' AND vendor = '$vendor' AND version ='$version'"));
	if ($mb_db) {
		# if it exists, just set the disk_id so we know what to use
		$mb_id = $mb_db["id"];
	} else {
		# if it doesn't exist, insert a new entry into the mb table and set mb_id
		query_db("INSERT INTO mb (`id`,`name`,`vendor`,`version`) VALUES (NULL,'$name','$vendor','$version')");
		echo "NEW: $name";
		$mb_id = mysql_insert_id();
	}
	
	# populate an array with the info from this mb that is in the host_mb map table, if it exists
	$host_mb_db = mysql_fetch_assoc(query_db("SELECT * FROM host_mb WHERE host_id = '$host_id'"));
	if ($host_mb_db) {
		$host_mb_id = $host_mb_db["id"];
	} else {
		# if it's not in the DB, insert it
		query_db("INSERT INTO host_mb (`id`,`host_id`,`mb_id`,`serial`,`timestamp`) VALUES (NULL,'$host_id','$mb_id','$serial',NULL)");
		$host_mb_id = mysql_insert_id();
	}
	
	# process again to disable inactive mbs
	# put the mb info from XML in an array
	$xml_host_mb = array ( 	"mb_id" => $mb_id,
					"host_id" => $host_id,
					"serial" => $serial);

	# put the mb info from db in an array
	$result = query_db("SELECT host_mb.mb_id,host_mb.host_id,host_mb.serial FROM host_mb,mb WHERE mb.id = host_mb.mb_id AND host_mb.host_id = '$host_id'");
	$db_host_mb = mysql_fetch_array($result, MYSQL_ASSOC);

	# compare the 2 newly created arrays
	$host_mb_diff = (array_diff_assoc($db_host_mb,$xml_host_mb));
	#echo "XML:\n"; print_r($xml_host_mb); echo "DB:\n"; print_r($db_host_mb); echo "diff:\n"; print_r($host_mb_diff);
	if (($host_mb_diff)) {	
		# if there is a diff, archive and update
		echo "Archiving & Updating...";
		query_db("INSERT INTO archive_host_mb SELECT * FROM host_mb WHERE id = '$host_mb_id'");
		query_db("UPDATE host_mb SET host_id='$host_id',mb_id='$mb_id',serial='$serial',timestamp=NOW() WHERE host_mb.id = '$host_mb_id'");
	}	
	echo "Done\n";

}
################

################
# insert/update system
################

function machdb_system($host_id,$system){
	if (!$host_id){echo "NO HOST ID\n"; die;};
	echo "System: ";
	# set up an array with all the right info
	#$array = array("device","model,","size","type");
	$name = (string) quotemeta($system->name);
	$version = (string) quotemeta($system->version);
	$vendor = (string) quotemeta($system->vendor);
	$serial = (string) quotemeta($system->serial);
	$uuid = (string) quotemeta($system->uuid);
	# lookup the package info based on type, model and size
	$system_db = mysql_fetch_assoc(query_db("SELECT * FROM system WHERE name = '$name' AND vendor = '$vendor' AND version ='$version'"));
	if ($system_db) {
		# if it exists, just set the disk_id so we know what to use
		$system_id = $system_db["id"];
	} else {
		# if it doesn't exist, insert a new entry into the system table and set system_id
		query_db("INSERT INTO system (`id`,`name`,`vendor`,`version`) VALUES (NULL,'$name','$vendor','$version')");
		echo "NEW: $name";
		$system_id = mysql_insert_id();
	}
	
	# populate an array with the info from this system that is in the host_system map table, if it exists
	$host_system_db = mysql_fetch_assoc(query_db("SELECT * FROM host_system WHERE host_id = '$host_id'"));
	if ($host_system_db) {
		$host_system_id = $host_system_db["id"];
	} else {
		# if it's not in the DB, insert it
		query_db("INSERT INTO host_system (`id`,`host_id`,`system_id`,`serial`,`uuid`,`timestamp`) VALUES (NULL,'$host_id','$system_id','$serial','$uuid',NULL)");
		$host_system_id = mysql_insert_id();
	}
	
	# process again to disable inactive systems
	# put the system info from XML in an array
	$xml_host_system = array ( 	"system_id" => $system_id,
					"host_id" => $host_id,
					"serial" => $serial,
					"uuid" => $uuid);

	# put the system info from db in an array
	$result = query_db("SELECT host_system.system_id,host_system.host_id,host_system.serial,host_system.uuid FROM host_system,system WHERE system.id = host_system.system_id AND host_system.host_id = '$host_id'");
	$db_host_system = mysql_fetch_array($result, MYSQL_ASSOC);

	# compare the 2 newly created arrays
	$host_system_diff = (array_diff($db_host_system,$xml_host_system));
	#echo "XML:\n"; print_r($xml_host_system); echo "DB:\n"; print_r($db_host_system); echo "diff:\n"; print_r($host_system_diff);
	if (($host_system_diff)) {	
		# if there is a diff, archive and update
		echo "Archiving & Updating...";
		query_db("INSERT INTO archive_host_system SELECT * FROM host_system WHERE id = '$host_system_id'");
		query_db("UPDATE host_system SET host_id='$host_id',system_id='$system_id',serial='$serial',uuid='$uuid',timestamp=NOW() WHERE host_system.id = '$host_system_id'");
	}	
	echo "Done\n";

}

##########################3

################
# insert/update packages
################

function machdb_package($host_id,$packages){

if (!$host_id){echo "NO HOST ID\n"; die;};
# set up an array with all the right info
#$array = array("device","model,","size","type");
echo "Packages: ";
foreach($packages as $param) {
	$name = (string) quotemeta($param->name);
	$version = (string) quotemeta($param->version);
	$release = (string) quotemeta($param->release);
	if (!$release) { $release = "0"; };
	$group = (string) quotemeta($param->group);
	$arch = (string) quotemeta($param->arch);
	# lookup the package info based on type, model and size
	$pkg_db = mysql_fetch_assoc(query_db("SELECT * FROM `pkg` WHERE pkg.name = '$name' AND `arch` = '$arch' AND `version` ='$version' AND `release` ='$release'"));
	if ($pkg_db) {
		# if it exists, just set the disk_id so we know what to use
		$pkg_id = $pkg_db["id"];
	} else {
		# if it doesn't exist, insert a new entry into the pkg table and set pkg_id
                $db_string="INSERT INTO pkg (`id`,`name`,`arch`,`version`,`release`) VALUES (NULL,'$name','$arch','$version','$release')";
                #echo "Running $db_string \n";
                query_db($db_string);

		echo "N";
		$pkg_id = mysql_insert_id();
	}

	# populate an array with the info from this pkg that is in the host_pkg map table, if it exists
	$host_pkg_db = mysql_fetch_assoc(query_db("SELECT * FROM host_pkg WHERE host_id = '$host_id' AND pkg_id = '$pkg_id'"));

	if ($host_pkg_db) {
		#echo ".";
	} else {
		# if it's not in the DB, insert it
		query_db("INSERT INTO host_pkg (`id`,`host_id`,`pkg_id`,`timestamp`) VALUES (NULL,'$host_id','$pkg_id',NULL)");
		echo "+";
	}
	
}


# process again to disable inactive pkgs
# put all the pkgs from XML in an array
foreach($packages as $param) {
	$xml_name = (string) $param->name;
	$xml_version = (string) $param->version;
	$xml_release = (string) $param->release;
	if (!$xml_release) { $release = "0"; };
	$group = (string) quotemeta($param->group);
	$xml_arch = (string) $param->arch;
	$cleanup_xml_pkg[] = "$xml_name\t$xml_version\t$xml_release\t$xml_arch";
}

# put all the pkg from db in an array
$result = query_db("SELECT pkg.name,pkg.version,pkg.release,pkg.arch FROM pkg,host_pkg WHERE pkg.id = host_pkg.pkg_id AND host_pkg.host_id = '$host_id'");
while ($db_pkg = mysql_fetch_array($result, MYSQL_ASSOC)) {
	$db_name = $db_pkg["name"];
	$db_version = $db_pkg["version"];
	$db_release = $db_pkg["release"];
	$db_arch = $db_pkg["arch"];
        $cleanup_db_pkg[] = "$db_name\t$db_version\t$db_release\t$db_arch";
}
# compare the 2 newly created arrays, delete old, record history 
#echo "XML:\n"; print_r($cleanup_xml_pkg); echo "DB:\n"; print_r($cleanup_db_pkg); print_r($pkg_diff);
$pkg_diff = (array_diff($cleanup_db_pkg,$cleanup_xml_pkg));
foreach($pkg_diff as $string) {
	list($name,$version,$release,$arch) = split("\t", $string);
	$host_pkg = mysql_fetch_assoc(query_db("SELECT id FROM pkg WHERE `name` = '$name' AND `version` = '$version' AND `arch` = '$arch' AND `release` = '$release'"));
	$pkg_id = $host_pkg["id"];
#	echo "$name: $version\n";
       	query_db("INSERT INTO archive_host_pkg SELECT * FROM host_pkg WHERE host_id = '$host_id' AND pkg_id = '$pkg_id'");
       	query_db("DELETE FROM host_pkg WHERE host_id = '$host_id' AND pkg_id = '$pkg_id'");
	query_db("UPDATE archive_host_pkg SET timestamp=NOW() WHERE host_id = '$host_id' AND pkg_id = '$pkg_id'");
	echo "-";
}

echo " Done\n";

}

##########################3


################
# update host function
################

function update_host($hostname,$domain,$hwid,$os_id,$cpu_id,$cpu_count,$swaptotal,$memtotal){
	
	$host_db = mysql_fetch_assoc(query_db("SELECT * FROM host WHERE host.hwid='$hwid'"));
	$host_xml =  array( 	"hostname" => $hostname,
				"domain" => $domain,
				"hwid" => $hwid,
				"os_id" => $os_id,
				"cpu_id" => $cpu_id,
				"cpu_count" => $cpu_count,
				"swaptotal" => $swaptotal,
				"memtotal" => $memtotal,
				);
	$updated_host_db = (array_merge($host_db,$host_xml));
	$host_diff = (array_diff_assoc($updated_host_db,$host_db));
	if ($host_diff){
		echo "host id " . $host_db["id"] . " has changed, archiving...";
		query_db("INSERT INTO archive_host SELECT * FROM host WHERE host.id = '$host_db[id]'");
		echo "updating...";
		array_walk($host_diff,'update_host_value',$host_db["id"]);
		echo "done.\n";

	}
	return $host_db["id"];
}
################

################
# insert host function
################

function insert_host($hostname,$domain,$hwid,$os_id,$cpu_id,$cpu_count,$swaptotal,$memtotal){

	$query = "INSERT INTO `host` (`id`,`hostname`,`domain`,`hwid`,`os_id`,`cpu_id`,`cpu_count`,`swaptotal`,`memtotal`,`location_id`,`status`,`timestamp`) VALUES (NULL,'$hostname','$domain','$hwid','$os_id','$cpu_id','$cpu_count','$swaptotal','$memtotal','1',NULL,CURRENT_TIMESTAMP)";

	#echo $query,"\n";
	$insert_query = query_db($query);
	$host_id = mysql_insert_id();
	echo "NEW host inserted as host_id:" . $host_id . "\n";
	return $host_id;
}
################


################
# cpu function
################
function insert_cpu($cpu_cache,$cpu_name,$cpu_speed){
if (func_num_args() == 4) {
$unified_cache = func_get_arg(3);
}
	$cpu = mysql_fetch_assoc(query_db("SELECT * FROM cpu WHERE cpu.cache = '$cpu_cache' AND cpu.name = '$cpu_name' AND cpu.speed = '$cpu_speed' AND cpu.unified_cache IS " . ($unified_cache ? "NOT" : "") . " NULL"));
	$cpu_id = $cpu["id"];
	if ($cpu["id"]) {
		echo "$cpu_name @ $cpu_speed Mhz id: $cpu[id]\n";
	} else {
		echo "NEW CPU $cpu_name @ $cpu_speed Mhz, inserting into DB...";
		query_db("INSERT INTO `cpu` (`id`,`name`,`speed`,`cache`, `unified_cache`) VALUES (NULL,'$cpu_name','$cpu_speed','$cpu_cache'," . ($unified_cache ? 1 : "NULL") . ")");
		echo "done.\n";
		# get the cpu_id newly created
		$cpu_id = mysql_insert_id();
	}
	return $cpu_id;
}
################
	


################
# os function
################

function insert_os($os_name,$os_arch,$os_basearch,$os_kernel) {
	$os = mysql_fetch_assoc(query_db("SELECT * FROM os WHERE os.name = '$os_name' AND os.arch = '$os_arch' AND os.basearch = '$os_basearch' AND os.kernel = '$os_kernel'"));
	$os_id = $os["id"];
	if ($os["id"]) {
		echo "$os_name id: $os[id]\n";
	} else {
		echo "NEW OS $os_name, inserting...";
		query_db("INSERT INTO `os` (`id`,`name`,`arch`,`basearch`,`kernel`) VALUES (NULL,'$os_name','$os_arch','$os_basearch','$os_kernel')\n");
		echo "done.\n";
		# get the os_id
		$os_id = mysql_insert_id();
	}
	return $os_id;
}
################


################
# function for use with the update_host array walk
################

function update_host_value($value, $key,$host_id) {
	query_db("UPDATE host SET $key = '$value',timestamp = NOW() WHERE host.id = '$host_id'");
	#echo "updated $key : $value \n";
}

################


################
# function for use with the insert_nic array walk
################
function update_nic_value($value, $key,$nic_id) {
       	query_db("UPDATE nic SET $key = '$value',timestamp = NOW(), status = '1' WHERE nic.id = '$nic_id'");
      	#echo "updated $key : $value \n";
}
################

################
# function for use with the machdb_packages array walk
################
function update_host_pkg_value($value, $key,$host_pkg_id) {
       	query_db("UPDATE host_pkg SET $key = '$value',timestamp = NOW() WHERE id = '$host_pkg_id'");
      	#echo "updated $key : $value \n";
}
################

################
# function for use with the machdb_disk array walk
################
function update_host_disk_value($value, $key,$host_disk_id) {
       	query_db("UPDATE host_disk SET $key = '$value',timestamp = NOW() WHERE id = '$host_disk_id'");
      	#echo "updated $key : $value \n";
}
################

################
# function for use with the machdb_filesystem array walk
################
function update_filesystem_value($value, $key,$filesystem_id) {
       	query_db("UPDATE filesystem SET $key = '$value',timestamp = NOW() WHERE id = '$filesystem_id'");
      	#echo "updated $key : $value \n";
}
################






close_db($db);

echo "\n\nFinished\n";


?>
