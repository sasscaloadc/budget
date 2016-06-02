<?php
  include 'check_access.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Principle Investigator Main Menu</title>
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

		var d = new Date();	
		switch(d.getMonth()) {
			case 11: case 0: case 1: thisquarter = 1;
				break; 
			case 2: case 3: case 4: thisquarter = 2;
				break; 
			case 5: case 6: case 7: thisquarter = 3;
				break; 
			case 8: case 9: case 10: thisquarter = 4;
				break; 
		} // NOTE: reporting switches to next quarter in the last month of the current quarter. This has bearing on the update_budget() trigger in the database.

		thisyear = d.getFullYear();
		nextq = thisquarter < 4 ? thisquarter + 1 : 1;
		previousq = thisquarter > 1 ? thisquarter - 1 : 4;
		nexty = thisquarter < 4 ? thisyear : thisyear + 1;
		previousy = thisquarter > 1 ? thisyear : thisyear - 1;

		$("#expense_quarter").html("(Q"+previousq+" "+previousy+")");
		$("#next_quarter").html("(Q"+thisquarter+" "+thisyear+", Q"+nextq+" "+nexty+")");

                $("#report_go").click(function() {
                        window.location.href = location_url+"pi_expense.php?taskid="+$("#tasks").val().trim()+"&year="+previousy+"&quarter="+previousq;
                });

                $("#budget_go").click(function() {
                        window.location.href = location_url+"pi_budget.php?taskid="+$("#tasks").val().trim()+"&year="+thisyear+"&quarter="+thisquarter;
                });

                $("#reports_go").click(function() {
                        window.location.href = location_url+"report_cumulative.php?taskid="+$("#tasks").val().trim();
                });

                $("#expenditure_go").click(function() {
                        window.location.href = location_url+"report_expenditure.php?taskid="+$("#tasks").val().trim();
                });

                $("#request_go").click(function() {
                        window.location.href = location_url+"report_request.php?taskid="+$("#tasks").val().trim()+"&year="+thisyear+"&quarter="+thisquarter;
                });

                $("#edit_go").click(function() {
                        window.location.href = location_url+"edit_completion.php";
                });

                $("#cancel").click(function() {
                        window.location.href = location_url+"index.php";
                });
		
        	$("#tasks").load("load_tasklist.php?database=budget", function() {
			setsessionvar('reload', 'ok',  // this is just to load the values into global variable "keys"
					function() {   //callback
						$("select#tasks option").each(function() { this.selected = (this.value.trim() == keys.taskid.trim()); });
					});
		});

		$("#tasks").change(function() {
			setsessionvar('taskid', $("#tasks").val().trim());
		});

	        $("#logout").click(function() {
                        window.location.href = location_url+"logout.php";
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
	Main Menu</span></p>
    <p  style="text-align: center;"><span  style="font-family: Helvetica,Arial,sans-serif; font-size: 11px;">
    <!-- MENU --->
        <table border=0 width="100%">
          <tr>
            <td style="text-align: left; font-size:11px">
        <?php
                echo "Logged in as ".$_SESSION['firstname']." : ".($_SESSION['access'] > 1 ? "(PI)" : "(Admin)");
        ?>
            </td>
            <td style="text-align: right">
                <input type="button" id="logout" value="Log Out"/>
            </td>
          </tr>
        </table>
     </span></p>

    <p>
    <table border="0">
      <tbody id="task">
        <tr>
	  <td>
	    Task:
	    <select id="tasks">
                 <option>Loading ...</option>
            </select>
	  </td>
	  <td>&nbsp;</td>
	</tr>
	<tr>
	  <td> Submit Expenditure Report <span id="expense_quarter"></span>
	  </td>
	  <td> 
	    <input type="button" id="report_go" value="Go"/>
	  </td>
	</tr>
        <tr>
	  <td> Budget & Plan <span id="next_quarter"></span></td>
	  <td> 
	    <input type="button" id="budget_go" value="Go"/>
	  </td>
	</tr>
        <tr>
	  <td> Task Cumulative Report </td>
	  <td> 
	    <input type="button" id="reports_go" value="Go"/>
	  </td>
	</tr>
        <tr>
	  <td> Financials for A – Quick overview </td>
	  <td> 
	    <input type="button" id="expenditure_go" value="Go"/>
	  </td>
	</tr>
        <tr>
	  <td> Print Request for Payment </td>
	  <td> 
	    <input type="button" id="request_go" value="Go"/>
	  </td>
	</tr>
        <tr>
	  <td> Edit Task Completion </td>
	  <td> 
	    <input type="button" id="edit_go" value="Go"/>
	  </td>
	</tr>
    </table>
    </p>
    <p>
   <!-- ---------------------------
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
    ----------------------------- -->
    </p>
   </div>
  </body>
</html>




