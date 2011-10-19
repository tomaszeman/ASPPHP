<?php

interface IButtonControl
{
	function getText();
	function setText($text);
	function setCausesValidation($value);
	function isCausingValidation();
	function getValidationGroup();
	function setValidationGroup($value);
}

?>