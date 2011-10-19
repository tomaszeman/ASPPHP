<?php

class GenericPrincipal
{
	private $identity, $roles;
	
	public function __construct($identity, $roles)
	{
		$this->identity= $identity;
		$this->roles= $roles;
	}
	
	function getRoles() { return $this->roles; }
	function getIdentity() { return $this->identity; }
}
?>