<?php
$ourthemes = array(
	'businesspro' => 'Business Pro',
	'ecommerce' => 'Ecommerce',
	'direct' => 'Direct Response',
	'jhtdwp' => 'JHT DWP',
	'sundancedwp' => 'Sundance Spas DWP',
	'realestate' => 'Real Estate',
	'bookit' => 'Book It (Secret Asset)'
);
/************** EDIT BELOW THIS LINE AT YOUR OWN RISK **************/

session_start();

if(!isset($_SESSION['progokeytime'])) {
	$_SESSION['progokeytime'] = date('Y-m-d H:i:s');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ProGo Key Gen?</title>
</head>

<body>
<p><img src="http://www.progo.com/wp-content/themes/progodotcom/images/logo_progo.png" alt="ProGo Themes" /></p>
<?php

if(isset($_POST['createnew'])) {
	$api_key = $_POST['apikey'];
	$humankey = $_POST['humankey'];
	$email = $_POST['email'];
	$currtime = $_POST['currtime'];
	$theme = $_POST['theme'];
	
	echo "<h1>Saving your key<span style='display:none'>$api_key</span> in the DB</h1>";
	
	$hashcheck = md5($email.':'.$currtime);
	if(!isset($_SESSION['progokey2'])) {
		echo '<h2>STEP 2 AUTHENTICATION NOT FOUND</h2>';
	} elseif($_SESSION['progokey2'] !== $hashcheck ) {
		echo '<h2>STEP 2 AUTH KEY DOES NOT COMPUTE</h2>';
		/*
	?>
<p><small>key: <?php echo $_SESSION['progokey2']; ?></small></p>
<p><small>should match: <?php echo $hashcheck; ?></small></p>
<p><small>stored time: <?php echo $_SESSION['progokeytime']; ?></small></p>
<p><small>sent time: <?php echo $currtime; ?></small></p>
<?php
*/
	} else { // aok
		echo '<h2>AUTHENTICATION SUCCESSFUL...</h2>';
		$db   = mysql_connect('localhost', 'progokeys', 'NFUh02y67U1') or die('Could not connect: ' . mysql_error());
		mysql_select_db('progokeys') or die('Could not select database');
		$server_ip = $_SERVER['SERVER_ADDR'];
		$url = 'newkey';
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$found = 0;
		if ( $api_key == '' ) {
			echo '<h1>THE API KEY APPEARS TO HAVE BEEN LOST...</h1>';
		} else {
			$query = "SELECT * FROM progo_keys WHERE api_key = '$api_key'";
			$result = mysql_query($query);		
			while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$found++;
				$url = $row[url];
				$theme = $row[theme];
			}
			
			if ( $found > 0 ) {
				echo "<h1>THAT API KEY APPEARS TO BE ALREADY IN USE ON $found SITE". ($found>1 ? "S" : "") ."</h1>";
				echo "<h2>site: $url<br />theme: $theme</h2>";
				echo "<p>for <a href='mailto:$email?subject=Your ProGoThemes API Key'>$email</a></p>";
				echo "<input type='text' name='humankey' readonly='readonly' value='$humankey' size='50' onfocus='this.select();' />";
			} else {
				// new entry
				$update_data['authcode'] = 0;
				
				$sql  = "INSERT INTO progo_keys (";
				$sql .= "ID,";
				$sql .= "url,";
				$sql .= "server_ip,";
				$sql .= "api_key,";
				$sql .= "theme,";
				$sql .= "user_agent,";
				$sql .= "last_checked,";
				$sql .= "auth_code,";
				$sql .= "wpec_id";
				$sql .= ") VALUES (";
				$sql .= "NULL,";
				$sql .= "'$url',";
				$sql .= "'$server_ip',";
				$sql .= "'$api_key',";
				$sql .= "'$theme',";
				$sql .= "'$user_agent',";
				$sql .= "'$currtime',";
				$sql .= "$update_data[authcode],";
				$sql .= "''";
				$sql .= ")";
				
				mysql_query($sql) || die("Invalid query: $sql<br>\n" . mysql_error());
				
				echo "<p>Success! Here is your new key:<br /><br /><input type='text' name='humankey' readonly='readonly' value='$humankey' size='50' onfocus='this.select();' /><br /><br />for ProGo Themes' <strong>$theme</strong> theme<br /><br />... which you might want to email to <a href='mailto:$email?subject=Your ProGoThemes API Key'>$email</a>?</p>";
			}
			mysql_close($db);
		}
	}
}
elseif(isset($_GET['email'])) {
	$email = $_GET['email'];
	$theme = $_GET['theme'];
	$currtime = $_SESSION['progokeytime'];
	if(!isset($_SESSION['progokey2']) || $_SESSION['progokey2']=='') {
		$_SESSION['progokey2'] = md5($email.':'.$currtime);
	}
	$api_key = md5(crypt("$email : $currtime : $theme"));
	$nice_key = implode( '-', str_split( strtoupper( $api_key ), 4) );
	echo "<h1>Please confirm the information below</h1><p><strong>email address</strong> $email<br /><strong>created at</strong> $currtime</p>";
	?>
<form action="keygen.php" method="post">
<input type="hidden" name="createnew" value="1" />
<input type="hidden" name="email" value="<?php echo $email; ?>" />
<input type="hidden" name="currtime" value="<?php echo $currtime; ?>" />
<input type="hidden" name="theme" value="<?php echo $theme; ?>" />
<input type="hidden" name="apikey" value="<?php echo $api_key ?>" />
<input type="hidden" name="humankey" value="<?php echo $nice_key ?>" />
<p><input type="submit" value="submit" /></p>
</form>
<p><a href="keygen.php">Change email address</a></p>
<?php /*
<p><br /></p>
<p><small>key: <?php echo $_SESSION['progokey2']; ?></small></p>
<p><small>should be like: <?php echo md5($email.':'.$currtime); ?></small></p>
*/
} else {
	$_SESSION['progokey2'] = '';
	?>
<form action="keygen.php" method="get">
<h1>Enter an Email address below?</h1>
<p><input type="text" size="40" name="email" /></p>
<h2>For which ProGo Theme?</h2>
<p><select name="theme">
<?php
foreach( $ourthemes as $k => $v ) {
	echo "<option value='$k'>$v</option>";
}
?>
</select></p>
<input type="submit" value="submit" />
</form>
<?php } ?>
</body>
</html>