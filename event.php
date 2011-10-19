<?php

class Event
{
	public $handlers= array();
	
	public function __construct()
	{
		$args= func_get_args();
		if(count($args) > 0) 
			$this->self= $args;
	}
	
	public function addHandler($handler)
	{
		$args= func_get_args();
		if(count($args) > 1)
			$this->handlers= array_merge($this->handlers, $args);
		else
			$this->handlers[]= &$handler;
	}
	
	public function removeHandler($handler)
	{
		$args= func_get_args();
		
		foreach($args as $arg)
		{
			$i= array_search($arg, $this->handlers);
			unset($this->handlers[$i]);
		}
	}
	
	public function invoke($source, EventArgs $args= null)
	{
		foreach($this->handlers as $handler)
			call_user_func($handler, $source, $args);
	}
}
?>