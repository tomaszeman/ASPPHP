<?php

abstract class TextControl extends WebControl implements IPostBackDataHandler, IEditableTextControl, IValidableControl, IBindableControl
{
	public function __construct($tag= null) 
	{
		$this->textChangedEvent= new Event(); 
		parent::__construct($tag); 
	}
	
	private $maxLength;
	public function getMaxLength() { return $this->maxLength; }
	public function setMaxLength($value) { $this->maxLength= $value; }
	
	private $text;
	public function getText() { return $this->text; }
	public function setText($text) { $this->text= $text; }
	
	private $textChangedEvent;
	public function getTextChangedEvent() { return $this->textChangedEvent; }
	
	private $isControlStateTrackingChanges= false;
	public function setControlStateTrackingChanges($track= null) { $this->isControlStateTrackingChanges= $track === null || $track; }
	public function isControlStateTrackingChanges() { return $this->isControlStateTrackingChanges; }
	
	public function getValue() 
	{ 
		$callback= $this->valueFromText;
		return $callback != null ? delegate_patch($callback, $this->text) : $this->text; 
	}
	
	public function setValue($value) 
	{
		$callback= $this->valueToText;
		$this->text= $callback != null ? delegate_patch($callback, $value) : $value; 
	}
	
	private $expression;
	public function setExpression($expression) { $this->expression= $expression; }
	public function getExpression() { return $this->expression; }
	
	private $valueToText, $valueFromText;
	
	public function setValueToText($callback) { $this->valueToText= $callback; }
	public function setValueFromText($callback) { $this->valueFromText= $callback; }
	public function getValueToText() { return $this->valueToText; }
	public function getValueFromText() { return $this->valueFromText; }
	
	private $validate;
	public function setValidate($callback) { $this->validate= $callback; }
	
	private $isRequired;
	public function setRequired($value= null) { $this->isRequired= $value === null || $value; }
	public function isRequired() { return $this->isRequired; }
	
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
		$text= $this->text;
		
		if(!$this->performValidation($text))
		{
			$isValid= false;
			return false;
		}
		$callback= $this->validate;
		$isValid= $callback == null || delegate_patch($callback, $text, $this);
		return $isValid;
	}
	
	public function performValidation($text= null)
	{
		return !($this->isRequired && $text == "");
	}
	
	protected function onInit() 
	{ 
		$page= $this->getPage();
		if($this->isControlStateTrackingChanges)
			$page->registerRequiresControlState($this); 
		$page->registerRequiresValidation($this);
		parent::onInit();	
	} 
	
	public function loadPostData($value) 
	{
		$value= trim($value);
		
		if(!$this->isControlStateTrackingChanges)
			$this->oldText= $this->text;
		$this->text= $value;
		return $this->oldText != $value;
	}
	
	public function raisePostDataChangedEvent() { $this->textChangedEvent->invoke($this, null); }
	
	public function saveControlState() { return $this->text; }
	
	public function loadControlState($state) { $this->oldText= $state; }	
} 