<?php

class ValidationSummary extends WebControl
{	
	public function __construct() 
	{	
		parent::__construct("ul");
		$this->clickEvent= new Event(); 
	}
	
	protected function isSelfClosedTag() { return false; }
	
	protected function renderContents() 
	{ 
		foreach($this->getPage()->getControlsRequiringValidation() as $control)
			if($control->wasValidated() && !$control->isValid())
				$this->renderErrorLine($control->getErrorMessage());
	}
	
	protected function renderErrorLine($message) 
	{
		echo "<li>$message</li>";
	}
}
?>