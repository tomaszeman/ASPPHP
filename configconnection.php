<?php

class ConfigConnection
{
	public $providerName;
	public $user;
	public $password;
	public $host;
	public $catalog;
	public $port;
	
	public function __construct($providerName= null, $host= null, $port= null, $user= null, $password= null, $catalog= null)
	{
		$this->providerName= $providerName;
		$this->user= $user;
		$this->password= $password;
		$this->host= $host;
		$this->catalog= $catalog;
		$this->port= $port;
	}
}
?>