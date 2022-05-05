<?php
//function to record last internal urls visited so that forward and back buttons work.
//based on history, not hierarchy.
//records pages and sub url, but not query string.
// session best practices https://www.phparch.com/2018/01/php-sessions-in-depth/


function back_stack($back_max=5)
{
//shift remove first value off array  [0]  //unshift push first value on array [0]

$current_url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (!isset ($_SESSION['back'])){ // no back stack add current_url to back
    $_SESSION['back']=[$current_url];
//    view_nav_states("New Session");
    }

// moved back one, create / add to the forward array, shift the back stack]
elseif ( isset($_SESSION['back'][1]) &&   ($_SESSION['back'][1]) ==  $current_url  ){

  if (!isset($_SESSION['fwd'])){
      $_SESSION['fwd'][0] = $_SESSION['back'][0];
      } else {
      array_unshift($_SESSION['fwd'], $_SESSION['back'][0]);
    }
    array_shift($_SESSION['back']);
//    view_nav_states("Pressed Back");
  }

// moved forward one, reduce forward array, add to the back array.
elseif ( (isset($_SESSION['fwd'][0])) && ($_SESSION['fwd'][0] == $current_url ) ) { // a new page
    array_shift($_SESSION['fwd']);
    array_unshift($_SESSION ['back'], $current_url);
//    view_nav_states("Pressed fwd");
  }

//It is a new url.  Add to back, clear forward
elseif(  $_SESSION['back'][0]!=$current_url ){ //not refresh
  unset ($_SESSION['fwd']);
  array_unshift($_SESSION ['back'], $current_url);
  if (count ($_SESSION['back']) > $back_max){
    array_pop($_SESSION['back']);
    }
//view_nav_states("A new url");
  }

//  else {
//    view_nav_states("Made it to the end: Page refresh");
//    }

if(!isset($_SESSION['fwd'])){ //store front url
  $_SESSION['fwd'][0]=false;
  }
if(!isset($_SESSION['back'][1])){ //store front url
    $_SESSION['back'][1]=false;
    }

}//end back stack


//note prob require array output for forward and back buttons.
//target url. Class disabled. [toggle].
function back_url()
{
return $_SESSION['back'][1];
}

function fwd_url()
{
return $_SESSION['fwd'][0];
}


//**************************For Testing only below here***************************************//
//**********Create html in page **************************************************************//
function back_link()
{
if (back_url()){
?>
<a href='<?= back_url()?>'>Back</a>
<?php
}else
echo "back";
}

function fwd_link()
{
if (fwd_url()){
  ?>
  <a href='<?= fwd_url()?>'>Fwd</a>
  <?php
}else
echo "fwd";
}

function view_nav_states($title)
{
  ?>
  <h4><?= $title ?></h4>
  <ul>
    <li>Current: <?= parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); ?>"</li>
    <li>Back: <?php print_r ($_SESSION['back']); ?></li>
    <li>Forward: <?php if (isset($_SESSION['fwd'])){ print_r ($_SESSION['fwd']);} ?></li>
  </ul><?php
}
