<?php 
// Original Plugin & Theme API by Kaspars Dambis (kaspars@konstruktors.com)
// Modified by Jeremy Clark http://clark-technet.com
// Fixed by Alex Chousmith http://www.ninthlink.com/author/alex

// Theme with update info
$packages = array(
	'businesspro' => array(
		'latest' => '1.3.2',
		'date' => '2012-10-08',
		'info' => 'http://www.progo.com'
	),
	'ecommerce' => array(
		'latest' => '1.2.6',
		'date' => '2012-04-20',
		'info' => 'http://www.progo.com'
	),
	'direct' => array(
		'latest' => '1.2.6',
		'date' => '2012-04-20',
		'info' => 'http://www.progo.com'
	),
	'jhtdwp' => array(
		'latest' => '0.0.2',
		'date' => '2012-04-27',
		'info' => 'http://www.progo.com'
	),
	'bookit' => array(
		'latest' => '1.0.1',
		'date' => '2011-03-31',
		'info' => 'http://www.progo.com'
	),
	'realestate' => array(
		'latest' => '1.1.0',
		'date' => '2011-04-01',
		'info' => 'http://www.progo.com'
	)
);


// Process API requests
$action = $_POST['action'];
$args = unserialize(stripcslashes($_POST['request']));

if (is_array($args))
	$args = array_to_object($args);

// theme_update
if ($action == 'theme_update') {
	$latest_package = $packages[$args->slug];
	
	$update_data = array();
	$update_data['package'] = 'http://www.progo.com/latest-releases/'. $args->slug .'.zip';	
	$update_data['new_version'] = $latest_package['latest'];
	$update_data['url'] = $latest_package['info'];
	
	// we also want to log the Update Check against the DB ?
	$db   = mysql_connect('localhost', 'progokeys', 'NFUh02y67U1') or die('Could not connect: ' . mysql_error());
    mysql_select_db('progokeys') or die('Could not select database');
	
	$server_ip = $_SERVER['REMOTE_ADDR'];
	$url = $args->siteurl;
	$api_key = $_POST['api-key'];
	$theme = $args->slug;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$currtime = date('Y-m-d H:i:s');
	
	// given the API key, look for existing entry...
	$found = false;
	if ( $api_key == '' ) {
		$update_data['authcode'] = 999;
	} else {
		$query = "SELECT * FROM progo_keys WHERE api_key = '$api_key'";
		$result = mysql_query($query);
		
		$update_data['authcode'] = 100;
	
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$found = true;
			$upd = "UPDATE progo_keys SET last_checked = '$currtime' WHERE progo_keys.ID = $row[ID]";
			mysql_query($upd) || die("Invalid query: $upd<br>\n" . mysql_error());
			
			if ( !in_array( $row['url'], array( '', 'newkey', $url ) ) ) {
				$update_data['authcode'] = 300;
			} elseif($row['url'] == 'newkey') {
				$update_data['authcode'] = 100;
				
				$upd = "UPDATE progo_keys SET url = '$url', auth_code = '100', last_checked = '$currtime' WHERE progo_keys.ID = $row[ID]";
				mysql_query($upd) || die("Invalid query: $upd<br>\n" . mysql_error());
			} else {
				$update_data['authcode'] = $row['auth_code'];
			}
		}
		
		if ( $found != true ) {
			// new entry
			$update_data['authcode'] = 999;
			
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
		}
		mysql_close($db);
	}
	// and return the info for the WP site
	//if (version_compare($args->version, $latest_package['version'], '<'))
	print serialize($update_data);
}

function array_to_object($array = array()) {
    if (empty($array) || !is_array($array))
		return false;
		
	$data = new stdClass;
    foreach ($array as $akey => $aval)
            $data->{$akey} = $aval;
	return $data;
}
?>
