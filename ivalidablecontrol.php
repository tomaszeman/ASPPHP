<?php

interface IValidableControl
{
	function getErrorMessage();
	function setErrorMessage($message);
	
	function getValidationGroup();
	function setValidationGroup($value);
	
	function isRequired();
	function setRequired($required= null);
	
	function isValid();
	function validate();
	function wasValidated();
	function performValidation($text= null);
}
?>