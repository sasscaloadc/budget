<?php
  include 'check_access.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Reports</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
var location_url = "<?php echo $location_url ?>";

        $(document).ready(function(){

                $("#tasks").click(function() {
                        window.location.href = location_url+"report_tasks.php";
                });

                $("#tasks_euro").click(function() {
                        window.location.href = location_url+"report_tasks_euro.php";
                });

        	$("#cumulative").load("load_tasklist.php?database=budget", function(){
			//
            	});

	        $("#quarterly").load("load_tasklist.php?database=budget", function(){
			$("#year").load("load_years.php?taskid="+$("#quarterly").val().trim(), function() {
				$("#quarter").load("load_quarters.php?taskid="+$("#quarterly").val().trim()+"&year="+$("#year").val().trim());
			});	
            	});

	        $("#quarterly").change(function(){
			$("#year").load("load_years.php?taskid="+$("#quarterly").val().trim(), function() {
				$("#quarter").load("load_quarters.php?taskid="+$("#quarterly").val().trim()+"&year="+$("#year").val().trim());
			});	
		});

		$("#year").change(function() {
                            $("#quarter").load("load_quarters.php?taskid="+$("#quarterly").val().trim()+"&year="+$("#year").val().trim());
                });

                $("#cumulative_go").click(function() {
                        window.location.href = location_url+"report_cumulative.php?taskid="+$("#cumulative").val().trim();
                });

                $("#quarterly_go").click(function() {
                        window.location.href = location_url+"report_quarterly.php?taskid="+$("#quarterly").val().trim()+"&year="+$("#year").val().trim()+"&quarter="+$("#quarter").val().trim();
                });

                $("#cancel").click(function() {
                        window.location.href = location_url+"index.php";
                });

        });
    </script>
    <style  type="text/css">
      #container {
            position: absolute;
            top: 30%;
            left: 50%;
            margin-right: -50%;
            transform: translate(-50%, -50%);
      }
      #submit_message {
        padding-left: 10px;
        font-size: 12px;
        font-weight: bold;
        color: green;
      }
      body {
	font-family: Helvetica,Arial,sans-serif;
      }
  </style>
  </head>
  <body>
   <div id="container">
    <p  style="text-align: center;"><span  style="font-size: 30px;">
	Reports</span></p>

    <p>
    <table  border="0">
      <tbody id="task">
        <tr>
	  <td> Tasks Report </td>
	  <td> <input type="button" id="tasks" value="Go"/>
	  </td>
	</tr>
        <tr>
	  <td> Tasks Report (Euro)</td>
	  <td> <input type="button" id="tasks_euro" value="Go"/>
	  </td>
	</tr>
        <tr>
	  <td> Cumulative Report </td>
	  <td> 
            <select id="cumulative">
                 <option>Loading ...</option>
            </select><input type="button" id="cumulative_go" value="Go"/>
	  </td>
	</tr>
        <tr>
	  <td> Quarterly Report </td>
	  <td> 
            <select id="quarterly">
                 <option>Loading ...</option>
            </select>
            <select id="year">
                 <option>Loading ...</option>
            </select>
            <select id="quarter">
                 <option>Loading ...</option>
            </select>
	    <input type="button" id="quarterly_go" value="Go"/>
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
	     <span id="submit_message"></span>
          </td>
        </tr>
      </tbody>
    </table>
    </p>
   </div>
  </body>
</html>




