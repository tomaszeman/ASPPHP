<?php

class FCKTextBox extends TextControl
{
	private $hiddenMain;
	private $hiddenConfig;
	private $toolbarSet;
	
	private $config= array();
	public function setConfig($key, $value) { $this->config[$key]= $value; }
	
	function setToolbarSet($toolbarSet) { $this->toolbarSet = $toolbarSet; }
	function getToolbarSet() { return $this->toolbarSet; }
	
	protected function onInit()
	{
		$id= $this->getId();
		
		$hiddenMain= &$this->hiddenMain;
		$hiddenConfig= &$this->hiddenConfig;
		$iframe= &$this->iframe;
		
		$hiddenMain= new HiddenTextBox();
		$hiddenMain->setId($id);
		
		$hiddenConfig= new HiddenTextBox();
		$hiddenConfig->setId($id . "___Config");
		
		$controls= $this->getControls();
		$controls->addRange($hiddenMain, $hiddenConfig);
	}
	
	protected function buildAttributes() {}
	
	//oreginal fckeditor php bullshit - recode it
	private function getConfigFieldString()
	{
		$params= array();
		$isFirst= true;
		foreach($this->config as $key => $value)
		{
			$param= $this->encodeConfig($key) .'=';
				
			if ($value === true)
				$param.= 'true';
			else if($value === false)
				$param.= 'false';
			else
				$param.= $this->encodeConfig($value);
			$params[]= $param;
		}
		return implode('&amp;', $params);
	}
	//oreginal fckeditor php bullshit - recode it
	private $chars= array('&' => '%26', '=' => '%3D', '"' => '%22' );
	private function encodeConfig($valueToEncode)
	{
		return strtr($valueToEncode, $this->chars) ;
	}
	
	public function render()
	{	
		$this->hiddenConfig->setText($this->getConfigFieldString());
		$this->hiddenMain->setText(htmlspecialchars($this->getText()));
		parent::render();
		$id= $this->getClientId();
		
		$htmlValue= htmlspecialchars($this->getText());
		
		$file= isset($_GET['fcksource']) && $_GET['fcksource'] == "true" 
			? 'fckeditor.original.html' 
			: 'fckeditor.html';
			
		$link= $this->getPage()->resolveClientUrl("~/fckeditor/editor/{$file}?InstanceName=". urlencode($id));

		$toolbarSet= $this->toolbarSet;
		if($toolbarSet !== null)
			$link.= "&amp;Toolbar=$toolbarSet" ;	
		
		echo "<div><iframe id=\"{$id}___Frame\" src=\"{$link}\"";
		$css= $this->getCssClass();
		if($css !== null)
			echo " class=\"$css\"";
		
		echo " frameborder=\"0\" scrolling=\"no\"></iframe></div>";
	}
}
?>