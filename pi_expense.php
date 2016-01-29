<?php
	include 'check_access.php';

        $taskid = array_key_exists("taskid",$_GET) ?  $_GET["taskid"] : "";
        if (empty($taskid)) {
                $taskid = array_key_exists("taskid",$_POST) ?  $_POST["taskid"] : "";
        }
        if (empty($taskid)) {
                echo "<h1>Task ID is undefined</h1>";
                die();
        }

        $year = array_key_exists("year",$_GET) ?  $_GET["year"] : "";
        if (empty($year)) {
                $year = array_key_exists("year",$_POST) ?  $_POST["year"] : "";
        }
        if (empty($year)) {
                echo "<h1>Year is undefined</h1>";
                die();
        }

        $quarter = array_key_exists("quarter",$_GET) ?  $_GET["quarter"] : "";
        if (empty($quarter)) {
                $quarter = array_key_exists("quarter",$_POST) ?  $_POST["quarter"] : "";
        }

        if (empty($quarter)) {
                echo "<h1>Quarter is undefined</h1>";
                die();
        }

?>
<!DOCTYPE html>
<html>
<head>
<meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
<title>Principle Investigator Expense Reporting</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script>
$.ajaxSetup({cache: false}); // This is to prevent caching in IE

var taskid = "<?php echo $taskid ?>";
var year = <?php echo $year ?>;
var quarter = <?php echo $quarter ?>;
var previous;
var budget;

function money(n) {
        var decPlaces = 2,
           decSeparator = ".",
           thouSeparator = ",",
           sign = n < 0 ? "-" : "",
           i = parseInt(n = Math.abs(+n || 0).toFixed(decPlaces)) + "",
           j = (j = i.length) > 3 ? j % 3 : 0;
        return sign + (j ? i.substr(0, j) + thouSeparator : "")
                + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thouSeparator)
                + (decPlaces ? decSeparator + Math.abs(n - i).toFixed(decPlaces).slice(2) : "");
};
function roundToTwo(num) {
        return money(+(Math.round(num + "e+2")  + "e-2"));
};

function display_error(message) {
	$("#submit_message").html("<span style=\"color:red\">"+message+"</span>");
	setTimeout(function() {
			$("#submit_message").html("");
		}, 3000);
}

function toEuro(amount, budget) {
	var weighted_rate =  (budget.prev_unused == 0) && (budget.received == 0) ? 1 : ((eval(budget.prev_unused) + eval(budget.received)) / ((budget.prev_unused / budget.prev_xrate) + (budget.received / budget.xrate)));
	return amount / weighted_rate;
}

function update_totals() {
	//pe = eval(budget.cum_personnel_euro) + toEuro(budget.personnel_actual, budget);
	//$("#personnel_euro").html( money(toEuro($("#personnel_local").val(), budget)) );
	//$("#personnel_euro").html( money(pe) );
	$("#personnel_euro").html( money(eval(budget.cum_personnel_euro) + toEuro($("#personnel_local").val() - budget.cum_personnel, budget)) );
	$("#personnel_available").html( money(task.personnel_budget - budget.cum_personnel_euro - toEuro($("#personnel_local").val() - budget.cum_personnel, budget)) );
	//$("#personnel_euro").html( money(toEuro($("#personnel_local").val(), budget)) );
	//$("#personnel_available").html( money(task.personnel_budget - toEuro($("#personnel_local").val(), budget)) );

	$("#investments_euro").html( money(eval(budget.cum_investments_euro) + toEuro($("#investments_local").val(), budget)) );
	$("#investments_available").html( money(task.investments_budget - budget.cum_investments_euro - toEuro($("#investments_local").val(), budget)) );
	$("#consumables_euro").html( money(eval(budget.cum_consumables_euro) + toEuro($("#consumables_local").val(), budget)) );
	$("#consumables_available").html( money(task.consumables_budget - budget.cum_consumables_euro - toEuro($("#consumables_local").val(), budget)) );
	$("#services_euro").html( money(eval(budget.cum_services_euro) + toEuro($("#services_local").val(), budget)) );
	$("#services_available").html( money(task.services_budget - budget.cum_services_euro - toEuro($("#services_local").val(), budget)) );
	$("#transport_euro").html( money(eval(budget.cum_transport_euro) + toEuro($("#transport_local").val(), budget)) );
	$("#transport_available").html( money(task.transport_budget - budget.cum_transport_euro - toEuro($("#transport_local").val(), budget)) );
	$("#admin_euro").html( money(eval(budget.cum_admin_euro) + toEuro($("#admin_local").val(), budget)) );
	$("#admin_available").html( money(task.admin_budget - budget.cum_admin_euro - toEuro($("#admin_local").val(), budget)) );

	//$("#investments_euro").html( money(budget.cum_investments_euro) );
	//$("#investments_available").html( money(task.investments_budget - budget.cum_investments_euro) );
	//$("#consumables_euro").html( money(budget.cum_consumables_euro) );
	//$("#consumables_available").html( money(task.consumables_budget - budget.cum_consumables_euro) );
	//$("#services_euro").html( money(budget.cum_services_euro) );
	//$("#services_available").html( money(task.services_budget - budget.cum_services_euro) );
	//$("#transport_euro").html( money(budget.cum_transport_euro) );
	//$("#transport_available").html( money(task.transport_budget - budget.cum_transport_euro) );
	//$("#admin_euro").html( money(budget.cum_admin_euro) );
	//$("#admin_available").html( money(task.admin_budget - budget.cum_admin_euro) );
}

function save_values(status, saving, type) {
	$("#save_1").prop("disabled",true); 
	$("#submit_1").prop("disabled",true);

	if ($("#personnel_local").val() < previous.personnel) {
		display_error("Total Personnel cannot be less than previously reported ("+money(previous.personnel)+")");
	}
	if ($("#investments_local").val() < previous.investments) {
		display_error("Total Investments cannot be less than previously reported ("+money(previous.investments)+")");
	}
	if ($("#consumables_local").val() < previous.consumables) {
		display_error("Total Consumables cannot be less than previously reported ("+money(previous.consumables)+")");
	}
	if ($("#services_local").val() < previous.services) {
		display_error("Total Services cannot be less than previously reported ("+money(previous.services)+")");
	}
	if ($("#transport_local").val() < previous.transport_local) {
		display_error("Total Transport_local cannot be less than previously reported ("+money(previous.transport_local)+")");
	}
	if ($("#admin_local").val() < previous.admin) {
		display_error("Total Admin cannot be less than previously reported ("+money(previous.admin)+")");
	}

	$.post("save_actual.php",
		{
			investments: $("#investments_local").val(), 
			personnel: $("#personnel_local").val(),
			services: $("#services_local").val(),
			transport: $("#transport_local").val(),
			consumables: $("#consumables_local").val(),
			admin: $("#admin_local").val(),
			taskid: taskid,
			year: year,
			quarter: quarter,
			status: 2
		},
		function(data, status){
			if (data == "OK") {
				$("#submit_message").html("Saved");
			} else {
				display_error(data);
			}
		});
}

$(document).ready(function(){
	// LOAD

        //$.get("load_expenses.php?taskid="+taskid+"&year="+year+"&quarter="+quarter, function(data, status){
	        //previous = JSON.parse(data);
		//$("#personnel_local").val(previous.personnel);
		//$("#investments_local").val(previous.investments);
		//$("#consumables_local").val(previous.consumables);
		//$("#services_local").val(previous.services);
		//$("#transport_local").val(previous.transport);
		//$("#admin_local").val(previous.admin);
	//});
        $.get("load_task.php?database=budget&taskid="+taskid, function(data, status){
                task = JSON.parse(data);
                $(".curr").html(task.currency+" ");
                $(".eur").html("&euro; ");

        	//previousq = quarter > 1 ? quarter - 1 : 4;
	        //previousy = quarter > 1 ? year : year - 1;

		//$.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+previousy+"&quarter="+previousq, function(data, status){
		$.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+year+"&quarter="+quarter, function(data, status){
        	        budget = JSON.parse(data);
			//$("#personnel_euro").html(money(budget.cum_personnel_euro));
			//$("#investments_euro").html(money(budget.cum_investments_euro));
			//$("#consumables_euro").html(money(budget.cum_consumables_euro));
			//$("#services_euro").html(money(budget.cum_services_euro));
			//$("#transport_euro").html(money(budget.cum_transport_euro));
			//$("#admin_euro").html(money(budget.admin));
	
			$("#personnel_local").val(eval(budget.cum_personnel));   
			$("#investments_local").val(eval(budget.cum_investments));   
			$("#consumables_local").val(eval(budget.cum_consumables));  
			$("#services_local").val(eval(budget.cum_services));   
			$("#transport_local").val(eval(budget.cum_transport));   
			$("#admin_local").val(eval(budget.cum_admin));   
                	//$("#investments_local").val(budget.investments_actual);
                	//$("#consumables_local").val(budget.consumables_actual);
                	//$("#services_local").val(budget.services_actual);
                	//$("#transport_local").val(budget.transport_actual);
                	//$("#admin_local").val(budget.admin);

			update_totals();
		});
        });
	
	$("#save_2").click(function() { save_values(2, true, "actual"); });
	$("#main").click(function() { 
			window.location.href = "http://caprivi.sasscal.org/budget/pi_main.php";
		});
        $("#logout").click(function() {
                        window.location.href = "http://caprivi.sasscal.org/budget/logout.php";
                });

	$("#personnel_local").change(function() { update_totals(); });
	$("#investments_local").change(function() { update_totals(); });
	$("#consumables_local").change(function() { update_totals(); });
	$("#services_local").change(function() { update_totals(); });
	$("#transport_local").change(function() { update_totals(); });
	$("#admin_local").change(function() { update_totals(); });
}); 
</script> 
<style  type="text/css">
table {  
width: 75%;
margin-left: auto; 
margin-right: auto; 
}

body {  
font-family: Arial, Helvetica, sans-serif;
}

td {  
padding-right: 10px;  
padding-bottom: 1px;  
padding-left: 10px;  
text-align: right;
border-spacing: 0px;
}
th {
padding-right: 10px;
padding-bottom: 1px;
padding-left: 10px;
border-spacing: 0px;
text-align: right;
color: green;
}

#submit_message {
padding-left: 10px;  
font-size: 12px;
font-weight: bold;
color: green;
}

#submit_rec_message {
padding-left: 10px;  
font-size: 12px;
font-weight: bold;
color: green;
}

.topline {
border-top:1px solid #000000;
}
td.unused {
background-color: #DDFFDD;
}
td.funds {
background-color: #FFEE77;
}
td.wrate {
background-color: #77EEFF;
}
</style></head>
<body>
<p  style="text-align: center;"><span  style="font-family: Helvetica,Arial,sans-serif; font-size: 30px;">Actual Expenses</span></p>
<p  style="text-align: center;"><span  style="font-family: Helvetica,Arial,sans-serif; font-size: 11px;">
   <!-- MENU --->
        <table border=0>
          <tr>
            <td style="text-align: left; font-size:11px">
        <?php
                echo "Logged in as ".$_SESSION['firstname']." : ".($_SESSION['access'] > 1 ? "(PI)" : "(Admin)");
        ?>
            </td>
            <td style="text-align: right">
                <input type="button" id="main" value="Main Menu"/>
                <input type="button" id="logout" value="Log Out"/>
            </td>
          </tr>
        </table>
    </span></p>

    <span id="figures">
    <table>
          <tr>
              <th>Expense</th>
              <th>Accrued Amount <span class="curr"></span></th>
              <th>Accrued Amount <span class="eur"></th>
              <th>Amount Available<span class="eur"></th>
	  </tr>
          <tr><td>Personnel: </td>
             <td><span class="curr"></span><input type="text" id="personnel_local"></td>
             <td><span class="eur"></span><span id="personnel_euro"></span></td>
             <td><span class="eur"></span><span id="personnel_available"></span></td>
	  </tr>
          <tr><td>Investments: </td>
             <td><span class="curr"></span><input type="text" id="investments_local"></td>
             <td><span class="eur"></span><span id="investments_euro"></span></td>
             <td><span class="eur"></span><span id="investments_available"></span></td>
	  </tr>
          <tr><td>Consumables: </td>
             <td><span class="curr"></span><input type="text" id="consumables_local"></td>
             <td><span class="eur"></span><span id="consumables_euro"></span></td>
             <td><span class="eur"></span><span id="consumables_available"></span></td>
	  </tr>
          <tr><td>Services: </td>
             <td><span class="curr"></span><input type="text" id="services_local"></td>
             <td><span class="eur"></span><span id="services_euro"></span></td>
             <td><span class="eur"></span><span id="services_available"></span></td>
	  </tr>
          <tr><td>Transport: </td>
             <td><span class="curr"></span><input type="text" id="transport_local"></td>
             <td><span class="eur"></span><span id="transport_euro"></span></td>
             <td><span class="eur"></span><span id="transport_available"></span></td>
	  </tr>
          <tr><td>Admin & Other: </td>
             <td><span class="curr"></span><input type="text" id="admin_local"></td>
             <td><span class="eur"></span><span id="admin_euro"></span></td>
             <td>N/A</td>
	  </tr>
          <tr style="font-weight:bold;"><td class="topline"> Total: </td>
             <td class="topline"><span class="curr"></span><span id="total_local"></span></td>
             <td class="topline"><span class="eur"></span><span id="total_euro"></span></td>
             <td class="topline"><span class="eur"></span><span id="total_available"></span></td>
          </tr>
          <tr><td>&nbsp;</td>
              <td> 
	          <span id="stat1_save">
				  <input type="button" value="Save"   id="save_1">
                                  <input type="button" value="Submit" id="submit_1">
	          </span>
		  <span id="submit_message"></span>
              </td>
          </tr>
    </table>
  </body>
</html>

