<?php

class ClientScriptManager
{
	const SCRIPT_BLOCK= "<script type=\"text/javascript\">\r\n//<![CDATA[\r\n%s\r\n//]]>\r\n</script>";
	const CLIENT_SCRIPT_START_LEGACY= "<script type=\"text/javascript\">\r\n<!--\r\n";
    const CLIENT_SCRIPT_END_LEGACY= "// -->\r\n</script>\r\n";
    const INCLUDE_SCRIPT= "<script src=\"%s\" type=\"text/javascript\"></script>";
    const POST_BACK_EVENT_REF= "setTimeout('__doPostBack(\'%s\',\'%s\')',0);";
    const POST_BACK_CLIENT_HYPERLINK= "javascript:__doPostBack('%s','%s');";
    
	private $includes= array();
	private $scriptBlocks= array();
	private $startUpScripts= array();
	private $submitScripts= array();
	private $page;
	
	public function __construct(Page $page)
	{
		$this->page= $page;
	}
	
	public function registerInclude($key, $link)
	{
		$all= &$this->includes;
		if(!key_exists($key, $all))
			$all[$key]= $link; 
	}
	
	public function renderIncludes()
	{
		$page= $this->page;
		foreach($this->includes as $link)
			printf(self::INCLUDE_SCRIPT, $page->resolveClientUrl($link));
	}
	
	public function registerScriptBlock($key, $code)
	{
		$all= &$this->scriptBlocks;
		if(!key_exists($key, $all))
		{
			$code= trim($code);
			if(substr($code, -1) !== ";")
				$code.= ";";
			$all[$key]= $code;
		}
	}
	
	public function renderScriptBlocks()
	{
		$blocks= &$this->scriptBlocks;
		$startUps= &$this->startUpScripts;
		$submits= &$this->submitScripts;
		
		if(!empty($startUps))
			$blocks[]= "function onLoad() {". implode("", $startUps). "}";
		
		if(!empty($submits))
			$blocks[]= "function onSubmit() {". implode("", $submits). "}";
	
		if(empty($blocks))
			return;
		$code= implode("", $blocks);
		printf(self::SCRIPT_BLOCK, $code);
	}
	
	public function hasSubmitScripts() { return !empty($this->submitScripts); }
	
	public function registerSubmitScript($key, $code)
	{
		$all= &$this->submitScripts;
		if(!key_exists($key, $all))
		{
			$code= trim($code);
			if(substr($code, -1) !== ";")
				$code.= ";";
			$all[$key]= $code;
		}
	}
	
	public function hasStartUpScripts() { return !empty($this->startUpScripts); }
	
	public function registerStartUpScript($key, $code)
	{
		$all= &$this->startUpScripts;
		if(!key_exists($key, $all))
		{
			$code= trim($code);
			if(substr($code, -1) !== ";")
				$code.= ";";
			$all[$key]= $code;
		}
	}
	
	public function getPostBackEventReference($control, $arguments= null)
	{
		if($arguments === null)
			$arguments= "";
		return sprintf(self::POST_BACK_EVENT_REF, $control->getClientId(), $arguments);
	}
	
	public function getPostBackClientHyperlink($control, $arguments= null)
	{
		if($arguments === null)
			$arguments= "";
			
		return sprintf(self::POST_BACK_CLIENT_HYPERLINK, $control->getClientId(), $arguments);
	}
}
?>