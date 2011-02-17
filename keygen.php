<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ProGo Key Gen?</title>
</head>

<body>
<?php
	//$url = 'http://www.ninthlink.com/direct/';
	$email = $_GET['email'];
	$currtime = date('Y-m-d H:i:s');
	$api_key = md5(crypt("$email : $currtime"));
	echo "<h1>Here is a new API Key</h1><p>for<br /><strong>email address</strong> $email<br /><strong>created at</strong> $currtime</p>";
	echo "<h3>API KEY for the DB</h3><input type='text' readonly='readonly' value='$api_key' size='50' onfocus='this.select();' />";
	$nice_key = implode( '-', str_split( strtoupper( $api_key ), 4) );
	echo "<h3>Human-Friendly API Key</h3><input type='text' readonly='readonly' value='$nice_key' size='50' onfocus='this.select();' />";
?>
</body>
</html>