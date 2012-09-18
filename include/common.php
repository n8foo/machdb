<?php
# This file is part of MachDB.  For license info, read LICENSE

# non configurable global variables
global $resultitems;


####################################################
# DB FUNCTIONS
####################################################


function connect_db($mysqlserver,$mysqluser,$mysqlpassword,$mysqldatabase) {
	// Connecting, selecting database
$link = mysql_connect($mysqlserver, $mysqluser, $mysqlpassword)
    or die('Could not connect: ' . mysql_error());
mysql_select_db($mysqldatabase) or die('Could not select database');	
	return $link;
}

function query_db($query){
	$result = mysql_query($query) or die('Query failed: ' . mysql_error());
	return $result;
}

function print_html_result($result) {
	echo "<table>\n";
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    echo "\t<tr>\n";
    foreach ($line as $col_value) {
        echo "\t\t<td>$col_value</td>\n";
    }
    echo "\t</tr>\n";
}
echo "</table>\n";
}

function print_result($result) {
while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    foreach ($line as $col_value) {
        echo "\"$col_value\" ";
    }
	echo "\n";
}
}

function close_db($link){
	mysql_close($link);
}



####################################################
# MISC FUNCTIONS
####################################################


# template generation function
function gettemplate($templatename,$templatechoice) {
        global $templatecache;
        #check if template has already been loaded
        if (isset($templatecache[$templatename])) {
                #return cached version
                $template = $templatecache[$templatename];
        } else {
                #retrieve from file
                $handle = fopen("./templates/$templatechoice/".$templatename,"r");
                $template = fread($handle,filesize("./templates/$templatechoice/".$templatename));
                #close the file
                fclose($handle);
                $template = str_replace("\"","\\\"",$template);
                #cache the contents
                $templatecache[$templatename] = $template;
        }
        return $template;
}



# function to return correct status in less code
function getstatus($status) {
	if ($status == 1) {
		$status = "online";
		$color="green";
	} else {
		$status = "offline";
		$color="red";
	}
	$statusarray = array("status" => $status,"color" => $color);
	return $statusarray;
}


?>
