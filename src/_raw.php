<?php

//all user driven data is put in __(  )  function within template to ensure all data is output safely.

//XSS script prevention see https://softwareengineering.stackexchange.com/questions/159529/how-to-structure-template-system-using-plain-php/159531
//use in html templates to ensure output is made safe
/*And then in your template, you'd do:  <title><?= _($title) ?></title> */


//require a 'user generated data segment'
//all user generated data to go through 'raw'  and to be part of the production templates.   ;

function __($raw)
{
if (isset($raw)) {

  //htmlentities($string, ENT_QUOTES, 'UTF-8');
  return htmlspecialchars($raw, ENT_QUOTES, 'UTF-8');
      }
}
