<?php

//database configuration
//replace with correct values for your setup

$db_host = 'localhost';
$db_port = '5432';
$db_name = 'dbname';  
$db_user = 'dbuser';
$db_password = 'dbpasswd';

//end of user settings
//leave the following untouched

if (empty($db_name) or empty($db_user) or empty($db_password)) {
	die('update database settings');
} else {
	//returns $dbconn connection if successful for further use
	$dbconn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_password port=$db_port") or die('database connection error');
}

?>
