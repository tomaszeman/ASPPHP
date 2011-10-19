<?php

class FormsIdentity implements IIdentity
{
	private $login, $name, $id, $isAuthenticated;
	
	private static $anonymous;
	
	public static function getAnonymous()
	{
		$anonymous= &self::$anonymous;
		if($anonymous === null)
			$anonymous= new FormsIdentity("Anonymous", false);
		return $anonymous;
	}
	
	public function getLogin() { return $this->login; }
	public function getName() { return $this->name; }
	public function getId() { return $this->id; }
	public function getIdentifier() { return $this->isAuthenticated; }
	
	public function isAuthenticated() { return $this->isAuthenticated; } 
	
	public function __construct($login, $isAuthenticated= true, $name= null, $id= null)
	{
		$this->login= $login;
		$this->isAuthenticated= $isAuthenticated;
		$this->name= $name === null ? $login : $name;
		$this->id= $id === null ? $login : $id;
	}
	
} 

?>