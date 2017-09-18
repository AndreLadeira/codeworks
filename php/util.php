<?php 

function cwGetMonthStr($mnum)
{
	if ($mnum < 1 || $mnum >12 ) return "Error";

	$months = array(
			"January",
			"February",
			"March",
			"April",
			"May",
			"June",
			"July",
			"August",
			"September",
			"October",
			"November",
			"December");	
	return $months[$mnum - 1];
}

function cwVariableHasValue($var)
{
	if( $var === null || !isset($var) || empty($var) ) return false;
	else return true;	
}

function cwAddTabs(&$text, $tabs)
{
	if ($tabs <= 0) return;
	
	$str = "\n" . str_repeat("\t",$tabs);
	$text = str_replace("\n",$str,$text);
}


// Perform a select in a json database
// Return the first occurence of the pair $key = $value

function cwGetRecord($file, $key, $value)
{
	$data = file_get_contents($file) or die("cwGetRecord: data file not found: $file\n");
	
	$arr = json_decode($data,true) or die("cwGetRecord: error decoding data file: $file\n");

	if ( $key === "" || $value === "" ) return $arr;
	
	$pos = array_search($value, array_column($arr, $key) );
	
	//echo "pos is $pos\n";
	
	$res = false;
	
	if ( $pos !== false ) $res = $arr[$pos];
	
	//print_r($res);
	
	return $res;
}

// Perform a select in a json database
// Return all the occurences where the data 
// pointed by $key contais $value

define ("COMPARE_EQUAL" , "true");
define ("COMPARE_LIKE" , "false");

function cwSelect($table_file, $key, $value, $comp_type = COMPARE_EQUAL, $case_sensitive = true)
{
	$data = file_get_contents($table_file) or die("cwSelect: data file not found: $table_file\n");

	// select all
	$arr = json_decode($data,true) or die("cwSelect: error decoding data file: $file\n");
	
	if (!count($arr) ) return false;
	
	if ( $key === "" ) return $arr;
	
	$res = array();
	
	// by default the comparisson is case sensitive
	$compare_fcn = 'strpos';
	if (!$case_sensitive) $compare_fcn = 'stripos';
	
	// filter by the key = value criteria
	foreach ($arr as $record)
	{
		
		if ( $comp_type == COMPARE_EQUAL && $record[$key] == $value )
			$res[] = $record;
		elseif ( $comp_type == COMPARE_LIKE && $compare_fcn($record[$key], $value) !== false )
			$res[] = $record;
	}
	//var_dump($res);
	return $res;
	
}

// Reads a json database
// Return all the occurences into an array

function cwGetJsonDataAsArray($file)
{
	$data = file_get_contents($file) or die("cwGetJsonAsArray: data file not found: $file\n");
	$arr = json_decode($data,true) or die("cwGetJsonAsArray: error decoding data file: $file\n");
	
	return $arr;
}

function cwGetLinesInFile($filename)
{
	return count(file($filename));
}
//$xpto = cwGetRecord("../data/post-images.json","id","plugin_factory");
//if ( $xpto !== false) echo $xpto["desc"];

?>