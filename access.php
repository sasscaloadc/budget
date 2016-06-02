<?php
require_once("db.php");

$conn = getConnection();

$redirect = array_key_exists("redirect",$_POST) ?  $_POST["redirect"] : "";
if (empty($redirect)) {
	$redirect = array_key_exists("redirect",$_GET) ?  $_GET["redirect"] : "/index.php";
}

$username = array_key_exists("username",$_POST) ?  $_POST["username"] : "";
if (empty($username)) {
      print "<h1>No parameter \"username\" received.</h1>";
      exit();
}

$password = array_key_exists("password",$_POST) ?  $_POST["password"] : "";

$pass_hash = substr(crypt($password, '$2y$09$xfr1kroEG5R0gdfGd5dfg4$'), 29);

$sql = "SELECT username, firstname, password, level, country FROM access WHERE username   = '".$username."' "; //AND password = '".$password."' ";

$output = "";
$country = "";
$result = pg_query($conn, $sql);
if ($result && (pg_num_rows($result) > 0)) {
        $row = pg_fetch_array($result); 
	if (empty($row["password"])) {
		$username = $row["username"];
		$output = "RESET PASSWORD";
	} else {
		if ($pass_hash == $row["password"]) {
		        $output .= $row["level"];
			$firstname = $row["firstname"];
			$username = $row["username"];
			$country = $row["country"];
		} else {
			$output = "INCORRECT PASSWORD";
		}
	}
} else {
        $output = "USER NOT FOUND";
}
pg_close($conn);

if (is_numeric($output)) {
	//redirect to $redirect
	session_start();
	session_regenerate_id(true); 
	$_SESSION['access'] = $output;
	$_SESSION['username'] = $username;
	$_SESSION['firstname'] = $firstname;
	$_SESSION['country'] = $country;
	err_log("LOGIN OK", $username);
	header("Location: ".$location_url.$redirect);
	#header("Location: https://budget.sasscal.org/".$redirect);
	die();
} else {
	if ($output == "RESET PASSWORD") {
                $fields = array(
                'username' => $username,
		'redirect' => $redirect
                );

                $postvars = http_build_query($fields);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $location_url."reset_password.php");
                curl_setopt($ch, CURLOPT_POST, count($fields));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);

                $result = curl_exec($ch);

                curl_close($ch);
                die();
	} else {
		err_log("LOGIN FAILED", $username);
		//POST error back to login
		$fields = array(
   		'error' => $output
		);
	
		$postvars = http_build_query($fields);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $location_url."login.php");
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
	
		$result = curl_exec($ch);
	
		curl_close($ch);
		die();
	}
}

?>
