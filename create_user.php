<?php
include 'check_access.php';
require_once("db.php");


        $firstname = array_key_exists("firstname",$_GET) ?  $_GET["firstname"] : "";
        if (empty($firstname)) {
                $firstname = array_key_exists("firstname",$_POST) ?  $_POST["firstname"] : "";
		if (empty($username)) {
			echo "Please enter a First Name";
			die();
		}
        }
        $lastname = array_key_exists("lastname",$_GET) ?  $_GET["lastname"] : "";
        if (empty($lastname)) {
                $lastname = array_key_exists("lastname",$_POST) ?  $_POST["lastname"] : "";
		if (empty($username)) {
			echo "Please enter a Last Name";
			die();
		}
        }
        $username = array_key_exists("username",$_GET) ?  $_GET["username"] : "";
        if (empty($username)) {
                $username = array_key_exists("username",$_POST) ?  $_POST["username"] : "";
		if (empty($username)) {
			echo "Please enter a username";
			die();
		}
        }
        $country = array_key_exists("country",$_GET) ?  $_GET["country"] : "";
        if (empty($country)) {
                $country = array_key_exists("country",$_POST) ?  $_POST["country"] : "";
        }
        $level = array_key_exists("level",$_GET) ?  $_GET["level"] : "";
        if (empty($level)) {
                $level = array_key_exists("level",$_POST) ?  $_POST["level"] : "";
        }
	$sql = " INSERT INTO access (username, password, level, country, firstname, lastname) VALUES ('".$username."', '', ".$level.", '".$country. "', '".$firstname. "', '".$lastname. "' )";

	$conn = getConnection();

	$result = pg_query($conn, $sql);
        if ($result) {
                echo "OK";
		$details = "[username:".$username.", password:".$password. ", level:".$level.", country:".$country. ", firstname:".$firstname. ", lastname:".$lastname. "]";
		err_log("ADD USER", $details);
        } else {
                echo pg_last_error($conn);
		err_log("ADD USER FAILED", pg_last_error($conn));
        }

	pg_close($conn);
	

?>
