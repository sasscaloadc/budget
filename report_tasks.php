<?php
	include 'check_access.php';
	if ($_SESSION["access"] > 1) {
		header("Location: http://caprivi.sasscal.org/budget/no_access.php");
	} else {
		echo "Logged in as ".$_SESSION['firstname'];
	}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta  content="text/html; charset=UTF-8"  http-equiv="content-type">
    <title>Task Summary Report</title>
    <style>
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
h3 {
	font-family: verdana,arial,sans-serif;
	font-size:16px;
}
    </style>
  </head>
  <body>
<?php
require_once("db.php");


	$conn = getConnection();

	$sql = " SELECT year, task_id, 
                    (SELECT  (personnel + investments + consumables + services + transport) 
                     FROM budget b WHERE b.year = k.year and b.task_id = k.task_id and quarter = 1) as q1_budget,
                    (SELECT  (personnel_actual + investments_actual + consumables_actual + services_actual + transport_actual) 
                     FROM budget b WHERE b.year = k.year and b.task_id = k.task_id and quarter = 1) as q1_spent,
                    (SELECT  (personnel + investments + consumables + services + transport) 
                     FROM budget b WHERE b.year = k.year and b.task_id = k.task_id and quarter = 2) as q2_budget,
                    (SELECT  (personnel_actual + investments_actual + consumables_actual + services_actual + transport_actual) 
                     FROM budget b WHERE b.year = k.year and b.task_id = k.task_id and quarter = 2) as q2_spent,
                    (SELECT  (personnel + investments + consumables + services + transport) 
                     FROM budget b WHERE b.year = k.year and b.task_id = k.task_id and quarter = 3) as q3_budget,
                    (SELECT  (personnel_actual + investments_actual + consumables_actual + services_actual + transport_actual) 
                     FROM budget b WHERE b.year = k.year and b.task_id = k.task_id and quarter = 3) as q3_spent,
                    (SELECT  (personnel + investments + consumables + services + transport) 
                     FROM budget b WHERE b.year = k.year and b.task_id = k.task_id and quarter = 4) as q4_budget,
                    (SELECT  (personnel_actual + investments_actual + consumables_actual + services_actual + transport_actual) 
                     FROM budget b WHERE b.year = k.year and b.task_id = k.task_id and quarter = 4) as q4_spent,
                    (SELECT SUM(personnel + investments + consumables + services + transport) 
                     FROM budget b WHERE b.year = k.year and b.task_id = k.task_id ) as budget_total,
                    (SELECT SUM(personnel_actual + investments_actual + consumables_actual + services_actual + transport_actual) 
                     FROM budget b WHERE b.year = k.year and b.task_id = k.task_id ) as spent_total
                 FROM budget k
                 group by 1, 2
                 order by 1, 2";
	
	$yearObject = 0;
	$total_budget_q1 = $total_budget_q2 = $total_budget_q3 = $total_budget_q4 = 0;
	$total_spent_q1 = $total_spent_q2 = $total_spent_q3 = $total_spent_q4 = 0;

	$result = pg_query($conn, $sql);
	if ($result && (pg_num_rows($result) > 0)) {
		while($row = pg_fetch_array($result)) {
			if ($yearObject != $row["year"]) {
				if ($yearObject != 0) {
					echo "<tr><td><b>Total:</b></td>"; 
					echo "<td><b>".$total_budget_q1."</b></td>"; 
					echo "<td><b>".$total_spent_q1."</b></td>"; 
					echo "<td><b>".$total_budget_q2."</b></td>"; 
					echo "<td><b>".$total_spent_q2."</b></td>"; 
					echo "<td><b>".$total_budget_q3."</b></td>"; 
					echo "<td><b>".$total_spent_q3."</b></td>"; 
					echo "<td><b>".$total_budget_q4."</b></td>"; 
					echo "<td><b>".$total_spent_q4."</b></td>"; 
					echo "<td>&nbsp;</td>"; 
					echo "<td><b>".$total_budget."</b></td>"; 
					echo "<td><b>".$total_spent."</b></td>"; 
					echo "</tr>";
					echo "</table>";
					$total_budget = $total_budget_q1 = $total_budget_q2 = $total_budget_q3 = $total_budget_q4 = 0;
					$total_spent = $total_spent_q1 = $total_spent_q2 = $total_spent_q3 = $total_spent_q4 = 0;
				}
				$yearObject = $row["year"];
				echo "<h3>".$row["year"]."</h3>";
				echo "<table class=\"task_table\">";
				echo "<tr><th>Task ID</th>"; 
				echo "<th>Q1 Budgeted</th>"; 
				echo "<th>Q1 Spent</th>"; 
				echo "<th>Q2 Budgeted</th>"; 
				echo "<th>Q2 Spent</th>"; 
				echo "<th>Q3 Budgeted</th>"; 
				echo "<th>Q3 Spent</th>"; 
				echo "<th>Q4 Budgeted</th>"; 
				echo "<th>Q4 Spent</th>"; 
				echo "<th>&nbsp;</th>"; 
				echo "<th>Budgeted Total</th>"; 
				echo "<th>Spent Total</th>"; 
				echo "</tr>";
			}
			echo "<tr><td>".$row["task_id"]."</td>"; 
			echo "<td>".$row["q1_budget"]."</td>";   $total_budget_q1 += $row["q1_budget"];
			echo "<td>".$row["q1_spent"]."</td>";    $total_spent_q1  += $row["q1_spent"]; 
			echo "<td>".$row["q2_budget"]."</td>";   $total_budget_q2 += $row["q2_budget"];
			echo "<td>".$row["q2_spent"]."</td>";    $total_spent_q2  += $row["q2_spent"];
			echo "<td>".$row["q3_budget"]."</td>";   $total_budget_q3 += $row["q3_budget"];
			echo "<td>".$row["q3_spent"]."</td>";    $total_spent_q3  += $row["q3_spent"];
			echo "<td>".$row["q4_budget"]."</td>";   $total_budget_q4 += $row["q4_budget"];
			echo "<td>".$row["q4_spent"]."</td>";    $total_spent_q4  += $row["q4_spent"];
			$total_budget += $row["q1_budget"] + $row["q2_budget"] + $row["q3_budget"] + $row["q4_budget"];
			$total_spent += $row["q1_spent"] + $row["q2_spent"] + $row["q3_spent"] + $row["q4_spent"];
			echo "<td>&nbsp;</td>"; 
			echo "<td>".$row["budget_total"]."</td>"; 
			echo "<td>".$row["spent_total"]."</td>"; 
			echo "</tr>";
		}
	}
	pg_close($conn);
?>
   </body>
</html>
