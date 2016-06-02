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

	$actual = Array();
	$budget = Array();

	$conn = getConnection();

	$sql = " SELECT SUM(investments_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
                    " THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_investments_euro, ".
                    "                 SUM(services_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
                    " THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_services_euro, ".
                    "                 SUM(consumables_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
                    " THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_consumables_euro, ".
                    "                 SUM(personnel_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
                    " THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_personnel_euro, ".
                    "                 SUM(transport_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
                    " THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_transport_euro, ".
                    "                 SUM(admin / xrate) AS cum_admin_euro ".
                    "                     FROM budget ".
                    "                     WHERE status = 3 ".
                    "                        AND task_id = ".$taskid.
                    "                     GROUP BY task_id ";
	

	$result = pg_query($conn, $sql);
	if ($result && (pg_num_rows($result) > 0)) {
		$row = pg_fetch_array($result);
		$actual["investments"] = $row["cum_investments_euro"];
		$actual["services"] = $row["cum_services_euro"];
		$actual["consumables"] = $row["cum_consumables_euro"];
		$actual["personnel"] = $row["cum_personnel_euro"];
		$actual["transport"] = $row["cum_transport_euro"];
		$actual["admin"] = $row["cum_admin_euro"];
	}

	$sql = " SELECT personnel_budget, investments_budget, consumables_budget, services_budget, transport_budget 
                 FROM task WHERE id = ".$taskid;

	$result = pg_query($conn, $sql);
	if ($result && (pg_num_rows($result) > 0)) {
		$row = pg_fetch_array($result);
		$budget["investments"] = $row["investments_budget"];
		$budget["services"] = $row["services_budget"];
		$budget["consumables"] = $row["consumables_budget"];
		$budget["personnel"] = $row["personnel_budget"];
		$budget["transport"] = $row["transport_budget"];
	}

	$total_budget = 0;
	$total_actual = 0;
	$total_disbursement = 0;
	foreach ($budget as $num) {
		$total_budget += $num;
	}
	foreach ($actual as $num) {
		$total_actual += $num;
	}
	setlocale(LC_MONETARY, 'en_US');
?>
<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Task Cumulative Report</title>
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
        <h2>FINANCIAL REPORT FOR TASK <?php echo $taskid; ?> on <?php echo date('j M o'); ?></h2>

	<table class="task_table">
	  <tr>
		<th>Budget Line</th> 
		<th>Total Budget</th> 
		<th>Cumulative Expenditure</th> 
		<th>Remaining Budget</th> 
		<th>% Spent</th> 
	  </tr>
	  <tr>
		<td>Investments</td>
		<td><?php echo money_format("€ %!n", $budget["investments"]); ?></td>
		<td><?php echo money_format("€ %!n", $actual["investments"]); ?></td>
		<td><?php echo money_format("€ %!n", $budget["investments"] - $actual["investments"]); ?></td>
		<td><?php echo round($actual["investments"] / $budget["investments"] * 100.0); ?>%</td>
	  </tr> 
	  <tr>
		<td>Consumables</td>
		<td><?php echo money_format("€ %!n", $budget["consumables"]); ?></td>
		<td><?php echo money_format("€ %!n", $actual["consumables"]); ?></td>
		<td><?php echo money_format("€ %!n", $budget["consumables"] - $actual["consumables"]); ?></td>
		<td><?php echo round($actual["consumables"] / $budget["consumables"] * 100.0); ?>%</td>
	  </tr> 
	  <tr>
		<td>Services</td>
		<td><?php echo money_format("€ %!n", $budget["services"]); ?></td>
		<td><?php echo money_format("€ %!n", $actual["services"]); ?></td>
		<td><?php echo money_format("€ %!n", $budget["services"] - $actual["services"]); ?></td>
		<td><?php echo round($actual["services"] / $budget["services"] * 100.0); ?>%</td>
	  </tr> 
	  <tr>
		<td>Transport</td>
		<td><?php echo money_format("€ %!n", $budget["transport"]); ?></td>
		<td><?php echo money_format("€ %!n", $actual["transport"]); ?></td>
		<td><?php echo money_format("€ %!n", $budget["transport"] - $actual["transport"]); ?></td>
		<td><?php echo round($actual["transport"] / $budget["transport"] * 100.0); ?>%</td>
	  </tr> 
	  <tr>
		<td>Personnel</td>
		<td><?php echo money_format("€ %!n", $budget["personnel"]); ?></td>
		<td><?php echo money_format("€ %!n", $actual["personnel"]); ?></td>
		<td><?php echo money_format("€ %!n", $budget["personnel"] - $actual["personnel"]); ?></td>
		<td><?php echo round($actual["personnel"] / $budget["personnel"] * 100.0); ?>%</td>
	  </tr> 
	  <tr>
		<td>Admin</td>
		<td>&nbsp;</td>
		<td><?php echo money_format("€ %!n", $actual["admin"]); ?></td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr> 
	  <tr>
		<td><b>Total</b></td>
		<td><b><?php echo money_format("€ %!n", $total_budget); ?></b></td>
		<td><b><?php echo money_format("€ %!n", $total_actual); ?></b></td>
		<td><b><?php echo money_format("€ %!n", $total_budget - $total_actual); ?></b></td>
		<td><b><?php echo round($total_actual / $total_budget * 100.0); ?>%</b></td>
	  </tr> 
	</table>

	<p></p>
	Disbursements<br/>
        <table class="task_table">
	  <tr>
		<th>Date</th> 
		<th>Local Amount</th> 
		<th>Exchange Rate</th> 
		<th>Euro Amount</th> 
	  </tr>
          <?php
	$sql = " SELECT received, xrate, received_date 
                 FROM budget
                 WHERE task_id = ".$taskid."
                   AND received_date IS NOT NULL
                 ORDER BY received_date  ";
	
	$result = pg_query($conn, $sql);
        if ($result && (pg_num_rows($result) > 0)) {
                while ($row = pg_fetch_array($result)) {
			echo "<tr>";
			echo "<td>".$row["received_date"]."</td>";
			echo "<td>".money_format("%!n", $row["received"])."</td>";
			echo "<td>".$row["xrate"]."</td>";
			echo "<td>".money_format("€ %!n", $row["received"]/$row["xrate"])."</td>";
			echo "</tr>";
			$total_disbursement += $row["received"]/$row["xrate"];
		}
	}

	pg_close($conn);

	     foreach ($receipts as $date => $amount) {
		echo "$date : $amount <br/>";
	     }

	  ?>
	</table>
	<p></p>
	Summary<br/>
        <table class="task_table">
	  <tr>
		<th>Summary</th> 
		<th>Euro</th> 
	  </tr>
	  <tr>
		<td>Total Budget</td>
		<td><?php echo money_format("€ %!n", $total_budget); ?></td>
	  </tr>
	  <tr>
		<td>Funds Disbursed</td>
		<td><?php echo money_format("€ %!n", $total_disbursement); ?></td>
	  </tr>
	  <tr>
		<td><b>Funds Available for Disbursement</b></td>
		<td><b><?php echo money_format("€ %!n", $total_budget - $total_disbursement); ?></b></td>
	  </tr>
	</table>
        <br/>
        <input type="button" id="cancel" value="< Back" onclick="window.location.href = '<?php echo $location_url."index.php" ?>'" />

   </div>
  </body>
</html>
