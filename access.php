<?php
require_once("db.php");

$conn = getConnection();

$redirect = array_key_exists("redirect",$_POST) ?  $_POST["redirect"] : "/budget/index.php";
if (empty($redirect)) {
	$redirect = array_key_exists("redirect",$_GET) ?  $_GET["redirect"] : "/budget/index.php";
}

$username = array_key_exists("username",$_POST) ?  $_POST["username"] : "";
if (empty($username)) {
      print "<h1>No parameter \"username\" received.</h1>";
      exit();
}

$password = array_key_exists("password",$_POST) ?  $_POST["password"] : "";

error_log("username = ".$username." and password = ".$password);

$sql = "SELECT username, firstname, password, level FROM access WHERE username   = '".$username."' "; //AND password = '".$password."' ";

$output = "";
$result = pg_query($conn, $sql);
if ($result && (pg_num_rows($result) > 0)) {
        $row = pg_fetch_array($result); 
	if ($password == $row["password"]) {
	        $output .= $row["level"];
		$firstname = $row["firstname"];
		$username = $row["username"];
	} else {
		$output .= "INCORRECT PASSWORD";
	}
} else {
        $output .= "USER NOT FOUND";
}
pg_close($conn);

if (is_numeric($output)) {
	//redirect to $redirect
	session_start();
	session_regenerate_id(true); 
	$_SESSION['access'] = $output;
	$_SESSION['username'] = $username;
	$_SESSION['firstname'] = $firstname;
	header("Location: http://caprivi.sasscal.org".$redirect);
	#header("Location: https://budget.sasscal.org/".$redirect);
	die();
} else {
	//POST error back to login
	$fields = array(
   	'error' => $output
	);

	$postvars = http_build_query($fields);
	$ch = curl_init();
	#curl_setopt($ch, CURLOPT_URL, "https://budget.sasscal.org/login.php");
	curl_setopt($ch, CURLOPT_URL, "http://caprivi.sasscal.org/budget/login.php");
	curl_setopt($ch, CURLOPT_POST, count($fields));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);

	$result = curl_exec($ch);

	curl_close($ch);
	die();
}

?>
