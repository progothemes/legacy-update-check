<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>testing key system</title>
</head>

<body>
<?php
// 8A54-F3DA-48C8-BE31-4690-5FE1-EDCF-A34A
$update_data = array();
$db   = mysql_connect('localhost', 'progokeys', 'NFUh02y67U1') or die('Could not connect: ' . mysql_error());
mysql_select_db('progokeys') or die('Could not select database');
	
$checks = array(
	array(
		'url' => 'http://www.ninthlink.net/direct',
		'key' => '29cf6c38c56f5dc60d1ab076684ac3f2'
	),
	array(
		'url' => 'http://www.ninthlink.com/direct/',
		'key' => '8f98598594f17dddd83fd319619ffc75'
	),
	array(
		'url' => 'http://www.ninthlink.com/',
		'key' => '11111111111111111111111111111111'
	)
);

foreach ( $checks as $i => $k ) :
	
	echo "<h1>check #$i </h1>";
	
	$currtime = date('Y-m-d H:i:s');
	$url = $k['url'];
	$api_key = $k['key'];
	
	echo "<p>checking<br />api key: $api_key <br />url: $url </p>";
	
	// given the API key, look for existing entry...
	$found = false;
	$query = "SELECT * FROM progo_keys WHERE api_key = '$api_key'";
	$result = mysql_query($query);
	
	$update_data['authcode'] = '100';

	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$found = true;
		
		echo '<h2>key match found</h2>';
		echo '<pre>'. print_r($row,true) .'</pre>';
		
		$upd = "UPDATE progo_keys SET last_checked = '$currtime' WHERE progo_keys.ID = $row[ID]";
		mysql_query($upd) || die("Invalid query: $upd<br>\n" . mysql_error());
		
		if ( ( $row['url'] != '' ) && ( $row['url'] != $url ) ) {
			$update_data['authcode'] = '300';
		}
	}
	
	if ( $found != true ) {
		$update_data['authcode'] = '999';
	}
	
	echo '<h2>Response Code #'. $update_data['authcode'] .' (';
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
	echo ') for API Key '. $api_key .'</h2>';
	
endforeach;
	
	mysql_close($db);
?>
</body>
</html>