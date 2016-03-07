<?php
	include 'check_access.php';
        if ($_SESSION['access'] > 1)  {
                header("Location: ".$location_url."no_access.php");
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

		tot_local = eval($("#personnel_local").val()) + eval($("#investments_local").val()) + eval($("#consumables_local").val()) 
				+ eval($("#services_local").val()) + eval($("#transport_local").val()) +  eval($("#admin_local").val());
        	$("#total_local").html( money(tot_local));

		tot_estimate = eval($("#personnel_estimate").val()) + eval($("#investments_estimate").val()) + eval($("#consumables_estimate").val()) 
				+ eval($("#services_estimate").val()) + eval($("#transport_estimate").val()) ;
        	$("#total_estimate").html( money(tot_estimate));
		tot_planned = eval($("#personnel_planned").val()) + eval($("#investments_planned").val()) + eval($("#consumables_planned").val()) 
				+ eval($("#services_planned").val()) + eval($("#transport_planned").val()) ;
        	$("#total_planned").html( money(tot_planned));
}

function load_task(y, q) {
        $.get("load_task.php?database=budget&taskid="+ $("#tasks").val().trim(), function(data, status){
                        task = JSON.parse(data);
                        load_years(y, q);
                        $("#taskcurrency").html(task.currency);
                        $(".curr").html(task.currency+" ");
                        $(".eur").html("&euro; ");
                        $("#taskbudget").html(roundToTwo(task.budget));
                        $("#investments_budget").html(roundToTwo(task.investments_budget));
                        $("#services_budget").html(roundToTwo(task.services_budget));
                        $("#consumables_budget").html(roundToTwo(task.consumables_budget));
                        $("#transport_budget").html(roundToTwo(task.transport_budget));
                        $("#personnel_budget").html(roundToTwo(task.personnel_budget));
                        $("#localxrate").html(task.localxrate);
                        $("#xratedescription").html("Current Local Exchange Rate<small>("+task.localxrateupdated+")</small>");
                        $("#local_received").html(task.currency);
                });
}

function load_years(y, q) {
        var html = "";
        for (i=0; i<task.years.length; i++) {
                html += "<option>" + task.years[i].year + "</option>";
        }
        $("#years").html(html);
        $("select#years option").each(function() { this.selected = (this.value == y); });
        load_quarters(q);
}

function load_quarters(qv) {
        var html = "";
        for (i=0; i<task.years.length; i++) {
                if (task.years[i].year == $("#years").val()) {
                        for (j=0; j<task.years[i].quarters.length; j++) {
                                var q = task.years[i].quarters[j];
                                html += "<option value=\""+q+"\">Q" + q + "</option>";
                        }
                        $("#quarters").html(html);
                        break;
                }
        }
        $("select#quarters option").each(function() { this.selected = (this.value == qv); });
        load_figures();
}

function setNavigationButtons() {
        $("#previous").prop("disabled", true);
        $("#next").prop("disabled", true);
        if (($("#quarters").prop("selectedIndex") > 0) || ($("#years").prop("selectedIndex") > 0)) {
                $("#previous").prop("disabled", false);
        }
        if (($("#quarters").prop("selectedIndex") < $("#quarters").prop("length") - 1) || ($("#years").prop("selectedIndex") < $("#years").prop("length") - 1)) {
                $("#next").prop("disabled", false);
        }
}

function go(direction) {

        if (direction == "next") {
                if ($("#quarters").prop("selectedIndex") < $("#quarters").prop("length") - 1) {
                                $("#quarters").prop("selectedIndex", $("#quarters").prop("selectedIndex") + 1);
                                load_figures();
                        } else {
                                $("#years").prop("selectedIndex", $("#years").prop("selectedIndex") + 1);
                                load_quarters();
                        }
        } else {
                if ($("#quarters").prop("selectedIndex") == 0){
                                $("#years").prop("selectedIndex", $("#years").prop("selectedIndex") - 1);
                                load_quarters(4);
                        } else {
                                $("#quarters").prop("selectedIndex", $("#quarters").prop("selectedIndex") - 1);
                                load_figures();
                        }
        }
};

function load_figures() {
        setNavigationButtons();

	year = $("#years").val();
	quarter = $("#quarters").val();
	taskid = $("#tasks").val();

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
	
		$("#personnel_planned").val(eval(budget.personnel_planned));   
		$("#investments_planned").val(eval(budget.investments_planned));   
		$("#consumables_planned").val(eval(budget.consumables_planned));  
		$("#services_planned").val(eval(budget.services_planned));   
		$("#transport_planned").val(eval(budget.transport_planned));   
		tot_planned = eval(budget.personnel_planned) + eval(budget.investments_planned) + eval(budget.consumables_planned) 
				+ eval(budget.services_planned) + eval(budget.transport_planned) ;
        	$("#total_planned").html( money(tot_planned));

		$("#personnel_local").val(eval(budget.personnel_actual));   
		$("#investments_local").val(eval(budget.investments_actual));   
		$("#consumables_local").val(eval(budget.consumables_actual));  
		$("#services_local").val(eval(budget.services_actual));   
		$("#transport_local").val(eval(budget.transport_actual));   
		$("#admin_local").val(eval(budget.admin));   
		tot_local = eval(budget.personnel_actual) + eval(budget.investments_actual) + eval(budget.consumables_actual) 
				+ eval(budget.services_actual) + eval(budget.transport_actual) + eval(budget.admin) ;
        	$("#total_local").html( money(tot_local));
	});

}

function save_values() {
	$("#save_1").prop("disabled",true); 

	$.post("save_loading.php",
		{
			investments_actual: $("#investments_local").val(), 
			personnel_actual: $("#personnel_local").val(),
			services_actual: $("#services_local").val(),
			transport_actual: $("#transport_local").val(),
			consumables_actual: $("#consumables_local").val(),
			admin: $("#admin_local").val(),

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

			taskid: $("#tasks").val(),
			year: $("#years").val(),
			quarter: $("#quarters").val()
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
        $("#tasks").load("load_tasklist.php?database=budget", function(){
                load_task();
            });

        $("#tasks").change(function() {
                        load_task();
                        });

        $("#years").change(function() {
                        load_quarters();
                        });

        $("#quarters").change(function() {
                        load_figures();
                        });

	$("#save_1").click(function() { save_values(); });
        $("#logout").click(function() {
                        window.location.href = location_url+"logout.php";
                });

	$("#personnel_local").change(function() { update_totals() });
	$("#investments_local").change(function() { update_totals() });
	$("#consumables_local").change(function() { update_totals() });
	$("#services_local").change(function() { update_totals() });
	$("#transport_local").change(function() { update_totals() });
	$("#admin_local").change(function() { update_totals() });

	$("#personnel_estimate").change(function() { update_totals() });
	$("#investments_estimate").change(function() { update_totals() });
	$("#consumables_estimate").change(function() { update_totals() });
	$("#services_estimate").change(function() { update_totals() });
	$("#transport_estimate").change(function() { update_totals() });
	$("#admin_estimate").change(function() { update_totals() });

	$("#personnel_planned").change(function() { update_totals() });
	$("#investments_planned").change(function() { update_totals() });
	$("#consumables_planned").change(function() { update_totals() });
	$("#services_planned").change(function() { update_totals() });
	$("#transport_planned").change(function() { update_totals() });
	$("#admin_planned").change(function() { update_totals() });

        $("#previous").click(function() { go("previous"); });
        $("#next").click(function() { go("next"); });

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
                <input type="button" id="logout" value="Log Out"/>
            </td>
          </tr>
        </table>
    </span></p>

    <table style="text-align: left; margin-left: auto; margin-right: auto;"  border="1">
      <tbody>
        <tr>
          <td  style="margin-left: 164px; text-align: right;">Task ID</td>
          <td>
            <select id="tasks" form="mainform"  name="task">
                 <option>Loading ...</option>
            </select>
            <br>
          </td>
        </tr>
        <tr>
          <td  style="text-align: right;">Year </td>
          <td>
            <select id="years"  form="mainform"  >
                 <option>Loading ...</option>
            </select>
            <br>
          </td>
        </tr>
        <tr>
          <td  style="text-align: right;">Quarter</td>
          <td>
            <select  form="mainform"  id="quarters">
                 <option>Loading ...</option>
            </select>
          </td>
        </tr>
        <tr>
          <td  style="text-align: right;">Task Status</td>
          <td> <span id="taskstatus"></span>
        </tr>
        <tr>
          <td  style="text-align: right;">Local Currency</td>
          <td> <span id="taskcurrency"></span>
        </tr>
        <tr class="stage1">
          <td  style="text-align: right;"><span id="xratedescription">Exchange Rate</span></td>
          <td> <span id="localxrate"></span>
        </tr>
        <tr>
          <td  style="text-align: right;">Task Budget</td>
          <td> <span class="eur"></span><span id="taskbudget"></span>
        </tr>
      </tbody>
    </table>
    <br/>


    <span id="figures">
    <table>
          <tr><td><input type="button" value="<= Prev" id="previous" /></td>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td><input type="button" value="Next =>" id="next" /></td>
          </tr>
          <tr>
              <th>Category</th>
              <th><span id="actual">Actual Expenditure </span></th>
              <th><span id="estimated">Estimated Expenditure </span></th>
              <th><span id="planned">Planned Expenditure </span></th>
	  </tr>
          <tr>
	     <td class="personnel">Personnel: </td>
             <td><span class="curr"></span><input type="text" class="personnel" id="personnel_local"></td>
             <td><span class="curr"></span><input type="text" class="personnel" id="personnel_estimate"></td>
             <td><span class="curr"></span><input type="text" class="personnel" id="personnel_planned"></td>
	  </tr>
          <tr>
	     <td class="investments">investments: </td>
             <td><span class="curr"></span><input class="investments" type="text" id="investments_local"></td>
             <td><span class="curr"></span><input type="text" class="investments" id="investments_estimate"></td>
             <td><span class="curr"></span><input type="text" class="investments" id="investments_planned"></td>
	  </tr>
          <tr>
	     <td class="services">services: </td>
             <td><span class="curr"></span><input class="services" type="text" id="services_local"></td>
             <td><span class="curr"></span><input type="text" class="services" id="services_estimate"></td>
             <td><span class="curr"></span><input type="text" class="services" id="services_planned"></td>
	  </tr>
          <tr>
	     <td class="consumables">consumables: </td>
             <td><span class="curr"></span><input class="consumables" type="text" id="consumables_local"></td>
             <td><span class="curr"></span><input type="text" class="consumables" id="consumables_estimate"></td>
             <td><span class="curr"></span><input type="text" class="consumables" id="consumables_planned"></td>
	  </tr>
          <tr>
	     <td class="transport">transport: </td>
             <td><span class="curr"></span><input class="transport" type="text" id="transport_local"></td>
             <td><span class="curr"></span><input type="text" class="transport" id="transport_estimate"></td>
             <td><span class="curr"></span><input type="text" class="transport" id="transport_planned"></td>
	  </tr>
          <tr>
	     <td class="admin">admin: </td>
             <td><span class="curr"></span><input class="admin" type="text" id="admin_local"></td>
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

