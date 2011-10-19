<?php

class WebConfig
{
	private static $instance;
	
	public $authentication;
	public $locations;
	public $connections;
	
	public static function getInstance()
	{
		$instance= &self::$instance;
		if($instance === null)
			$instance= new WebConfig();
		return $instance;
	}
	
	private function __construct() 
	{
		$this->authentication= new Authentication();
		$this->locations= array();
	}
}
?>