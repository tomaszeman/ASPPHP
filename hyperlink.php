<?php

class HyperLink extends WebControl
{
	private $target;
	public function getTarget() { return $this->target; }
	public function setTarget($target) { $this->target= $target; }
	
	private $toolTip;
	public function getToolTip(){ return $this->toolTip; }
	public function setToolTip($value) { $this->toolTip= $value; }
	
	private $url;
	public function getUrl() { return $this->url; }
	public function setUrl($url) { $this->url= $url; }
	
	public function __construct() 
	{	
		parent::__construct("a");
		$this->clickEvent= new Event(); 
	}
	
	protected function buildAttributes()
	{
		$attributes= &$this->getAttributes();
		if($this->url !== null)
			$attributes["href"]= $this->getPage()->resolveClientUrl($this->url);
			
		if($this->target !== null)
			$attributes["target"]= $this->target;
			
		if($this->toolTip !== null)
			$attributes["title"]= $this->toolTip;
		
		return parent::buildAttributes();
	}
	
	protected function isSelfClosedTag() { return false; }
}
?>