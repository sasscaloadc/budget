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
    <title>Edit Task Figures</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
var location_url = "<?php echo $location_url ?>";

	function load_task_figures() {

	        $.get("load_task_figures.php?database=budget&taskid="+ $("#tasks").val().trim(), function(data, status){
			output = JSON.parse(data);

			$("#investments_budget").val(output.investments_budget);
                        $("#services_budget").val(output.services_budget);
                        $("#consumables_budget").val(output.consumables_budget);
                        $("#transport_budget").val(output.transport_budget);
                        $("#personnel_budget").val(output.personnel_budget);
                        $("#year_budget").val(output.year_budget);
                        $("#kfw_budget").val(output.kfw_phase_budget);

		});

	}

        $(document).ready(function(){


                $("#cancel").click(function() {
                        window.location.href = location_url+"index.php";
                });
		
        	$("#tasks").load("load_tasklist.php?database=budget", function() { 
			load_task_figures(); 
		});

		$("#tasks").change(function() { load_task_figures(); });
		
                $("#save").click(function() {
                        $("#save").prop("disabled", true);
                        $.post("save_task_figures.php",
                        {
                                database: "budget",
                                taskid: $("#tasks").val(),
                                investments_budget: $("#investments_budget").val(),
                                services_budget: $("#services_budget").val(),
                                consumables_budget: $("#consumables_budget").val(),
                                transport_budget: $("#transport_budget").val(),
                                personnel_budget: $("#personnel_budget").val(),
                                year_budget: $("#year_budget").val(),
                                kfw_phase_budget: $("#kfw_budget").val()
                        },
                        function(data, status){
                                if (data == "OK") {
                                                $("#submit_message").html("Saved");
                                } else {
                                        $("#submit_message").html("<span style=\"color:red\">"+data+"</span>");
                                }
                                setTimeout(function() {
                                                $("#save").prop("disabled", false);
                                                $("#submit_message").html("");
                                        }, 3000);
                        });

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
        Edit Task Figures</span></p>

    <p>
    <table border="0">
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
          <td> Investments Budget </td>
          <td> <input type="text" id="investments_budget"/>
          </td>
        </tr>
        <tr>
          <td> Services Budget </td>
          <td> <input type="text" id="services_budget"/>
          </td>
        </tr>
        <tr>
          <td> Consumables Budget </td>
          <td> <input type="text" id="consumables_budget"/>
          </td>
        </tr>
        <tr>
          <td> Transport Budget </td>
          <td> <input type="text" id="transport_budget"/>
          </td>
        </tr>
        <tr>
          <td> Personnel Budget </td>
          <td> <input type="text" id="personnel_budget"/>
          </td>
        </tr>
        <tr>
          <td> Budget for Current Year</td>
          <td> <input type="text" id="year_budget"/>
          </td>
        </tr>
        <tr>
          <td> KFW Phase Budget </td>
          <td> <input type="text" id="kfw_budget"/>
          </td>
        </tr>

    </table>
    </p>
    <p>
   <!-- --------------------------- -->
    <table  border="0">
      <tbody id="in_table">
        <tr>
          <td  style="text-align: right;"></td>
             <input type="button" id="cancel" value="< Back"/>
             <input type="button" id="save" value="Save"/>
             <span id="submit_message"></span>
          </td>
        </tr>
      </tbody>
    </table>
    <!-- ----------------------------- -->
    </p>
   </div>
  </body>
</html>




