<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Re-Zip the Latest Releases</title>
</head>

<body>
<?php
$zippath = '/home/admin/progo/latest-releases/';
$themes = array();

if ( $dir = @opendir( $zippath ) ) {
	//echo "dir open <br />";
	while (($file = readdir( $dir ) ) !== false ) {
		if ( in_array($file, array('.', '..','.svn') ) ) {
			//echo "skipping file $file <br />";
			continue;
		}
		if ( !is_readable($zippath.$file) ) {
			//echo "File or Folder is not readable: $folder$file<br />";
		} elseif ( is_link($zippath.$file) ) {
			//echo "Link not followed: $folder$file<br />";
		} elseif ( is_dir( $zippath.$file )) {
			//echo "is_dir( $folder$file ) <br />";
			$themes[] = $file;
		}
	}
	@closedir( $dir );
}
sort($themes);

function _file_list_folder( $folder = '', $levels = 100 ) {
	global $tempfilelist, $allfilesize;
	if( empty($folder) ) {
		return false;
	}
	if( ! $levels ) {
		return false;
	}
	if ( $dir = @opendir( $folder ) ) {
		while (($file = readdir( $dir ) ) !== false ) {
			if ( in_array($file, array('.', '..','.svn') ) ) {
				continue;
			}
			if ( !is_readable($folder.$file) ) {
				//echo "File or Folder is not readable: $folder$file<br />";
			} elseif ( is_link($folder.$file) ) {
				//echo "Link not followed: $folder$file<br />";
			} elseif ( is_dir( $folder.$file )) {
				//echo "is_dir( $folder$file ) <br />";
				_file_list_folder( $folder.$file.'/', $levels - 1 );
			} elseif ( is_file( $folder.$file ) or is_executable($folder.$file) ) { //add file to filelist
				//echo "is_file( $folder$file ) <br />";
				$tempfilelist[]=$folder.$file;
				$allfilesize=$allfilesize+filesize($folder.$file);
			} else {
				//echo "Is not a file or directory : $folder$file <br />";
			}
		}
		@closedir( $dir );
	}
}

function tempcleanup( $templist ) {
	global $zippath;
	$fl = array();
	$templist=array_unique($templist); //all files only one time in list
	sort($templist);
	//echo '<pre>'. print_r($tempfilelist,true) .'</pre>';
	//make file list
	foreach ($templist as $files) 
		$fl[]=array(79001=>$files,79003=>str_replace($zippath,'',$files));
		
	return $fl;
}

foreach ( $themes as $theme ) {
	$backupdir = $zippath . $theme .'/';
	$backupfile = $theme .'.zip';
	echo "archiving $backupdir into $backupfile ...<br />";
	
	$filelist = $tempfilelist = array();
	$allfilesize = 0;
	
	_file_list_folder( $backupdir );
	$filelist = tempcleanup( $tempfilelist );
	
	$tempfilelist=array();
	
	$zip = new ZipArchive;
	if ($res=$zip->open($zippath.$backupfile,ZIPARCHIVE::CREATE) === TRUE) {
		foreach($filelist as $key => $files) {
			if (!is_file($files[79001])) //check file exists
				continue;
			$zip->addFile($files[79001], $files[79003]);
		}
		$zip->close();
		echo 'Backup Zip file create done!<br />';
	} else {
		echo 'Can not create Backup ZIP file<br />';
	}
	
	// also rearchive the zips that people are able to download upon purchase...
	if(in_array($theme,array('direct','ecommerce'))) {
		$filelist = $tempfilelist = array();
		$allfilesize = 0;
		
		_file_list_folder( $backupdir );
		$filelist = tempcleanup( $tempfilelist );
		
		$tempfilelist=array();
		
		$zip = new ZipArchive;
		if ($res=$zip->open('/home/admin/progo/wp-content/uploads/wpsc/downloadables/'.$backupfile,ZIPARCHIVE::CREATE) === TRUE) {
			foreach($filelist as $key => $files) {
				if (!is_file($files[79001])) //check file exists
					continue;
				$zip->addFile($files[79001], $files[79003]);
			}
			$zip->close();
			echo 'Backup Zip file create done!<br />';
		} else {
			echo 'Can not create Backup ZIP file<br />';
		}
	}
}
?>
</body>
</html>
