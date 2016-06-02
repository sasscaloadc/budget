<?php
require_once("db.php");


        $username = array_key_exists("username",$_GET) ?  $_GET["username"] : "";
        if (empty($username)) {
                $username = array_key_exists("username",$_POST) ?  $_POST["username"] : "";
        }
        $password = array_key_exists("password",$_GET) ?  $_GET["password"] : "";
        if (empty($password)) {
                $password = array_key_exists("password",$_POST) ?  $_POST["password"] : "";
        }
        $password2 = array_key_exists("password2",$_GET) ?  $_GET["password2"] : "";
        if (empty($password2)) {
                $password2 = array_key_exists("password2",$_POST) ?  $_POST["password2"] : "";
        }

	if (empty($username)) {
		echo "USERNAME UNDEFINED";
		die();
	}
	if ($password !== $password2) {
		echo "PASSWORDS DO NOT MATCH";
		die();
	}

	$pass_hash = substr(crypt($password, '$2y$09$xfr1kroEG5R0gdfGd5dfg4$'), 29);

	$sql = " UPDATE access set password = '".$pass_hash."' "
               ."WHERE username = '".$username."' RETURNING level, firstname, country";

	$conn = getConnection();

	$result = pg_query($conn, $sql);
        if ($result) {
		$rows = pg_affected_rows($result);
		$row = pg_fetch_array($result);
		if ($rows > 0) {
	                echo "OK";
		        session_start();
        		session_regenerate_id(true);
        		$_SESSION['access'] = $row["level"];
        		$_SESSION['username'] = $username;
        		$_SESSION['firstname'] = $row["firstname"];
        		$_SESSION['country'] = $row["country"];
			err_log("PASSWORD RESET FOR USER", $username);
        		die();

		} else {
	                echo "USER NOT FOUND: ".$username;
			err_log("PASSWORD RESET FAILED FOR ".$username, "Username Not Found");
		}
        } else {
                echo pg_last_error($conn);
		err_log("PASSWORD RESET FAILED FOR ".$username, pg_last_error($conn));
        }

	pg_close($conn);
	

?>
