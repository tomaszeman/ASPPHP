<?php

class FormsAuthentication
{
	const FORMS_COOKIE_NAME="ASPPHPAuth";
	const RETURN_URL="ReturnUrl";
	
	public static function setAuthCookie($userName, $isPersistant= false, $path= null)
	{
		setcookie(
			self::FORMS_COOKIE_NAME,
			$userName,
			$isPersistant ? time() + 2592000 : null,
			$path !== null ? $path : "/");
		//str_replace("www", "", $_SERVER["HTTP_HOST"]);
	}
	
	public static function signOut($path= null)
	{
		setcookie(self::FORMS_COOKIE_NAME, false, 1, $path !== null ? $path : "/");
	}
	
	public static function impersonate()
	{
		if(!array_key_exists(self::FORMS_COOKIE_NAME, $_COOKIE))
			return FormsIdentity::getAnonymous();
		
		return new FormsIdentity($_COOKIE[self::FORMS_COOKIE_NAME]);
	}
	
	//check for cross site scripting attempts
	public static function getReturnUrl() 
	{
		return key_exists(self::RETURN_URL, $_GET) ? $_GET[self::RETURN_URL] : null;
	}
	
	public static function redirectToLoginPage(Page $page)
	{
		//we need a page to resolve and redirect, will fix this issue later
		$config= WebConfig::getInstance();
		
		$url= $config->authentication->loginPage;
		if($url !== null)
			$page->redirect("$url?". self::RETURN_URL ."=". urlencode($_SERVER["PHP_SELF"]));
		else
			throw new Exception("LoginUrl isn't set in webConfig");
	}
}

?>