<?php

abstract class Controls_ClientMasterPage extends Page
{
	private $languages;
	private $idLanguage;
	private $url;
	private $siteTree;
	private $siteNode;
	private $resources;
	private $pageMode;
	private $tbxEmail;
	private $btnSubscribe;
	
	public function getSiteNode() { return $this->siteNode; }
	public function getPageMode() { return $this->pageMode; }
	public function getSiteTree() { return $this->siteTree; }
	
	protected function getIdLanguage()
	{
		return $this->idLanguage;
	}
	
	protected function onInit()
	{
		session_start();
		parent::onInit();
		
		/*$tbxEmail= &$this->tbxEmail;
		$btnSubscribe= &$this->btnSubscribe;
		
		$tbxEmail= new TextBox();
		$tbxEmail->setValidate(validateEmail);
		$tbxEmail->setMaxLength(255);
		$tbxEmail->setId("tbxEmail");
		$tbxEmail->setCssClass("tbx");
		
		$btnSubscribe= new Button();
		$btnSubscribe->setCssClass("btn");
		$btnSubscribe->setText("Potvrdiť");
		$btnSubscribe->getClickEvent()->addHandler(array($this, 'subscribe'));
		
		$this->getControls()->addRange($tbxEmail, $btnSubscribe);*/
		
		$resources= &$this->resources;
		$resources= $this->loadResources("~/Controls/clientmasterpage", null);
		$this->setTitle($resources["page.title"]);
		
		$this->siteTree= Data_SiteNodeService::getTree($this->idLanguage, true);
		
		if(isset($_GET["i"]))
			$id= intval($_GET["i"]);
			
		if(isset($id))
			$this->siteNode= $this->findNodeRecursive($this->siteTree, $id);
		else
		{
			$idDefaultPage= Data_SettingsService::get()->idDefaultPage;
			if($idDefaultPage != null)
				$this->siteNode= $this->findNodeRecursive($this->siteTree, $idDefaultPage);
			//$this->siteNode= $this->findNodeRecursive($this->siteTree, 129);
		}
		if(isset($_GET["m"]))
		{
			$this->pageMode= $_GET["m"] == "t" ? 1 : 0;
			$_SESSION["mode"]= $this->pageMode;
		}
		else
			$this->pageMode= isset($_SESSION["mode"]) ? $_SESSION["mode"] : 0;
		
		if($this->pageMode == 0)	
			$this->registerStyleSheet("main", "~/css/main.css");
	}
	
	//public function validateEmail($text) { return filter_var($text, FILTER_VALIDATE_EMAIL); }
	
	/*public function subscribe()
	{
		$resources= &$this->resources;
		$message= $resources["subscribe.msg.invalidEmail"];
		if($this->isValid())
		{
			$contact= new Data_Contact();
			$contact->email= $this->tbxEmail->getValue();
			$contact->lastName= $contact->email; 
			
			if(!Data_ContactService::emailExists($contact->email))
			{
				$id= Data_ContactService::create($contact);
				$this->tbxEmail->setValue(null);
				$message= $resources["subscribe.msg.success"];
			}
			else
				$message= $resources["subscribe.msg.emailAlreadyExists"];
		}
		
		$this->getScriptManager()->registerStartUpScript(
			"subscribeMessage", 
			"alert('$message');");
	}*/
	
	protected function onLoad()
	{
		parent::onLoad();
		$this->url= $this->resolveClientUrl("~/index.php");
	}
	
	protected function findNodeRecursive($nodes, $id)
	{
		foreach($nodes as $node)
		{
			if($node->id === $id)
				return $node;
			if(count($node->children) > 0)
			{
				$return= $this->findNodeRecursive($node->children, $id);
				if ($return !== null)
					return $return;
			}
		}
		return null;
	}
	
	private function checkLanguage($idLang= null)
	{
		return $idLang= isset($idLang) && $idLang > 0 && $idLang < 4 ? 
				$idLang 
				: Data_LanguageService::getDefault()->id;
	}
	
	protected function initializeCulture()
	{
		$this->languages= Data_LanguageService::getAll();
		
		$idLang= null;
		if(isset($_GET["language"]))
		{
			$idLang= $this->checkLanguage(intval($_GET["language"]));
			$_SESSION["lang"]= $idLang;
		}
		else
			$idLang= isset($_SESSION["lang"]) ? $_SESSION["lang"] : 1;
		
		$this->idLanguage= $idLang;
		
		$culture= array(1 => "sk", 3 => "en");
		CultureInfo::setCurrentUICulture(new CultureInfo($culture[$idLang]));
	}
	
	protected function renderHeader()
	{
		parent::renderHeader();
		echo '<link rel="shortcut icon" href="favicon.ico"/>';
		echo '<link rel="icon" type="image/gif" href="anim_favicon.gif"/>';
		//echo "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"rss_vystupenia.php\" title=\"RSS Najbližšie vystúpenia\" />";
	}
	

	protected function renderContents()
	{
		echo '<div class="root">';
			echo '<a name="top_bookmark"></a>';
			if($this->pageMode === 1)
				echo '<a href="#content_bookmark">Preskočiť menu</a>';

			echo '<div class="top_menu_bar">';
			$this->renderTopMenu();
			$this->renderLanguages();
			echo '<div class="cl"></div></div>';
			
			if($this->getPageMode() === 0)
			{
				//$this->renderBreadCrumb(); 
			}
			
			echo '<div class="content_main">';
				$this->renderPageContent();
			echo '</div>';
							
		echo '</div>';
	}
	
	protected function renderMode()
	{
		$title= $this->resources[$this->pageMode == 1 ? "page.graphmode" : "page.textmode" ];
		$url= $this->resolveClientUrl("~/index.php?m=") . ($this->pageMode == 1 ? "g" : "t");
		echo "<a href=\"$url\">$title</a>";	
	}
	
	protected function renderLanguages()
	{
		echo '<ul class="top_lang">';
		for($i= 0, $len= count($this->languages); $i < $len; $i++)
		{
			$lang= $this->languages[$i];
			$liCssClass= $i + 1 == $len ? " class=\"last\"" : "";
			$cssClass= $lang->id == $this->idLanguage ? " class=\"sel\"" : "";
			echo "<li$liCssClass><a href=\"{$this->url}?language={$lang->id}\"$cssClass>{$lang->name}</a></li>";
		}
		echo '</ul>';
	}
	
	protected function renderTopMenu()
	{
		echo '<ol class="top_menu">';
		$nodes= $this->siteTree[0]->children;
		for($i= 0, $len= count($nodes); $i < $len; $i++)
		{
			$node= $nodes[$i];
			$liCssClass= $i == 0 ? " class=\"first\"" : "";
			$url= $this->normalizeNavigationUrl($node);
			echo "<li$liCssClass><a href=\"$url\">{$node->title}</a></li>";
		}
		echo "</ol>";
	}
	
	protected function renderBreadCrumb()
	{
		$node= $this->siteNode;
		if($node !== null)
		{
			$path= array();
			$node= $this->siteNode->parentNode;
			while($node->parentNode->parentNode != null)
			{
				$type= $node->siteNodeType->id;
				$url= $this->normalizeNavigationUrl($node);
				$path[]= array($node->title, $url);
				$node= $node->parentNode;
			}
		}
		$path[]= array($this->resources["page.home"], $this->url);
			
		for($i= count($path) -1; $i >= 0; $i--)
		{
			$url= $path[$i][1];
			$name= $path[$i][0];
			if($url === null)
				echo "<span>$name<span>";
			else
				echo "<a href=\"$url\">$name</a>";	
			if($i > 0)
				echo " / ";
		}
	}
	
	protected function normalizeNavigationUrl($node)
	{
		$type= $node->siteNodeType->id;
			
		$url= null;
		if($type == Data_SiteNodeType::PAGE)
			$url= $this->resolveClientUrl("~/article.php"). "?i={$node->id}";
		if($type == Data_SiteNodeType::FOLDER)
			$url= $this->resolveClientUrl("~/list.php"). "?i={$node->id}";
		elseif($type == Data_SiteNodeType::URL)
		{
			$url= $node->url;
			if(substr($url, 0, 2) == "~/")
			{
				$url= $this->resolveUrl($url);
				if(stripos($url, "i=") === false)
					$url.= (strpos($url, "?") ? "&" : "?") ."i=$node->id";
			}	
		}
		return $url;
	}
	
	public function render()
	{
		$this->setTargetUrl(str_replace("&", "&amp;", $this->getTargetUrl()));
		
		parent::render();
	}
	
	protected function renderContentActions() {}
	
	protected function renderPageContent() {}
} 
?>