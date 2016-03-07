<?php
include 'check_access.php';
require_once("db.php");


        $firstname = array_key_exists("firstname",$_GET) ?  $_GET["firstname"] : "";
        if (empty($firstname)) {
                $firstname = array_key_exists("firstname",$_POST) ?  $_POST["firstname"] : "";
        }
        $lastname = array_key_exists("lastname",$_GET) ?  $_GET["lastname"] : "";
        if (empty($lastname)) {
                $lastname = array_key_exists("lastname",$_POST) ?  $_POST["lastname"] : "";
        }
        $username = array_key_exists("username",$_GET) ?  $_GET["username"] : "";
        if (empty($username)) {
                $username = array_key_exists("username",$_POST) ?  $_POST["username"] : "";
        }
        $password = array_key_exists("password",$_GET) ?  $_GET["password"] : "";
        if (empty($password)) {
                $password = array_key_exists("password",$_POST) ?  $_POST["password"] : "";
        }
        $country = array_key_exists("country",$_GET) ?  $_GET["country"] : "";
        if (empty($country)) {
                $country = array_key_exists("country",$_POST) ?  $_POST["country"] : "";
        }
        $level = array_key_exists("level",$_GET) ?  $_GET["level"] : "";
        if (empty($level)) {
                $level = array_key_exists("level",$_POST) ?  $_POST["level"] : "";
        }
	$sql = " INSERT INTO access (username, password, level, country, firstname, lastname) VALUES ('".$username."', '".$password. "', ".$level.", '".$country. "', '".$firstname. "', '".$lastname. "' )";

	$conn = getConnection();

	$result = pg_query($conn, $sql);
        if ($result) {
                echo "OK";
		$details = "[username:".$username.", password:".$password. ", level:".$level.", country:".$country. ", firstname:".$firstname. ", lastname:".$lastname. "]";
		error_log($_SESSION['username'].": ADD USER ".$details."\n", 3, "/var/www/sasscal_secure/budget_tool/logs/audit.log");
        } else {
                echo pg_last_error($conn);
		error_log($_SESSION['username'].": ADD USER FAILED ".pg_last_error($conn)."\n", 3, "/var/www/sasscal_secure/budget_tool/logs/audit.log");
        }

	pg_close($conn);
	

?>
