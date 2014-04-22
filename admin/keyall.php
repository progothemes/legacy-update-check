<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>testing key system?</title>
</head>

<body>
<?php
// 8A54-F3DA-48C8-BE31-4690-5FE1-EDCF-A34A
$update_data = array();
$db   = mysql_connect('localhost', 'progokeys', 'NFUh02y67U1') or die('Could not connect: ' . mysql_error());
mysql_select_db('progokeys') or die('Could not select database');

	// given the API key, look for existing entry...
	$found = false;
	$query = "SELECT * FROM progo_keys ORDER BY theme ASC";
	$result = mysql_query($query);
	
	echo '<table><thead><tr><th>#</th></tr></thead><tbody>';
	$i = 0;
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$found = true;
		
		$theme = isset( $row['theme'] ) ? $row['theme'] : '';
		$url = isset( $row['url'] ) ? $row['url'] : '';
		
		echo '<tr><td>'. $i .'</td><td>'. $theme .'</td><td>'. $url .'</td></tr>';
	}
	echo '</tbody></table>';
	
	mysql_close($db);
?>
</body>
</html>