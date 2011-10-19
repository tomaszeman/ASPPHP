<?php

class LinkButton extends WebControl implements IPostBackEventHandler, IButtonControl
{
	private $clickEvent;
	
	public function getClickEvent() { return $this->clickEvent; }
	
	public function __construct() 
	{	
		parent::__construct("a");
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
	
	private $toolTip;
	public function getToolTip(){ return $this->toolTip; }
	public function setToolTip($value) { $this->toolTip= $value; }
	
	private $target;
	public function getTarget() { return $this->target; }
	public function setTarget($target) { $this->target= $target; }
	
	protected function isSelfClosedTag(){ return false; }
	
	protected function onInit() 
	{  
		parent::onInit();
		$this->getPage()->registerRequiresPostBack($this);
	}
	 
	protected function buildAttributes()
	{
		$attributes= &$this->getAttributes();
		$attributes["href"]= $this->getPage()->getScriptManager()->getPostBackClientHyperlink($this);
		
		if($this->toolTip !== null)
			$attributes["toolTip"]= $this->toolTip; 
		
		if($this->target !== null)
			$attributes["target"]= $this->target;	
			
		return parent::buildAttributes();
	}
	
	public function raisePostBackEvent() 
	{ 
		if($this->causesValidation)
			$this->getPage()->validate($this->validationGroup);
			
		$this->clickEvent->invoke($this, null); 
	}
	
	protected function renderContents() 
	{ 
		if($this->text != null)
			echo htmlspecialchars($this->text);
	}
}
?>