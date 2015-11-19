<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Budget Tool</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>

	var task;

	function roundToTwo(num) {    
		return +(Math.round(num + "e+2")  + "e-2");
	};
	function update_euro(item) {
		$("#"+item+"_euro").val(roundToTwo($("#"+item+"_local").val() / $("#localxrate").html())); 
	};

	function update_local(item) {
		$("#"+item+"_local").val(roundToTwo($("#"+item+"_euro").val() * $("#localxrate").html())); 
	};

	function load_figures() {
		$("#figures").load("load_figures.php?database=budget&taskid="+ $("#tasks").val().trim()+"&year=" + $("#years").val().trim() + "&quarter=" + $("#quarters").val().trim(), function() { 
			update_euro("investments");
			update_euro("services");
			update_euro("consumables");
			update_euro("transport");
			update_euro("personnel");
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
		//$("#currency").change(function() {
				//$("#xrate").load("load_currency.php?database=budget&currency=" + $("#currency").val().trim() );
				//load_figures();
				//});
		//$("#xrate").load("load_currency.php?database=budget&currency=" + $("#currency").val().trim() );
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
          <td>&nbsp;
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>

