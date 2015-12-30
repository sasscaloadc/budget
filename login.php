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
    </style>
  </head>
  <body>
  <div id="container">
   <form name="loginform" action="access.php" method="POST">
    <table  border="0">
      <tbody id="select_table">
        <tr>
	  <td>Username:</td>
	  <td><input type="text" name="username" /></td>
	</tr>
        <tr>
	  <td>Password:</td>
	  <td><input type="password" name="password" /></td>
	</tr>
        <tr>
	  <td> </td>
	  <td><input type="submit" value="Go" /></td>
	</tr>
        <tr>
	  <td colspan="2"> </td>
	  <td><?php echo $_POST["error"] ?></td>
	</tr>
    </table>
    <input type="hidden" name="redirect" value="<?php echo empty($_GET['redirect']) ? $_POST['redirect'] : $_GET['redirect'] ?>"/>
   </form>
  </div>
  </body>
</html>




