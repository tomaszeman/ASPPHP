<?php

class HiddenTextBox extends TextControl
{
	public function __construct() 
	{
		parent::__construct("input"); 
	}
	
	protected function buildAttributes()
	{
		$attributes= &$this->getAttributes();
		$attributes["type"]= "hidden";
		$attributes["name"]= $this->getUniqueId();
		$value= $this->getText();
		if($value != null)
			$attributes["value"]= $value;
			
		return parent::buildAttributes();
	}
}
?>