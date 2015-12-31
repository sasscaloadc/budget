<?php
	include 'check_access.php'
?>
<!DOCTYPE html>
<html>
<head>
<meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
<title>Budget Tool</title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script>

var task;
var wxr = 1;
var budgetyears;
var budgetquarters;

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
function val(item) {
	if (item.prop("tagName") == "INPUT") {
		return eval(item.val().replace(/ /g, '').replace(/,/g, ''));
	} else {
		if (item.children(":first").prop("tagName") == "INPUT") {
			return eval(item.children(":first").val().replace(/ /g, '').replace(/,/g, ''));
		} else {
			if ((typeof item.html() == 'undefined') || (isNaN(eval(item.html().replace(/ /g, '').replace(/,/g, ''))))) {
				return 0;
			} else {
				return eval(item.html().replace(/ /g, '').replace(/,/g, ''));
			}
		}
	}
}
function vset(item, val) {
	hideagain = item
	if (item.prop("tagName") == "INPUT") {
		item.val(val);
	} else {
		if (item.children(":first").prop("tagName") == "INPUT") {
			item.children(":first").val(val);
		} else {
			item.html(val);
		}
	}
}

function update_totals() {
	vset($("#total_requested"), roundToTwo(val($("#investments_requested")) 
					+ val($("#personnel_requested")) 
					+ val($("#services_requested")) 
					+ val($("#transport_requested")) 
					+ val($("#consumables_requested"))));
	vset($("#total_local"), roundToTwo(val($("#investments_local")) 
					+ val($("#personnel_local")) 
					+ val($("#services_local")) 
					+ val($("#transport_local")) 
					+ val($("#consumables_local")) 
					+ val($("#admin_local")) ));
	vset($("#total_euro"), roundToTwo(val($("#investments_euro")) 
					+ val($("#personnel_euro")) 
					+ val($("#services_euro")) 
					+ val($("#transport_euro")) 
					+ val($("#consumables_euro")) 
					+ val($("#admin_euro")) ));
	vset($("#total_actual"), roundToTwo(val($("#investments_actual")) 
					+ val($("#personnel_actual")) 
					+ val($("#services_actual")) 
					+ val($("#transport_actual")) 
					+ val($("#consumables_actual"))));
	vset($("#total_available"), roundToTwo(val($("#investments_available")) 
					+ val($("#personnel_available")) 
					+ val($("#services_available")) 
					+ val($("#transport_available")) 
					+ val($("#consumables_available")) 
					- val($("#admin_euro")) ));
	vset($("#unused_local"), roundToTwo(val($("#funds_available")) - val($("#total_local"))));
	vset($("#unused_euro"), roundToTwo((val($("#funds_available")) - val($("#total_local"))) / wxr   ));
}
	
function update_value(item) {
	base = item.attr('id').substr(0, item.attr('id').indexOf('_'));
	ext = item.attr('id').indexOf('euro') >= 0 ? "_local" : "_euro";
	rate = $(".stage2").is(":visible") ? val($("#weighted_rate")) : val($("#localxrate")); 
	value1 = val(item);
	value2 = ext == "_euro" ? value1 / rate : value1 * rate;
	vset($("#"+base+ext), roundToTwo(value2)); 
	vset($("#"+base+"_available"), roundToTwo(task[base+"_budget"] - budget["cum_"+base+"_euro"] - val($("#"+base+"_euro"))));
	update_totals();
}

function calc_weighted_rate(budget) {
	$("#surplus_local").html(roundToTwo(budget.prev_unused));
	$("#surplus_rate").html(Number(budget.prev_xrate).toFixed(4));
	$("#surplus_euro").html(roundToTwo(budget.prev_unused / budget.prev_xrate));
	$("#funds_available").html(money(val($("#surplus_local")) + val($("#funds_received_local"))));
	$("#unused_local").html(money(val($("#funds_available")) - val($("#total_local"))));
	wxt = (budget.prev_unused / (budget.prev_xrate == 0 ? 1 : budget.prev_xrate)) 
	    + (val($("#funds_received_local")) / (val($("#funds_received_rate")) == 0 ? 1 : val($("#funds_received_rate"))));  // ** avoid division by zero ..
	wxr = val($("#funds_available")) / (wxt == 0 ? 1 : wxt);
	$("#weighted_rate").html(Number(wxr).toFixed(4));
	$("#weighted_euro").html(money(roundToTwo(wxt)));	
	return wxr == 0 ? 1 : wxr;
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

function load_figures() {
	setNavigationButtons();
	$("#figures").show();
	$("#receive").hide(); 
	$("#save_1").prop("disabled",false); 
	$("#submit_1").prop("disabled",false);
	$("#save_2").prop("disabled",false); 
	$("#submit_2").prop("disabled",false);
	$("#submit_message").html("");

	$.get("load_figures_data.php?database=budget&taskid="+ $("#tasks").val().trim()+"&year=" + $("#years").val().trim() + "&quarter=" + $("#quarters").val().trim(), function(data, status){
			budget = JSON.parse(data);
			$("#heading_available").html("Available Budget (Euro)");
			switch (budget.status) {
			   case "1":
				$("#taskstatus").html("Capturing Estimates for Quarter");
				$("[id*='requested']").hide();
				$("[id*='unused']").hide();
				$("[id*='surplus']").show();
				$("[id*='funds']").hide();
				$("[id*='weighted']").hide();
				$("#stat1_save").show();
				$("#stat2_save").hide();
				$(".stage1").show();
				$(".stage2").hide();
				$("#heading_local").html("Estimates "+budget.currency);
				$("#heading_euro").html("Estimates Euro");

				$("#investments_local").html("<input type=\"text\" id=\"investments_local_input\" value=\""+budget.investments+"\" onchange=\"update_value($(this))\"/>");
				$("#investments_euro").html("<input type=\"text\" id=\"investments_euro_input\" value=\""+roundToTwo(budget.investments/ budget.livexrate)+"\" onchange=\"update_value($(this))\"/>");
				$("#services_local").html("<input type=\"text\" id=\"services_local_input\" value=\""+budget.services+"\" onchange=\"update_value($(this))\"/>");
				$("#services_euro").html("<input type=\"text\" id=\"services_euro_input\" value=\""+roundToTwo(budget.services/ budget.livexrate)+"\" onchange=\"update_value($(this))\"/>");
				$("#consumables_local").html("<input type=\"text\" id=\"consumables_local_input\" value=\""+budget.consumables+"\" onchange=\"update_value($(this))\"/>");
				$("#consumables_euro").html("<input type=\"text\" id=\"consumables_euro_input\" value=\""+roundToTwo(budget.consumables/ budget.livexrate)+"\" onchange=\"update_value($(this))\"/>");
				$("#transport_local").html("<input type=\"text\" id=\"transport_local_input\" value=\""+budget.transport+"\" onchange=\"update_value($(this))\"/>");
				$("#transport_euro").html("<input type=\"text\" id=\"transport_euro_input\" value=\""+roundToTwo(budget.transport/ budget.livexrate)+"\" onchange=\"update_value($(this))\"/>");
				$("#personnel_local").html("<input type=\"text\" id=\"personnel_local_input\" value=\""+budget.personnel+"\" onchange=\"update_value($(this))\"/>");
				$("#personnel_euro").html("<input type=\"text\" id=\"personnel_euro_input\" value=\""+roundToTwo(budget.personnel/ budget.livexrate)+"\" onchange=\"update_value($(this))\"/>");
				$("#admin_local").html("");
				wxr = calc_weighted_rate(budget);

				$("#investments_available").html(roundToTwo(task.investments_budget - budget.cum_investments_euro ));
				$("#services_available").html(roundToTwo(task.services_budget - budget.cum_services_euro ));
				$("#consumables_available").html(roundToTwo(task.consumables_budget - budget.cum_consumables_euro ));
				$("#transport_available").html(roundToTwo(task.transport_budget - budget.cum_transport_euro ));
				$("#personnel_available").html(roundToTwo(task.personnel_budget - budget.cum_personnel_euro ));
				break;
			   case "2":
				$("#taskstatus").html("Capturing Funds Received and Actual Expenditures");
				$("[id*='requested']").show();
				$("[id*='unused']").show();
				$("[id*='surplus']").show();
				$("[id*='funds']").show();
				$("[id*='weighted']").show();
				$(".stage1").hide();
				$(".stage2").show();
				$("#stat1_save").hide();
				$("#stat2_save").show();

				$("#heading_requested").html("Requested "+budget.currency);
				$("#heading_local").html("Spent "+budget.currency);
				$("#heading_euro").html("Spent Euro");

				$("#investments_requested").html(money(budget.investments));
				$("#services_requested").html(money(budget.services));
				$("#consumables_requested").html(money(budget.consumables));
				$("#transport_requested").html(money(budget.transport));
				$("#personnel_requested").html(money(budget.personnel));
				$("#investments_local").html("<input type=\"text\" id=\"investments_local_input\" value=\""+budget.investments_actual+"\" onchange=\"update_value($(this))\"/>");
				$("#services_local").html("<input type=\"text\" id=\"services_local_input\" value=\""+budget.services_actual+"\" onchange=\"update_value($(this))\"/>");
				$("#consumables_local").html("<input type=\"text\" id=\"consumables_local_input\" value=\""+budget.consumables_actual+"\" onchange=\"update_value($(this))\"/>");
				$("#transport_local").html("<input type=\"text\" id=\"transport_local_input\" value=\""+budget.transport_actual+"\" onchange=\"update_value($(this))\"/>");
				$("#personnel_local").html("<input type=\"text\" id=\"personnel_local_input\" value=\""+budget.personnel_actual+"\" onchange=\"update_value($(this))\"/>");
				$("#admin_local").html("<input type=\"text\" id=\"admin_local_input\" value=\""+budget.admin+"\" onchange=\"update_value($(this))\"/>");
				if (budget.received_date == null) {
					$("#funds_received_local").html("0");
					$("#funds_received_rate").html("0");
					$("#funds_received_euro").html("<input id=\"record_receipt\" type=\"button\" value=\"Record Receipt\" />");
					<?php
						if ($_SESSION["access"] > 1) {
							echo " $(\"#record_receipt\").prop(\"disabled\",true); ";
						} else {
							echo " $(\"#record_receipt\").prop(\"disabled\",false); ";
							echo " $(\"#record_receipt\").click(function() { load_receive(); });";
						}
					?>
				} else {
					$("#funds_received_local").html(money(budget.received));
					$("#funds_received_rate").html(budget.xrate);
					$("#funds_received_euro").html(roundToTwo(eval(budget.received) / eval(budget.xrate)));
				}
				wxr = calc_weighted_rate(budget);
				$("#investments_euro").html(roundToTwo(budget.investments_actual / wxr ));
				$("#services_euro").html(roundToTwo(budget.services_actual / wxr ));
				$("#consumables_euro").html(roundToTwo(budget.consumables_actual / wxr ));
				$("#transport_euro").html(roundToTwo(budget.transport_actual / wxr ));
				$("#personnel_euro").html(roundToTwo(budget.personnel_actual / wxr ));
				$("#admin_euro").html(roundToTwo(budget.admin / wxr ));

				$("#investments_available").html(roundToTwo(task.investments_budget - budget.cum_investments_euro - val($("#investments_euro"))));
				$("#services_available").html(roundToTwo(task.services_budget - budget.cum_services_euro - val($("#services_euro"))));
				$("#consumables_available").html(roundToTwo(task.consumables_budget - budget.cum_consumables_euro - val($("#consumables_euro"))));
				$("#transport_available").html(roundToTwo(task.transport_budget - budget.cum_transport_euro - val($("#transport_euro"))));
				$("#personnel_available").html(roundToTwo(task.personnel_budget - budget.cum_personnel_euro - val($("#personnel_euro"))));

				break;
			   case "3":
				$("#taskstatus").html("Closed and Submitted for Quarter");
				$("[id*='requested']").show();
				$("[id*='unused']").show();
				$("[id*='surplus']").show();
				$("[id*='funds']").show();
				$("[id*='weighted']").show();
				$(".stage1").hide();
				$(".stage2").show();
				$("#stat1_save").hide();
				$("#stat2_save").hide();

				$("#heading_requested").html("Requested "+budget.currency);
				$("#heading_local").html("Spent "+budget.currency);
				$("#heading_euro").html("Spent Euro");

				$("#investments_requested").html(money(budget.investments));
				$("#services_requested").html(money(budget.services));
				$("#consumables_requested").html(money(budget.consumables));
				$("#transport_requested").html(money(budget.transport));
				$("#personnel_requested").html(money(budget.personnel));

				$("#investments_local").html(money(budget.investments_actual));
				$("#services_local").html(money(budget.services_actual));
				$("#consumables_local").html(money(budget.consumables_actual));
				$("#transport_local").html(money(budget.transport_actual));
				$("#personnel_local").html(money(budget.personnel_actual));
				$("#admin_local").html(money(budget.admin));

				$("#funds_received_local").html(money(budget.received));
				$("#funds_received_rate").html(budget.xrate);
				$("#funds_received_euro").html(roundToTwo(eval(budget.received) / eval(budget.xrate)));

				wxr = calc_weighted_rate(budget);
				$("#investments_euro").html(roundToTwo(budget.investments_actual / wxr ));
				$("#services_euro").html(roundToTwo(budget.services_actual / wxr ));
				$("#consumables_euro").html(roundToTwo(budget.consumables_actual / wxr ));
				$("#transport_euro").html(roundToTwo(budget.transport_actual / wxr ));
				$("#personnel_euro").html(roundToTwo(budget.personnel_actual / wxr ));
				$("#admin_euro").html(roundToTwo(budget.admin / wxr ));

				$("#investments_available").html(roundToTwo(task.investments_budget - budget.cum_investments_euro));
				$("#services_available").html(roundToTwo(task.services_budget - budget.cum_services_euro));
				$("#consumables_available").html(roundToTwo(task.consumables_budget - budget.cum_consumables_euro));
				$("#transport_available").html(roundToTwo(task.transport_budget - budget.cum_transport_euro));
				$("#personnel_available").html(roundToTwo(task.personnel_budget - budget.cum_personnel_euro));

				break;
			   default:
			}
			update_totals();
	});
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

function load_years(y, q) {
	var html = "";
	for (i=0; i<task.years.length; i++) {
		html += "<option>" + task.years[i].year + "</option>";
	}
	$("#years").html(html);
	$("select#years option").each(function() { this.selected = (this.value == y); });
	load_quarters(q);
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

function load_receive() {
	$("#figures").hide();
	$("#receive").show(); 
	$("#submit_rec").prop("disabled",false);
	$("#cancel").prop("disabled",false);
	
	$("#received_date").datepicker({ dateFormat: "yy-mm-dd" });

	$("#received_local").val("");
	$("#received_date").val("");
	$("#received_xrate").val("");
}

function save_values(status, saving, type) {
	$("#save_1").prop("disabled",true); 
	$("#submit_1").prop("disabled",true);
	$("#save_2").prop("disabled",true); 
	$("#submit_2").prop("disabled",true);
	$.post("save_"+type+".php",
		{
			database: "budget",
			investments: val($("#investments_local")), 
			personnel: val($("#personnel_local")),
			services: val($("#services_local")),
			transport: val($("#transport_local")),
			consumables: val($("#consumables_local")),
			admin: val($("#admin_local")),
			prev_unused: val($("#unused_local")),
			prev_xrate: wxr,
			taskid: $("#tasks").val(),
			year: $("#years").val(),
			quarter: $("#quarters").val(),
			status: status
		},
		function(data, status){
			if (data == "OK") {
				if (saving) {
					$("#submit_message").html("Saved");
				} else {
					$("#submit_message").html("Submitted");
				}
			} else {
				$("#submit_message").html("<span style=\"color:red\">"+data+"</span>");
			}
			setTimeout(function() {
					load_task($("#years").val(), $("#quarters").val());
				}, 3000);
		});
}

function save_receipts() {
	$("#submit_rec").prop("disabled",true);
	$("#cancel").prop("disabled",true);
	$.post("save_receipts.php",
		{
			database: "budget",
			taskid: $("#tasks").val(),
			year: $("#years").val(),
			quarter: $("#quarters").val(),
			received: $("#received_local").val().replace(/ /g, '').replace(/,/g, ''),
			received_date: $("#received_date").val(),
			xrate: $("#received_xrate").val(),
			status: 2
		},
		function(data, status){
			if (data == "OK") {
				$("#submit_rec_message").html("Saved");
			} else {
				$("#submit_rec_message").html("<span style=\"color:red\">"+data+"</span>");
			}
			setTimeout(load_figures, 3000);
		});
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

	$("#submit_rec").click(function() { 
					save_receipts();
				}); 

	$("#cancel").click(function() { 
			load_figures();
				}); 

	$("#adduser").click(function() { 
			window.location.href = "http://caprivi.sasscal.org/budget/add_user.php";
				}); 

	$("#addtask").click(function() { 
			window.location.href = "http://caprivi.sasscal.org/budget/add_task.php";
				}); 

	$("#reports").click(function() { 
			window.location.href = "http://caprivi.sasscal.org/budget/reports.php";
				}); 

	$("#logout").click(function() { 
			window.location.href = "http://caprivi.sasscal.org/budget/logout.php";
				}); 

	$("#receive").hide();
	$("#save_1").click(function() { save_values(1, true, "figures"); });
	$("#submit_1").click(function() { save_values(2, false, "figures"); });
	$("#save_2").click(function() { save_values(2, true, "actual"); });
	$("#submit_2").click(function() { save_values(3, false, "actual"); });
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
<p  style="text-align: center;"><span  style="font-family: Helvetica,Arial,sans-serif; font-size: 30px;">Budget
Tool</span></p>
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
                <input type="button" id="adduser" value="Add User"/>
                <input type="button" id="addtask" value="Add Task"/>
                <input type="button" id="reports" value="Reports"/>
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
             <td>&nbsp;</td>
             <td><input type="button" value="Next =>" id="next" /></td>
	  </tr>
          <tr>
              <th>Expense</th>
              <th><span id="heading_requested"></span></th>
              <th><span id="heading_local"></span></th>
              <th><span id="heading_euro"></span></th>
              <th><span id="heading_available"></span></th>
	  </tr>
          <tr><td>Personnel: </td>
             <td><span class="stage2"><span class="curr"></span></span><span id="personnel_requested"></span> </td>
             <td><span class="curr"></span><span id="personnel_local"></span></td>
             <td><span class="eur"></span><span id="personnel_euro"></span> </td>
             <td><span class="eur"></span><span id="personnel_available"></span> </td>
	  </tr>
          <tr><td>Investments: </td>
             <td><span class="stage2"><span class="curr"></span></span><span id="investments_requested"></span> </td>
             <td><span class="curr"></span><span id="investments_local"></span></td>
             <td><span class="eur"></span><span id="investments_euro"></span> </td>
             <td><span class="eur"></span><span id="investments_available"></span> </td>
	  </tr>
          <tr><td>Consumables: </td>
             <td><span class="stage2"><span class="curr"></span></span><span id="consumables_requested"></span> </td>
             <td><span class="curr"></span><span id="consumables_local"></span></td>
             <td><span class="eur"></span><span id="consumables_euro"></span> </td>
             <td><span class="eur"></span><span id="consumables_available"></span> </td>
	  </tr>
          <tr><td>Services: </td>
             <td><span class="stage2"><span class="curr"></span></span><span id="services_requested"></span> </td>
             <td><span class="curr"></span><span id="services_local"></span></td>
             <td><span class="eur"></span><span id="services_euro"></span> </td>
             <td><span class="eur"></span><span id="services_available"></span> </td>
	  </tr>
          <tr><td>Transport: </td>
             <td><span class="stage2"><span class="curr"></span></span><span id="transport_requested"></span> </td>
             <td><span class="curr"></span><span id="transport_local"></span></td>
             <td><span class="eur"></span><span id="transport_euro"></span> </td>
             <td><span class="eur"></span><span id="transport_available"></span> </td>
	  </tr>
          <tr class="stage2"><td>Admin & Other: </td>
             <td>&nbsp;</td>
             <td><span class="curr"></span><span id="admin_local"></span></td>
             <td><span class="eur"></span><span id="admin_euro"></span> </td>
             <td>&nbsp;</td>
	  </tr>
          <tr style="font-weight:bold;"><td class="topline"> Total: </td>
             <td class="topline"><span class="stage2"><span class="curr"></span></span><span id="total_requested"></span></td>
             <td class="topline"><span class="curr"></span><span id="total_local"></span></td>
             <td class="topline"> <span class="eur"></span><span id="total_euro"></span></td>
             <td class="topline"> <span class="eur"></span><span id="total_available"></span></td>
          </tr>
          <tr><td>&nbsp;</td>
              <td><span id="euro_requested"></span></td>
              <td colspan="2"> 
	          <span id="stat1_save">
				  <input type="button" value="Save"   id="save_1">
                                  <input type="button" value="Submit" id="submit_1">
	          </span>
	          <span id="stat2_save">
				  <input type="button" value="Save"   id="save_2">
                                  <input type="button" value="Submit" id="submit_2">
	          </span>
		  <span id="submit_message"></span>
              </td>
          </tr>
          <tr class="stage2"><td>&nbsp;</td>
              <td>&nbsp;</td>
              <td class="unused"><span id="heading_unused_local">Unused </span></td>
              <td class="unused"><span id="heading_unused_euro">Unused </span></td>
              <td>&nbsp;</td>
          </tr>
          <tr class="stage2"><td>&nbsp;</td>
              <td>&nbsp;</td>
              <td class="unused"><span class="curr"></span><span id="unused_local"> </span></td>
              <td class="unused"><span class="eur"></span><span id="unused_euro"> </span></td>
              <td>&nbsp;</td>
          </tr>
    </table>
    <p></p>
    <table>
          <tr><td class="funds"><span id="surplus_label">Surplus from previous quarter</span></td>
              <td class="funds"><span class="curr"></span><span id="surplus_local"> </span></td>
              <td class="funds"><span id="surplus_rate"> </span></td>
              <td class="funds"><span class="eur"></span><span id="surplus_euro"> </span></td>
          </tr>
          <tr class="stage2"><td class="funds"><span id="funds_received_label">Funds Received for This Quarter</span></td>
              <td class="funds"><span class="curr"></span><span id="funds_received_local"> </span></td>
              <td class="funds"><span id="funds_received_rate"> </span></td>
              <td class="funds"><span class="eur"></span><span id="funds_received_euro"> </span></td>
          </tr>
          <tr class="stage2"><td class="funds"><span id="funds_available_label">Funds Available</span></td>
              <td class="funds"><span class="curr"></span><span id="funds_available"> </span></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
          </tr>
          <tr class="stage2"><td class="wrate"><span id="weighted_label">Weighted Exchange Rate</span></td>
              <td>&nbsp;</td>
              <td class="wrate"><span id="weighted_rate"> </span></td>
              <td class="wrate"><span class="eur"></span><span id="weighted_euro"> </span></td>
          </tr>
    </table>
    </span>
    <span id="receive">
    <table>
       <tr><td></td><td> Received (Local Currency: <span id="local_received"></span>)</td><td> 
				<input id="received_local" type="text"></td></tr>
       <tr><td></td><td> Date Received </td><td> <input id="received_date" type="text"></td></tr>
       <tr><td></td><td> Exchange Rate </td><td> <input id="received_xrate" type="text"></td></tr>
       <tr><td></td><td></td><td> <input type="button" value="Submit" id="submit_rec">
				<input type="button" value="Cancel" id="cancel">
				<span id="submit_rec_message"></span>
	   </td></tr>
    </table>
    </span>
  </body>
</html>

