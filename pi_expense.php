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
<title>Principle Investigator Expenditure Reporting</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script>
$.ajaxSetup({cache: false}); // This is to prevent caching in IE

var location_url = "<?php echo $location_url ?>";

var taskid = "<?php echo $taskid ?>";
var year = <?php echo $year ?>;
var quarter = <?php echo $quarter ?>;
var previous;
var budget;
var budgetprevious;

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
	$("#personnel_euro").html( money(eval(budget.cum_personnel_euro) + toEuro($("#personnel_local").val() - budget.cum_personnel, budget)) );
	$("#personnel_available").html( money(task.personnel_budget - budget.cum_personnel_euro - toEuro($("#personnel_local").val() - budget.cum_personnel, budget)) );
	$("#investments_euro").html( money(eval(budget.cum_investments_euro) + toEuro($("#investments_local").val() - budget.cum_investments, budget)) );
	$("#investments_available").html( money(task.investments_budget - budget.cum_investments_euro - toEuro($("#investments_local").val() - budget.cum_investments, budget)) );
	$("#consumables_euro").html( money(eval(budget.cum_consumables_euro) + toEuro($("#consumables_local").val() - budget.cum_consumables, budget)) );
	$("#consumables_available").html( money(task.consumables_budget - budget.cum_consumables_euro - toEuro($("#consumables_local").val() - budget.cum_consumables, budget)) );
	$("#services_euro").html( money(eval(budget.cum_services_euro) + toEuro($("#services_local").val() - budget.cum_services, budget)) );
	$("#services_available").html( money(task.services_budget - budget.cum_services_euro - toEuro($("#services_local").val() - budget.cum_services, budget)) );
	$("#transport_euro").html( money(eval(budget.cum_transport_euro) + toEuro($("#transport_local").val() - budget.cum_transport, budget)) );
	$("#transport_available").html( money(task.transport_budget - budget.cum_transport_euro - toEuro($("#transport_local").val() - budget.cum_transport, budget)) );
	$("#admin_euro").html( money(eval(budget.cum_admin_euro) + (($("#admin_local").val() - budget.cum_admin) / budget.xrate)) );
	$("#admin_available").html( money(task.admin_budget - budget.cum_admin_euro - toEuro($("#admin_local").val() - budget.cum_admin, budget)) );

	tot_local = eval($("#personnel_local").val()) + eval($("#investments_local").val()) + eval($("#consumables_local").val()) + eval($("#services_local").val())
						      + eval($("#transport_local").val()) + eval($("#admin_local").val()) ; 
	$("#total_local").html(	money(tot_local));
	cum_total = eval(budget.cum_personnel) + eval(budget.cum_investments) + eval(budget.cum_consumables) + eval(budget.cum_services) + eval(budget.cum_investments) + eval(budget.cum_admin);
	cum_total_euro = eval(budget.cum_personnel_euro) + eval(budget.cum_investments_euro) + eval(budget.cum_consumables_euro) 
						   + eval(budget.cum_services_euro) + eval(budget.cum_investments_euro) + eval(budget.cum_admin_euro);
	$("#total_euro").html( money(eval(cum_total_euro) + toEuro(tot_local - cum_total, budget)) );
	$("#total_available").html(  money(task.budget - cum_total_euro - toEuro(tot_local - cum_total, budget)) );
}

function check_amount(exp, testval) {
	if (eval($("#"+exp+"_local").val()) < eval(testval)) {
		$(".eur").html("");	
		$("#"+exp+"_euro").html("<span style=\"color:red; font-size:12px\">This amount is LESS than previously reported ("+money(testval)+")</span>");
		setTimeout(function() {
			$(".eur").html("&euro; ");
			$("."+exp).css('color', '#FF0000');
			update_totals();
		}, 3000);
	} else {
		update_totals();
		$("."+exp).css('color', '#000000');
	}
}

function save_values() {
	$("#save_1").prop("disabled",true); 

	$.post("save_actual.php",
		{
			investments: $("#investments_local").val() - budgetprevious.cum_investments, 
			personnel: $("#personnel_local").val() - budgetprevious.cum_personnel,
			services: $("#services_local").val() - budgetprevious.cum_services,
			transport: $("#transport_local").val() - budgetprevious.cum_transport,
			consumables: $("#consumables_local").val() - budgetprevious.cum_consumables,
			admin: $("#admin_local").val() - budgetprevious.cum_admin,
			taskid: taskid,
			year: year,
			quarter: quarter
		},
		function(data, status){
			if (data == "OK") {
				$("#submit_message").html("Saved");
			} else {
				display_error(data);
			}
                        setTimeout(function() {
                                        $("#submit_message").html("");
					$("#save_1").prop("disabled",false); 
                                }, 3000);
 
		});
}

$(document).ready(function(){
	// LOAD

        $.get("load_task.php?database=budget&taskid="+taskid, function(data, status){
                task = JSON.parse(data);
                $(".curr").html(task.currency+" ");
                $(".eur").html("&euro; ");


		$.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+year+"&quarter="+quarter, function(data, status){
        	        budget = JSON.parse(data);
	
			$("#personnel_local").val(eval(budget.cum_personnel));   
			$("#investments_local").val(eval(budget.cum_investments));   
			$("#consumables_local").val(eval(budget.cum_consumables));  
			$("#services_local").val(eval(budget.cum_services));   
			$("#transport_local").val(eval(budget.cum_transport));   
			$("#admin_local").val(eval(budget.cum_admin));   

        		previousq = quarter > 1 ? quarter - 1 : 4;
	        	previousy = quarter > 1 ? year : year - 1;
			$.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+previousy+"&quarter="+previousq, function(data, status){
        	        	budgetprevious = JSON.parse(data);
				check_amount("personnel", budgetprevious.cum_personnel, 1);
				check_amount("investments", budgetprevious.cum_investments, 2);
				check_amount("consumables", budgetprevious.cum_consumables, 3);
				check_amount("services", budgetprevious.cum_services, 4);
				check_amount("transport", budgetprevious.cum_transport, 5);
				check_amount("admin", budgetprevious.cum_admin, 6);
			});
		});
        });

	$("#save_1").click(function() { save_values(); });
	$("#main").click(function() { 
			window.location.href = location_url+"pi_main.php";
		});
        $("#logout").click(function() {
                        window.location.href = location_url+"logout.php";
                });

	$("#personnel_local").change(function() { check_amount("personnel", budgetprevious.cum_personnel);});
	$("#investments_local").change(function() { check_amount("investments", budgetprevious.cum_investments);});
	$("#consumables_local").change(function() { check_amount("consumables", budgetprevious.cum_consumables);});
	$("#services_local").change(function() { check_amount("services", budgetprevious.cum_services);});
	$("#transport_local").change(function() { check_amount("transport", budgetprevious.cum_transport);});
	$("#admin_local").change(function() { check_amount("admin", budgetprevious.cum_admin);});

	$("#title").append("  Q"+quarter+" "+year);
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
<p  style="text-align: center;"><span  style="font-family: Helvetica,Arial,sans-serif; font-size: 30px;" id="title">Actual Expenditure</span></p>
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
              <th>Category</th>
              <th>Total Expenditure To Date <span class="curr"></span></th>
              <th>Total Expenditure To Date <span class="eur"></th>
              <th>Amount Available<span class="eur"></th>
	  </tr>
          <tr><span id="personnel">
	     <td class="personnel">Personnel: </td>
             <td><span class="curr"></span><input type="text" class="personnel" id="personnel_local"></td>
             <td><span class="eur"></span><span class="personnel" id="personnel_euro"></span></td>
             <td><span class="eur"></span><span class="personnel" id="personnel_available"></span></td>
	     </span>
	  </tr>
          <tr><td class="investments">Investments: </td>
             <td><span class="curr"></span><input class="investments" type="text" id="investments_local"></td>
             <td><span class="eur"></span><span class="investments" id="investments_euro"></span></td>
             <td><span class="eur"></span><span class="investments" id="investments_available"></span></td>
	  </tr>
          <tr><td class="consumables">Consumables: </td>
             <td><span class="curr"></span><input class="consumables" type="text" id="consumables_local"></td>
             <td><span class="eur"></span><span class="consumables" id="consumables_euro"></span></td>
             <td><span class="eur"></span><span class="consumables" id="consumables_available"></span></td>
	  </tr>
          <tr><td class="services">Services: </td>
             <td><span class="curr"></span><input class="services" type="text" id="services_local"></td>
             <td><span class="eur"></span><span class="services" id="services_euro"></span></td>
             <td><span class="eur"></span><span class="services" id="services_available"></span></td>
	  </tr>
          <tr><td class="transport">Transport: </td>
             <td><span class="curr"></span><input class="transport" type="text" id="transport_local"></td>
             <td><span class="eur"></span><span class="transport" id="transport_euro"></span></td>
             <td><span class="eur"></span><span class="transport" id="transport_available"></span></td>
	  </tr>
          <tr><td class="admin">Admin & Other: </td>
             <td><span class="curr"></span><input class="admin" type="text" id="admin_local"></td>
             <td><span class="eur"></span><span class="admin" id="admin_euro"></span></td>
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
	          </span>
              </td>
	      <td colspan="2" style="text-align: left;">
		  <span id="submit_message"></span>
	      </td>
          </tr>
    </table>
  </body>
</html>

