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

	function roundToTwo(num) {    
		return +(Math.round(num + "e+2")  + "e-2");
	};
	function val(item) {
		if (item.prop("tagName") == "INPUT") {
			return eval(item.val());
		} else {
			return eval(item.html());
		}
	}
	function vset(item, val) {
		if (item.prop("tagName") == "INPUT") {
			item.val(val);
		} else {
			item.html(val);
		}
	}

        function update_totals() {
		vset($("#total_requested"), roundToTwo(val($("#investments_requested")) + val($("#personnel_requested")) + val($("#services_requested")) + val($("#transport_requested")) + val($("#consumables_requested"))));
		vset($("#total_local"), roundToTwo(val($("#investments_local")) + val($("#personnel_local")) + val($("#services_local")) + val($("#transport_local")) + val($("#consumables_local"))));
		vset($("#total_euro"), roundToTwo(val($("#investments_euro")) + val($("#personnel_euro")) + val($("#services_euro")) + val($("#transport_euro")) + val($("#consumables_euro"))));
		vset($("#total_actual"), roundToTwo(val($("#investments_actual")) + val($("#personnel_actual")) + val($("#services_actual")) + val($("#transport_actual")) + val($("#consumables_actual"))));
		vset($("#total_unused"), roundToTwo((val($("#total_requested")) - val($("#total_local"))) / val($("#localxrate"))));
	}
	function update_totalsh(html) {
		if (html) {
    			$("#total_local").html(roundToTwo(eval($("#investments_local").val())+ eval($("#personnel_local").val())+ eval($("#services_local").val())+ eval($("#transport_local").val())+ eval($("#consumables_local").val())));
	    		$("#total_euro").html(roundToTwo(eval($("#investments_euro").val())+ eval($("#personnel_euro").val())+ eval($("#services_euro").val())+ eval($("#transport_euro").val())+ eval($("#consumables_euro").val())));
	    		$("#total_actual").html(roundToTwo(eval($("#investments_actual").val())+ eval($("#personnel_actual").val())+ eval($("#services_actual").val())+ eval($("#transport_actual").val())+ eval($("#consumables_actual").val())));
		} else {
    			$("#total_local").html(roundToTwo(eval($("#investments_local").html())+ eval($("#personnel_local").html())+ eval($("#services_local").html())+ eval($("#transport_local").html())+ eval($("#consumables_local").html())));
    			$("#total_euro").html(roundToTwo(eval($("#investments_euro").html())+ eval($("#personnel_euro").html())+ eval($("#services_euro").html())+ eval($("#transport_euro").html())+ eval($("#consumables_euro").html())));
    			$("#total_actual").html(roundToTwo(eval($("#investments_actual").html())+ eval($("#personnel_actual").html())+ eval($("#services_actual").html())+ eval($("#transport_actual").html())+ eval($("#consumables_actual").html())));
		}
		$("#total_unused").html = roundToTwo($("#total_local").html() - $("#total_actual").html());
	}
		
	function update_euro(item) {
		if ($("#"+item+"_local").prop("tagName") == "INPUT") {
			if ($("#"+item+"_euro").prop("tagName") == "INPUT") {
				$("#"+item+"_euro").val(roundToTwo($("#"+item+"_local").val() / $("#localxrate").html())); 
			} else {
				$("#"+item+"_euro").html(roundToTwo($("#"+item+"_local").val() / $("#localxrate").html())); 
			}
			update_totals(false);
		} else {
			if ($("#"+item+"_euro").prop("tagName") == "INPUT") {
				$("#"+item+"_euro").val(roundToTwo($("#"+item+"_local").html() / $("#localxrate").html())); 
			} else {
				$("#"+item+"_euro").html(roundToTwo($("#"+item+"_local").html() / $("#localxrate").html())); 
			}
			update_totals(true);
		}
	};

	function update_local(item) {
		//$("#"+item+"_local").text(roundToTwo(parseFloat($("#"+item+"_euro").text()) * parseFloat($("#localxrate").text()))); 
		//$("#"+item+"_local").text(roundToTwo(parseFloat($("#"+item+"_euro").text()) * parseFloat($("#localxrate").text()))); 
		//update_totals();
		if (typeof $("#"+item+"_euro").val() === 'undefined' || $("#"+item+"_euro").val() === null) {
			$("#"+item+"_local").html(roundToTwo($("#"+item+"_euro").html() * $("#localxrate").html())); 
			update_totals(false);
		} else {
			$("#"+item+"_local").val(roundToTwo($("#"+item+"_euro").val() * $("#localxrate").html())); 
			update_totals(true);
		}
	};

	function load_figures() {
		$("#figures").load("load_figures.php?database=budget&taskid="+ $("#tasks").val().trim()+"&year=" + $("#years").val().trim() + "&quarter=" + $("#quarters").val().trim(), function() { 
			update_euro("investments");
			update_euro("services");
			update_euro("consumables");
			update_euro("transport");
			update_euro("personnel");
			$("#save_1").click(function() { save_figures(1); });
			$("#submit_1").click(function() { save_figures(2); });
			$("#submit_2").click(function() { save_receipts(); });
			$("#received_date").datepicker( { dateFormat: "yy-mm-dd" });
			$("#save_3").click(function() { save_actual(3); });
			$("#submit_3").click(function() { save_actual(4); });
		});
	}

	function load_quarters() {
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
		load_figures();
	}

	function load_years() {
		var html = "";
		for (i=0; i<task.years.length; i++) {
			html += "<option>" + task.years[i].year + "</option>";
		}
		$("#years").html(html);
		load_quarters();
	}

	function load_task() {
		$.get("load_task.php?database=budget&taskid="+ $("#tasks").val().trim(), function(data, status){
		        	task = JSON.parse(data);
				load_years();
				$("#taskcurrency").html(task.currency);
				$("#taskbudget").html(roundToTwo(task.budget));
				$("#localxrate").html(task.localxrate);
				$("#localxrateupdated").html(task.localxrateupdated);
				load_figures();
    			});
	}

	function save_actual(setstatus) {
		$.post("save_actual.php",
			{
				database: "budget",
    				investments: $("#investments_actual").val(), 
    				personnel: $("#personnel_actual").val(),
    				services: $("#services_actual").val(),
    				transport: $("#transport_actual").val(),
    				consumables: $("#consumables_actual").val(),
				taskid: $("#tasks").val(),
				year: $("#years").val(),
				quarter: $("#quarters").val(),
				status: setstatus
			},
			function(data, status){
				if (data == "OK") {
					if (setstatus == 3) {
						$("#save_message").html("Saved");
					} else {
						$("#submit_message").html("Saved");
					}
				} else {
					if (setstatus == 3) {
						$("#save_message").html("<span style=\"color:red\">"+data+"</span>");
					} else {
						$("#submit_message").html("<span style=\"color:red\">"+data+"</span>");
					}
				}
				setTimeout(load_figures, 3000);
			});
	}

	function save_figures(setstatus) {
		$.post("save_figures.php",
			{
				database: "budget",
    				investments: $("#investments_local").val(), 
    				personnel: $("#personnel_local").val(),
    				services: $("#services_local").val(),
    				transport: $("#transport_local").val(),
    				consumables: $("#consumables_local").val(),
				taskid: $("#tasks").val(),
				year: $("#years").val(),
				quarter: $("#quarters").val(),
				status: setstatus
			},
			function(data, status){
				if (data == "OK") {
					if (setstatus == 1) {
						$("#save_message").html("Saved");
					} else {
						$("#submit_message").html("Saved");
					}
				} else {
					if (setstatus == 1) {
						$("#save_message").html("<span style=\"color:red\">"+data+"</span>");
					} else {
						$("#submit_message").html("<span style=\"color:red\">"+data+"</span>");
					}
				}
				setTimeout(load_figures, 3000);
			});
	}

	function save_receipts() {
                $.post("save_receipts.php",
                        {
                                database: "budget",
                                taskid: $("#tasks").val(),
                                received: $("#received_euro").val(),
                                received_date: $("#received_date").val(),
                                xrate: $("#received_xrate").val(),
                                year: $("#years").val(),
                                quarter: $("#quarters").val(),
                                status: 3
                        },
                        function(data, status){
                                if (data == "OK") {
                                        $("#submit_message").html("Saved");
                                } else {
                                        $("#submit_message").html("<span style=\"color:red\">"+data+"</span>");
                                }
                                setTimeout(load_figures, 3000);
                        });
	}

	$(document).ready(function(){

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

		$("#search").click(function() {
				load_figures();
                                });
	}); 
    </script> 
    <style  type="text/css">
table {  
  width: 75%;
}

body {  
  font-family: Arial, Helvetica, sans-serif;
}

td {  
  padding-right: 10px;  
  padding-bottom: 1px;  
  padding-left: 10px;  
  border-spacing: 0px;
}
th {
  padding-right: 10px;
  padding-bottom: 1px;
  padding-left: 10px;
  border-spacing: 0px;
  text-align: left;
  color: green;
}
#save_message {
  padding-left: 10px;  
  font-size: 12px;
  font-weight: bold;
  color: green;
}

#submit_message {
  padding-left: 10px;  
  font-size: 12px;
  font-weight: bold;
  color: green;
}
.topline {
  border-top:1px solid #000000;
}
</style></head>
  <body>
    <p  style="text-align: center;"><span  style="font-family: Helvetica,Arial,sans-serif; font-size: 30px;">Budget
        Tool</span></p>
    <p  style="text-align: left;"><span  style="font-family: Helvetica,Arial,sans-serif; font-size: 30px;"></span></p>
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
          <td  style="text-align: right;">Local Currency</td>
          <td> <span id="taskcurrency"></span>
	</tr>
        <tr>
          <td  style="text-align: right;">Current Local Exchange Rate (<span id="localxrateupdated"></span>)</td>
          <td> <span id="localxrate"></span>
	</tr>
        <tr>
          <td  style="text-align: right;">Task Budget</td>
          <td> <span id="taskbudget"></span>
	</tr>
      </tbody>
    </table>
    <!--p  style="text-align: left;">
	1 &euro; = 
		<select id="currency">
			<option>AOA</option>
			<option>BWP</option>
			<option selected>NAD</option>
			<option>USD</option>
			<option>ZAR</option>
			<option>ZMW</option>
		</select>
		<span id="xrate"></span>
			
    </p-->
    <p  style="text-align: left;">
	<input type="button" value="search" id="search"/>
    </p>
    <table>
      <tbody id="figures">
        <tr>
          <td>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>

