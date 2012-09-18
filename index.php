<?
# This file is part of MachDB.  For license info, read LICENSE

include 'include/config.php';
include 'include/common.php';
include 'include/smarty.php';

# connect to DB
$db = connect_db($mysqlserver,$mysqluser,$mysqlpassword,$mysqldatabase);

#set the page title and query
$pagetitle = "Mach DB";
$querystring[] = "latest changes";
#get the database information
$query = query_db("	SELECT 
			SUM(memtotal) as mem,
			COUNT(id) as hostcount
			FROM host;
			");
while($_row=mysql_fetch_assoc($query)) {
	$hoststats[] = $_row;
}
$query = query_db("	SELECT 
			SUM(cpu.speed) AS speed 
			FROM cpu,host 
			WHERE host.cpu_id = cpu.id;
			");
while($_row=mysql_fetch_assoc($query)) {
	$cpustats[] = $_row;
}
$query = query_db(" 	SELECT
			SUM(size) AS disk_size,
			COUNT(size) AS disk_count
			FROM disk,host_disk 
			WHERE host_disk.disk_id = disk.id
			AND host_disk.status = 1;
		");
while($_row=mysql_fetch_assoc($query)) {
	$diskstats[] = $_row;
}
$query = query_db("	SELECT 
			host.id,
			host.hostname,
			host.domain,
			host.timestamp,
			os.name AS os_name,
			host.cpu_count,
			host.memtotal,
			cpu.name AS cpu_name,
			cpu.shortname AS cpu_shortname,
			cpu.speed AS cpu_speed 
			FROM host,os,cpu 
			WHERE host.os_id = os.id 
			AND host.cpu_id = cpu.id 
			ORDER BY host.timestamp 
			DESC LIMIT 25");
while($_row=mysql_fetch_assoc($query)) {
	$host[] = $_row;
}
$query = query_db("	SELECT 
			host.hostname,
			host_disk.host_id,
			host_disk.device,
			host_disk.id,
			disk.size,
			disk.model,
			host_disk.timestamp,
			host_disk.status 
			FROM host_disk,disk,host 
			WHERE host_disk.host_id=host.id 
			AND host_disk.disk_id=disk.id 
			ORDER BY disk.timestamp 
			DESC LIMIT 10");
while($_row=mysql_fetch_assoc($query)) {
	$disk[] = $_row;
}
$query = query_db("	SELECT 
			nic.interface,
			nic.id,
			host.hostname,
			nic.host_id,
			nic.ipaddr,
			nic.timestamp,
			nic.status 
			FROM nic,host 
			WHERE nic.host_id=host.id 
			ORDER BY nic.timestamp 
			DESC LIMIT 10");
while($_row=mysql_fetch_assoc($query)) {
	$nic[] = $_row;
}

# show the error page if no hosts are in the DB
if (! $host) {
	$smarty->assign('errormsg', "no hosts found in database");
	$smarty->display('error.tpl');
} else {

	$smarty->assign('hoststats', $hoststats);
	$smarty->assign('diskstats', $diskstats);
	$smarty->assign('cpustats', $cpustats);
	$smarty->assign('host', $host);
	$smarty->assign('disk', $disk);
	$smarty->assign('nic', $nic);
	$smarty->assign('date', $date);
	$smarty->assign('pagetitle', $pagetitle);
	$smarty->assign('querystring', $querystring);
	$smarty->display('index.tpl');
}

close_db($db);

?>




