<?php

interface IPostBackDataHandler
{
	function loadPostData($postDataKey);
	function raisePostDataChangedEvent(); 
}
?>