<?php

class DateTimeEx
{
	public static function sqlDateToSlovak($date)
	{
		if(empty($date))
			return "";
		$parts= date_parse($date);
		return "{$parts["day"]}.{$parts["month"]}.{$parts["year"]}";
	}
	
	public static function sqlDateTimeToSlovak($dateTime)
	{
		if(empty($dateTime))
			return "";
		$parts= date_parse($dateTime);
		$minutes= str_pad($parts["minute"], 2, "0", STR_PAD_LEFT);
		return "{$parts["day"]}.{$parts["month"]}.{$parts["year"]} {$parts["hour"]}:{$minutes}";
	}
	
	public static function sqlDateOrDateTimeToSlovak($dateTime)
	{
		return str_replace(" 0:00", "", self::sqlDateTimeToSlovak($dateTime));	
	}
}
?>