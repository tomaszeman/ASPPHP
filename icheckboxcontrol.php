<?php

interface ICheckBoxControl
{
	function isChecked();
	function setChecked($checked= null);
	
	function getCheckedChangedEvent();
	
	function setControlStateTrackingChanges($track= null);
	function isControlStateTrackingChanges();
}
?>