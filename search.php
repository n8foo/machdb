<?
# This file is part of MachDB.  For license info, read LICENSE

include 'include/config.php';
include 'include/common.php';
include 'include/smarty.php';

$pagetitle = "browse";

# connect to DB
$db = connect_db($mysqlserver,$mysqluser,$mysqlpassword,$mysqldatabase);
$query = $_GET;
$archive = $_GET[archive];


# loop thru the variables in the query 
foreach ($query as $key => $variable) {
	$querystring[] = " $key=$variable";

        $variable = str_replace("*","%",$variable);

	# host search
	if ($key == "hostname"){
		$hostname_query = query_db("SELECT hostname,id,timestamp FROM host WHERE $key LIKE '%$variable%' ORDER BY hostname");
		while ($_row=mysql_fetch_array($hostname_query)) {
			$hostname[]=$_row;
		}
		# if 'archive' is clicked
		if ($archive == "on") {
                	$hostname_query = query_db("SELECT hostname,id,timestamp FROM archive_host WHERE $key LIKE '%$variable%' ORDER BY hostname");
                	while ($_row=mysql_fetch_array($hostname_query)) {
                	        $hostname[]=$_row;
                	}
		}
	$smarty->assign('hostname',$hostname);
	}
	# failed query
	else {
	$smarty->assign('noquery',$_GET);
	}
}

$smarty->assign('date',$date);
$smarty->assign('pagetitle',$pagetitle);
$smarty->assign('querystring',$querystring);

$smarty->display('search.tpl');











close_db($db);

?>

