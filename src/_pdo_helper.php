<?php

_pdo_helper


function primary_naming_convention($table) //modify function to suit naming convention of primary_key
{
  return $table.'_id';  //or modify to primary_key naming convension
}


function insertUpdate($table, $data, $where=array(), $primary='')
{
if (empty($primary)){
  $primary = primary_naming_convention($table);  //or modify to table naming convension
  }
//Do a select on the where condition, use keys from data for output

//If no results do the insert
//output is the datakeys plus the primary key
}



function row_query($table, $data, $where=array(), $primary='')
{
  $select_fields =  (array)$primary + array_keys($data);
  echo $select = arr2str($select_fields, $separator=', ', $q='`');

}





function select_string($w, $primary)
{
  foreach ($w AS $placeholder  => $param){
    if (!isset ($param['op'])){
      $param['op'] = '=';
      }
      if (isset ($param['k'])){
        $field = $param['k'];
      }else {
        $field = $placeholder;}

    if (!isset ($whereString)){
      $whereString = "WHERE $field {$param['op']} :$placeholder ";
    }else{
      $whereString = "$whereString AND $field {$param['op']} :$placeholder ";
      }
  }
return $whereString;
}

function wherePDO($w) //see /pnl/invoice for usage example
{
  foreach ($w AS $placeholder  => $param){
    if (!isset ($param['op'])){
      $param['op'] = '=';
      }
      if (isset ($param['k'])){
        $field = $param['k'];
      }else {
        $field = $placeholder;}

    if (!isset ($whereString)){
      $whereString = "WHERE $field {$param['op']} :$placeholder ";
    }else{
      $whereString = "$whereString AND $field {$param['op']} :$placeholder ";
      }
  }
return $whereString;
}


//extract where keys and add to the primary key.


}










function wherePDO($w) //see /pnl/invoice for usage example
{
  foreach ($w AS $placeholder  => $param){
    if (!isset ($param['op'])){
      $param['op'] = '=';
      }
      if (isset ($param['k'])){
        $field = $param['k'];
      }else {
        $field = $placeholder;}

    if (!isset ($whereString)){
      $whereString = "WHERE $field {$param['op']} :$placeholder ";
    }else{
      $whereString = "$whereString AND $field {$param['op']} :$placeholder ";
      }
  }
return $whereString;
}
