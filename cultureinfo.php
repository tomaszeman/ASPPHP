<?php

class CultureInfo
{
	private static $UICulture;
	
	public static function getCurrentUICulture() { return self::$UICulture; }
	public static function setCurrentUICulture(CultureInfo $culture) { self::$UICulture= $culture; }
	
	private $name;
	
	public function getName() { return $this->name; }
	
	public function __construct($name)
	{
		$this->name= $name;
	}
}

?>