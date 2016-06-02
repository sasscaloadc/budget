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
	body {
	    font-family: Arial, Helvetica, sans-serif;
	}
    </style>
  </head>
  <body>
  <div id="container">
   <form name="loginform" action="access.php" method="POST">
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
	  <td><input type="text" name="username" /></td>
	</tr>
        <tr>
	  <td align="right">Password:</td>
	  <td><input type="password" name="password" /></td>
	</tr>
        <tr>
	  <td> </td>
	  <td><input type="submit" value="Go" /></td>
	</tr>
        <tr>
	  <td colspan="2"> </td>
	  <td><?php echo array_key_exists('error', $_POST) ? $_POST['error'] : '' ?></td>
	</tr>
    </table>
    <input type="hidden" name="redirect" value="<?php echo  array_key_exists('redirect', $_GET) ? $_GET['redirect'] : (array_key_exists('redirect', $_POST) ? $_POST['redirect'] : '') ?>"/>
   </form>
  </div>
  </body>
</html>




