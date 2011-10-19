<?php

class DropDownList extends ListControl
{
	public function __construct() {	parent::__construct("select"); }
	
	protected function buildAttributes()
	{
		$attributes= &$this->getAttributes();
		$attributes["name"]= $this->getUniqueId();
		if($this->isAutoPostBack())
			$attributes["onchange"]= $this->getPage()->getScriptManager()->getPostBackEventReference($this);
		return parent::buildAttributes();
	}
	
	
	
	public function render()
	{
		if(!$this->isVisible())
			return;
		
		if(!$this->isEnabled())
			echo "<input type=\"hidden\" name=\"{$this->getClientId()}\" value=\"{$this->getText()}\"/>";	
			
		parent::render();
	}
	
	protected function renderChildren()
	{
		foreach($this->getItems() as $item)
		{
			echo "<option value=\"{$item->getValue()}\"";
			if($item->isSelected())
				echo " selected=\"selected\"";
			echo ">". htmlspecialchars($item->getText()) ."</option>";
		}
	}
	
	protected function isSelfClosedTag() { return false; }
	
}

?>