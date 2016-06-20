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
var saved;

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
	$("#personnel_euro").html( money(eval(budget.cum_personnel_euro) + toEuro($("#personnel_local").val(), budget)) );
	$("#personnel_available").html( money(task.personnel_budget - budget.cum_personnel_euro - toEuro($("#personnel_local").val() , budget)) );
	$("#personnel_inc").html( money(eval(budget.cum_personnel) + eval($("#personnel_local").val())) );

	$("#investments_euro").html( money(eval(budget.cum_investments_euro) + toEuro($("#investments_local").val() , budget)) );
	$("#investments_available").html( money(task.investments_budget - budget.cum_investments_euro - toEuro($("#investments_local").val() , budget)) );
	$("#investments_inc").html( money(eval(budget.cum_investments) + eval($("#investments_local").val())) );

	$("#consumables_euro").html( money(eval(budget.cum_consumables_euro) + toEuro($("#consumables_local").val() , budget)) );
	$("#consumables_available").html( money(task.consumables_budget - budget.cum_consumables_euro - toEuro($("#consumables_local").val() , budget)) );
	$("#consumables_inc").html( money(eval(budget.cum_consumables) + eval($("#consumables_local").val())) );

	$("#services_euro").html( money(eval(budget.cum_services_euro) + toEuro($("#services_local").val() , budget)) );
	$("#services_available").html( money(task.services_budget - budget.cum_services_euro - toEuro($("#services_local").val() , budget)) );
	$("#services_inc").html( money(eval(budget.cum_services) + eval($("#services_local").val())) );

	$("#transport_euro").html( money(eval(budget.cum_transport_euro) + toEuro($("#transport_local").val() , budget)) );
	$("#transport_available").html( money(task.transport_budget - budget.cum_transport_euro - toEuro($("#transport_local").val() , budget)) );
	$("#transport_inc").html( money(eval(budget.cum_transport) + eval($("#transport_local").val())) );

	//$("#admin_euro").html( money(eval(budget.cum_admin_euro) + (($("#admin_local").val() ) / budget.xrate)) );
	$("#admin_euro").html( money(eval(budget.cum_admin_euro) + toEuro($("#admin_local").val() , budget)) );
	$("#admin_available").html( money(task.admin_budget - budget.cum_admin_euro - toEuro($("#admin_local").val() , budget)) );
	$("#admin_inc").html( money(eval(budget.cum_admin) + eval($("#admin_local").val())) );


	tot_local = eval($("#personnel_local").val()) + eval($("#investments_local").val()) + eval($("#consumables_local").val()) 
                  + eval($("#services_local").val()) + eval($("#transport_local").val()) + eval($("#admin_local").val()) ; 
	cum_total = eval(budget.cum_personnel) + eval(budget.cum_investments) + eval(budget.cum_consumables) 
                  + eval(budget.cum_services) + eval(budget.cum_transport) + eval(budget.cum_admin);
	cum_total_euro = eval(budget.cum_personnel_euro) + eval(budget.cum_investments_euro) + eval(budget.cum_consumables_euro) 
	 	       + eval(budget.cum_services_euro) + eval(budget.cum_transport_euro) + eval(budget.cum_admin_euro);

	$("#total_previous").html(money(cum_total));
	$("#total_local").html(	money(tot_local));
	$("#total_inc").html(	money(tot_local + cum_total));
	$("#total_euro").html( money(eval(cum_total_euro) + toEuro(tot_local , budget)) );
	$("#total_available").html(  money(task.budget - cum_total_euro - toEuro(tot_local , budget)) );
	return task.budget - cum_total_euro - toEuro(tot_local , budget);
}

function save_values(redir) {
	$("#save_1").prop("disabled",true); 
	update_totals();

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
			quarter: quarter
		},
		function(data, status){
			if (data == "OK") {
				$("#submit_message").html("Saved");
				saved = true;
				if (redir) {
         		               window.location.href = redir;
				}
			} else {
				display_error(data);
			}
                        setTimeout(function() {
                                        $("#submit_message").html("");
					$("#save_1").prop("disabled",false); 
					return true;
                                }, 3000);
 
		});
}

function check_amount(inputbox) {
	savedstatus = saved;
	saved = false;
	if (update_totals() < 0) {
		alert("Cannot declare expenditure larger than available budget");
		inputbox.val(0);
		setTimeout(function() { inputbox.focus(); }, 100);
		update_totals();
		saved = savedstatus;
	} 
}

$(document).ready(function(){
	// LOAD

	saved = true;

        $.get("load_task.php?database=budget&taskid="+taskid, function(data, status){
                task = JSON.parse(data);
                $(".curr").html(task.currency+" ");
                $(".eur").html("&euro; ");


		$.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+year+"&quarter="+quarter, function(data, status){
        	        budget = JSON.parse(data);
	
			$("#personnel_local").val(eval(budget.personnel_actual));   
			$("#investments_local").val(eval(budget.investments_actual));   
			$("#consumables_local").val(eval(budget.consumables_actual));  
			$("#services_local").val(eval(budget.services_actual));   
			$("#transport_local").val(eval(budget.transport_actual));   
			$("#admin_local").val(eval(budget.admin));   

        		previousq = quarter > 1 ? quarter - 1 : 4;
	        	previousy = quarter > 1 ? year : year - 1;
			$.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+previousy+"&quarter="+previousq, function(data, status){
        	        	budgetprevious = JSON.parse(data);

				$("#personnel_previous").html(budgetprevious.cum_personnel);
				$("#investments_previous").html(budgetprevious.cum_investments);
				$("#consumables_previous").html(budgetprevious.cum_consumables);
				$("#services_previous").html(budgetprevious.cum_services);
				$("#transport_previous").html(budgetprevious.cum_transport);
				$("#admin_previous").html(budgetprevious.cum_admin);

				update_totals();
			});
		});
        });

	$("#save_1").click(function() { save_values(); });
	$("#main").click(function() { 
			if (!saved) {
				if (confirm("Would you like to save your changes?")) {
					save_values(location_url+"pi_main.php");
				} else {
					window.location.href = location_url+"pi_main.php";
				}
			} else {
				window.location.href = location_url+"pi_main.php";
			}
		});
        $("#logout").click(function() {
                        if (!saved) {
                                if (confirm("Would you like to save your changes?")) {
                                        save_values(location_url+"logout.php");
                                } else {
					window.location.href = location_url+"logout.php";
				}
                        } else {
				window.location.href = location_url+"logout.php";
			}
                });


	$("#personnel_local").change(function() { check_amount($("#personnel_local")); });
	$("#investments_local").change(function() { check_amount($("#investments_local")); });
	$("#consumables_local").change(function() { check_amount($("#consumables_local"));  });
	$("#services_local").change(function() { check_amount($("#services_local"));  });
	$("#transport_local").change(function() { check_amount($("#transport_local"));  });
	$("#admin_local").change(function() { check_amount($("#admin_local"));  });

	$("#title").append("  Q"+quarter+" "+year);

	$("#thisquarter").html(quarter);
	$("#lastquarter").html(quarter > 1 ? quarter - 1 : 4);
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
.blue, .eur {
color: #3333FF;
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
              <th>Total <span class="curr"></span> End Q<span id="lastquarter"></span></th>
              <th>Expenditure Q<span id="thisquarter"></span> <span class="curr"></span></th>
              <th>Total To Date <span class="curr"></th>
              <th><span class="blue">Total To Date <span class="eur"></span></th>
              <th><span class="blue">Amount Available <span class="eur"></span></th>
	  </tr>
          <tr><span id="personnel">
	     <td> Personnel: </td>
             <td><span class="curr"></span><span id="personnel_previous"></span></td>
             <td><span class="curr"></span><input type="text" id="personnel_local" size="10"></td>
             <td><span class="curr"></span><span id="personnel_inc"></span></td>
             <td class="blue"><span class="eur"></span><span id="personnel_euro"></span></td>
             <td class="blue"><span class="eur"></span><span id="personnel_available"></span></td>
	     </span>
	  </tr>
          <tr><td> Investments: </td>
             <td><span class="curr"></span><span id="investments_previous"></span></td>
             <td><span class="curr"></span><input type="text" id="investments_local" size="10"></td>
             <td><span class="curr"></span><span id="investments_inc"></span></td>
             <td class="blue"><span class="eur"></span><span id="investments_euro"></span></td>
             <td class="blue"><span class="eur"></span><span id="investments_available"></span></td>
	  </tr>
          <tr><td> Consumables: </td>
             <td><span class="curr"></span><span id="consumables_previous"></span></td>
             <td><span class="curr"></span><input type="text" id="consumables_local" size="10"></td>
             <td><span class="curr"></span><span id="consumables_inc"></span></td>
             <td class="blue"><span class="eur"></span><span id="consumables_euro"></span></td>
             <td class="blue"><span class="eur"></span><span id="consumables_available"></span></td>
	  </tr>
          <tr><td> Services: </td>
             <td><span class="curr"></span><span id="services_previous"></span></td>
             <td><span class="curr"></span><input type="text" id="services_local" size="10"></td>
             <td><span class="curr"></span><span id="services_inc"></span></td>
             <td class="blue"><span class="eur"></span><span id="services_euro"></span></td>
             <td class="blue"><span class="eur"></span><span id="services_available"></span></td>
	  </tr>
          <tr><td> Transport: </td>
             <td><span class="curr"></span><span id="transport_previous"></span></td>
             <td><span class="curr"></span><input type="text" id="transport_local" size="10"></td>
             <td><span class="curr"></span><span id="transport_inc"></span></td>
             <td class="blue"><span class="eur"></span><span id="transport_euro"></span></td>
             <td class="blue"><span class="eur"></span><span id="transport_available"></span></td>
	  </tr>
          <tr><td> Admin & Other: </td>
             <td><span class="curr"></span><span id="admin_previous"></span></td>
             <td><span class="curr"></span><input type="text" id="admin_local" size="10"></td>
             <td><span class="curr"></span><span id="admin_inc"></span></td>
             <td class="blue"><span class="eur"></span><span id="admin_euro"></span></td>
             <td class="blue">N/A</td>
	  </tr>
          <tr style="font-weight:bold;"><td class="topline"> Total: </td>
             <td class="topline"><span class="curr"></span><span id="total_previous"></span></td>
             <td class="topline"><span class="curr"></span><span id="total_local"></span></td>
             <td class="topline"><span class="curr"></span><span id="total_inc"></span></td>
             <td class="topline"><span class="eur"></span><span class="blue" id="total_euro"></span></td>
             <td class="topline"><span class="eur"></span><span class="blue" id="total_available"></span></td>
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

