<?php

class Pager
{	
	var $m_curPage, $pages, $pageSize, $maxPages= 11;
	var $cssClass= "pager";
	var $urlAdd;
	var $txtBtnFr, $txtBtnNe, $txtBtnLa, $txtBtnPr;
	var $textMode= false;
	
	function Pager()
	{
		$pg= isset($_GET["pg"])? intval($_GET["pg"]): 1;
		if(empty($pg))
			$pg= 1;
		$this->m_curPage= $pg;
		$this->pages= 1;
		$this->pageSize= 10;
		$this->urlAdd= "";
		$this->txtBtnFr= "Prvá stránka";
		$this->txtBtnLa= "Posledná stránka";
		$this->txtBtnPr= "Predchádzajúca stránka";
		$this->txtBtnNe= "Posledná stránka";
	}
	
	function setPages($cnt)
	{
		$this->pages= ceil($cnt / $this->pageSize);		
	}	
	function getOfs()
	{		
		return ($this->m_curPage - 1) * $this->pageSize;
	}
	
	function rndrAddBtns() {}

	function rndr()
	{
		if($this->pages < 2) return;
		$url= $_SERVER['PHP_SELF'];
		$pg= $this->m_curPage;
		$firstText="";
		$previousText= "";
		$nextText= "";
		$lastText= "";
		if($this->textMode)
		{
			$firstText= " &lt;&lt; ";
			$previousText= " &lt; ";
			$nextText= " &gt; ";
			$lastText= " &gt;&gt; ";
		}
		echo "<div". $this->m_getAttribs($this->cssClass) .">";	
		if($pg > 1)	
		{
			echo "<a title=\"$this->txtBtnFr\" class=\"ico_first_page\" href=\"$url?pg=1$this->urlAdd\">$firstText</a>";
			echo "<a title=\"$this->txtBtnPr\" class=\"ico_previous_page\" href=\"$url?pg=". ($pg - 1) ."$this->urlAdd\">$previousText</a>";
		}
		else
		{
			echo "<span class=\"ico_first_page_dis\"></span>";		
			echo "<span class=\"ico_previous_page_dis\"></span>";
		}
		$this->rndrAddBtns();
		if($pg < $this->pages)
		{	
			echo "<a title=\"$this->txtBtnNe\" class=\"ico_next_page\" href=\"$url?pg=". ($pg + 1) ."$this->urlAdd\">$nextText</a>";
			echo "<a title=\"$this->txtBtnLa\" class=\"ico_last_page\" href=\"$url?pg=$this->pages$this->urlAdd\">$lastText</a>";
		}
		else
		{
			echo "<span class=\"ico_next_page_dis\"></span>";
			echo "<span class=\"ico_last_page_dis\"></span>";
		}
		echo "<span class=\"cl\"></span></div>";
		
		$max= $this->maxPages;
		if($max % 2 == 0) $max++;
		$hlf= ($max - 1) / 2;		
		if($max > $this->pages)
		{
			$from= 1; $to= $this->pages;
		}
		else if($pg - $hlf < 1)
		{
			$from= 1; $to= $max;
		}
		else if($pg + $hlf > $this->pages)
		{
			$to= $this->pages; $from= $to - $max + 1;
		}
		else
		{
			$from= $pg - $hlf; $to= $pg + $hlf;
		}
		echo "<div class=\"pager_pages\">";
		for($i= $from; $i <= $to; $i++)			
			if($i != $pg)
				echo "<a class=\"goto_page\" href=\"$url?pg=$i$this->urlAdd\">$i</a>";
			else
				echo "<span class=\"goto_page_dis\">$i</span>";
		echo "</div>";
	}
	
	function m_getAttribs($cls)
	{		
		return ((isset($cls) && !empty($cls))? " class=\"". $cls . "\"" : "");
	}
}
?>