<?php

class TextBox extends TextControl
{
	public function __construct() 
	{
		parent::__construct("input"); 
	}
	
	private $passwordMode= false;
	public function setPasswordMode($value= null) { $this->passwordMode= $value === null || $value; }
	public function isInPasswordMode() { return $this->passwordMode; }
	
	private $readonly= false;
	public function setReadOnly($value= null) { $this->readonly= $value === null || $value; }
	public function isReadOnly() { return $this->readonly; }
	
	protected function buildAttributes()
	{
		$attributes= &$this->getAttributes();
		$attributes["type"]= $this->passwordMode ? "password" : "text";
		$attributes["name"]= $this->getUniqueId();
		
		$value= $this->getText();
		if($value != null)
			$attributes["value"]= htmlspecialchars($value);
			
		$maxLength= $this->getMaxLength();
		if($maxLength != null)
			$attributes["maxlength"]= $maxLength;
			
		if($this->readonly)
			$attributes["readonly"]= "readonly"; 
			
		return parent::buildAttributes();
	}
}
?>