<?php
require_once("db.php");

    session_cache_expire( 20 );
    session_start(); // NEVER FORGET TO START THE SESSION!!!
    $inactive = 1200;
    if(isset($_SESSION['start']) ) {
    	$session_life = time() - $_SESSION['start'];
    	if($session_life > $inactive){
    		header("Location: logout.php");
    	}
    }
    $_SESSION['start'] = time();
 
    $access = array_key_exists('access', $_SESSION) ? $_SESSION['access'] : 'NOT DEFINED';
    
    if (!is_numeric($access)) {
          header("Location: ".$location_url."login.php?redirect=".urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']));
    }

?>
