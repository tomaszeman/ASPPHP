<?php
namespace Web\Configuration;

class Location
{
	public $path, $deny;
	
	public function __construct($path) 
	{
		$this->path= $path;
	}
}

?>