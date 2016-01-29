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
        $(document).ready(function(){

		var d = new Date();	
		switch(d.getMonth()) {
			case 0: case 1: case 2: thisquarter = 1;
				break; 
			case 3: case 4: case 5: thisquarter = 2;
				break; 
			case 6: case 7: case 8: thisquarter = 3;
				break; 
			case 9: case 10: case 11: thisquarter = 4;
				break; 
		}
		thisyear = d.getFullYear();
		nextq = thisquarter < 4 ? thisquarter + 1 : 1;
		previousq = thisquarter > 1 ? thisquarter - 1 : 4;
		//nextnextq = nextq < 4 ? nextq + 1 : 1;
		nexty = thisquarter < 4 ? thisyear : thisyear + 1;
		previousy = thisquarter > 1 ? thisyear : thisyear - 1;
		//nextnexty = nextq < 4 ? nexty : nexty + 1;

		$("#expense_quarter").html("(Q"+previousq+" "+previousy+")");
		$("#next_quarter").html("(Q"+thisquarter+" "+thisyear+", Q"+nextq+" "+nexty+")");

                $("#report_go").click(function() {
                        window.location.href = "http://caprivi.sasscal.org/budget/pi_expense.php?taskid="+$("#tasks").val().trim()+"&year="+previousy+"&quarter="+previousq;
                });

                $("#budget_go").click(function() {
                        window.location.href = "http://caprivi.sasscal.org/budget/report_quarterly.php?taskid="+$("#quarterly").val().trim()+"&year="+$("#year").val().trim()+"&quarter="+$("#quarter").val().trim();
                });

                $("#reports_go").click(function() {
                        window.location.href = "http://caprivi.sasscal.org/budget/report_quarterly.php?taskid="+$("#quarterly").val().trim()+"&year="+$("#year").val().trim()+"&quarter="+$("#quarter").val().trim();
                });

                $("#cancel").click(function() {
                        window.location.href = "http://caprivi.sasscal.org/budget/index.php";
                });
		
        	$("#tasks").load("load_tasklist.php?database=budget");

	        $("#logout").click(function() {
                        window.location.href = "http://caprivi.sasscal.org/budget/logout.php";
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
    <table  border="0">
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
	  <td> Submit Expense Report <span id="expense_quarter"></span>
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
	  <td> View Reports </td>
	  <td> 
	    <input type="button" id="reports_go" value="Go"/>
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




