<?php

class Control
{
	private $id;
	public function getId() { return $this->id; }
	public function setId($id) { $this->id= $id; }
	
	private $lastIdIndex= -1;
	
	const DEFAULT_ID_SEPARATOR= "_";
	
	protected function getClientIdSeparator() { return self::DEFAULT_ID_SEPARATOR; }
	protected function getIdSeparator() { return self::DEFAULT_ID_SEPARATOR; } 
	
	private $clientId;
	public function getClientId() 
	{
		$mode= $this->clientIdMode;
		$id= $this->id;
		
		if($mode == ClientIDMode::Hidden)
			return null;
		
		if($mode == ClientIDMode::Fixed)
		{
			if($id == null)
				throw new Exception("Id is required in fixed mode.");
			return $id;
		}
		
		//Legacy mode
		$id= $this->getUniqueId();
		$csep= $this->getClientIdSeparator();
		$sep= $this->getIdSeparator();
		if($csep == $sep)
			return $id; 
		//translate	
		return strtr($id, $sep, $csep);  
	}
	
	public function getUniqueId()
	{
		$id= $this->id;
		$separator= $this->getIdSeparator();
		$nc= $this->namingContainer;
		//create dynamic id
		$page= $this->page;
		while($nc !== $page)
		{
			$id= "{$nc->getId()}$separator$id";
			$nc= $nc->getNamingContainer();
		}
		return $id;
	}
	
	public function setClientId($clientId) { $this->clientId= $clientId; }
	
	private $clientIdMode;
	public function getClientIdMode() { return $this->clientIdMode; }
	public function setClientIdMode($clientIdMode) { $this->clientIdMode= $clientIdMode; }
	
	private $page;
	public function getPage() { return $this->page; }
	public function setPage(Page $page) { $this->page= $page; }
	
	private $namingContainer;
	public function getNamingContainer() { return $this->namingContainer; }
	public function setNamingContainer(INamingContainer $namingContainer) { $this->namingContainer= $namingContainer; }
	
	private $parent;
	public function getParent() { return $this->parent; }
	public function setParent(Control $parent) { $this->parent= $parent; }
	
	private $controls;
	public function getControls() { return $this->controls; }
	
	public function __construct()
	{
		$this->clientId= ClientIDMode::Legacy;
		$this->controls= $this->createControlCollection();
	}
	
	private $visible= true;
	public function isVisible() { return $this->visible; }
	public function setVisible($visible= null) { $this->visible= $visible === null || $visible; }
	
	protected function renderChildren()
	{
		foreach($this->controls as $control)
			$control->Render();
	}
	
	protected function createControlCollection() { return new ControlCollection($this); }
	
	//protected function onInit() { $this->ensureChildControlsCreated(); }
	
	protected function onInit() {}
	
	protected function onLoad() {}
	
	protected function generateAutomaticId() { return "c". ++$this->lastIdIndex; }
	
	/*protected function ensureId()
	{
		$nc= $this->namingContainer;
		if($this->id == null && $nc != null)
			$this->id= $nc->generateAutomaticId();
	}*/
	
	public function render() 
	{ 
		if($this->visible)
			$this->renderChildren(); 
	}
	
	protected function saveControlState() { return null; }
	protected function loadControlState($state) {}
	
	public function controlAdded(Control $control)
	{
		$control->setParent($this);
		//$page= $this->getPage();
		//is it possible to get page and namingcontainers? 
		
		/*$nc= $this instanceof INamingContainer 
			? $this 
			: $this->getNamingContainer();
			
		if($nc != null)
			$this->updateNamingContainer($control, $nc);*/
		
		/*if($page != null)
		{
			$page->addedControlToPage($control);
			self::traverseControlStructure(
				$control->getControls(), 
				function(Control $c, $page) { $page->addedControlToPage($c); },
				$page);
		}*/
	}
	
	/*private $childControlsCreated= false;
	protected function createChildControls() { $this->childControlsCreated= true; }
	protected function getChildControlsCreated() { return $this->$childControlsCreated; }
	protected function ensureChildControlsCreated() 
	{
		if(!$this->childControlsCreated)
		{
			$this->createChildControls();
			$this->childControlsCreated= true;
		}  
	}*/
	
	protected function initRecursive(Control $control, INamingContainer $nc)
	{	
			
		$page= $nc->getPage();
		$control->setPage($page);
		$page->addedControlToPage($control);
		
		$control->setNamingContainer($nc);
		
		if($control->getId() === null)
			$control->setId($nc->generateAutomaticId());
		
		$control->onInit();
		
		if($control instanceof INamingContainer)
			$nc= $control;
	
		foreach($control->getControls() as $child)
			$this->initRecursive($child, $nc);
	}
	
	protected function loadRecursive(Control $control)
	{	
		foreach($control->getControls() as $child)
			$this->loadRecursive($child);
			
		$control->onLoad();
	}
	
	/*protected function initRecursive(Control $control, INamingContainer $nc)
	{
		/*if($control instanceof INamingContainer)
			$nc= $control;
			
		foreach($control->getControls() as $child)
		{
			$page->addedControlToPage($control->getPage());
			$control->setNamingContainer= $nc;
			if($control->id == null)
				$control->id= $nc->generateAutomaticId();
			$this->initRecursive($child, $nc);
		}
		
		$control.OnInit();
	}*/
	
	/*protected function updateNamingContainer(Control $control, INamingContainer $nc)
	{
		if($control->id === null)
			$control->id= $nc->generateAutomaticId();
			
		$control->setNamingContainer($nc);
		
		if($control instanceof INamingContainer)
			return;
			//$nc= $control;
		
		foreach($control->getControls() as $child)		
			$this->updateNamingContainer($child, $nc);
	}*/
	
	/*
	public function findControl($id)
	{
		
	}*/
	
	/*public function findControl($id)
	{
		if((!$this instanceof INamingContainer))
			return ($this->getNamingConainer()->findControl($id);
			
		return $this->findControlRecursive($this->controls, $id);
	}
	
	public function findControlRecursive(ControlCollection &$controls, $id)
	{
		foreach($controls as $child)
		{
			$curId= $child.getId();
			if($id == $curId)
				return $id;	
				
			if((!$child instanceof INamingContainer))
			{
				$curId= $this->findControlRecursive($child);
				if($curId != null)
					return $curId;
			}
		}
		return null;
	}*/
	
	static function traverseControlStructure(ControlCollection $controls, $apply, $params= null)
	{
		foreach($controls as $child)
		{
			$apply($child, $params);
			self::traverseControlStructure($child->getControls(), $apply, $params);
		}
	}
	
	public function populate($data) { $this->populateRecursive($this, $data); }

	protected function populateRecursive(Control $control, $data)
	{
		if($control instanceof IBindableControl)
		{
			$expression= $control->getExpression();
			
			if($expression === null)
				return;
			
			if(!strpos($expression, "->"))
				$control->setValue($data->$expression);
			else
				$control->setValue(eval("return \$data->$expression;"));
			
			return;
		}
		foreach($control->getControls() as $child)
			$this->populateRecursive($child, $data);
	}
	
	public function persist($data) { $this->persistRecursive($this, $data); }
	
	protected function persistRecursive(Control $control, $data)
	{
		if($control instanceof IBindableControl)
		{
			$expression= $control->getExpression();
			
			if($expression === null)
				return;
				
			$value= $control->getValue();
			if(!strpos($expression, "->"))
				$data->$expression= $value;
			else
				eval("\$data->$expression= \$value;");
				
			return;
		}
		foreach($control->getControls() as $child)
			$this->persistRecursive($child, $data);
	}
}

?>