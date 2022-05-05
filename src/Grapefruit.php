<?php namespace Unsized\Grapefruit;

// want dependancies none!   for production
// hugely simplified!  More robust!
// additioanl modules eg third party login are optional.

/******** grapefruit is a model - its output is data for the components****

/it takes dynamic inputs
/** $_SESSIONreturn $csrf_token;
  }
/** $_POST
/** url
/**  db

//All data segments can be fed into a template using gf
//gf is the  'one simple pipe' for getting data into a view template.

/*** and produces data segments that are used by the components
// eg header
// eg nav_title

//a database data segment
// main component eg - from db
a 2 dimensional array, to populate into a flat view or html template. ***/

//datat segments for communications eg
// texting
// construct email
// bespoke messaging eg whatsapp / third party websites.

// data is divided by its dynamism
// static [config files for fast development - later cached]
// dynamic eg taken from flat file or db. [not cached]

require_once ('_raw.php');
//rewrite backstack as a class and 'nav html components';
require_once ('_backstack.php'); //records browser history for creation of back and forward urls.
require_once ('_url_helper.php'); //get data from url
require_once('_array_helper.php');
require_once('_json_ld_helper.php');
require_once('_internationalisation.php');

class Grapefruit
{
//public $svg_symbols=array();

public $navTitle='';
public $csrf_state = [];

function __construct ()
{
  $this->session('domain');  // load session - default session is the name of the subdomain.
  $this->setAuthenticationState();  //sets authentication state;
  $this->setPageRefresh('0');
  $this->authTarget();
  $this->env = 1;
  include_once(BASE.'/.gf_env.php');   //configuration file for website, no secrets kept here! Allow each website on one host to be different.
  }

//save arrays of data for use in templates
function setSegment($objectName, $dataArray)
{
  $this->$objectName = (array)$dataArray;
  }

function getSegment($objectName)
{
  return $this->$objectName;
  }

function credGoogleCloud($cred)
{
$google_key=ROOT.'/.cred/'.$cred.'.json';
$credentials  = ['credentials' => json_decode(file_get_contents($google_key), true )];
return $credentials;
}

function urban($urban){ //used for crypto
  return file_get_contents(CREDENTIALS.'/'.$urban.'.txt');
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
//simplify this by loading the state and the session info into the segment in one go.

function sign_in()
{
  if (!$this->is_authenticated){
    require (BASE.'/.gf_sign_in.php'); //set to git ignore. third_part sign in vary by website
    $this->setSegment('sign_in', $sign_in);
    }
}

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

function authTarget()
{
if ($this->is_public() ){
  //checkif target_url is set
  if (isset($_GET['target_url'])){
      //for security - create an array of target urls and set target_url only if in list.
      //risk minimal if using relative urls?
      $_SESSION['target_url'] = $_GET['target_url'];
    }
 }
}
//End authentication segment


//head segments - simplify by loading into a single segment.
function setPageRefresh($refresh = 0){
  $this->pageRefresh = $refresh;
}

function getPageRefresh(){
  return $this->pageRefresh;
}

// forms functions

function formSubmitted($form_name){
  //echo 'form submitted function: - ';
  $token_name= $form_name.'_token';
  //echo '<br>form token_name: '.$token_name.'<br>';
  //echo '<br>'.$_POST[$token_name];
  if (isset($_POST[$token_name])){
    //$this->verifyCRSF($form_name);
    return true;
  }
  return false;
}

// CRSF functions - adapted from https://code-boxx.com/simple-csrf-token-php/
function setCSRF($form_name, $valid_for='3600')
{
$token_name= $form_name.'_token';
$token_expire=$form_name.'_expiry';
  $_SESSION[$token_name] = bin2hex(random_bytes(32));
  $_SESSION[$token_expire] = time() + $valid_for; // 1 hour = 3600 secs //echo $this->token_expire;
}

function expectedCSRF($form_name){

  $token_name= $form_name.'_token';
  if (isset ($_SESSION[$token_name])){
    return $_SESSION[$token_name];
    }
  return false;
}

function verifyCRSF($form_name)
{
  //token not set - attack?  fail silently
  $token_name= $form_name.'_token';
  $token_expire=$form_name.'_expiry';
  // (A) CHECK IF TOKEN IS SET
  if (!isset($_POST[$token_name]) || !isset($_SESSION[$token_name]) || !isset($_SESSION[$token_expire])) {

    echo "CRSF Token is not set properly!";
    echo $_POST[$token_name];
    echo $_SESSION[$token_name];
    echo $_SESSION[$token_expire];

    $this->csrf_state[$form_name] = 'bad';
    return false;
  }

  // (B) COUNTER CHECK SUBMITTED TOKEN AGAINST SESSION
  if ($_SESSION[$token_name]==$_POST[$token_name]) {
    // (B1) EXPIRED
    if (time() >= $_SESSION[$token_expire]) {
      echo "Token expired. Please reload form.";
      //set CRSF_state[$form_name] = 'expired';
      $this->csrf_state[$form_name] =  'expired';
      return false;
    }
    // (C2) OK - DO YOUR PROCESSING
    else {
      unset($_SESSION[$token_name]);
      unset($_SESSION[$token_expire]);
      echo "OK";
      $this->csrf_state[$form_name] =  'ok';
      }
    }
  return true;
}

function formExpired($form_name)
{
  if (isset ( $this->csrf_state['form_name']) && ( $this->csrf_state['form_name'] ==  'expired' ) ){
    return true;
    }
  return false;
}
/// end CSRF

// PDO segment credentials
function getCredentials( $filename, $third_party) // API credential loader
  {
    include_once (CREDENTIALS.'/'.$filename.'.php');
    $credentials = $third_party();
    return $credentials;
    }

function pdo_connect($credentials)
{
  //echo print_r($credentilas);
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
  $this->$db_alias=$pdo;
return $pdo;
}//end pdo_connect

//see https://phpdelusions.net/pdo/pdo_wrapper
function pdo($db_alias, $sql, $args = NULL)
{
  $pdo=$this->$db_alias;
    if (!$args)
    {
      return $pdo->query($sql);
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($args);
    return $stmt;
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

function argsPDO($w){ //see /pnl/invoice for usage example
  foreach ($w AS $k  => $param){
  {
      $output[$k] = $param['v'];
      }
  }
return $output;
}



// Connection loader for sergeytsalkov\meekrodb [ deprecate in favour of pdo]
function db_connect($credentials)
  {
    $credentials=array_mapper($credentials, ['charset'=>'encoding']);

    DB::$param_char = ':';
    foreach ($credentials AS $k => $v){
      DB::$$k = $v;
      }
  }

  //deprecated in favour of expectedCSRF
  /*
  function getCRSFInput($form_name)
  {
    echo 'deprecated in favour of expectedCSRF';

    $token_name= $form_name.'_token';
    $token_expire=$form_name.'_expiry';

  if (!isset ($_SESSION[$token_name])){
    $this->setCSRF($form_name);
    }
  $csrf_token[$token_name] = $_SESSION[$token_name];

    //ob_start();

    //naughty - no html in gf  -  need to put html in webcomponent builder.
    /* <input type='hidden' name='<?= $token_name ?>' value='<?=$_SESSION[$token_name]?>'/><?php

    return $csrf_token;
  }
  */
}//end class
