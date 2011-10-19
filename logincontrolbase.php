<?php

abstract class LoginControlBase extends WebControl
{
	private $lblMessage, $tbxLogin, $tbxPassword, $btnLogin, $chbxPersistant;
	
	private $authenticateEvent;
	public function getAuthenticateEvent() { return $this->authenticateEvent; }

	protected function getValidationGroup() { return "login"; }
	
	private $redirectUrl;
	public function getRedirectUrl() { return $this->redirectUrl; }
	public function setRedirectUrl($url) { $this->redirectUrl= $url; }
	
	public function getLogin() { return $this->tbxLogin->getValue(); }
	public function setLogin($login) { $this->tbxLogin->setValue($login); }
	
	public function getPassword() { return $this->tbxPassword->getValue(); }
	public function setPassword($password) { $this->tbxPassword->setValue($password); }
	
	public function getFailureMessage() { return $this->lblMessage->getText(); }
	public function setFailureMessage($message) { $this->lblMessage->setText($message); }
	
	protected function createMessageTextControl(){ return new Label(); }
	protected function createLoginEditableTextControl() { return new TextBox();  }
	protected function createPasswordEditableTextControl() { return new TextBox(); }
	protected function createLoginButton() { return new Button(); }
	protected function createPersistentCheckBoxControl() { return new CheckBox(); } 
	
	protected function getMessageTextControl(){ return $this->lblMessage; }
	protected function getLoginEditableTextControl() { return $this->tbxLogin;  }
	protected function getPasswordEditableTextControl() { return $this->tbxPassword; }
	protected function getLoginButton() { return $this->btnLogin; }
	protected function getPersistentCheckBoxControl() { return $this->chbxPersistant; } 

	public function __construct() 
	{	
		parent::__construct("div");
		$this->authenticateEvent= new Event();
		//$this->forgottenPasswordEvent= new \System\Event();
		
		$this->lblMessage= $this->createMessageTextControl();
		$this->tbxLogin= $this->createLoginEditableTextControl();
		$this->tbxPassword= $this->createPasswordEditableTextControl();
		$this->btnLogin= $this->createLoginButton();
		$this->chbxPersistant= $this->createPersistentCheckBoxControl();
	}
	
	protected function onInit() 
	{ 
		$valGroup= $this->getValidationGroup();
	
		$this->tbxLogin->setValidationGroup($valGroup);
	
		$this->tbxPassword->setPasswordMode();
		$this->tbxPassword->setValidationGroup($valGroup);
		
		$this->btnLogin->getClickEvent()->addHandler(array($this, 'btnLogin_click'));
		$this->btnLogin->setValidationGroup($valGroup);
		
		$this->getControls()->addRange($this->tbxLogin, $this->tbxPassword, $this->btnLogin, $this->lblMessage, $this->chbxPersistant);
	}
	
	public function btnLogin_click()
	{
		$this->onAuthenticate(
			new AuthenticateEventArgs(
				$this->tbxLogin->getValue(),
				$this->tbxPassword->getValue(),
				$this->chbxPersistant->isChecked()));
	}
	
	protected function onAuthenticate(AuthenticateEventArgs $args)
	{
		$this->authenticateEvent->invoke($this, $args);
		
		if(!$args->isAuthenticated())
		{
			$message= $args->getMessage();
			if($message !== null)
				$this->lblMessage->setText($message);
			return;
		}
		elseif(($returnUrl= FormsAuthentication::getReturnUrl()) !== null)
			$this->getPage()->redirect($returnUrl);
		elseif($this->redirectUrl !== null)
			$this->getPage()->redirect($this->redirectUrl);
		else
			$this->setVisible(false);
	}
}
?>