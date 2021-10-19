<?php namespace Unsized\Grapefruit;


use League\OAuth2\Client\Provider\Google;
/******** grapefruit is a model - its output is data for the components****

/it takes dynamic inputs
/** $_SESSION
/** $_POST
/** url
/**  db

/*** and produces data segments that are used by the components
// eg header
// eg nav_title
// main component eg - from db
a 2 dimensional array, to populate into a flat view or html template. ***/

// data is divided by its dynamism
// static [config files for fast development - later cached]
// dynamic eg taken from flat file or db. [not cached]

// grapefruit helpers are grouped by  dev, test and production
// each loads progressively less helpers.  [since production uses cached pages]



require ('_raw.php');

//rewrite backstack as a class and 'nav html components';
require ('_backstack.php'); //records browser history for creation of back and forward urls.
require ('_url_helper.php');

class Grapefruit
{
//public $svg_symbols=array();


function __construct ()
{
  $this->session('domain');  // load session - default session is the name of the subdomain.
  $this->setAuthenticationState();  //sets authentication state;
  }

//move to helper?
function getCredentials( $filename, $third_party) // API credential loader
  {
    include_once (CREDENTIALS.'/'.$filename.'.php');
    $credentials = $third_party();
    return $credentials;
    }



function session($session_name)
{
  //sets a different session name depending upon the subdomain, so test.domain and local.domain can have separate sessions.
  //records browser history for creation of back and forward urls. //back_link(); //fwd_link();
  //Start session
  $subdomain = join('.', explode('.', $_SERVER['HTTP_HOST'], -2));
  if (!empty ($subdomain)){$session_name=$subdomain;}
  //echo $session_name;
  session_name($session_name);
  session_start();
  back_stack();   //remember pages so we can go back
  //to improve performance close the session for writing at the earliest stage in the script. //sessions are locked till script is completed.
  //see https://thisinterestsme.com/releasing-session-locks-php/
  //session_write_close
}

function kill_session() // Kill the session as documented here https://www.php.net/manual/en/function.session-destroy.php
{
  $_SESSION = array();
  // If it's desired to kill the session, also delete the session cookie.
  // Note: This will destroy the session, and not just the session data!
  if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000,
          $params["path"], $params["domain"],
          $params["secure"], $params["httponly"]
      );
  }
  // Finally, destroy the session.
  session_destroy();
}




//authentication segment
function setAuthenticationState()
{
if ( (isset ($_SESSION['authenticated'])) &&  $_SESSION['authenticated'] == 1){
  $this->is_authenticated = true;
  return true;
  }
  $this->is_authenticated = false;
return false;
}

function is_authenticated()
{
  if ($this->is_authenticated ){
    return true;
  }
return false;
}

function is_public()
{
  if (!$this->is_authenticated ){
    return true;
  }
return false;
}

function getAuthenticationName()
{
  if (isset ($_SESSION['first_name']) ){
    return $_SESSION['first_name'];
  } else {
    return false;
  }
}

function getAuthenticationId()
{
  if (isset ($_SESSION['authentication_id']) ){
    return $_SESSION['authentication_id'];
      } else {
    return false;
  }
}

function setGoogleAuthUrl(){
  $credentials=$this->getCredentials('openid', 'google');
  //print_r($credentials);
  $provider = new Google( $credentials );
  $this->google_auth_url = $provider->getAuthorizationUrl();
  $_SESSION['oauth2state'] = $provider->getState();
}

//End authentication segment



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
