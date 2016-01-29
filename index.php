<?php
	include 'check_access.php'
?>
<!DOCTYPE html>
<html>
<head>
<meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
<title>Budget Tool</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script>
$.ajaxSetup({cache: false}); // This is to prevent caching in IE

var access = <?php echo $_SESSION['access'] ?>

if (access == 1) {
	window.location.href = "http://caprivi.sasscal.org/budget/admin_main.php";
} else {
	window.location.href = "http://caprivi.sasscal.org/budget/pi_main.php";
}
</script> 
</head>
<body>
<p  style="text-align: center;"><span  style="font-family: Helvetica,Arial,sans-serif; font-size: 11px;">Redirecting ...  </span></p>
  </body>
</html>

