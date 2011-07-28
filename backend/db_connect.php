<?php
	$db_hostname = 'localhost';
	$db_username = 'root';
	$db_password = 'okapistudio';
	$db_name = 'refinery';
	$db_conn = mysql_connect($db_hostname, $db_username, $db_password);
	if (!$db_conn) die('Could not connect to DB');
	mysql_select_db($db_name) or die('Could not select DB');
?>