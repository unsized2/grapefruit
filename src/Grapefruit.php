<?php namespace Unsized\Grapefruit;

/******** grapefruit is a controller ****
/it takes dynamic inputs
/** $_SESSION
/** $_POST
/** url
/**  db
/*** and produce a 2 dimensional array, to populate into a flat view or html template. ***/

require ('_raw.php');

//rewrite backstack as a class and 'nav html components';
require ('_backstack.php'); //records browser history for creation of back and forward urls.
require ('_url_helper.php');

class Grapefruit
{
//public $svg_symbols=array()

function __construct ()
{
  //sets a different session name depending upon the subdomain, so test.domain and local.dommain can have separate sessions.
  //records browser history for creation of back and forward urls. //back_link(); //fwd_link();
  //Start session
  $session_name='domain';
  $subdomain = join('.', explode('.', $_SERVER['HTTP_HOST'], -2));
  if (!empty ($subdomain)){$session_name=$subdomain;}
  session_name($session_name);
  session_start();
  back_stack();
  //to improve performance close the session for writing at the earliest stage in the script. //sessions are locked till script is completed.
  //see https://thisinterestsme.com/releasing-session-locks-php/
  //session_write_close
  }

function getCredentials( $filename, $third_party) // API credential loader
{
  include_once (ROOT.'/credentials/'.$filename.'.php');
  $credentials = $third_party();
  return $credentials;
  }

function pdo_connect($credentials)
{
  extract ($credentials);
  $options = [
      \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
      \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
      \PDO::ATTR_EMULATE_PREPARES   => false,
  ];

  $dsn = "mysql:host=$host;dbname=$dbName;charset=$charset;port=$port";

  try {
       $pdo = new \PDO($dsn, $user, $password, $options);
  } catch (\PDOException $e) {
       throw new \PDOException($e->getMessage(), (int)$e->getCode());
  }
return $pdo;
}//end pdo_connect

// Connection loader for sergeytsalkov\meekrodb [ deprecate in favour of pdo]
function db_connect($credentials)
  {
    $credentials=array_mapper($credentials, ['charset'=>'encoding']);

    DB::$param_char = ':';
    foreach ($credentials AS $k => $v){
      DB::$$k = $v;
      }
  }



}//end class
