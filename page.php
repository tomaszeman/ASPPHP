<?php

class Page extends Control implements INamingContainer
{	
	const STATE_FIELD= "__state";
	const PAGE_ID= "__page";
	
	const POST_EVENT_ARGUMENT_ID= "__EVENTARGUMENT";
   	const POST_EVENT_SOURCE_ID= "__EVENTTARGET";

	private $postBackDataHandlers;
	private $controlsRequiringControlState;
	private $controlsRequiringValidation;
	private $controlsRequiringPostBack;
	private $styleSheets;
	
	private $encType;
	public function getEncType(){ return $this->encType; }
	public function setEncType($value) 
	{
		if($this->encType !== null && $this->encType != $value)
				throw new Exception("There are multiple different requirements for encType"); 
		$this->encType= $value;
	}
	
	private $scriptManager;
	public function getScriptManager() { return $this->scriptManager; }
	
	private $targetUrl;
	protected function setTargetUrl($url) { $this->targetUrl= $this->resolveUrl($url); }
	public function getTargetUrl() { return $this->targetUrl; }
	
	public function &getControlsRequiringValidation() { return $this->controlsRequiringValidation; }
	
	private $docType;
	public function getDocType() { return $this->docType; }
	public function setDocType($docType) { $this->docType= $docType; }
	
	private $title;
	public function getTitle() { return $this->title; }
	public function setTitle($title) { $this->title= $title; }

	public function getPage() { return $this; }
	public function getNamingContainer() { return $this; }
	public function getUniqueId() { return self::PAGE_ID; }
	public function getId() { return self::PAGE_ID; }
	public function isPostBack() { return !empty($_POST); }
	
	private $keywords, $description;
	public function getKeywords() { return $this->keywords; }
	public function getDescription() { return $this->description; }
	public function setKeywords($value) { $this->keywords= $value; }
	public function setDescription($value) { $this->description= $value; }
	
	public function registerRequiresControlState(Control $control)
	{
		$controls= &$this->controlsRequiringControlState;
		if($controls == null)
			$controls= array();
	
		if(!array_search($control, $controls, true))
			$controls[]= $control;
	}
	
	public function registerRequiresPostBack(IPostBackEventHandler $control)
	{
		$controls= &$this->controlsRequiringPostBack;
		if($controls == null)
			$controls= array();
	
		if(!array_search($control, $controls, true))
			$controls[$control->getClientId()]= $control;
	}
	
	public function registerRequiresValidation(IValidableControl $control)
	{
		$controls= &$this->controlsRequiringValidation;
		if($controls == null)
			$controls= array();
	
		if(!array_search($control, $controls, true))
			$controls[]= $control;	
	}
	
	public function registerStyleSheet($key, $link)
	{
		$styleSheets= &$this->styleSheets;
		if($styleSheets === null)
			$styleSheets= array();
			
		$styleSheets[$key]= $link;
	}
	
	protected function renderStyleSheets()
	{
		$styleSheets= &$this->styleSheets;
		if($styleSheets === null)
			return;
		foreach($styleSheets as $link)
		{
			$link= $this->resolveClientUrl($link);
			echo "<link rel=\"StyleSheet\" href=\"$link\" type=\"text/css\"></link>";
		}
	}
	
	public function __construct($docType= null) 
	{	
		parent::__construct(); 
		$this->docType= $docType == null ? DocType::XHTML_TRANSITIONAL : $docType;
			
		$this->targetUrl= basename($_SERVER['PHP_SELF']);
		$queryString= $_SERVER['QUERY_STRING'];
		if($queryString !== null && trim($queryString) !== "")
			$this->targetUrl.= "?$queryString";
			
		$this->scriptManager= new ClientScriptManager($this);
		$this->scriptManager->registerInclude("__phpasp", "~/js/phpasp.js");
	}
	
	public function render()
	{
		$scriptManager= $this->scriptManager;
		
		echo '<?xml version="1.0" encoding="utf-8"?>';
		if($this->docType !== null)
			echo $this->docType;
		echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head>';
		
		if($this->title != null)
			echo "<title>{$this->title}</title>";
		
		$this->renderMetaTag("content-type", "application/xhtml+xml; charset=UTF-8");
		
		if($this->keywords !== null)
			$this->renderMetaTag("keywords", $this->keywords);
		
		if($this->description !== null)
			$this->renderMetaTag("description", $this->description);
			
		$this->renderStyleSheets();
		$scriptManager->renderIncludes();
		$scriptManager->renderScriptBlocks();
		
		$this->renderHeader();
		
		echo "</head><body";
		
		if($scriptManager->hasStartUpScripts())
			echo " onload=\"javascript: onLoad();\"";
		
		echo "><form";
		
		if($this->encType !== null)
			echo " enctype=\"$this->encType\"";
		
		if($scriptManager->hasSubmitScripts())
			echo " onsubmit=\"javascript:return onSubmit();\"";
		
		echo " id=\"phpaspform\" action=\"{$this->targetUrl}\" method=\"post\">";
		
		$anyPostBackControls= !empty($this->controlsRequiringPostBack);
		$state = $this->saveAllState();
		
		//if(($renderSystemHiddenFields= $anyPostBackControls || $state !== null))
		//	echo "<fieldset>";
		if(!empty($this->controlsRequiringPostBack))
		{
			echo "<input type=\"hidden\" name=\"". self::POST_EVENT_SOURCE_ID ."\" id=\"". self::POST_EVENT_SOURCE_ID ."\" value=\"\" />";
			echo "<input type=\"hidden\" name=\"". self::POST_EVENT_ARGUMENT_ID ."\" id=\"". self::POST_EVENT_ARGUMENT_ID ."\" value=\"\" />";
		}
	
		if($state !== null)
			echo '<input type="hidden" name="'. self::STATE_FIELD ."\" value=\"$state\"/>";
		
		//if($renderSystemHiddenFields)
		//	echo "</fieldset>";	
			
		$this->renderContents();
		
		echo '</form></body></html>';	
	}
	
	protected function renderMetaTag($name, $value)
	{
		echo "<meta http-equiv=\"$name\" content=\"$value\" />";
	}
	
	protected function renderHeader() {}
	
	protected function renderContents() { $this->renderChildren(); }
	
	public function process()
	{
		$this->onPreInit();
		
		$this->initRecursive($this, $this);
		
		$changedControls= null;
		if($this->isPostBack())
		{
			$this->loadAllState();
			$changedControls= &$this->processPostData();
		}
		
		$this->loadRecursive($this);
		
		if($this->isPostBack())
		{
			if(!empty($changedControls))
				$this->raiseChangedEvents($changedControls);
				
			$this->raisePostBackEvent();
		}
		$this->render();
	}
	
	protected function raisePostBackEvent()
	{
		if(key_exists(self::POST_EVENT_SOURCE_ID, $_POST))
		{
			$target= $_POST[self::POST_EVENT_SOURCE_ID];
			$controls= &$this->controlsRequiringPostBack;
			if(key_exists($target, $controls))
				$controls[$target]->raisePostBackEvent();
		}
	}
	
	protected function onPreInit()
	{
		$this->authenticate();
		$this->authorize();
		$this->initializeCulture();
	}
	
	private $user;
	public function getUser() { return $this->user; }
	
	protected function authenticate()
	{
		$config= WebConfig::getInstance();
		$mode= strtolower($config->authentication->mode);
		if($mode == "none")
			return;
		if($mode == "forms")
			$this->user= FormsAuthentication::impersonate();
	}
	
	protected function authorize()
	{
		$config= WebConfig::getInstance();
		$mode= strtolower($config->authentication->mode);
		if($mode == "none")
			return;
		if(!$this->isUserAuthorized($this->user))
			FormsAuthentication::redirectToLoginPage($this);
	}
	
	protected function isUserAuthorized(IIdentity $user) { return true; }
	
	protected function initializeCulture() {}
		
	protected function raiseChangedEvents(&$changedControls)
	{
		foreach($changedControls as $control)
			$control->raisePostDataChangedEvent();
	}
	
	protected function loadAllState()
	{
		$controls= &$this->controlsRequiringControlState;
		if($controls == null)
			return;
		
		$state= $_POST[self::STATE_FIELD];
		if($state == null)
			return;	 
				
		$state= base64_decode($state);
		$state= unserialize($state);
		
		$all= array();	
		foreach($controls as $control)
			$all[$control->getUniqueId()]= $control;
			
		foreach($state as $id => $value)
		{
			$control= $all[$id];
			$control->loadControlState($value);
		}	
	}
	
	protected function saveAllState()
	{
		$controls= &$this->controlsRequiringControlState;
		if($controls == null)
			return null;
			
		$state= array();
		foreach($controls as $control)
			$state[$control->getUniqueId()]= $control->saveControlState();
			
		$state= serialize($state);
		$state= base64_encode($state);
		return $state;	
	}
	//cross reference :-(
	public function addedControlToPage(Control &$control)
	{
		$control->setPage($this);
		if($this->isPostBack())
		{
			if($control instanceof IPostBackDataHandler)
			{
				$postBackControls= &$this->postBackDataHandlers;
				if($postBackControls == null)
					$postBackControls= array();
				
				$postBackControls[]= $control; 
			}
		}
	}
	
	protected function &processPostData()
	{
		$changedControls= array();
		$controls= &$this->postBackDataHandlers;
		
		if(empty($controls))
			return $changedControls;
			
		foreach($this->postBackDataHandlers as $control)
		{
			$value= array_key_exists($control->getUniqueId(), $_POST) ?
				$_POST[$control->getUniqueId()] : null;
			
			$raise= $control->loadPostData($value);
			if($raise)
				$changedControls[]= $control;
		}
		return $changedControls;
	}
	
	//gets the state of last validation. Implicit validation is triggered by button control for specified validation group, or
	//for all IValidable controls, when group is not specified
	private $isValid;
	public function isValid() 
	{ 
		$isValid= $this->isValid;
		
		if($isValid === null)
			throw new Exception("isValid cannot be called. Page needs to be validated first.");
		return $isValid;
	}
	
	public function validate($group= null)
	{	
		$isValid= &$this->isValid;
		$isValid= true;
		$controls= &$this->controlsRequiringValidation;
		if(empty($controls))
			return true;
		
		if($group != null)
		{
			/*PHP 5.3. 
			$controls= array_filter(
				$controls, 
				function(Control $c) use($group) { return $c->getValidationGroup() == $group; });
			*/
			$groupValidators= array();
			foreach($controls as $control)
				if($control->getValidationGroup() == $group)
					$groupValidators[]= $control;
			$controls= $groupValidators;
			
			if(empty($groupValidators))
				return true;
		}
	
		foreach($controls as $control)
			$isValid &= $control->validate();
		
		return $isValid;
	}
	
	public function redirect($url)
	{
		//header("HTTP/1.0 302 Redirect");
		header("Location: ". $this->resolveClientUrl($url));
		exit;
	}
	
	public function resolveClientUrl($url)
	{
		$segsUrl= explode("/", $url);
	
		if(count($segsUrl) == 1 || !(array_shift($segsUrl) == "~"))
			return $url;
			
		$file= array_pop($segsUrl);
		$dir= dirname(substr($_SERVER['PHP_SELF'], strlen(DOC_ROOT) + 1));

		if($dir == ".")
			return count($segsUrl) == 0 ? $file : substr($url, 2);
			
		$segsCur= explode("/", $dir);
		$diff= array_diff_assoc($segsUrl, $segsCur);
		
		$downCnt= count($diff);
		$upCnt= count($segsCur) - count($segsUrl) + $downCnt;
		
		if($downCnt == 0)
			return str_repeat("../", $upCnt) . $file;
		
		return str_repeat("../", $upCnt) . implode($diff, "/") . "/$file";
	}
	
	public function resolveUrl($url) { return $this->resolveClientUrl($url); }
	
	public function &loadResources($file, CultureInfo $culture= null)
	{	
		if($culture === null)
			$culture= CultureInfo::getCurrentUICulture();
			
		$toLoad= null;
		if($culture !== null)
		{
			$toLoad= $this->resolveClientUrl("$file.{$culture->getName()}.resx");
			if(!file_exists($toLoad))
				$toLoad= null;
		}
			
		if($toLoad === null)
		{
			$toLoad= $this->resolveClientUrl("$file.resx");
				if(!file_exists($toLoad))
					throw new Exception("Resource file '$file' not found.");
		}
		
		$result= array();
		/*$reader= new \XmlReader();
		$reader->open($toLoad, "utf-8");
		
		while($reader->read())
			if($reader->nodeType == \XmlReader::ELEMENT && $reader->localName == "data")
				$result[$reader->getAttribute("key")]= $reader->readString();
		$reader->close();*/
		
		$data = implode("", file($toLoad)); 
		$values= null;
		$tags= null;
		$parser = xml_parser_create();
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	    xml_parse_into_struct($parser, $data, $values, $tags);
	    xml_parser_free($parser);

		foreach($values as $value)
			if($value["tag"] == "data")
				$result[$value["attributes"]["key"]]= $value["value"];

		return $result;
	}
	
	
	
}

?>