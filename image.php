<?php

class Image extends WebControl
{
	private $toolTip;
	public function getToolTip(){ return $this->toolTip; }
	public function setToolTip($value) { $this->toolTip= $value; }
	
	private $url;
	public function getUrl(){ return $this->url; }
	public function setUrl($value) { $this->url= $value; }
	
	public function __construct() 
	{	
		parent::__construct("img");
		$this->clickEvent= new Event(); 
	}
	
	protected function buildAttributes()
	{
		$attributes= &$this->getAttributes();
		
		if($this->url !== null)
			$attributes["src"]= $this->getPage()->resolveClientUrl($this->url);
		if($this->toolTip !== null)
			$attributes["alt"]= $this->toolTip; 
			
		return parent::buildAttributes();
	}
}
?>