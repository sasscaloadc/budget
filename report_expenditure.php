<?php
require_once("db.php");
	include 'check_access.php';

        $country = array_key_exists("country",$_GET) ?  $_GET["country"] : "";
        if (empty($country)) {
                $country = array_key_exists("country",$_POST) ?  $_POST["country"] : "";
        }
        $taskid = array_key_exists("taskid",$_GET) ?  $_GET["taskid"] : "";
        if (empty($taskid)) {
                $taskid = array_key_exists("taskid",$_POST) ?  $_POST["taskid"] : "";
        }
        if (empty($taskid)&& empty($country)) {
		echo "<h1>Task ID and Country are both undefined</h1>";
		die();
	}	

        switch(date('n') - 1) {
                        case 11: case 0: case 1: $thisquarter = 1;
                                break;
                        case 2: case 3: case 4: $thisquarter = 2;
                                break;
                        case 5: case 6: case 7: $thisquarter = 3;
                                break;
                        case 8: case 9: case 10: $thisquarter = 4;
                                break;
        }

        $thisyear = date('o');
        $nextq = $thisquarter < 4 ? $thisquarter + 1 : 1;
        $previousq = $thisquarter > 1 ? $thisquarter - 1 : 4;
        $nexty = $thisquarter < 4 ? $thisyear : $thisyear + 1;
        $previousy = $thisquarter > 1 ? $thisyear : $thisyear - 1;


	$actual = Array();

	$conn = getConnection();

	$sql = " SELECT SUM(cum_thisyear_euro) AS cum_thisyear_euro, SUM(year_budget) AS year_budget, SUM(kfw_phase_budget) AS kfw_phase_budget, round(AVG(completed_percentage)) AS completed_percentage ".
               " FROM ( ".
               " SELECT task_id, SUM((investments_actual + services_actual + consumables_actual + personnel_actual + transport_actual) / CASE WHEN (prev_unused = 0 AND received = 0) ".
               "    THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_thisyear_euro, ".
	       "    max(year_budget) AS year_budget, ".
	       "    max(kfw_phase_budget) AS kfw_phase_budget, ".
	       "    max(completed_percentage) AS completed_percentage ".
               " FROM budget b inner join task t on t.id = b.task_id ".
               " WHERE year = ".$thisyear.
               (empty($country) ? " AND task_id = ".$taskid : " AND country = '".$country."' ").
               " GROUP BY 1 ) a ";

	$result = pg_query($conn, $sql);
	if ($result && (pg_num_rows($result) > 0)) {
		$row = pg_fetch_array($result);
		$actual["cum_thisyear_euro"] = $row["cum_thisyear_euro"];
		$actual["year_budget"] = $row["year_budget"];
		$actual["kfw_phase_budget"] = $row["kfw_phase_budget"];
		$actual["completed_percentage"] = $row["completed_percentage"];
	}

	$sql = "  SELECT SUM((investments_actual + services_actual + consumables_actual + personnel_actual + transport_actual) / CASE WHEN (prev_unused = 0 AND received = 0) ".
               "    THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_euro ".
               " FROM budget ".
               " WHERE year >= 2016 ".
               "   AND task_id in ( ".
               (empty($country) ? $taskid : " SELECT id FROM task WHERE country = '".$country."' ").
               "   ) ";

	$result = pg_query($conn, $sql);
	if ($result && (pg_num_rows($result) > 0)) {
		$row = pg_fetch_array($result);
		$actual["cum_euro"] = $row["cum_euro"];
	}
	pg_close($conn);

	setlocale(LC_MONETARY, 'en_US');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Financials for A – Quick overview</title>
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
#container {
	width: 800px;
	margin: 0 auto;
}
    </style>
  </head>
  <body>
   <div id="container">
        <h2>Financials for A – Quick overview <?php if (empty($taskid)) {
					echo "summary for ".$country. " on ". date('j M o');
                                     } else {
                                       echo "TASK ".$taskid. " on ". date('j M o');
 				     } ?> </h2>

	<table class="task_table">
	  <tr>
		<th>Description</th> 
		<th>Amount</th> 
	  </tr>
	  <tr>
		<td>Planned cash outflow during the year  (ONLY <?php echo date('o'); ?>)</td>
		<td><?php echo money_format("€ %!n", $actual["year_budget"]); ?></td>
	  </tr> 
	  <tr>
		<td>Current cash outflow</td>
		<td><?php echo money_format("€ %!n", $actual["cum_thisyear_euro"]); ?></td>
	  </tr> 
	  <tr>
		<td>Projected cash outflow (keine Angabe bitte)</td>
		<td>NO ENTRY</td>
	  </tr> 
	  <tr>
		<td>Original total expenditure</td>
		<td><?php echo money_format("€ %!n", $actual["kfw_phase_budget"]); ?></td>
	  </tr> 
	  <tr>
		<td>Revised total expenditure</td>
		<td>NO ENTRY</td>
	  </tr> 
	  <tr>
		<td>Updated actual expenditure</td>
		<td><?php echo money_format("€ %!n", $actual["cum_euro"]); ?></td>
	  </tr> 
	  <tr>
		<td>Estimated expenditure</td>
		<td><?php echo money_format("€ %!n", $actual["kfw_phase_budget"] - $actual["cum_euro"]); ?></td>
	  </tr> 
	  <tr>
		<td>Updated overall expenditure forecast</td>
		<td><?php echo money_format("€ %!n", $actual["kfw_phase_budget"]); ?></td>
	  </tr> 
	  <tr>
		<td><?php if (empty($taskid)) echo "Average ";?>Percentage Completion - <?php echo "Q".$previousq." ".$previousy; ?></td>
		<td><?php echo $actual["completed_percentage"]."%" ?></td>
	  </tr> 
	</table>
	<br/>
	<input type="button" id="cancel" value="< Back" onclick="window.location.href = '<?php echo $_SESSION['access'] <= 1 ? $location_url."reports.php" : $location_url."pi_main.php" ?>'" />


   </div>
  </body>
</html>
