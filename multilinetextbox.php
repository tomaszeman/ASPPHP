<?php

class MultilineTextBox extends TextControl
{
	public function __construct() 
	{
		parent::__construct("textarea"); 
	}
	
	private $rows, $columns;
	function setColumns($columns) { $this->columns = $columns; }
	function setRows($rows) { $this->rows = $rows; }
	function getColumns() { return $this->columns; }
	function getRows() { return $this->rows; }
	
	private $isPassword= false;
	public function setPasswordMode($value= null) { $this->isPassword= $value === null || $value; }
	public function isInPasswordMode() { return $this->isPassword; }
	
	protected function isSelfClosedTag() { return false; }	
	
	protected function buildAttributes()
	{	
		$attributes= &$this->getAttributes();
		
		$attributes["name"]= $this->getUniqueId();
		$columns= $this->columns;
		if($columns != null)
			$attributes["cols"]= $columns;
		$rows= $this->rows;
		if($columns != null)
			$attributes["rows"]= $rows;
			
		return parent::buildAttributes();
	}
	
	protected function renderContents() 
	{ 
		$text= $this->getText();
		if($text != null)
			echo htmlspecialchars($text);
	}	
}
?>