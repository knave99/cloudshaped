<?php
$maxFileSize = 512000;					// max file size in bytes
$destination = 'c:/xuploads/';  		//define the path to the upload folder
$permittedFileTypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'text/plain', 'text/log');

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
		$OK = $this->checkError($field['name'], $field['error']);
		if ( $OK ) {
			$sizeOK = $this->checkSize($field['name'], $field['size']);
			$typeOK = $this->checkType($field['name'], $field['type']);
			$success = move_uploaded_file($field['tmp_name'], $this->_destination . $field['name']);		
			if ($success) {
				$this->_messages[] = $field['name'] . ' uploaded successfully';
			} else {
				$this->_messages[] = 'Could not upload ' . $field['name'];
			}		
		}
	}
	
	public function getMessages() {
		return $this->_messages;
	}
	
	protected function checkError($filename, $error) {
		switch ($error) {
			case 0: {
						return true;
					}
			case 1:
			case 2: {
						$this->_messages[] = "$filename exceeds maximum size: ". $this->getMaxSize();
						return true;
					}
			case 3: {
						$this->_messages[] = "Error uploading $filename.  Please try again.";
						return false;
					}
			case 4: {
						$this->_messages[] = "No file selected.";
						return false;
					}
			default: 	{
							$this->_messages[] = "System error uploading $filename.  Contact webmaster.";
							return false;
						}
		}
	}
	
	protected function checkSize($filename, $size) {
		if ($size==0) {
			return false;
		} elseif ($size > $this->_maxFileSize) {
			$this->_messages[] = "$filename exceeds maximum size: " . $this->getMaxSize();
			return false;
		} else {
			return true;
		}
	}
	
	protected function checkType($filename, $type) { 
		if ( !in_array($type, $this->_permittedFileTypes )) {
			$this->_messages[] = "$filename is not a permitted type of file.";
			return false;
		} else {
			return true;
		}
	}
	public function getMaxSize() {
		return number_format($this->_maxFileSize / 1024, 1) . 'kB';
	}
}