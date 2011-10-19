<?php

class DatePicker extends TextBox
{
	public function __construct()
	{
		parent::__construct();
		$this->setMaxLength(10);
	} 
	

	
	public function performValidation($text= null)
	{
		if($text == "" && !$this->isRequired())
			return true;
		
		if(!preg_match("/^[0-3]?[0-9].[1-2]?[0-9].[1-2][0-9]{3}$/i", $text))
			return false;

		$parts= explode(".", $text);
		return checkdate($parts[1], $parts[0], $parts[2]);
	}
	
	public function getValue() 
	{ 
		$callback= $this->getValueFromText();
		if($callback !== null)
			return $callback($this->text);
		
		$text= $this->getText();
		if($text === null || $text == "")
			return null;
				
		$parts= explode(".", $text);
		$day= str_pad($parts[0], 2, "0", STR_PAD_LEFT);
		$month= str_pad($parts[1], 2, "0", STR_PAD_LEFT);
	
		return "{$parts[2]}-$month-$day"; 
	}
	
	public function setValue($value) 
	{
		$callback= $this->getValueToText();
		if($callback !== null)
		{
			$this->setText($callback($value));
			return;
		}
		if(empty($value))
		{
			$this->setText(null);
			return;
		}
		$parts= date_parse($value);
		$this->setText("{$parts["day"]}.{$parts["month"]}.{$parts["year"]}");
	}
	
}
?>