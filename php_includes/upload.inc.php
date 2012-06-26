<?php
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

	//move the file and rename
	//move_uploaded_file($_FILES['image']['tmp_name'], $destination . $_FILES['image']['name']);
	try {
		$upload = new Rsc_Upload($destination, $permittedFileTypes, $maxFileSize);
		$upload->move(true);
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
	
	public function addPermitedMimeTypes($mimeTypes) {
		$fileTypes = (array) $mimeTypes;
		$this->isValidMimeType($mimeTypes);
		$this->_permittedFileTypes = array_merge($this->_permittedFileTypes, $mimeTypes);
	}
	
	public function setPermittedMimeTypes($mimeTypes) {
		$mimeTypes = (array) $mimeTypes;
		$this->isValidMimeType($mimeTypes);
		$this->_permittedFileTypes = $mimeTypes;
	}
	
	protected function isValidMimeType($mimeTypes) {
		$alsoValid = 	array(		'image/tiff',
									'application/pdf',
									'text/plain',
									'text/rtf'
							);
		$valid = array_merge($this->_permittedFileTypes, $alsoValid);
		foreach ($mimeTypes as $type) {
			if (!in_array($type, $valid)) {
				throw new Exception("$type is not permitted MIME type");
			}
		}
	}
	
	public function setMaxSize($num) {
		if (!is_numeric($num)) {
			throw new Exception("Maximum size must be a number.");
		} else if ( $num < 0 ) {
			throw new Exception("Maximum size cannot be less than 0.");
		}
		$this->_maxFileSize = (int) $num;
	}
	
	/**
	 * Moves the file from the temp directory to the assigned directory
	 */  
	public function move($overwrite = false) {
		$field = current($this->_uploaded);
		$OK = $this->checkError($field['name'], $field['error']);
		if ( $OK ) {
			$sizeOK = $this->checkSize($field['name'], $field['size']);
			$typeOK = $this->checkType($field['name'], $field['type']);
			if ( $sizeOK && $typeOK ) {
				$name = $this->checkName($field['name'], $overwrite);
				$success = move_uploaded_file($field['tmp_name'], $this->_destination . $name);		
				if ($success) {
					$message = $field['name'] . ' uploaded successfully';
					if ($this->_renamed) {
						$message .= " and renamed $name.";
					}
					$this->_messages[] = $message;
				} else {
					$this->_messages[] = 'Could not upload ' . $field['name'];
				}
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
		if ( empty($type) ) {
			return false;
						
		} elseif ( !in_array($type, $this->_permittedFileTypes )) {
			$this->_messages[] = "$filename is not a permitted type of file.";
			return false;
			
		} else {
			return true;
		}
	}
	public function getMaxSize() {
		return number_format($this->_maxFileSize / 1024, 1) . 'kB';
	}
	
	protected function checkName($name, $overwrite) {
		$noSpaces = str_replace(' ', '_', $name);
		if ($noSpaces != $name) {
			$this->_renamed = true; 
		}
		if (!$overwrite) {
			//rename the file if it already exists
			$existing = scandir($this->_destination);
			if (in_array($noSpaces, $existing)) {
				$dot = strrpos($noSpaces, '.');
				if ($dot) {
					$base = substr($noSpaces,0, $dot);
					$extension = substr($noSpaces, $dot);
				} else {
					$base = $noSpaces;
					$extension = '';
				}
				$i = 1;
				do {
					$noSpaces = $base . '_' . $i++ . $extension;	// seems sloppy?
				} while (in_array($noSpaces, $existing));
				$this->_renamed = true;
			}
		}
		return $noSpaces;
	}
}