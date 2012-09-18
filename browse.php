<?
# This file is part of MachDB.  For license info, read LICENSE

include 'include/config.php';
include 'include/common.php';
include 'include/smarty.php';

$pagetitle = "browse";

# connect to DB
$db = connect_db($mysqlserver,$mysqluser,$mysqlpassword,$mysqldatabase);
$query = $_GET;


# loop thru the variables in the query 
foreach ($query as $key => $variable) {
	$querystring[] = " $key=$variable";


	# network browse
	if ($key == "macaddr" || $key == "ipaddr" || $key == "netmask" || $key == "broadcast"){
		$nic_query = query_db("SELECT nic.*,inet_aton(nic.ipaddr) as num,ipaddr,inet_aton(nic.netmask) as num_netmask,inet_aton(nic.broadcast) as num_broadcast,host.hostname FROM nic,host WHERE $key = '$variable' AND nic.host_id = host.id");
		while ($_row=mysql_fetch_array($nic_query)) {
			$nic[]=$_row;
		}
	$smarty->assign('nic',$nic);
	}
	# os browse
	elseif ($key == "os" || $key == "kernel" || $key == "arch" || $key == "vendor" || $key == "release_date"){
		if ($key == "os") { $key = "name"; }
		$os_query = query_db("SELECT os.*,host.hostname,host.id FROM os,host WHERE $key = '$variable' AND host.os_id = os.id");
		while ($_row=mysql_fetch_array($os_query)) {
			$os[]=$_row;
		}
	$smarty->assign('os',$os);
	}
	# cpu browse
	elseif ($key == "cpu_id" || $key == "cpu_speed" || $key == "cpu_cache" || $key == "cpu_count" || $key == "swaptotal" || $key == "memtotal" || $key == "location_id" || $key == "status"){
		if ($key == "cpu_id") { $key = "cpu.id"; }
		if ($key == "cpu_speed") { $key = "speed"; }
		if ($key == "cpu_cache") { $key = "cache"; }
		$cpu_query = query_db("SELECT cpu.*,host.hostname,host.cpu_count,host.swaptotal,host.memtotal,host.location_id,host.status,host.id AS host_id FROM cpu,host WHERE $key = '$variable' AND host.cpu_id = cpu.id");
		while ($_row=mysql_fetch_array($cpu_query)) {
			$cpu[]=$_row;
		}
	$smarty->assign('cpu',$cpu);
	}
	# disk browse
	elseif ($key == "disk_id" || $key == "disk_size" || $key == "disk_type"){
		if ($key == "disk_id") { $key = "disk.id"; }
		if ($key == "disk_size") { $key = "size"; }
		if ($key == "disk_type") { $key = "type"; }
		$disk_query = query_db("select disk.*,host_disk.*,host.hostname,host_disk.id AS host_disk_id from disk,host_disk,host where disk_id = disk.id and host_disk.host_id = host.id and $key = '$variable'");
		while ($_row=mysql_fetch_array($disk_query)) {
			$disk[]=$_row;
		}
	$smarty->assign('disk',$disk);
	}
	# pkg browse
	elseif ($key == "pkg_id" || $key == "pkg_arch" || $key == "pkg_name"){
		if ($key == "pkg_id") { $key = "pkg.id"; }
		if ($key == "pkg_arch") { $key = "arch"; }
		if ($key == "pkg_name") { $key = "pkg.name"; }
		$pkg_query = query_db("SELECT   pkg.id,pkg.name,pkg.arch,pkg.version,pkg.release,
						host_pkg.host_id,host_pkg.timestamp,
						host.hostname,
						host_pkg.id AS host_pkg_id
						FROM pkg,host_pkg,host
						WHERE host_pkg.pkg_id = pkg.id
						AND host_pkg.host_id = host.id
						AND $key = '$variable'");
		while ($_row=mysql_fetch_array($pkg_query)) {
			$pkg[]=$_row;
		}
	$smarty->assign('pkg',$pkg);
	}
	# failed query
	else {
	}
}

$smarty->assign('date',$date);
$smarty->assign('pagetitle',$pagetitle);
$smarty->assign('querystring',$querystring);

$smarty->display('browse.tpl');











close_db($db);

?>

