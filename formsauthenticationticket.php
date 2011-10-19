<?php
namespace Web\Security;

class FormsAuthenticationTicket
{	
	private $version, $name, $expiration, $issueDate, $isPersistent, $userData, $cookiePath;

	public function __contruct($version, $name, $expiration, $issueDate, $isPersistent, $userData, $cookiePath)
	{
		$this->version= $version;
		$this->name= $name;
		$this->expiration= $expiration;
		$this->issueDate= $issueDate;
		$this->isPersistent= $isPersistent;
		$this->userData= $userData;
		$this->cookiePath= $cookiePath === null ? FormsAuthentication::FORMS_COOKIE_PATH : $cookiePath;
	}
}

?>