<?php 
require_once("db.php");

$username = array_key_exists("username",$_POST) ?  $_POST["username"] : "";
if (empty($username)) {
	$username = array_key_exists("username",$_GET) ?  $_GET["username"] : "";
}
$redirect = array_key_exists("redirect",$_POST) ?  $_POST["redirect"] : "";
if (empty($redirect)) {
	$redirect = array_key_exists("redirect",$_GET) ?  $_GET["redirect"] : "";
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Login</title>
    <style type="text/css">
        #container{
            position: absolute;
            top: 30%;
            left: 50%;
            margin-right: -50%;
            transform: translate(-50%, -50%);
        }
	#title {
	    font-size: 30px;
	}
	#submit_go_message {
		padding-left: 10px;
		font-size: 12px;
		font-weight: bold;
		color: green;
	}
	body {
	    font-family: Arial, Helvetica, sans-serif;
	}
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>

	function error_message(message) {
		$("#submit_go_message").html("<span style=\"color:red\">"+message+"</span>");
		setTimeout( function () {       $("#submit_go_message").html("");
						$("#submit_go").prop("disabled",false);
					} , 3000);
	}

	function verify_password() {
		if ($("#password").val() !== $("#password2").val()) {
			error_message("PASSWORDS DO NOT MATCH");
			$("#password2").val('');
			$("#password2").focus();
			return;
		}
		if ($("#password").val().length < 8) {
			error_message("MINIMUM 8 CHARACTERS PASSWORD");
			return;
		}
		update_password();

	}

	function update_password() {
        	$("#submit_go").prop("disabled",true);
        	$.post("reset.php",
                	{
                        	database: "budget",
                        	username: $("#username").html(),
                        	password: $("#password").val(),
                        	password2: $("#password2").val()
                	},
                	function(data, status){
                        	if (data == "OK") {
                                	$("#submit_go_message").html("Updated Successfully");
                                	setTimeout(redirect(), 3000);
                        	} else {
                                	error_message(data);
                        	}
                	});
	}

	function redirect() {
        	window.location.href = "<?php echo $location_url.$redirect ?>";
	}

	$(document).ready(function(){
        	// LOAD
        	$("#submit_go").click(function() {
                                        	verify_password();
                                	});
                $("#username").html("<?php echo $username ?>");
	});

    </script>
  </head>
  <body>
  <div id="container">
   <form name="loginform" >
    <table  border="0">
      <tbody id="select_table">
        <tr>
	  <td colspan="2" style="padding-top: 50px"><span id="title">Budget Monitoring Tool</span></td>
	</tr>
        <tr>
	  <td colspan="2" style="padding: 20px" align="center"><img src="logo.png"></td>
	</tr>
        <tr>
	  <td align="right">Username:</td>
	  <td><span id="username">none</span></td>
	</tr>
        <tr>
	  <td colspan="2"><span style="font-size: 20px; color: red">Please reset your password below:</span></td>
	</tr>
        <tr>
	  <td align="right">Password:</td>
	  <td><input type="password" id="password" /></td>
	</tr>
        <tr>
	  <td align="right">Retype Password:</td>
	  <td><input type="password" id="password2" /></td>
	</tr>
        <tr>
	  <td colspan="2" align="right"><span id="submit_go_message"></span></td>
	</tr>
        <tr>
	  <td> </td>
	  <td><input type="button" id="submit_go" value="Go" /></td>
	</tr>
    </table>
   </form>
  </div>
  </body>
</html>




