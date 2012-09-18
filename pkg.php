<?
# This file is part of MachDB.  For license info, read LICENSE

include 'include/config.php';
include 'include/common.php';
include 'include/smarty.php';

# connect to DB
$db = connect_db($mysqlserver,$mysqluser,$mysqlpassword,$mysqldatabase);
$queryhostid = $_GET['id'];

# query pkgs
$pkg_query = query_db("SELECT pkg.name,pkg.version,pkg.release,pkg.arch,host_pkg.timestamp,host_pkg.id AS host_pkg_id,pkg.id FROM pkg,host_pkg WHERE host_pkg.host_id = '$queryhostid' AND pkg.id = host_pkg.pkg_id ORDER BY timestamp,name");
while ($_row=mysql_fetch_assoc($pkg_query)) {
	$pkg[]=$_row;
}

$smarty->assign('pkg',$pkg);
$smarty->assign('hostid',$queryhostid);

$smarty->display('pkg.tpl');

close_db($db);

?>




	
