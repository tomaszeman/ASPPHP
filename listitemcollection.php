<?php

//TODO: Empty Collection

class ListItemCollection implements IteratorAggregate, Countable
{
	private $self= array();
		
	public function add(ListItem $item) { $this->self[$item->getValue()]= $item; }
	
	public function addRange()
	{	
		$self= &$this->self;
		foreach(func_get_args() as $item)
			$self[$item->getValue()]= $item;
	}
	
	//private $owner;
	
	/*public function getOwner() { return $this->owner; }
	
	public function __construct($owner) { $this->owner= $owner; }
	*/
	public function clear() { $this->self= array(); }
	
	public function filter($callback) { return array_filter($this->self, $callback); }
	
	public function getIterator() { return new ArrayIterator($this->self); }
	
	public function count() { return count($this->self); }
	
	public function isEmpty() { return empty($this->self); }
	
	public function indexOf(ListItem $item) { return array_search($item, $this->self); }
	
	public function findByValue($value) { return $this->self[$value]; }
	
	public function findByText($text)
	{
		$self= &$this->self;
		$key= array_search($self, $text);
		return $key ? $self[$key] : null;
	}
	
	
}

?>