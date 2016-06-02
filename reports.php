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
var keys = JSON.parse(' { "taskid" : "?", "year" : "?", "quarter" : "?" } ');    // these are the last chosen values for task, year and quarter.

        function setsessionvar(key, value, callback) {
                $.post("sessionvalues.php", { 'key' : key, 'value' : value }, function(data) {
                        keys = JSON.parse(data);
                        if (callback) callback();
                });
        }

        $(document).ready(function(){

                $("#tasks").click(function() {
                        window.location.href = location_url+"report_tasks.php";
                });

                $("#tasks_euro").click(function() {
                        window.location.href = location_url+"report_tasks_euro.php";
                });

        	$("#cumulative").load("load_tasklist.php?database=budget", function(){
                        setsessionvar('reload', 'ok',  // this is just to load the values into global variable "keys"
                                 function() {   //callback
                                     $("select#cumulative option").each(function() { this.selected = (this.value.trim() == keys.taskid.trim()); });
                                 });
            	});

	        $("#quarterly").load("load_tasklist.php?database=budget", function(){
                   setsessionvar('reload', 'ok',  // this is just to load the values into global variable "keys"
                      function() {   //callback
                         $("select#quarterly option").each(function() { this.selected = (this.value.trim() == keys.taskid.trim()); });
		         $("#year").load("load_years.php?taskid="+$("#quarterly").val().trim(), function() {
                            $("select#year option").each(function() { this.selected = (this.value.trim() == keys.year.trim()); });
			    $("#quarter").load("load_quarters.php?taskid="+$("#quarterly").val().trim()+"&year="+$("#year").val().trim(), function() {
				$("select#quarter option").each(function() { this.selected = (this.value.trim() == keys.quarter.trim()); });
			    });
			 });
		      });	
            	});

	        $("#quarterly").change(function(){
                    setsessionvar('taskid', $("#quarterly").val().trim(), function () {
			$("#year").load("load_years.php?taskid="+$("#quarterly").val().trim(), function() {
                            $("select#year option").each(function() { this.selected = (this.value.trim() == keys.year.trim()); });
			    $("#quarter").load("load_quarters.php?taskid="+$("#quarterly").val().trim()+"&year="+$("#year").val().trim(), function() {
                                $("select#quarter option").each(function() { this.selected = (this.value.trim() == keys.quarter.trim()); });
                            });
			});	
                    });
		});

		$("#year").change(function() {
                    setsessionvar('year', $("#year").val().trim(), function() {
                        $("#quarter").load("load_quarters.php?taskid="+$("#quarterly").val().trim()+"&year="+$("#year").val().trim(), function() {
                           $("select#quarter option").each(function() { this.selected = (this.value.trim() == keys.quarter.trim()); });
		        });
                    });
                });

                $("#quarter").change(function(){
                    setsessionvar('quarter', $("#quarter").val().trim());
                });

	        $("#cumulative").change(function() {
                        setsessionvar('taskid', $("#cumulative").val().trim());
		});

	        $("#quarterly_f").change(function() {
                        setsessionvar('taskid', $("#quarterly_f").val().trim());
		});

	        $("#quarterly_f").load("load_tasklist.php?database=budget", function() {
                        setsessionvar('reload', 'ok',  // this is just to load the values into global variable "keys"
                                 function() {   //callback
                                     $("select#quarterly_f option").each(function() { this.selected = (this.value.trim() == keys.taskid.trim()); });
                                 });
		});

                $("#cumulative_go").click(function() {
                        window.location.href = location_url+"report_cumulative.php?taskid="+$("#cumulative").val().trim();
                });

                $("#quarterly_go").click(function() {
                        window.location.href = location_url+"report_quarterly.php?taskid="+$("#quarterly").val().trim()+"&year="+$("#year").val().trim()+"&quarter="+$("#quarter").val().trim();
                });

                $("#financial_go").click(function() {
                        window.location.href = location_url+"report_expenditure.php?taskid="+$("#quarterly_f").val().trim();
                });

                $("#financialc_go").click(function() {
                        window.location.href = location_url+"report_expenditure.php?country="+$("#country").val().trim();
                });

                $("#cancel").click(function() {
                        window.location.href = location_url+"index.php";
                });

		$("select#country option").each(function() { this.selected = (this.value.trim() == '<?php echo isset($_SESSION['country']) ? $_SESSION['country'] : '' ?>'); })
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
	  <td> Financials for A – Quick overview </td>
	  <td> 
            <select id="quarterly_f">
                 <option>Loading ...</option>
            </select>
	    <input type="button" id="financial_go" value="Go"/> (By Task)
	  </td>
	</tr>
        <tr>
	  <td> Financials for A – Quick overview </td>
	  <td> 
            <select id="country">
                   <option>Angola</option>
                   <option>Botswana</option>
                   <option>Germany</option>
                   <option>Namibia</option>
                   <option>South Africa</option>
                   <option>Zambia</option>
            </select>
	    <input type="button" id="financialc_go" value="Go"/> (By Country)
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




