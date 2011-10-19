<?php

//deprecated
class DataGrid
{
	var $tableClass, $rowClass, $alternRowClass, $cellClass, $hdrCellClass, $hdrRowClass, $arrowClass;
	var $tableStyle, $rowStyle, $alternRowStyle, $cellStyle, $hdrCellStyle, $hdrRowStyle, $arrowStyle; 
	var $columns, $dataColumns, $orderAcs, $allowSort, $orderBy= 0, $emptyMsg, $urlAdd;
	
	public function __construct()
	{
		$this->allowSort= false;
		if(isset($_REQUEST["o"]))	
			$this->orderAcs= !(isset($_REQUEST["o"]) && $_REQUEST["o"] == "0");		
		$this->emptyMsg= "&nbsp;";
		if(isset($_REQUEST["i"]) && is_numeric($_REQUEST["i"]))
			$this->orderBy= $_REQUEST["i"];		
	}
	
	function getOrderCol()
	{
		return $this->dataColumns[$this->orderBy];
	}
	function getUrlAdd()
	{
		return "i=$this->orderBy&o=". (($this->orderAcs)? "1": "0") . $this->urlAdd;
	}
	
	function rndrBeginTag()
	{
		echo "<table cellspacing=\"0\" ". $this->m_getTableAttribs() .">";
	}
	function rndrEndTag() { echo "</table>"; }
	
	// Renders header row, used also for rendering col styles	
	function rndrHeader(&$captions)
	{						
		echo "<thead><tr". $this->m_getHdrRowAttribs() .">";		
		for($i= 0; $i < count($captions); $i++)
		{
			$idx= (!empty($this->dataColumns[$i])? $i: null);
			$this->rndrHeaderCell($captions[$i], ($this->allowSort && $i == $this->orderBy), $idx);
		}			
		echo "</tr></thead>";				
	}
		
	function rndrHeaderCell($caption, $bCurrent, $colIdx)
	{			
		if(empty($caption)) $caption= "&nbsp;";
		echo "<th ". $this->m_getHdrCellAttribs() .">";
		if($this->allowSort)
		{
			$arrow= (($this->orderAcs)? "&#9650": "&#9660;");
			$url= $_SERVER['PHP_SELF'] ."?i=$colIdx&o=". (($this->orderAcs && $bCurrent)? "0": "1");	
			echo "<a href=\"". $url . $this->urlAdd ."\">". $caption;
			if($bCurrent) 
				echo "<span ". $this->m_getArrowAttribs() .">$arrow<span>";
			echo "</a>";
		}
		else
			echo $caption;
		echo "</th>";
	}
	
	function rndrBody(&$arr)
	{
		echo "<tbody>";		
		if($arr == null) 
			$this->rndrEmptyResult();
		else
			for($i= 1, $iCnt= count($arr); $i <= $iCnt; $i++)
				$this->rndrRow($arr[$i-1], ($i % 2 == 1), $i, $iCnt);
					
		echo "</tbody>";
	}	
	
	function rndrEmptyResult()
	{		
		echo "<tr ". $this->m_getRowAttribs() ."><td style=\"text-align:center;\" ". $this->m_getCellAttribs() ." colspan=\"". count($this->columns) ."\">$this->emptyMsg</td></tr>";
	}
	
	function rndrRow(&$row, $altern= false, $idx, $cnt= null)
	{		
		echo "<tr". (($altern)? $this->m_getAlternRowAttribs(): $this->m_getRowAttribs()) .">";		
		for($i= 0; $i < count($row); $i++)		
			$this->rndrCell($row[$i]);										
		echo "</tr>";
	}	
	
	function rndrCell()
	{	
		$args= func_get_args();
		$attribs= $this->m_getCellAttribs();
   	  	for ($i= 0; $i < count($args); $i++) 				
			echo "<td". $attribs .">". (($args[$i] == "")?"&nbsp;":$args[$i]) ."</td>";
	}
	
	function m_getAttribs($cls, $stl)
	{		
		return ((isset($stl) && !empty($stl))? " style=\"". $stl . "\"": "")
			. ((isset($cls) && !empty($cls))? " class=\"". $cls ."\"": "");
	}
		
	function m_getCellAttribs() 
	{		
		return $this->m_getAttribs($this->cellClass, $this->cellStyle);	
	}	
	function m_getRowAttribs() 
	{ 
		return $this->m_getAttribs($this->rowClass, $this->rowStyle); 
	}			
	function m_getHdrRowAttribs() 
	{ 
		return $this->m_getAttribs($this->hdrRowClass, $this->hdrRowStyle);	
	}
	function m_getHdrCellAttribs() 
	{ 
		return $this->m_getAttribs($this->hdrCellClass, $this->hdrCellStyle);	 
	}
	function m_getTableAttribs()
	{ 
		return $this->m_getAttribs($this->tableClass, $this->tableStyle);	 
	}
	function m_getArrowAttribs()
	{ 
		return $this->m_getAttribs($this->arrowClass, $this->arrowStyle);	 
	}
	function m_getAlternRowAttribs()
	{
		$toRet= $this->m_getAttribs($this->alternRowClass, $this->alternRowStyle);
		return ($toRet != "")? $toRet: $this->m_getRowAttribs($this->rowClass, $this->rowStyle);
	}
	
	function setStyle($tableClass, $rowClass= null, $alternRowClass= null, $cellClass= null, $hdrRowClass= null, $hdrCellClass= null, $arrowClass= null)
	{
		$this->tableClass= $tableClass;
		if(!is_null($rowClass)) $this->rowClass= $rowClass;
		if(!is_null($alternRowClass)) $this->alternRowClass= $alternRowClass;
		if(!is_null($cellClass)) $this->cellClass= $cellClass;
		if(!is_null($hdrCellClass)) $this->hdrCellClass= $hdrCellClass;
		if(!is_null($hdrRowClass)) $this->hdrRowClass= $hdrRowClass;
		if(!is_null($arrowClass)) $this->arrowClass= $arrowClass;
	}
	
	function rndr($result= null)
	{						
		$this->rndrBeginTag();
		$this->rndrHeader($this->columns);
		$this->rndrBody($result);		
		$this->rndrEndTag();
	}	
}
?>