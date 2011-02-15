<?php 

// Original Plugin & Theme API by Kaspars Dambis (kaspars@konstruktors.com)
// Modified by Jeremy Clark http://clark-technet.com
// Fixed by Alex Chousmith http://www.ninthlink.com/author/alex

// Theme with update info
$packages['direct'] = array(
	'versions' => array(
		'1.0.50' => array(
			'version' => '1.0.50',
			'date' => '2011-02-15',
			'package' => 'http://www.progothemes.com/direct.zip'
		)
	),
	'info' => array(
		'url' => 'http://www.progothemes.com'
	)
);


// Process API requests

$action = $_POST['action'];
$args = unserialize(stripcslashes($_POST['request']));

if (is_array($args))
	$args = array_to_object($args);

$latest_package = array_shift($packages[$args->slug]['versions']);



// basic_check

if ($action == 'basic_check') {	
	$update_info = array_to_object($latest_package);
	$update_info->slug = $args->slug;
	
	if (version_compare($args->version, $latest_package['version'], '<'))
		$update_info->new_version = $update_info->version;
	
	print serialize($update_info);
}

// theme_update

if ($action == 'theme_update') {
	$update_info = array_to_object($latest_package);
	
	//$update_data = new stdClass;
	$update_data = array();
	$update_data['package'] = $update_info->package;	
	$update_data['new_version'] = $update_info->version;
	$update_data['url'] = $packages[$args->slug]['info']['url'];
	
	// we also want to log the Update Check against the DB ?
	$db   = mysql_connect('localhost', 'ninthlin_wrd13', 'pxKj0zyRUQ') or die('Could not connect: ' . mysql_error());
    mysql_select_db('ninthlin_wrd13') or die('Could not select database');
	/*
INSERT INTO `ninthlin_wrd13`.`progo_keys` (`ID`, `server_ip`, `api_key`, `user_agent`, `last_checked`, `auth_code`) VALUES (NULL, '68.105.255.166', '12341234123412344321432143214321', 'alex testing', '2011-02-15 12:07:00', '100');
	*/
	$server_ip = $_SERVER['REMOTE_ADDR'];
//	$url = '';
	$api_key = $_POST['api-key'];
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$currtime = date('Y-m-d H:i:s');
	$auth_code = '001';
	
	$sql  = "INSERT INTO progo_keys (";
	$sql .= "ID,";
	$sql .= "url,";
	$sql .= "server_ip,";
	$sql .= "api_key";
	$sql .= "user_agent,";
	$sql .= "last_checked,";
	$sql .= "auth_code";
	$sql .= ") VALUES (";
	$sql .= "NULL,";
	$sql .= "NULL,";
	$sql .= "'$server_ip',";
	$sql .= "'$api_key',";
	$sql .= "'$user_agent',";
	$sql .= "'$currtime',";
	$sql .= "'$auth_code',";
	$sql .= ")";
	
	mysql_query($sql) || die("Invalid query: $sql<br>\n" . mysql_error());
	mysql_close($db);
	
	// and return the info for the WP site
	if (version_compare($args->version, $latest_package['version'], '<'))
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
