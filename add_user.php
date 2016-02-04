<?php
include "check_access.php";

if ($_SESSION['access'] > 1)  {
        header("Location: ".$location_url."no_access.php");
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Add User</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
var location_url = "<?php echo $location_url ?>";

        $(document).ready(function(){

                $("#cancel").click(function() {
                        window.location.href = location_url+"index.php";
                });

		$("#save").click(function() { 
			if ($("#password").val() != $("#verify_password").val()) {
				 $("#submit_message").html("<span style=\"color:red\">Passwords do not match!</span>");
				setTimeout(function() {$("#submit_message").html("");}, 3000);
				return;
			}
                	$.post("create_user.php",
                        {
				database: "budget",
                                username: $("#username").val(),
                                password: $("#password").val(),
                                firstname: $("#firstname").val(),
                                lastname: $("#lastname").val(),
                                level: $("#level").val(),
                        },
                        function(data, status){
				var saved = false;
                                if (data == "OK") {
                                                $("#submit_message").html("Saved");
					        saved = true;	
                                } else {
                                        $("#submit_message").html("<span style=\"color:red\">"+data+"</span>");
                                }
                                setTimeout(function() {
					$("#submit_message").html("");
					if (saved) {
						window.location.href = location_url+"index.php";
					}
				}, 3000);
                        });

		});
        });
    </script>
    <style  type="text/css">
#container{
position: absolute;
        top: 50%;
        left: 50%;
        margin-right: -50%;
        transform: translate(-50%, -50%);
}
body {
    font-family: Arial, Helvetica, sans-serif;
}
#submit_message {
  padding-left: 10px;
  font-size: 12px;
  font-weight: bold;
  color: green;
}
  </style>
  </head>
  <body>
   <div id="container">
    <p  style="text-align: center;"><span  style="font-size: 30px;">
	Create New User</span></p>

    <p>
    <table  border="0">
      <tbody id="task">
        <tr>
	  <td> Username </td>
	  <td> <input type="text" id="username"/>
	  </td>
	</tr>
        <tr>
	  <td> Password </td>
	  <td> <input type="password" id="password"/>
	  </td>
	</tr>
        <tr>
	  <td> Retype Password </td>
	  <td> <input type="password" id="verify_password"/>
	  </td>
	</tr>
        <tr>
	  <td> First Name </td>
	  <td> <input type="text" id="firstname"/>
	  </td>
	</tr>
        <tr>
	  <td> Last Name </td>
	  <td> <input type="text" id="lastname"/>
	  </td>
	</tr>
        <tr>
	  <td> Access Level </td>
	  <td> <select id="level">
		 <option value="1">Administrator</option>
		 <option value="2">Principal Investigator</option>
	  </td>
	</tr>
    </table>
    </p>
    <p>
    <table  border="0">
      <tbody id="in_table">
        <tr>
          <td  style="text-align: right;"></td>
             <input type="button" id="cancel" value="< Back"/>
             <input type="button" id="save" value="Create"/>
	     <span id="submit_message"></span>
          </td>
        </tr>
      </tbody>
    </table>
    </p>
   </div>
  </body>
</html>




