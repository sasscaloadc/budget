<?php

    $location_url = "https://budget.sasscal.org/";

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
 

    if (!is_numeric($_SESSION['access'])) {
          header("Location: ".$location_url."login.php?redirect=".urlencode($_SERVER['PHP_SELF']));
    }

?>
