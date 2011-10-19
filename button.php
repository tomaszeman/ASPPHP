<?php

class Button extends WebControl implements IPostBackEventHandler, IButtonControl
{
	private $clickEvent;
	
	public function getClickEvent() { return $this->clickEvent; }
	
	public function __construct() 
	{	
		parent::__construct("input");
		$this->clickEvent= new Event(); 
	}
	
	private $text;
	public function getText() { return $this->text; }
	public function setText($text) { $this->text= $text; }
	
	private $causesValidation= true;
	public function setCausesValidation($value) { $this->causesValidation= $value == null || $value; }
	public function isCausingValidation() { return $this->causesValidation; }
	
	private $validationGroup;
	public function getValidationGroup() { return $this->validationGroup; }
	public function setValidationGroup($value) { $this->validationGroup= $value; }
	
	protected function buildAttributes()
	{
		$attributes= &$this->getAttributes();
		$attributes["type"]= "button";
		$text= $this->text;
		if($text != null)
			$attributes["value"]= htmlspecialchars($text);
		$attributes["name"]= $this->getUniqueId();
		
		$attributes["onclick"]= $this->getPage()->getScriptManager()->getPostBackEventReference($this);
		return parent::buildAttributes();
	}
	
	protected function onInit() 
	{  
		parent::onInit();
		$this->getPage()->registerRequiresPostBack($this);
	}
	
	public function raisePostBackEvent() 
	{ 
		if($this->causesValidation)
			$this->getPage()->validate($this->validationGroup);
			
		$this->clickEvent->invoke($this, null); 
	}
}
?>