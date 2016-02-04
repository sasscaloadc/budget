<?php
require_once("db.php");
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
    <title>Request For Payment</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <style  type="text/css">
body { 
	font-family: arial, helvetica, verdana;
	font-size: 11px;
}
table {  
  border-spacing: 0px;
}

p {  -webkit-border-radius: 0px;
  border-radius: 0px;  
  border-spacing: 0px;
}

td {  
  border-spacing: 0px;  
  border-collapse: collapse;
  text-align: right;
}

th {
  font-weight: bold;
}
.total {
  font-weight: bold;
}
#title {
  font-weight: bold;
  font-size: 14px;
}
#period {
  font-weight: bold;
  font-size: 14px;
  font-style: italic;
}
.bhead {
  text-align: left;
  font-weight: bold;
}
</style>
<script>
$.ajaxSetup({cache: false}); // This is to preven caching in IE

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
function toEuro(amount, budget) {
        var weighted_rate =  (budget.prev_unused == 0) && (budget.received == 0) ? 1 : ((eval(budget.prev_unused) + eval(budget.received)) / ((budget.prev_unused / budget.prev_xrate) + (budget.received / budget.xrate)));
        return amount / weighted_rate;
}

$(document).ready(function(){
        // LOAD

        nextq = quarter < 4 ? quarter + 1 : 1;
        nexty = quarter < 4 ? year : year + 1;
        previousq = quarter > 1 ? quarter - 1 : 4;
        previousy = quarter > 1 ? year : year - 1;
        var tot_total = 0;

        $.get("load_task.php?database=budget&taskid="+taskid, function(data, status){
                task = JSON.parse(data);
		$("#xrate").html(task.currency + " " + task.localxrate);
		$("#repdate").html(task.localxrateupdated);
        });


        $.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+year+"&quarter="+quarter, function(data, status){
                budget = JSON.parse(data);

                $("#personnel_estimate").html(money(toEuro(budget.personnel, budget)));
                $("#investments_estimate").html(money(toEuro(budget.investments, budget)));
                $("#consumables_estimate").html(money(toEuro(budget.consumables, budget)));
                $("#services_estimate").html(money(toEuro(budget.services, budget)));
                $("#transport_estimate").html(money(toEuro(budget.transport, budget)));
                tot_estimate = eval(toEuro(budget.personnel, budget)) + eval(toEuro(budget.investments, budget)) + eval(toEuro(budget.consumables, budget))
                                + eval(toEuro(budget.services, budget)) + eval(toEuro(budget.transport, budget)) ;
                $("#total_estimate").html( money(tot_estimate));
		tot_total += tot_estimate;
	

        	$.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+nexty+"&quarter="+nextq, function(data, status){
                	budgetnext = JSON.parse(data);
	
                	$("#personnel_planned").html(money(toEuro(budgetnext.personnel_planned, budgetnext)));
                	$("#investments_planned").html(money(toEuro(budgetnext.investments_planned, budgetnext)));
                	$("#consumables_planned").html(money(toEuro(budgetnext.consumables_planned, budgetnext)));
                	$("#services_planned").html(money(toEuro(budgetnext.services_planned, budgetnext)));
                	$("#transport_planned").html(money(toEuro(budgetnext.transport_planned, budgetnext)));
                	tot_planned = eval(toEuro(budgetnext.personnel_planned, budgetnext)) + eval(toEuro(budgetnext.investments_planned, budgetnext)) 
                               	+ eval(toEuro(budgetnext.consumables_planned, budgetnext))
                                	+ eval(toEuro(budgetnext.services_planned, budgetnext)) + eval(toEuro(budgetnext.transport_planned, budgetnext)) ;
                	$("#total_planned").html( money(tot_planned));
			tot_total += tot_planned;
	
        		$.get("load_figures_data.php?database=budget&taskid="+taskid+"&year="+previousy+"&quarter="+previousq, function(data, status){
                		budgetprevious = JSON.parse(data);
                		$("#personnel_local").html(money(budgetprevious.cum_personnel_euro));
                		$("#investments_local").html(money(budgetprevious.cum_investments_euro));
                		$("#consumables_local").html(money(budgetprevious.cum_consumables_euro));
                		$("#services_local").html(money(budgetprevious.cum_services_euro));
                		$("#transport_local").html(money(budgetprevious.cum_transport_euro));
                		$("#admin_local").html(money(budgetprevious.cum_admin_euro));
                		tot_local = eval(budgetprevious.cum_personnel_euro) + eval(budgetprevious.cum_investments_euro) + eval(budgetprevious.cum_consumables_euro)
                                		+ eval(budgetprevious.cum_services_euro) + eval(budgetprevious.cum_transport_euro) + eval(budgetprevious.cum_admin_euro) ;
                		$("#total_local").html( money(tot_local));
				tot_total += tot_local;

				
                		$("#personnel_total").html(money(eval(budgetprevious.cum_personnel_euro) + eval(budgetnext.personnel_planned) + eval(budget.personnel)));
                		$("#investments_total").html(money(eval(budgetprevious.cum_investments_euro) + eval(budgetnext.investments_planned) + eval(budget.investments)));
                		$("#consumables_total").html(money(eval(budgetprevious.cum_consumables_euro) + eval(budgetnext.consumables_planned) + eval(budget.consumables)));
                		$("#services_total").html(money(eval(budgetprevious.cum_services_euro) + eval(budgetnext.services_planned) + eval(budget.services)));
                		$("#transport_total").html(money(eval(budgetprevious.cum_transport_euro) + eval(budgetnext.transport_planned) + eval(budget.transport)));
                		$("#admin_total").html(money(budgetprevious.cum_admin_euro));

                		$("#total_total").html(money(tot_total));
				
				//$("#btotal").html($("#total_total").html());
				$("#btotal").html(money(tot_total));

				$("#breceived").html(money(budget.cum_received_euro));
				$("#bprevious").html(money(budget.prev_unused / budget.prev_xrate));
				$("#brequested").html(money(tot_total - budget.cum_received_euro - (budget.prev_unused / budget.prev_xrate)));
				$("#badmin").html(money((tot_total - budget.cum_received_euro - (budget.prev_unused / budget.prev_xrate)) * 0.15));
				$("#brequestedtotal").html(money((tot_total - budget.cum_received_euro - (budget.prev_unused / budget.prev_xrate)) * 1.15));
        		});
        	});
        });

	$("#period").html("Quarter "+quarter+", "+year);
	var monthNames = [ "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" ];
	
	var date = new Date();
	var day = date.getDate();
	var monthIndex = date.getMonth();
	$("#thisq").html(quarter + " " + year);
	$("#previousq").html(previousq + " " + previousy);
	$("#nextq").html(nextq + " " + nexty);
	$("#title").append(" - " + day + " " + monthNames[monthIndex] + " " + date.getFullYear());
});
</script>
</head>
  <body  id="body1">
    <p><span id="title">Request For Payment</span></p>
    <p><span id="period">Quarter 3, 2016</span></p>
    <p>Exchange Rate: &euro; 1 = <span id=xrate>16.1232</span>&nbsp;&nbsp; on <span id="repdate">4 February 2016</span></p>
    <table  id="maintable" border="1">
      <tbody>
        <tr>
          <th width="100px"></th>
          <th width="150px">Actual Expenditure Until <br/>Q<span id="previousq"></span></th>
          <th width="150px">Estimated Expenditure <br/>Q<span id="thisq"></span></th>
          <th width="150px">Expenditure Plan <br/>Q<span id="nextq"></span></th>
          <th width="150px">Total</th>
        </tr>
        <tr>
          <td>Personnel</td>
          <td>&euro; <span id="personnel_local"></span></td>
          <td>&euro; <span id="personnel_estimate"></span></td>
          <td>&euro; <span id="personnel_planned"></span></td>
          <td>&euro; <span id="personnel_total"></span></td>
        </tr>
        <tr>
          <td>Investments</td>
          <td>&euro; <span id="investments_local"></span></td>
          <td>&euro; <span id="investments_estimate"></span></td>
          <td>&euro; <span id="investments_planned"></span></td>
          <td>&euro; <span id="investments_total"></span></td>
        </tr>
        <tr>
          <td>Consumables</td>
          <td>&euro; <span id="consumables_local"></span></td>
          <td>&euro; <span id="consumables_estimate"></span></td>
          <td>&euro; <span id="consumables_planned"></span></td>
          <td>&euro; <span id="consumables_total"></span></td>
        </tr>
        <tr>
          <td>Services</td>
          <td>&euro; <span id="services_local"></span></td>
          <td>&euro; <span id="services_estimate"></span></td>
          <td>&euro; <span id="services_planned"></span></td>
          <td>&euro; <span id="services_total"></span></td>
        </tr>
        <tr>
          <td>Transport</td>
          <td>&euro; <span id="transport_local"></span></td>
          <td>&euro; <span id="transport_estimate"></span></td>
          <td>&euro; <span id="transport_planned"></span></td>
          <td>&euro; <span id="transport_total"></span></td>
        </tr>
        <tr>
          <td>Admin Fee</td>
          <td>&euro; <span id="admin_local"></span></td>
          <td></td>
          <td></td>
          <td>&euro; <span id="admin_total"></span></td>
        </tr>
        <tr>
          <td class="total">Total</td>
          <td class="total">&euro; <span id="total_local"></span></td>
          <td class="total">&euro; <span id="total_estimate"></span></td>
          <td class="total">&euro; <span id="total_planned"></span></td>
          <td class="total">&euro; <span id="total_total"></span></td>
        </tr>
      </tbody>
    </table>
    <br>
    <table border="1">
      <tbody>
        <tr>
          <td width="220px" class="bhead">1 Total (Column 4)</td>
          <td width="100px">&euro; <span id="btotal"></span>
          </td>
        </tr>
        <tr>
          <td class="bhead">2 Received Payments</td>
          <td>&euro; <span id="breceived"></span>
          </td>
        </tr>
        <tr>
          <td class="bhead">Carried Forward from Previous Period</td>
          <td>&euro; <span id="bprevious"></span>
          </td>
        </tr>
        <tr>
          <td class="bhead">4 Requested Payment</td>
          <td>&euro; <span id="brequested"></span>
          </td>
        </tr>
        <tr>
          <td class="bhead">Admin Fee</td>
          <td>&euro; <span id="badmin"></span>
          </td>
        </tr>
        <tr>
          <td class="bhead">Requested Payment incl. Admin Fee</td>
          <td>&euro; <span id="brequestedtotal"></span>
          </td>
        </tr>
      </tbody>
    </table>
    <p>This request applies to the task as attached.</p>
    <p>I herewith confirm that the information given above is objectively and arithmetically correct.</p>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p></p>
    <p>______________________________________________________________________<br/>Date, Signed</p>
  </body>
</html>

