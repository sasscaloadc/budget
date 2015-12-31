<?php
session_start();

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Logout</title>
<style type="text/css">
#container{
position: absolute;
        top: 30%;
        left: 50%;
        margin-right: -50%;
        transform: translate(-50%, -50%);  
}
body {
    font-family: Arial, Helvetica, sans-serif;
}
</style>
</head>

<body>
<div id="container">
<h1>You have been logged out</h1>
<a href="index.php"> Log in again </a>
</div>
</body>
</html>
