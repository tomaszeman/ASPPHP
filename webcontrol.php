<?php

class WebControl extends Control
{
	private $tag;
	protected function getTag() { return $this->tag; }
	protected function setTag($tag= null) { return $this->tag= $tag; }
	
	private $cssClass;
	public function getCssClass() { return $this->cssClass; }
	public function setCssClass($cssClass) 
	{
		$this->cssClass= $cssClass; 
	}
	
	private $attributes;
	public function &getAttributes() { return $this->attributes; }
	public function setAttribute($attribute, $value) { $this->attributes[$attribute]= $value; }
	public function getAttribute($attribute) { return $this->attributes[$attribute]; }
	
	private $enabled= true;
	public function setEnabled($value= null) { $this->enabled= $value === null || $value; }
	public function isEnabled() { return $this->enabled; }
	
	public function __construct($tag= null) 
	{	
		parent::__construct(); 
		$this->tag= $tag;
		$this->attributes= array();
	}
	
	public function render()
	{
		if(!$this->isVisible())
			return;
			
		if(isset($this->tag))
			$this->renderBeginTag();
			
		$this->renderContents();
		
		if(isset($this->tag))
			$this->renderEndTag();
	}
	
	protected function renderContents() { $this->renderChildren(); }
	
	protected function renderBeginTag()
	{
		echo "<{$this->tag}";
		
		$attributes= $this->buildAttributes();
		if(strlen($attributes) > 0)
			echo " $attributes";
		
		if($this->isSelfClosedTag())
			echo "/";
		echo ">";
	}
	
	protected function isSelfClosedTag() { return $this->getControls()->isEmpty(); }
	
	protected function buildAttributes()
	{
		$attributes= &$this->attributes;
		$id= $this->getClientId();
		//hidden
		if($id !== null)
			$attributes["id"]= $id;
		
		if($this->cssClass !== null) 
			$attributes["class"]= $this->cssClass;
			
		if(!$this->enabled)
			$attributes["disabled"]= "disabled";
		
		$toRender= array();	
		foreach($attributes as $name => $value)
			$toRender[]= "$name=\"$value\"";
		return implode(" ", $toRender);
	}
	
	protected function renderEndTag()
	{
		if(!$this->isSelfClosedTag())
			echo "</". $this->tag . ">";
	}
}

?>