<?php
  include 'check_access.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Edit Task Completion</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script>
var location_url = "<?php echo $location_url ?>";
var original;

	function load_completion() {

	        $.get("load_completion.php?database=budget&taskid="+ $("#tasks").val().trim(), function(data, status){
			output = JSON.parse(data);

			$("#completion").val(output.completion);
			original = eval($("#completion").val());

		});

	}

        $(document).ready(function(){


                $("#cancel").click(function() {
                        window.location.href = location_url+"index.php";
                });
		
        	$("#tasks").load("load_tasklist.php?database=budget", function() { 
			load_completion(); 
		});

		$("#tasks").change(function() { load_completion(); });
		
                $("#save").click(function() {

                        $("#save").prop("disabled", true);
			if (eval($("#completion").val()) < original) {
                                $("#submit_message").html("<span style=\"color:red\">Cannot enter a smaller value than previously</span>");
                                setTimeout(function() {
                                        $("#save").prop("disabled", false);
                                        $("#submit_message").html("");
					$("#completion").val(original);
                                }, 3000);
				return;
			}
                        $.post("save_completion.php",
                        {
                                database: "budget",
                                taskid: $("#tasks").val(),
                                completion: $("#completion").val(),
                        },
                        function(data, status){
                                if (data == "OK") {
                                                $("#submit_message").html("Saved");
						original = eval( $("#completion").val());
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
        Edit Task Completion</span></p>

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
          <td> Percentage Task Completion </td>
          <td> <input type="text" id="completion"/>% </td>
        </tr>
        <tr>
          <td> &nbsp; </td>
          <td valign="top"> <span style="font-size:8px; font-style:italic; vertical-align:top">Please enter a new value here</span> </td>
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




