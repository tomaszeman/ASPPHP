<?php

class ListItem
{	
	private $selected= false;
	public function isSelected() { return $this->selected; }
	public function setSelected($selected= null) { $this->selected= $selected === null || $selected; }
	
	private $enabled= true;
	public function isEnabled() { return $this->enabled; }
	public function setEnabled($enabled= null) { $this->enabled= $enabled === null || $enabled; }
	
	private $text;
	public function getText() { return $this->text; }
	public function setText($text) { $this->text= $text; }
	
	private $value;
	public function getValue() { return $this->value; }
	public function setValue($value) { $this->value= $value; }
	
	public function __construct($value= null, $text= null, $selected= null) 
	{	
		if($value === null)
			return;
		$this->value= $value;
		$this->text= $text == null ? (string) $value : $text;
		if($selected != null)
			$this->selected == $selected;
	}
}

?>