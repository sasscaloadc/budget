<?php
require_once("db.php");
include 'check_access.php';
	echo "Logged in as ".$_SESSION['firstname'];

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

	$conn = getConnection();

	$sql = " SELECT *
                 FROM task t INNER JOIN access a on t.owner = a.username
		 WHERE id = ".$taskid;

	$task = Array();
	$result = pg_query($conn, $sql);
	if ($result && (pg_num_rows($result) > 0)) {
		$row = pg_fetch_array($result);
		$task["id"] = $taskid;
		$task["title"] = $row["description"];
		$task["thematic_area"] = $row["thematic_area"];
		$task["country"] = $row["country"];
		$task["institution"] = $row["institution"];
		$task["pi"] = $row["firstname"]." ".$row["lastname"];
	}

	$sql = " SELECT *, personnel + investments + consumables + services + transport as total_budget,
		   personnel_actual + investments_actual + consumables_actual + services_actual + transport_actual as total_actual
                 FROM budget
		 WHERE task_id = ".$taskid."
		   AND year = ".$year."
		   AND quarter = ".$quarter;
error_log($sql);
	$result = pg_query($conn, $sql);
	if ($result && (pg_num_rows($result) > 0)) {
		$row = pg_fetch_array($result);
		$task["personnel_budget"] = $row["personnel"];
		$task["investments_budget"] = $row["investments"];
		$task["consumables_budget"] = $row["consumables"];
		$task["services_budget"] = $row["services"];
		$task["transport_budget"] = $row["transport"];
		$task["personnel_actual"] = $row["personnel_actual"];
		$task["investments_actual"] = $row["investments_actual"];
		$task["consumables_actual"] = $row["consumables_actual"];
		$task["services_actual"] = $row["services_actual"];
		$task["transport_actual"] = $row["transport_actual"];
		$task["total_actual"] = $row["total_actual"];
		$task["total_budget"] = $row["total_budget"];
		$task["prev_unused"] = $row["prev_unused"];
		$task["received"] = $row["received"];
	}

	$year_next = $quarter < 4 ? $year : $year + 1;
	$quarter_next = $quarter < 4 ? $quarter + 1 : 1;

	$sql = " SELECT *, personnel + investments + consumables + services + transport as total_forecast
                 FROM budget
		 WHERE task_id = ".$taskid."
		   AND year = ".$year_next."
		   AND quarter = ".$quarter_next;

	$result = pg_query($conn, $sql);
	if ($result && (pg_num_rows($result) > 0)) {
		$row = pg_fetch_array($result);
		$task["personnel_forecast"] = $row["personnel"];
		$task["investments_forecast"] = $row["investments"];
		$task["consumables_forecast"] = $row["consumables"];
		$task["services_forecast"] = $row["services"];
		$task["transport_forecast"] = $row["transport"];
		$task["total_forecast"] = $row["total_forecast"];
	}
	setlocale(LC_MONETARY, 'en_US');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Task Quarterly Report</title>
    <style>
body {
	font-family: verdana,arial,sans-serif;
}
table.task_table {
	font-family: verdana,arial,sans-serif;
	font-size:11px;
	color:#333333;
	border-width: 1px;
	border-color: #666666;
	border-collapse: collapse;
}
table.task_table th {
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #666666;
	background-color: #dedede;
}
table.task_table td {
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #666666;
	background-color: #ffffff;
}
    </style>
  </head>
  <body>
   <div id="container">
        <h2>QUARTERLY FINANCIAL STATEMENT: TASK <?php echo $taskid; ?> </h2>
	<h3>Reporting Period <?php echo $year; ?> Q<?php echo $quarter; ?></h3>
	<table class="task_table">
	  <tr>
		<td><b>Budget Task ID:</b></td>
		<td><?php echo $task["id"]; ?></td>
	  </tr> 
	  <tr>
		<td><b>Title:</b></td>
		<td><?php echo $task["title"]; ?></td>
	  </tr> 
	  <tr>
		<td><b>Thematic Area:</b></td>
		<td><?php echo $task["thematic_area"]; ?></td>
	  </tr> 
	  <tr>
		<td><b>Country:</b></td>
		<td><?php echo $task["country"]; ?></td>
	  </tr> 
	  <tr>
		<td><b>Institution:</b></td>
		<td><?php echo $task["institution"]; ?></td>
	  </tr> 
	  <tr>
		<td><b>Principal Investigator:</b></td>
		<td><?php echo $task["pi"]; ?></td>
	  </tr> 
	</table>

	<p></p>
        <table class="task_table">
	  <tr>
		<th>Budget Line</th> 
		<th>Local Amount of Measure as Agreed</th> 
		<th>Reported Expenditure</th> 
		<th>Still to be Disbursed</th> 
		<th>Planned Expenditure for Next 3 Months</th> 
	  </tr>
	  <tr>
		<td>Personnel</td>
		<td><?php echo money_format("%!n", $task["personnel_budget"]); ?></td>
		<td><?php echo money_format("%!n", $task["personnel_actual"]); ?></td>
		<td><?php echo money_format("%!n", $task["personnel_tbd"]); ?></td>
		<td><?php echo money_format("%!n", $task["personnel_forecast"]); ?></td>
	  </tr>
	  <tr>
		<td>Investments</td>
		<td><?php echo money_format("%!n", $task["investments_budget"]); ?></td>
		<td><?php echo money_format("%!n", $task["investments_actual"]); ?></td>
		<td><?php echo money_format("%!n", $task["investments_tbd"]); ?></td>
		<td><?php echo money_format("%!n", $task["investments_forecast"]); ?></td>
	  </tr>
	  <tr>
		<td>Consumables</td>
		<td><?php echo money_format("%!n", $task["consumables_budget"]); ?></td>
		<td><?php echo money_format("%!n", $task["consumables_actual"]); ?></td>
		<td><?php echo money_format("%!n", $task["consumables_tbd"]); ?></td>
		<td><?php echo money_format("%!n", $task["consumables_forecast"]); ?></td>
	  </tr>
	  <tr>
		<td>Services</td>
		<td><?php echo money_format("%!n", $task["services_budget"]); ?></td>
		<td><?php echo money_format("%!n", $task["services_actual"]); ?></td>
		<td><?php echo money_format("%!n", $task["services_tbd"]); ?></td>
		<td><?php echo money_format("%!n", $task["services_forecast"]); ?></td>
	  </tr>
	  <tr>
		<td>Transport</td>
		<td><?php echo money_format("%!n", $task["transport_budget"]); ?></td>
		<td><?php echo money_format("%!n", $task["transport_actual"]); ?></td>
		<td><?php echo money_format("%!n", $task["transport_tbd"]); ?></td>
		<td><?php echo money_format("%!n", $task["transport_forecast"]); ?></td>
	  </tr>
	  <tr>
		<td><b>Total</b></td>
		<td><b><?php echo money_format("%!n", $task["total_budget"]); ?></b></td>
		<td><b><?php echo money_format("%!n", $task["total_actual"]); ?></b></td>
		<td><b><?php echo money_format("%!n", $task["total_tbd"]); ?></b></td>
		<td><b><?php echo money_format("%!n", $task["total_forecast"]); ?></b></td>
	  </tr>
	</table>
	<p></p>
        <table class="task_table">
	  <tr>
		<td><b>1 Balance of Account at Beginning of Quarter</b></td>
		<td><b><?php echo money_format("%!n", $task["prev_unused"]); ?></b></td>
	  </tr>
	  <tr>
		<td>2 Received Payments (amounts credited)</td>
		<td><?php echo money_format("%!n", $task["received"]); ?></td>
	  </tr>
	  <tr>
		<td>3 Reported Expenditures (amounts debited)</td>
		<td><?php echo money_format("%!n", $task["total_actual"]); ?></td>
	  </tr>
	  <tr>
		<td><b>4 Cash Balance End of Quarter</b></td>
		<td><b><?php echo money_format("%!n", $task["prev_unused"] + $task["received"] - $task["total_actual"]); ?></b></td>
	  </tr>
	  <tr>
		<td>5 Expenditures Planned for Next 3 Months</td>
		<td><?php echo money_format("%!n", $task["total_forecast"]); ?></td>
	  </tr>
	  <tr>
		<td><b>6 Amount to be Replenished</b></td>
		<td><b><?php echo money_format("%!n", $task["total_forecast"] - $task["prev_unused"] - $task["received"] + $task["total_actual"]); ?></b></td>
	  </tr>
	</table>
   </div>
  </body>
</html>
