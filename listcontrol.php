<?php

class ListControl extends WebControl implements 
	IPostBackDataHandler, 
	IEditableTextControl, 
	IValidableControl, 
	IBindableControl,
	IPostBackEventHandler
{	
	private $items; 
	public function getItems() { return $this->items; }
	
	private $oldValue;
	
	public function __construct($tag= null) 
	{	
		parent::__construct($tag);
		$this->textChangedEvent= new Event();
		$this->items= new ListItemCollection();
	}
	
	protected function onInit() 
	{  
		$page= $this->getPage();
		if($this->isControlStateTrackingChanges)
			$page->registerRequiresControlState($this);
		$page->registerRequiresValidation($this);
		if($this->autoPostBack)
			$page->registerRequiresPostBack($this);
	}
	
	private $textChangedEvent;
	public function getTextChangedEvent() { return $this->textChangedEvent; }
	public function selectedItemChanged() { return $this->textChangedEvent; }
	
	private $isControlStateTrackingChanges= false;
	public function setControlStateTrackingChanges($track= null) { $this->isControlStateTrackingChanges= $track === null || $track; }
	public function isControlStateTrackingChanges() { return $this->isControlStateTrackingChanges; }
	
	public function getText() { return $this->getSelectedValue(); }	
	public function setText($text) { $this->setSelectedValue($text); } 
	
	private $autoPostBack= false;
	public function setAutoPostBack($value= null) { $this->autoPostBack= $value === null || $value; }
	public function isAutoPostBack() { return $this->autoPostBack; }
	
	public function getValue() 
	{ 
		$value= $this->getSelectedValue();
		if($value == "")
			return null;
		$callback= $this->valueFromText;
		return $callback != null ? $callback($value) : $value; 
	}
	public function setValue($value) 
	{
		if($value === null)
		{
			$this->setSelectedValue("");
			return;
		}
		$callback= $this->valueToText;
		$this->setSelectedValue($callback != null ? $callback($value) : $value); 
	}
	
	private $valueToText= null, $valueFromText= null;
	
	public function setValueToText($callback) { $this->valueToText= $callback; }
	public function setValueFromText($callback) { $this->valueFromText= $callback; }
	public function getValueToText() { return $this->valueToText; }
	public function getValueFromText() { return $this->valueFromText; }
	
	private $expression;
	public function setExpression($expression) { $this->expression= $expression; }
	public function getExpression() { return $this->expression; }
	
	public function clearSelection()
	{
		foreach($this->items as $item)
			$item->setSelected(false);
	}
	
	public function getSelectedValue()
	{
		foreach($this->items as $item)
			if($item->isSelected())
				return $item->getValue();
		return null;
	}
	
	public function setSelectedValue($value)
	{
		$item= $this->items->findByValue($value);
		if($item != null)
			$item->setSelected();
	}
	
	protected function isSelfClosedTag() { return $this->items->isEmpty(); }
	
	public function loadPostData($value) 
	{
		$value= trim($value);
		
		if(!$this->isControlStateTrackingChanges)
			$this->oldValue= $this->getSelectedValue();
		
		$this->setSelectedValue($value);
		return $this->oldValue != $value;
	}
	
	public function saveControlState() { return $this->getSelectedValue(); }
	
	public function loadControlState($state) { $this->oldValue= $state; }
	
	public function raisePostDataChangedEvent() 
	{
		$this->textChangedEvent->invoke($this, null);
	}
	
	public function raisePostBackEvent() {}
	
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
		$text= $this->getSelectedValue();
		
		if(!$this->performValidation($text))
		{
			$isValid= false;
			return false;
		}
		$callback= $this->validate;
		$isValid= $callback == null || $callback($text, $this);
		return $isValid;
	}
	
	public function performValidation($text= null)
	{
		return !($this->isRequired && $text == "");
	}
	
	public function populateItems($dataSource, $idProp, $nameProp= null, $appendEmpty= true)
	{
		$items= $this->items;
		if($appendEmpty)
			$items->add(new ListItem(null));
			
		foreach($dataSource as $data)
		{
			$item= $this->createListItem();
			$value= $data->$idProp;
			$item->setValue($value);
			$item->setText($nameProp === null ? $value : $data->$nameProp);
			$items->add($item);
		}
	}
	
	protected function createListItem() { return new ListItem(); }
	
	
}

/*protected function buildAttributes()
	{
		$attributes= &$this->getAttributes();
		$attributes["name"]= $this->getUniqueId();
		return parent::buildAttributes();
	}*/
	
	/*protected function renderChildren()
	{
		foreach($this->items as $item)
		{
			echo "<option value=\"{$item->getValue()}\"";
			if($item->isSelected())
				echo "selected=\"selected\"";
			echo ">{$item->getText()}</option>";
		}
	}*/
	
	/*protected function getSelectedValues()
	{
		return $this->items->filter( function($i) { return $i->isSelected(); });
	}*/
/*public function setSelectedValues($value)
	{
		$item= $this->items->findByValue($value);
		if($item != null)
			$item->setSelected();
	}*/ 
	
	//clear selection
	/*private $multiSelect= false;
	public function isMultiSelect() { return $this->multiSelect; }
	public function setMultiSelect($multi) { $this->multiSelect= $multi === null ? true : $multi; }*/
	
//	$emptyText;
//	public function getEmptyText() { return $this->emptyText; }
//	public function setEmptyText($empty) { $this->emptyText= $empty; }
	
?>