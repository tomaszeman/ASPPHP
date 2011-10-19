<?php

class DateTimePicker extends TextBox
{	
	public function __construct()
	{
		parent::__construct();
		$this->setMaxLength(16);
	} 
	
	private $allowDates= false;
	public function setAllowDates($value= null) { $this->allowDates= $value === null || $value; }
	public function getDatesAllowed() { return $this->allowDates; }
	
	public function performValidation($text= null)
	{
		if($text == "" && !$this->isRequired())
			return true;
			
		$regEx= $this->allowDates ? "( [0-2]?[0-9]:[0-6]?[0-9])?" : " [0-2]?[0-9]:[0-6]?[0-9]";
		if(!preg_match("/^[0-3]?[0-9].[1-2]?[0-9].[1-2][0-9]{3}$regEx$/i", $text))
			return false;

		$parts= preg_split("/[. ]+/i", $text);
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
		
		$parts= preg_split("/[ .:]+/i", $text);
		$day= str_pad($parts[0], 2, "0", STR_PAD_LEFT);
		$month= str_pad($parts[1], 2, "0", STR_PAD_LEFT);
		$hours= isset($parts[3]) ? str_pad($parts[3], 2, "0", STR_PAD_LEFT) : "00";	
		$minutes= isset($parts[4]) ? str_pad($parts[4], 2, "0", STR_PAD_LEFT) : "00";
			
		return "{$parts[2]}-$month-$day $hours:$minutes";  
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
		$minutes= str_pad($parts["minute"], 2, "0", STR_PAD_LEFT);
		$this->setText("{$parts["day"]}.{$parts["month"]}.{$parts["year"]} {$parts["hour"]}:{$minutes}");
	}
}
?>