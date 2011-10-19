<?php

interface IBindableControl
{
	function setValueToText($callback);
	function setValueFromText($callback);
	
	function getValue();
	function setValue($value);
	
	function getExpression();
	function setExpression($expression);
}
?>