<?php

class ImageProcessor
{

	public static function copyImage($srcFile, $destFile, $w, $h, $quality = 75)
	{
	    $tmpSrc= pathinfo(strtolower($srcFile));
	    $tmpDest= pathinfo(strtolower($destFile));
	   
	    list($width, $height, $type, $attr) = getimagesize($srcFile); 
	
	    if ($tmpDest['extension'] == "gif" || $tmpDest['extension'] == "jpg")
	    {
	    	//$destFile= substr_replace($destFile, 'jpg', -3);
	    	//$dest= imagecreate($w, $h);
			$destFile= substr_replace($destFile, 'jpg', -3);
			$dest= imagecreatetruecolor($w, $h);
			imageantialias($dest, true);
	    } 
	    elseif ($tmpDest['extension'] == "png") 
	    {
	    	//$dest= imagecreate($w, $h);
			$dest = imagecreatetruecolor($w, $h);
			imageantialias($dest, true);
	    } 
	    else 
	      return false;
	
	    switch($type)
	    {
	       case 1:       //GIF
	           $src = imagecreatefromgif($srcFile);
	           break;
	       case 2:       //JPEG
	           $src = imagecreatefromjpeg($srcFile);
	           break;
	       case 3:       //PNG
	           $src = imagecreatefrompng($srcFile);
	           break;
	       default:
	           return false;
	           break;
	    }
		
	    imagecopyresampled($dest, $src, 0, 0, 0, 0, $w, $h, $width, $height);
	
	    switch($type)
	    {
	       case 1:
	       case 2:
	       case 3:
	           imagejpeg($dest, $destFile, $quality);
	           break;
	    }
	    return true;
	}	
	
	public static function createThumbnail(
		$srcFile,
		$destFile,
		$width= null,
		$height= null,
		$quality = 75,
		$fill= false)
	{	
		if(!file_exists($srcFile))
			throw new Exception("Source file '$srcFile' doesn't exists.");
			
		if(!isset($destFile))
			throw new Exception("Missing destination file.");
		
		if($width == null && $height == null)
			throw new Exception("One of height or width need to be specified.");
			
		if($width != null && !is_numeric($width))
			throw new Exception("Width need to be a numberic values. '$width' given.");
		
		if($height != null && !is_numeric($height))
			throw new Exception("Height need to be a numberic values. '$height' given.");
		
		$size= getimagesize($srcFile);
		$origWidth= $size[0];
		$origHeight= $size[1];
		$w= $origWidth;
		$h= $origHeight;
		
		if($height == null && ($width <  $origWidth || $fill))
		{
			$w= number_format($width, 0, ',', '');
			$h= number_format(($origHeight / $origWidth) * $width, 0, ',', '');
		}
		elseif($width == null && ($height <  $origHeight || $fill))
		{
			$w= number_format(($origWidth / $origHeight) * $height, 0, ',', '');
			$h= number_format($height, 0, ',', '');
		}
		
		return self::copyImage($srcFile, $destFile, $w, $h, $quality);
	}
}
?>