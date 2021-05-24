<?php

function get_url_segments()
{
  return  ( explode ( '/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ) );
  }

function subdomain_is($name='test'){
      $test=false;
      $subdomain = join('.', explode('.', $_SERVER['HTTP_HOST'], -2));
      if ($subdomain == $name){
        $test='true';
        }
      return $test;
  }

//gets scriptname with the .ext removed
function script_name(){
  $script = pathinfo ( $_SERVER['SCRIPT_NAME'] , PATHINFO_BASENAME );
  return substr ( $script , 0 , strpos ($script , '.' , 0 ));  //remove .ext
  }
