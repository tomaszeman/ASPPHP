<?php

class Literal extends Control implements ITextControl
{
	private $text;
	public function getText() { return $this->text; }
	public function setText($text) { $this->text= $text; }
	
	public function render() 
	{
		if($this->isVisible()) 
			echo htmlspecialchars($this->text); 
	}
}
?>