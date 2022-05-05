<?php
//purpose - Format sql dates into human readable for use in templates.
//reason - keeps formatting in the template and out of the model.

//date formatter.

function f_date($date, $date_format = 'jS F Y')
{
//if sql date convert to timestanp
//echo $date;
//echo strpos( $date, '-' );

if (strpos( $date, '-' ) == 4  ){ //fast test for sql date
$date = (strtotime($date));
}

if ($date > 0){
  //echo $date;
  $f_date=date($date_format, $date);
  } else {
  $f_date = false;
}
return $f_date;
}

//used to return in format for open banking IS0
function atom( $timestamp )
{
return  f_date($timestamp, DATE_ATOM);
}

/*
if ( $timestamp > 0){
  $f_date=date($date_format, DATE_ATOM );
  }else {
  $f_date = false;
}
return $f_date;
*/

function _£p($number=0, $show_zero=0)
{
  $number=(float)$number;
  //$number=$number*1.0; //cast to a float
  if( ($number!=0 ) || ($show_zero==1) ) {

  if (!is_float($number)){
    echo "number not a float: ".$number."<br>";
    return false;
  }else{
  $output= chr(163).number_format($number,2);
  return $output;
  }
} //skip if number=0;  //dont show zeros
}
