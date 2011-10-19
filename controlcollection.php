<?php

//TODO: Empty Collection

class ControlCollection implements IteratorAggregate, Countable
{
	private $self= array();
	private $owner;
	
	public function getOwner() { return $this->owner; }
	
	public function __construct($owner) { $this->owner= $owner; }
	
	public function add(Control $control)
	{	
		$this->self[]= $control;
		$this->owner->controlAdded($control);
	}
	
	public function remove(Control $control)
	{
		$key= array_search($this->self, $control);
		if($key)
			unset($this->self[$key]);
	}
	
	public function addRange()
	{	
		$self= &$this->self;
		$owner= $this->owner;
		foreach(func_get_args() as $control)
		{
			$self[]= $control;
			$owner->controlAdded($control);
		}
	}
	
	public function getIterator() { return new ArrayIterator($this->self); }
	
	public function count() { return count($this->self); }
	
	public function isEmpty() { return empty($this->self); }
	
	public function indexOf(Control $control) { return array_search($control, $this->self); }
}

?>