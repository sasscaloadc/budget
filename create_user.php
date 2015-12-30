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
        $level = array_key_exists("level",$_GET) ?  $_GET["level"] : "";
        if (empty($level)) {
                $level = array_key_exists("level",$_POST) ?  $_POST["level"] : "";
        }
	$sql = " INSERT INTO access (username, password, level, firstname, lastname) VALUES ('".$username."', '".$password. "', ".$level.", '".$firstname. "', '".$lastname. "' )";

	$conn = getConnection();

//error_log($sql);
	$result = pg_query($conn, $sql);
        if ($result) {
                echo "OK";
        } else {
                echo pg_last_error($conn);
        }

	pg_close($conn);
	

?>
