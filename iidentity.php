<?php

interface IIdentity
{
	function getLogin();
	function getName();
	function getIdentifier();
	
	function isAuthenticated();
}

?>