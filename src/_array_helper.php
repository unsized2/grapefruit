<?php
function filterArray ($record, $filterKeys=array() )
{
  //echo nl2br (print_r($filterKeys, 1));
	$output=array();
	//$record = (array)$record;
	//$filterKeys = (array)$filterKeys;

	if (!empty($filterKeys)){
	//echo nl2br(print_r($filterKeys,1));
	foreach ($filterKeys AS $k)
		{
		if (isset ($record[$k])){
			$output[$k]=$record[$k];
		}else{
			$output[$k]=''; //initialise the variable
			}
		}
	}
	return $output;
}


function arr2str($array, $separator=', ', $quotes=false)
{
	if ($quotes == false){
		$q = '';} else{
			$q ="'";
		}

	$list='';
	foreach ($array AS $k => $v){
		if (empty($list)){
			$list = $q.$v.$q;
		}else{
			$list = $list.$separator.$q.$v.$q;
		}
	}
	return $list;
}

/******************** MYSQL result helpers below here ***********************/

//takes one column and returns total as a variable
function sumCol($array, $col)
{
	$total=0;
	foreach ($array AS $k => $row){
		if(!isset($row[$col])){
				$row[$col]=0;
				}  //if one of the rows is blank
		$total = $total + $row[$col];
	}
	return $total;
}

//takes one or more col and returns total as an array
function sumCols($array, $cols)
{
$cols=(array)$cols;

foreach ($cols AS $col)
	{
	$total[$col]=0;
		foreach ($array AS $k => $row){
			if(!isset($row[$col])){
				$row[$col]=0;
				}  //if one of the rows is blank
		$total[$col] = $total[$col] + $row[$col];
		}
	}
	return $total;
}


//this is a columnlist - takes sql results and for a column and puts in a comma separated list.
//Eg email list
//to do modify so separator can be :  ; - etc
function colList($array, $field)
	{
	$list='';
	foreach ($array AS $k => $v){
	  if (empty($list)){
	    $list = $v[$field];
	  }else{
	    $list = $v[$field].', '.$list;
	  }
	}
	return $list;
}

function array_mapper($source=array(), $mapper=array())
{
    foreach ($mapper as $key=>$new_key) {
        if (array_key_exists($key, $source)) {
            $source[$new_key]=$source[$key];
            unset($source[$key]);
        }
    }
    return $source;
}

//like array mapper, but takes a string input instead of array.
//prob poss to combine both functions
function translate($input, $translation)
{
if (array_key_exists($input, $translation)){
    return ($translation[$input]);
      }
return $input;
}

//Take sql result and return headings as an associative array
//where key have _ replaced with space.
function get_headings($results)
{
	$output=[];
  $row = $results[0];
  $headings = array_keys($row);
  foreach ($headings as $k => $v){
    $output[$v]=str_replace('_',' ', $v);
    }
  return $output;
}
