<?php

class FormLayout extends WebControl 
{	
	private $rowControls= array();
	private $messages= array();
	const MSG_ERROR= 0;
	const MSG_INFO= 3;
	const MSG_WARNING= 2;
	const MSG_SUCCESS= 1;
	
	public function addMessage($text, $type= 3)
	{
		$this->messages[]= array($type, $text);
	}
	private $msgCssClasses;
	public function setMessageCssClass($error, $success, $warning, $info)
	{
		$this->msgCssClasses= array($error, $success, $warning, $info);
	}
	
	/*private $showResetButton= true;
	public function setAutoPostBack($value= null) { $this->autoPostBack= $value === null || $value; }
	public function isAutoPostBack() { return $this->autoPostBack; }*/
	
	private $actionButtons;
	public function addActionButton(IButtonControl $button)
	{
		$buttons= &$this->actionButtons;
		if($buttons === null)
			$buttons= array();
		$this->actionButtons[]= $button;
		$this->getControls()->add($button);
	}
	
	private $submitButton;
	function setSubmitButton(IButtonControl $submitButton) 
	{ 
		$button= &$this->submitButton;
		$controls= $this->getControls();
		if($button !== null)
			$controls->remove($button);
			
		$button= $submitButton;
		$controls->add($button);
		$button->getClickEvent()->addHandler(array($this, 'submitButton_click'));
	}
	function getSubmitButton() { return $this->submitButton; }
	
	private $submitEvent;
	function getSubmitEvent() { return $this->submitEvent; }

	private $validationGroup;
	function setValidationGroup($validationGroup) { $this->validationGroup = $validationGroup; }
	function getValidationGroup() { return $this->validationGroup; }
	
	private $rowCssClass;
	function setRowCssClass($value) { $this->rowCssClass = $value; }
	function getRowCssClass() { return $this->rowCssClass; }
	
	private $labelCssClass;
	function setLabelCssClass($value) { $this->labelCssClass = $value; }
	function getLabelCssClass() { return $this->labelCssClass; }
	
	private $requiredLabelCssClass;
	function setRequiredLabelCssClass($value) { $this->requiredLabelCssClass = $value; }
	function getRequiredLabelCssClass() { return $this->requiredLabelCssClass; }
	
	private $actionBarCssClass;
	function setActionBarCssClass($value) { $this->actionBarCssClass = $value; }
	function getActionBarCssClass() { return $this->actionBarCssClass; }
	
	private $errorCssClass;
	function setErrorCssClass($value) { $this->errorCssClass = $value; }
	function getErrorCssClass() { return $this->errorCssClass; }
	
	private $messageBarCssClass;
	function setMessageBarCssClass($value) { $this->messageBarCssClass = $value; }
	function getMessageBarCssClass() { return $this->messageBarCssClass; }	
	
	function __construct() 
	{
		parent::__construct("div");
		$this->submitEvent= new Event();
	}
	
	public function submitButton_click()
	{
		$this->onSubmit();
	}
	
	/*public function addLabelRow($label, $forControl= null)
	{
		$lab= new Label();
		$lab->setText($label);
		if($forControl !== null)
			$lab->setAssociatedControl($forControl);
		$lab->setClientIdMode(ClientIdMode::Hidden);
		$this->getControls()->add($lab);
		
		$rowControls= new \stdClass;
		$rowControls->label= $lab;
		$this->rowControls[]= $rowControls;
	}*/
	
	public function addRow($label= null, Control $control, $expression= null, $errorMessage= null)
	{
		$controls= $this->getControls();
		
		$validable= $control instanceof IValidableControl;
		$bindable= $control instanceof IBindableControl;
		if($validable)
		{
			if($errorMessage === null)
			{
				if($control->getErrorMessage() == null)
					$control->setErrorMessage("*");
			}
			else
				$control->setErrorMessage($errorMessage);
				
			if($this->validationGroup !== null && $control->getValidationGroup() !== null)
				$control->setValidationGroup($this->validationGroup);
		}
			
		if($bindable && $expression !== null)
			$control->setExpression($expression);
		
		$lab= null;
		if($label !== null)
		{
			$lab= new Label();
			$lab->setAssociatedControl($control);
			$lab->setText($label);
			$lab->setClientIdMode(ClientIdMode::Hidden);
			
			$cssClass= "";
			if($this->labelCssClass !== null)
				$cssClass= $this->labelCssClass;
			
			if($validable && $control->isRequired() && $this->requiredLabelCssClass !== null)
				$cssClass.= $cssClass == "" ? $this->requiredLabelCssClass : " {$this->requiredLabelCssClass}";
				
			if($cssClass != "")
				$lab->setCssClass($cssClass);
			
			$controls->add($lab);
		}
		
		$controls->add($control);
		
		$error= null;
		if($validable)
		{
			$error= new ValidationErrorLabel();
			$error->setAssociatedControl($control);
			
			if($this->errorCssClass !== null)
				$error->setCssClass($this->errorCssClass);
			$controls->add($error);
		}
		
		$rowControls= new stdClass;
		$rowControls->label= $lab;
		$rowControls->control= $control;
		$rowControls->error= $error;
		$this->rowControls[]= $rowControls;
	}
	
	protected function isSelfClosedTag() { return false; }
	
	protected function onInit()
	{
		parent::onInit();
	}
	
	protected function onSubmit()
	{
		$this->submitEvent->invoke($this);
	}
	
	protected function renderContents()
	{
		$this->renderErrorBar();
		foreach($this->rowControls as $controls)
			$this->renderRow($controls);
		$this->renderActionBar();
	}
	
	protected function renderRow($controls)
	{
		if($this->rowCssClass === null)
			echo "<div>";
		else
			echo "<div class=\"{$this->rowCssClass}\">";
			
		if(isset($controls->label))
			$controls->label->render();
		if(isset($controls->control))
		{
			echo "<span class=\"form_control\">";
			$controls->control->render();
			echo "</span>";
		}
		if(isset($controls->error))	
			$controls->error->render();
		echo "<span class=\"cls\"></span></div>";
	}
	
	protected function renderErrorBar()
	{
		$messages= $this->messages;
		if(count($messages) > 0)
		{
			usort($messages,array($this, 'sortMessages'));
			$cssClasses= $this->msgCssClasses;
			
			$messageBarCssClass= $this->messageBarCssClass;
			if($messageBarCssClass !== null)
				$messageBarCssClass= " class=\"$messageBarCssClass\"";
			
			echo "<ul$messageBarCssClass>";
			foreach($messages as $message)
			{
				$cssClass= $cssClasses[$message[0]];
				if($cssClass !== null)
					$class= " ";
				$cssClass= $cssClass !== null ? " class=\"$cssClass\"" : "";
				echo "<li$cssClass>{$message[1]}</li>";
			}
			echo "</ul>";	
		}
	}
	
	public function sortMessages($message, $message2)
	{
		$a= $message[0];
		$b= $message[0];
		if ($a == $b) 
			return $a;
		return ($a < $b) ? -1 : 1;
	}
	
	protected function renderActionBar()
	{
		$css= $this->actionBarCssClass;
		
		if($css=== null)
			echo "<div>";
		else
			echo "<div class=\"{$css}\"><span>";
			
		if($this->submitButton !== null)
			$this->submitButton->render();
			
		$buttons= &$this->actionButtons;
		if($buttons != null)
			foreach($buttons as $button)
				$button->render();
		echo "</span></div>";
	}
	
}
?>