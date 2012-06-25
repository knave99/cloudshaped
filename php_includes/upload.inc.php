<?php
$maxFileSize = 512000;					// max file size in bytes
$destination = 'c:/xuploads/';  		//define the path to the upload folder
$permittedFileTypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'text/plain');

if ( isset( $_POST['upload'] ) ) {

	//move the file and rename
	//move_uploaded_file($_FILES['image']['tmp_name'], $destination . $_FILES['image']['name']);
	try {
		$upload = new Rsc_Upload($destination, $permittedFileTypes, $maxFileSize);
		$upload->move();
		$result = $upload-> getMessages();
	} catch (Exception $e) {
		echo $e->getMessage();
	}
}

/**
 * Generic file uploading class.
 * @author rstuart
 *
 */
class Rsc_Upload {
	
	protected $_uploaded = array();
	protected $_destination;
	protected $_maxFileSize;
	protected $_messages = array();
	protected $_permittedFileTypes = array();
	protected $_renamed = false;
	
	public function __construct($path, $permittedFileTypes, $maxFileSize) {
		if (!is_dir($path) || !is_writeable($path)) {
			throw new Exception("$path must be a valid, writeable directory.");
		}
		$this->_destination = $path;
		$this->_uploaded = $_FILES; // superglobal
		$this->_permittedFileTypes = $permittedFileTypes;
		$this->_maxFileSize = $maxFileSize;
	}
	
	/**
	 * Moves the file from the temp directory to the assigned directory
	 */  
	public function move() {
		$field = current($this->_uploaded);
		$success = move_uploaded_file($field['tmp_name'], $this->_destination . $field['name']);
		if ($success) {
			$this->_messages[] = $field['name'] . ' uploaded successfully';
		} else {
			$this->_messages[] = 'Could not upload ' . $field['name'];
		}		
	}
	
	public function getMessages() {
		return $this->_messages;
	}
}