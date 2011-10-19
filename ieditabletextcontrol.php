<?php

interface IEditableTextControl extends ITextControl
{
	function getTextChangedEvent();
	
	function setControlStateTrackingChanges($track= null);
	function isControlStateTrackingChanges();
}	
?>