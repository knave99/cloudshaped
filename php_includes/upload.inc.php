<?php
include_once('php_includes_class/rsc_upload.inc.php');
$maxFileSize = 512000;					// max file size in bytes
$destination = 'c:/xuploads/';  		//define the path to the upload folder
$permittedFileTypes = array('image/gif', 
							'image/jpeg', 
							'image/pjpeg', 
							'image/png', 
							'text/plain',
							'application/vnd.ms-excel', 
							'text/log');

if ( isset( $_POST['upload'] ) ) {

	
	try {
		$upload = new net_cloudshaped\Upload($destination, $permittedFileTypes, $maxFileSize);
		$upload->addPermitedMimeTypes('application/pdf');
		$upload->move();
		$result = $upload-> getMessages();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

