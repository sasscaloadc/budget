<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>No Access</title>
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
	    font-weight: bold;
	    color: red;
	}
	body {
	    font-family: Arial, Helvetica, sans-serif;
	}
    </style>
  </head>
  <body>
  <div id="container">
    <table  border="0">
      <tbody id="select_table">
        <tr>
	  <td colspan="2" style="padding-top: 50px"><span id="title">Your Account Does Not Have Access To This Page</span></td>
	</tr>
        <tr>
	  <td colspan="2" style="padding: 20px" align="center">&nbsp;</td>
	</tr>
        <tr>
	  <td>&nbsp;</td>
	  <td><input type="button" value="Back" onclick="window.history.back()" /></td>
    </table>
    <input type="hidden" name="redirect" value="<?php echo empty($_GET['redirect']) ? $_POST['redirect'] : $_GET['redirect'] ?>"/>
  </div>
  </body>
</html>




