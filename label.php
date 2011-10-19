<?php

class Label extends WebControl implements ITextControl
{
	private $text;
	public function getText() { return $this->text; }
	public function setText($text) { $this->text= $text; }
	
	private $associatedControl;
	public function getAssociatedControl() { return $this->associatedControl; }
	public function setAssociatedControl(Control $control) { $this->associatedControl= $control; }
	
	public function __construct() 
	{	
		parent::__construct("span");
		$this->clickEvent= new Event(); 
	}
	
	protected function buildAttributes()
	{
		$control= $this->associatedControl;
		if($control != null)
		{
			$attributes= &$this->getAttributes();
			$attributes["for"]= $control->getClientId();
		}
		
		return parent::buildAttributes();
	}
	
	protected function isSelfClosedTag() { false; }
	
	public function render()
	{
		if($this->associatedControl != null)
			$this->setTag("label");
		parent::render();
	}
	
	protected function renderContents() 
	{ 
		if($this->text != null)
			echo htmlspecialchars($this->text);
	}
}
?>