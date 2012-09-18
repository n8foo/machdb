<?php
# This file is part of MachDB.  For license info, read LICENSE

include 'include/config.php';
include 'include/common.php';
include 'include/smarty.php';

header("Cache-Control: no-cache, must-revalidate");     // Must do cache-control headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 

// Return back the numeric OIDs, instead of text strings.
snmp_set_oid_numeric_print(1);
// Get just the values.
snmp_set_quick_print(TRUE);
// For sequence types, return just the numbers, not the string and numbers.
snmp_set_enum_print(TRUE);

// Don't let the SNMP library get cute with value interpretation.  This makes
// MAC addresses return the 6 binary bytes, timeticks to return just the integer
// value, and some other things.
#snmp_set_valueretrieval(SNMP_VALUE_PLAIN);  

$host = $_GET['h'];

//system description
$sysdesc = snmpget($host, $snmpcommunity, "sysDescr.0");

if (isset($sysdesc)) {

//current tcp connections
$tcpcons = snmpget($host, $snmpcommunity, "tcpCurrEstab.0");

//get system uptime
$uptime = snmpget($host,$snmpcommunity,"hrSystemUptime.0");
#$uptime = ereg_replace("Timeticks: \([0-9]+\) ","",$sysUpTime);
#$uptime = $uptime / 60 / 60 / 24;

// get memory usage
$memtotal = snmpget($host,$snmpcommunity,"hrStorageSize.2");
$memused = snmpget($host,$snmpcommunity,"hrStorageUsed.2");
$mem = $memtotal - $memused;

// get swap usage
$swap = snmpget($host,$snmpcommunity,"hrStorageUsed.3");

// get root partition usage
$roottotal = snmpget($host,$snmpcommunity,"hrStorageSize.4");
$rootused = snmpget($host,$snmpcommunity,"hrStorageUsed.4");
$root = $rootused / $roottotal;

//get load
$load1 = snmpget($host,$snmpcommunity,".iso.org.dod.internet.private.enterprises.ucdavis.laTable.laEntry.laLoad.1");
$load5 = snmpget($host,$snmpcommunity,".iso.org.dod.internet.private.enterprises.ucdavis.laTable.laEntry.laLoad.2");
$load15 = snmpget($host,$snmpcommunity,".iso.org.dod.internet.private.enterprises.ucdavis.laTable.laEntry.laLoad.3");

#set the page title and query
$smarty->assign('tcpcons',$tcpcons);
$smarty->assign('uptime',$uptime);
$smarty->assign('mem',$mem);
$smarty->assign('swap',$swap);
$smarty->assign('root',$root);
$smarty->assign('load1',$load1);
$smarty->assign('load5',$load5);
$smarty->assign('load15',$load15);
} 

$smarty->display('snmp.tpl');



?>
