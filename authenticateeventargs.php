<?php

class AuthenticateEventArgs extends EventArgs
{	
	private $isAuthenticated= false;	
	public function isAuthenticated() { return $this->isAuthenticated; }
	public function setAuthenticated($value= null) { $this->isAuthenticated= $value === null || $value; }
	
	private $login, $password, $persistant, $message;
	
	public function getLogin() { return $this->login; }
	public function getPassword() { return $this->password; }
	public function isPersistant() { return $this->persistant; }
	
	public function getMessage() { return $this->message; }
	public function setMessage($message) { $this->message= $message; }
	
	public function __construct($login, $password, $persistant)
	{
		$this->login= $login;
		$this->password= $password;
		$this->persistant= $persistant;
	}
}
?>