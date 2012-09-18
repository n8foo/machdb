<?
# This file is part of MachDB.  For license info, read LICENSE

include 'include/config.php';
include 'include/common.php';
include 'include/smarty.php';

# connect to DB
$db = connect_db($mysqlserver,$mysqluser,$mysqlpassword,$mysqldatabase);
$query = $_GET;

$pagetitle = "History";

# loop thru the variables in the query 
foreach ($query as $key => $variable) {
	$querystring[]= "$key=$variable";

	# network history
	if ($key == "nic_id"){
		$nic_query = query_db("SELECT nic.*,host.hostname FROM nic,host WHERE nic.id = '$variable' AND nic.host_id = host.id");
		while ($_row=mysql_fetch_array($nic_query)) {
			$nic[]=$_row;
		}
		$nic_query = query_db("SELECT archive_nic.*,host.hostname FROM archive_nic,host WHERE archive_nic.id = '$variable' AND archive_nic.host_id = host.id ORDER BY timestamp DESC");
		while ($_row=mysql_fetch_array($nic_query)) {
			$nic[]=$_row;
		}
	$smarty->assign('nic',$nic);
	}
	# host history
	elseif ($key == "host_id"){
		$host_query = query_db("SELECT os.name AS os_name,os.kernel,os.arch,os.basearch,cpu.name AS cpu_name,cpu.shortname,cpu.speed,cpu.cache,cpu.id AS cpu_id,host.* FROM os,cpu,host WHERE host.id = '$variable' AND host.cpu_id = cpu.id AND host.os_id = os.id;");
		while ($_row=mysql_fetch_array($host_query)) {
			$host[]=$_row;
		}
		$host_query = query_db("SELECT os.name AS os_name,os.kernel,os.arch,os.basearch,cpu.name AS cpu_name,cpu.shortname,cpu.speed,cpu.cache,cpu.id AS cpu_id,archive_host.* FROM os,cpu,archive_host WHERE archive_host.id = '$variable' AND archive_host.cpu_id = cpu.id AND archive_host.os_id = os.id ORDER BY timestamp DESC;");
		while ($_row=mysql_fetch_array($host_query)) {
			$host[]=$_row;
		}
	$smarty->assign('host',$host);
	}
	# disk history
	elseif ($key == "host_disk_id"){
		$disk_query = query_db("SELECT disk.id AS disk_id,disk.*,host_disk.*,host_disk.id AS host_disk_id,host.hostname FROM disk,host_disk,host WHERE disk_id = disk.id AND host_disk.host_id = host.id AND host_disk.id = '$variable';");
		while ($_row=mysql_fetch_array($disk_query)) {
			$disk[]=$_row;
		}
		$disk_query = query_db("SELECT disk.id AS disk_id,disk.*,archive_host_disk.*,archive_host_disk.id AS host_disk_id,host.hostname FROM disk,archive_host_disk,host WHERE disk_id = disk.id AND archive_host_disk.host_id = host.id AND archive_host_disk.id = '$variable' ORDER BY archive_host_disk.timestamp DESC;");
		while ($_row=mysql_fetch_array($disk_query)) {
			$disk[]=$_row;
		}
	$smarty->assign('disk',$disk);
	}
	# fs history
	elseif ($key == "fs_id"){
		if ($key == "fs_id") { $key = "filesystem.id"; }
		$disk_query = query_db("SELECT = '$variable'");
		while ($_row=mysql_fetch_array($disk_query)) {
			$fs[]=$_row;
		}
	$smarty->assign('fs',$fs);
	}
	# failed query
	else {
	}
}

$smarty->assign('date',$date);
$smarty->assign('pagetitle',$pagetitle);
$smarty->assign('querystring',$querystring);

$smarty->display('history.tpl');


close_db($db);

?>

