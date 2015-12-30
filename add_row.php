<?php
        include 'check_access.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Insert Table Row</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>

	function load_columns() {
		$("#in_table").load("load_columns.php?database="+$("#databases").val().trim()+"&table="+$("#tables").val().trim());
	};

        $(document).ready(function(){
                $("#databases").load("load_databases.php?database=postgres", function(responseTxt, statusTxt, xhr){
				$("#tables").load("load_tables.php?database="+$("#databases").val());
                    });
        	$("#databases").change(function() {
				$("#tables").load("load_tables.php?database="+$("#databases").val());
				});
        	$("#tables").change(load_columns());
		$("#search").click(function() { load_columns(); });
        });
    </script>
  </head>
  <body>
    <p  style="text-align: center;"><span  style="font-family: Helvetica,Arial,sans-serif; font-size: 30px;">
	Insert Table Row</span></p>

    <p>
    <table  border="0">
      <tbody id="select_table">
        <tr>
	  <td>
    		<select id="databases">
      		   <option  value="0">Loading...</option>
		</select>
	  </td>
	</tr>
        <tr>
	  <td>
    		<select  id="tables">
      		   <option  value="0">Loading...</option>
		</select>
	  </td>
	</tr>
    </table>
    </p>
    <p>
    <table  border="0">
      <tbody id="in_table">
        <tr>
          <td  style="text-align: right;"></td>
    <input type="button" id="search" value="Search"/>
          </td>
        </tr>
      </tbody>
    </table>
    </p>
  </body>
</html>




