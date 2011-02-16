<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
// d1196c1c8ab714ec163335d1a988ae3f

	//$url = 'http://www.ninthlink.com/direct/';
	$email = $_GET['email'];
	$currtime = date('Y-m-d H:i:s');
	$api_key = md5(crypt("$email : $currtime"));
	echo "<h1>API Key $api_key</h1>";
?>
</body>
</html>