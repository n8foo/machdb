<?
# This file is part of MachDB.  For license info, read LICENSE

include 'include/config.php';
include 'include/common.php';
include 'include/smarty.php';

# connect to DB
$db = connect_db($mysqlserver,$mysqluser,$mysqlpassword,$mysqldatabase);
$queryhostid = $_GET['id'];

#get the database information
$hostquery = query_db("SELECT * FROM host WHERE id = '$queryhostid'");
$host=mysql_fetch_assoc($hostquery);
$os_id = $host['os_id'];
$osquery = query_db("SELECT * FROM os WHERE id = '$os_id'");
$os=mysql_fetch_assoc($osquery);
$cpu_id = $host['cpu_id'];
$cpuquery = query_db("SELECT * FROM cpu WHERE id = '$cpu_id'");
$cpu=mysql_fetch_assoc($cpuquery);


# query disks
$disk_query = query_db("
		SELECT host_disk.id AS host_disk_id,
		       disk.id, disk.model, disk.type, host_disk.device, disk.size,
		       host_disk.timestamp AS timestamp
		  FROM host_disk,disk 
	 	 WHERE host_id = '$queryhostid' 
		   AND host_disk.disk_id = disk.id
		   AND host_disk.status = '1'
		 ORDER BY device;");
while ($_row=mysql_fetch_assoc($disk_query)) {
	$disk[]=$_row;
}

# query partitions
$fs_query = query_db("SELECT * FROM filesystem WHERE host_id = '$queryhostid' ORDER BY mountpoint");
while ($_row=mysql_fetch_assoc($fs_query)) {
	$fs[]=$_row;
}

# query bios system
$bios_query = query_db("SELECT system.name,system.vendor,host_system.serial,host_system.uuid FROM system,host_system WHERE host_system.host_id = '$queryhostid' AND system.id = host_system.system_id");
$bios=mysql_fetch_assoc($bios_query);

# query nics
$nic_query = query_db("SELECT * FROM nic WHERE host_id = '$queryhostid' AND status=1 ORDER BY interface");
while ($_row=mysql_fetch_assoc($nic_query)) {
	$nic[]=$_row;
}

#set the page title and query
$pagetitle = "$host[hostname] detail";
$querystring[] = "detail for host id $queryhostid";
$smarty->assign('date',$date);
$smarty->assign('pagetitle',$pagetitle);
$smarty->assign('querystring',$querystring);
$smarty->assign('bios',$bios);
$smarty->assign('host',$host);
$smarty->assign('os',$os);
$smarty->assign('cpu',$cpu);
$smarty->assign('disk',$disk);
$smarty->assign('fs',$fs);
$smarty->assign('nic',$nic);
$smarty->assign('snmp',$snmp);

$smarty->display('host.tpl');

close_db($db);

?>




	
