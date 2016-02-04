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
<title>Principle Investigator Budgeting & Planning</title>
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
var budgetnext;

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

		tot_estimate = eval($("#personnel_estimate").html()) + eval($("#investments_estimate").html()) + eval($("#consumables_estimate").html()) 
				+ eval($("#services_estimate").html()) + eval($("#transport_estimate").html()) ;
        	$("#total_estimate").html( money(tot_estimate));
		tot_planned = eval($("#personnel_planned").html()) + eval($("#investments_planned").html()) + eval($("#consumables_planned").html()) 
				+ eval($("#services_planned").html()) + eval($("#transport_planned").html()) ;
        	$("#total_planned").html( money(tot_planned));
		tot_local = eval($("#personnel_local").html()) + eval($("#investments_local").html()) + eval($("#consumables_local").html()) 
				+ eval($("#services_local").html()) + eval($("#transport_local").html()) + eval($("#admin_local").html()) ;
        	$("#total_local").html( money(tot_local));
}

function save_values() {
	$("#save_1").prop("disabled",true); 

	$.post("save_figures.php",
		{
			investments: $("#investments_estimate").val(), 
			personnel: $("#personnel_estimate").val(),
			services: $("#services_estimate").val(),
			transport: $("#transport_estimate").val(),
			consumables: $("#consumables_estimate").val(),

			investments_planned: $("#investments_planned").val(), 
			personnel_planned: $("#personnel_planned").val(),
			services_planned: $("#services_planned").val(),
			transport_planned: $("#transport_planned").val(),
			consumables_planned: $("#consumables_planned").val(),

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

       	nextq = quarter < 4 ? quarter + 1 : 1;
       	nexty = quarter < 4 ? year : year + 1;
       	previousq = quarter > 1 ? quarter - 1 : 4;
     	previousy = quarter > 1 ? year : year - 1;

        $.get("load_task.php?database=budget&taskid="+taskid, function(data, status){
                task = JSON.parse(data);
                $(".curr").html(task.currency+" ");
                $(".eur").html("&euro; ");
	});


	$.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+year+"&quarter="+quarter, function(data, status){
       	        budget = JSON.parse(data);
	
		$("#personnel_estimate").val(eval(budget.personnel));   
		$("#investments_estimate").val(eval(budget.investments));   
		$("#consumables_estimate").val(eval(budget.consumables));  
		$("#services_estimate").val(eval(budget.services));   
		$("#transport_estimate").val(eval(budget.transport));   
		tot_estimate = eval(budget.personnel) + eval(budget.investments) + eval(budget.consumables) 
				+ eval(budget.services) + eval(budget.transport) ;
        	$("#total_estimate").html( money(tot_estimate));
        });

	$.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+nexty+"&quarter="+nextq, function(data, status){
       	        budgetnext = JSON.parse(data);
	
		$("#personnel_planned").val(eval(budgetnext.personnel_planned));   
		$("#investments_planned").val(eval(budgetnext.investments_planned));   
		$("#consumables_planned").val(eval(budgetnext.consumables_planned));  
		$("#services_planned").val(eval(budgetnext.services_planned));   
		$("#transport_planned").val(eval(budgetnext.transport_planned));   
		tot_planned = eval(budgetnext.personnel_planned) + eval(budgetnext.investments_planned) + eval(budgetnext.consumables_planned) 
				+ eval(budgetnext.services_planned) + eval(budgetnext.transport_planned) ;
        	$("#total_planned").html( money(tot_planned));
        });

	$.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+previousy+"&quarter="+previousq, function(data, status){
               	budgetprevious = JSON.parse(data);
		$("#personnel_local").html(money(budgetprevious.personnel_actual));   
		$("#investments_local").html(money(budgetprevious.investments_actual));   
		$("#consumables_local").html(money(budgetprevious.consumables_actual));  
		$("#services_local").html(money(budgetprevious.services_actual));   
		$("#transport_local").html(money(budgetprevious.transport_actual));   
		$("#admin_local").html(money(budgetprevious.admin));   
		tot_local = eval(budgetprevious.personnel_actual) + eval(budgetprevious.investments_actual) + eval(budgetprevious.consumables_actual) 
				+ eval(budgetprevious.services_actual) + eval(budgetprevious.transport_actual) + eval(budgetprevious.admin) ;
        	$("#total_local").html( money(tot_local));
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

	$("#actual").append("  Q"+previousq+" "+previousy);
	$("#estimated").append("  Q"+quarter+" "+year);
	$("#planned").append("  Q"+nextq+" "+nexty);
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
<p  style="text-align: center;"><span  style="font-family: Helvetica,Arial,sans-serif; font-size: 30px;" id="title">Budgeting & Planning</span></p>
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
              <th><span id="actual">Actual Expenditure </span></th>
              <th><span id="estimated">Estimated Expenditure </span></th>
              <th><span id="planned">Planned Expenditure </span></th>
	  </tr>
          <tr>
	     <td class="personnel">Personnel: </td>
             <td><span class="curr"></span><span class="personnel" id="personnel_local"></span></td>
             <td><span class="curr"></span><input type="text" class="personnel" id="personnel_estimate"></td>
             <td><span class="curr"></span><input type="text" class="personnel" id="personnel_planned"></td>
	  </tr>
          <tr>
	     <td class="investments">investments: </td>
             <td><span class="curr"></span><span class="investments" id="investments_local"></span></td>
             <td><span class="curr"></span><input type="text" class="investments" id="investments_estimate"></td>
             <td><span class="curr"></span><input type="text" class="investments" id="investments_planned"></td>
	  </tr>
          <tr>
	     <td class="services">services: </td>
             <td><span class="curr"></span><span class="services" id="services_local"></span></td>
             <td><span class="curr"></span><input type="text" class="services" id="services_estimate"></td>
             <td><span class="curr"></span><input type="text" class="services" id="services_planned"></td>
	  </tr>
          <tr>
	     <td class="consumables">consumables: </td>
             <td><span class="curr"></span><span class="consumables" id="consumables_local"></span></td>
             <td><span class="curr"></span><input type="text" class="consumables" id="consumables_estimate"></td>
             <td><span class="curr"></span><input type="text" class="consumables" id="consumables_planned"></td>
	  </tr>
          <tr>
	     <td class="transport">transport: </td>
             <td><span class="curr"></span><span class="transport" id="transport_local"></span></td>
             <td><span class="curr"></span><input type="text" class="transport" id="transport_estimate"></td>
             <td><span class="curr"></span><input type="text" class="transport" id="transport_planned"></td>
	  </tr>
          <tr>
	     <td class="admin">admin: </td>
             <td><span class="curr"></span><span class="admin" id="admin_local"></span></td>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
	  </tr>

          <tr style="font-weight:bold;"><td class="topline"> Total: </td>
             <td class="topline"><span class="curr"></span><span id="total_local"></span></td>
             <td class="topline"><span class="curr"></span><span id="total_estimate"></span></td>
             <td class="topline"><span class="curr"></span><span id="total_planned"></span></td>
          </tr>
          <tr><td>&nbsp;</td>
	      <td>&nbsp;</td>
              <td> 
	          <span id="stat1_save">
				  <input type="button" value="Save"   id="save_1">
	          </span>
              </td>
	      <td style="text-align: left;">
		  <span id="submit_message"></span>
	      </td>
          </tr>
    </table>
  </body>
</html>

