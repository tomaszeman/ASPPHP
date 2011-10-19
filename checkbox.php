<?php

class CheckBox extends WebControl  implements ICheckBoxControl, IPostBackDataHandler, IValidableControl
{
	private $checked;
	private $oldState;
	
	function isChecked() { return $this->checked; } 
	function setChecked($checked= null){ $this->checked= $checked === null || $checked; }
	
	private $checkedChangedEvent;
	public function getCheckedChangedEvent() { return $this->checkedChangedEvent; } 
	
	private $isControlStateTrackingChanges= false;
	public function setControlStateTrackingChanges($track= null) { $this->isControlStateTrackingChanges= $track === null || $track; }
	public function isControlStateTrackingChanges() { return $this->isControlStateTrackingChanges; }
	
	public function getValue() { return $this->checked; }
	public function setValue($value) { $this->checked= $value; }
	
	private $valueToText, $valueFromText, $validate;
	
	public function setValueToText($callback) { $this->valueToText= $callback; }
	public function setValueFromText($callback) { $this->valueFromText= $callback; }
	public function setValidate($callback) { $this->validate= $callback; }
	
	public function __construct() 
	{
		$this->checkedChangedEvent= new Event(); 
		parent::__construct("input"); 
	}
	
	private $required;
	public function setRequired($required= null) { $this->required= $required === null || $required; }
	public function isRequired() { return $this->required; }
	
	private $errorMessage;
	public function setErrorMessage($text) { $this->errorMessage= $text; }
	public function getErrorMessage() { return $this->errorMessage; }
	
	private $validationGroup;
	public function getValidationGroup() { return $this->validationGroup; }
	public function setValidationGroup($value) { $this->validationGroup= $value; }
	
	private $isValid;
	public function isValid()
	{
		$isValid= &$this->isValid;
		if($isValid === null)
			$isValid= $this->validate();
		return $isValid;
	}
	public function wasValidated() { return $this->isValid !== null; }
	
	public function validate()
	{	
		$isValid= &$this->isValid;
		$isValid= !$this->required || $this->checked;
		return $isValid; 
	}
	
	protected function onInit() 
	{ 
		$page= $this->getPage();
		if($this->isControlStateTrackingChanges)
			$page->registerRequiresControlState($this); 
		$page->registerRequiresValidation($this);	
	} 
	
	protected function buildAttributes()
	{
		$attributes= &$this->getAttributes();
		$attributes["type"]= "checkbox";
		$attributes["name"]= $this->getUniqueId();
		if($this->checked)
			$attributes["checked"]= "checked";
		return parent::buildAttributes();
	}
	
	public function loadPostData($value) 
	{
		$checked= $value !== null;
		
		if(!$this->isControlStateTrackingChanges)
			$this->oldState= $this->checked;

		$this->checked= $checked;
		return $this->oldState !== $checked;
	}
	
	public function raisePostDataChangedEvent() { $this->checkedChangedEvent->invoke($this, null); }
	
	public function saveControlState() { return $this->checked; }
	
	public function loadControlState($state) { $this->oldState= $state; }
	
	function performValidation($text= null) {}
}
?>