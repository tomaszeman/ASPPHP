<?php

class ValidationErrorLabel extends WebControl implements ITextControl
{
	private $text;
	public function getText() { return $this->text; }
	public function setText($text) { $this->text= $text; }
	
	private $associatedControl;
	public function getAssociatedControl() { return $this->associatedControl; }
	public function setAssociatedControl(IValidableControl $control) { $this->associatedControl= $control; }
	
	public function __construct() 
	{	
		parent::__construct("span");
		$this->clickEvent= new Event(); 
	}
	
	protected function isSelfClosedTag() { return false; }
	
	protected function renderContents() 
	{ 
		$control= $this->associatedControl;
		if($control == null)
			throw new Exception("Associated validable control is not set.");
		
		if($control->wasValidated() && !$control->isValid())
		{
			$message= $this->text;
			if($message == null)
				$message= $control->getErrorMessage();
			if($message == null)
				throw new Exception("One of text of validation label or error message of validated control have to be set.");
			echo $message;
		}
	}
}
?>