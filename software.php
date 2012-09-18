<?
# This file is part of MachDB.  For license info, read LICENSE

include 'include/config.php';
include 'include/common.php';
include 'include/smarty.php';

# connect to DB
$db = connect_db($mysqlserver,$mysqluser,$mysqlpassword,$mysqldatabase);

#set the page title and query
$pagetitle = "Mach DB";
$querystring[] = "Software";
#get the database information
$query = query_db("SELECT DISTINCT name FROM os ORDER BY name");
while($_row=mysql_fetch_assoc($query)) {
	$os[] = $_row;
}
$query = query_db("SELECT DISTINCT kernel FROM os ORDER BY kernel");
while($_row=mysql_fetch_assoc($query)) {
	$kernel[] = $_row;
}
$query = query_db("SELECT DISTINCT arch FROM os ORDER BY arch");
while($_row=mysql_fetch_assoc($query)) {
	$os_arch[] = $_row;
}
$query = query_db("SELECT DISTINCT vendor FROM os ORDER BY vendor");
while($_row=mysql_fetch_assoc($query)) {
	$os_vendor[] = $_row;
}
$query = query_db("SELECT DISTINCT name FROM pkg ORDER BY name");
while($_row=mysql_fetch_assoc($query)) {
	$pkg_name[] = $_row;
	$pkg_name_index_word = $_row['name'];
	$pkg_name_index[] = substr($pkg_name_index_word,0,1);
}
$query = query_db("SELECT DISTINCT arch FROM pkg ORDER BY arch");
while($_row=mysql_fetch_assoc($query)) {
	$pkg_arch[] = $_row;
}


$smarty->assign('os', $os);
$smarty->assign('kernel', $kernel);
$smarty->assign('os_arch', $os_arch);
$smarty->assign('os_vendor', $os_vendor);
$smarty->assign('pkg_name_index', $pkg_name_index);
$smarty->assign('pkg_name', $pkg_name);
$smarty->assign('pkg_arch', $pkg_name);
$smarty->assign('date', $date);
$smarty->assign('pagetitle', $pagetitle);
$smarty->assign('querystring', $querystring);
$smarty->display('software.tpl');


close_db($db);

?>




