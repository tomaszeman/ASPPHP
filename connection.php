<?php

class Connection extends mysqli
{	
	public static function get()
	{	
		$config= WebConfig::getInstance();
		$settings= $config->connections["crm"];
		$conn= new Connection(
			$settings->host, 
			$settings->user, 
			$settings->password,
			$settings->catalog,
			$settings->port);	
		//$conn->set_charset("utf8");
		return $conn;
	}
	
	public function __construct($host, $user, $password, $catalog, $port)
	{
		parent::__construct($host, $user, $password, $catalog, $port);
	}
	
	public function prepare($query)
	{
		$stmt= parent::prepare($query);
		if($stmt)
			return new Command($stmt);
		else
			throw new Exception($this->error);
	}
}
?>