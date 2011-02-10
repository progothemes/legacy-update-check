<?php 

// Original Plugin & Theme API by Kaspars Dambis (kaspars@konstruktors.com)
// Modified by Jeremy Clark http://clark-technet.com
// Fixed by Alex Chousmith http://www.ninthlink.com/author/alex

// Theme with update info
$packages['direct'] = array(
	'versions' => array(
		'1.0.28' => array(
			'version' => '1.0.28',
			'date' => '2011-02-10',
			'package' => 'http://www.ninthlink.net/direct.zip'
		)
	),
	'info' => array(
		'url' => 'http://www.progo.com'
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
