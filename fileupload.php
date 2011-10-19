<?php

class FileUpload extends TextControl
{
	private $fileName;
	private $temp;
	private $fileType;
	private $errors;
	
	public function __construct() 
	{
		parent::__construct("input");
		$this->errors= array(
				UPLOAD_ERR_INI_SIZE =>
					"The uploaded file exceeds the upload_max_filesize directive in php.ini.",
				UPLOAD_ERR_FORM_SIZE =>
					"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
				UPLOAD_ERR_PARTIAL =>
					"The uploaded file was only partially uploaded.",
				UPLOAD_ERR_NO_TMP_DIR =>
					"Missing a temporary folder.",
				UPLOAD_ERR_CANT_WRITE =>
					"Failed to write file to disk.",
				UPLOAD_ERR_EXTENSION =>
					"File upload stopped by extension.");
	}
	
	protected function onInit()
	{
		parent::onInit();
		$page= $this->getPage()->setEncType("multipart/form-data");	
	}
	
	protected function buildAttributes()
	{
		$attributes= &$this->getAttributes();
		$attributes["type"]= "file";
		$attributes["name"]= $this->getUniqueId();
			
		return parent::buildAttributes();
	}
	
	public function loadPostData($value) 
	{	
		$id= $this->getClientId();
		
		if(!isset($_FILES[$id]))
			return false;
		
		$file= $_FILES[$id];
		$error= $file['error'];
		
		if($error == UPLOAD_ERR_NO_FILE)
			return false;
		elseif($error != UPLOAD_ERR_OK)
			throw new Exception($this->errors[$file['error']]);
		
		$fileName = basename($file['name']);
		
		if(empty($fileName))
			return false;
		
		$this->fileName= $fileName;
		$this->temp= $file['tmp_name'];	
		$this->fileType= $file['type'];
		
		return true;
	}
	
	public function getFileName() { return $this->fileName; }
	public function getFileType() { return $this->fileType; }
	public function getFullTempFileName() { return $this->temp; }
	
	public function hasFile() { return $this->fileName !== null; }
	
	public function saveFile($target)
	{
		$result= @move_uploaded_file($this->temp, $target);
	}
}
?>