<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>testing the key check system</title>
</head>

<body>
<?php
// 8A54-F3DA-48C8-BE31-4690-5FE1-EDCF-A34A
$update_data = array();
$db   = mysql_connect('localhost', 'progokeys', 'NFUh02y67U1') or die('Could not connect: ' . mysql_error());
    mysql_select_db('progokeys') or die('Could not select database');
	$server_ip = $_SERVER['REMOTE_ADDR'];
	$url = 'http://www.ninthlink.com/direct';
	
	
	$currtime = date('Y-m-d H:i:s');
	/*
	$api_key = md5(crypt('alex@ninthlink.com : '. $currtime));
	*/
	$api_key = '29cf6c38c56f5dc60d1ab076684ac3f2';
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	
	// given the API key, look for existing entry...
	$found = false;
	$query = "SELECT * FROM progo_keys WHERE api_key = '$api_key'";
	$result = mysql_query($query);
	
	$update_data['authcode'] = '100';

	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$found = true;
		$upd = "UPDATE progo_keys SET last_checked = '$currtime' WHERE progo_keys.ID = $row[ID]";
		mysql_query($upd) || die("Invalid query: $upd<br>\n" . mysql_error());
		
		if ( ( $row['url'] != '' ) && ( $row['url'] != $url ) ) {
			$update_data['authcode'] = '300';
		}
	}
	
	mysql_close($db);
	
	if ( $found != true ) {
		$update_data['authcode'] = '999';
	}
	
	echo '<h1>Response Code #'. $update_data['authcode'] .' (';
	switch($update_data['authcode']) {
		case '100':
			echo 'aok';
			break;
		case '300':
			echo 'wrong URL';
			break;
		case '999':
			echo 'key not found';
			break;
	}
	echo ') for API Key '. $api_key .'</h1>';
?>
</body>
</html>