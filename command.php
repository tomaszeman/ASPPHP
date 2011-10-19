<?php

class Command
{
	public $stmt;
	
	public function __construct(mysqli_stmt $stmt) { $this->stmt= $stmt; }
	public function execute() { return $this->stmt->execute(); }
	public function fetch() { return $this->stmt->fetch(); }
	
	public function bind_param($types)
	{
		$args= func_get_args();
		call_user_func_array(array($this->stmt, 'bind_param'), $args);
	}
	
	public function bind_result() { throw new Exception("Not supported"); }
	
	function __get($name)
	{
		if($name == "insert_id")
			return $this->stmt->insert_id;
		else
			throw new Exception("Invalid property '$name'");
	}
	
	public function result_metadata() { return $this->stmt->result_metadata(); }
	public function free_result() { return $this->stmt->free_result(); }
	
	public function executeObject($type= null)
	{
		if($type == null)
			$type= new stdClass();
			
		$stmt= $this->stmt;
		$stmt->execute();
		$stmt->store_result();
		
		$result= $stmt->result_metadata();
		$fields= $result->fetch_fields();
		
		$object= new $type();
		
		$argsArray= array();
		foreach($fields as $field)
			$argsArray[]= "\$object->{$field->name}";
		
		$args= implode(",", $argsArray);
		$args= str_replace("_", "->", $args);
		
		eval("\$stmt->bind_result($args);");
		
		if(!$stmt->fetch())
			$object= null;
		
		$stmt->free_result();
		return $object;
	}
	
	public function &executeObjectArray($type)
	{
		$toRet= array();
		
		$stmt= $this->stmt;
		$stmt->execute();
		$stmt->store_result();
		$result= $stmt->result_metadata();
		$fields= $result->fetch_fields();
		
		$argsArray= array();
		foreach($fields as $field)
		{
			$name= $field->name;
			
			//do skip?
			if(strlen($name) > 2 && substr($name, 0, 2) == "__")
				$argsArray[]= "\$foo";
			else
				$argsArray[]= "\$object->$name";
		}
		
		$args= implode(",", $argsArray);
		$args= str_replace("_", "->", $args);
		$bind= create_function("\$stmt,\$object", "\$foo= null;\$stmt->bind_result($args);");
		
		while(1)
		{
			$object= new $type;
			$bind($stmt,$object);
			
			if(!$stmt->fetch())
				break;
			$toRet[]=$object;
		}
		$stmt->free_result();
		
		return $toRet;
	}
	
	public function executeScalar()
	{
		$stmt= $this->stmt;
		$stmt->execute();
		$toRet= null;
		$stmt->bind_result($toRet);
		$stmt->fetch();
		$stmt->free_result();
		return $toRet;
	}
	
	public function getRowCount() { return $this->stmt->affected_rows; }
}
?>