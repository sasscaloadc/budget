
<?php
require_once("load.php");

class load_figures extends load
{
        public function get_sql() {
                $taskid = array_key_exists("taskid",$_GET) ?  $_GET["taskid"] : "";
                if (empty($taskid)) {
                        $taskid = array_key_exists("taskid",$_POST) ?  $_POST["taskid"] : "";
                }
		
                $year = array_key_exists("year",$_GET) ?  $_GET["year"] : "";
                if (empty($year)) {
                        $year = array_key_exists("year",$_POST) ?  $_POST["year"] : "";
                }
		
                $quarter = array_key_exists("quarter",$_GET) ?  $_GET["quarter"] : "";
                if (empty($quarter)) {
                        $quarter = array_key_exists("quarter",$_POST) ?  $_POST["quarter"] : "";
                }
		
                return "SELECT b.*, t.currency, c.value as livexrate FROM budget b INNER JOIN task t on t.id = task_id INNER JOIN currencies c on t.currency = c.code WHERE task_id = ".$taskid." AND year = ".$year." AND quarter = ".$quarter;
        }

        public function process_row($row) {
		$status = $row["status"];
		$total = $row["investments"]+$row["services"]+$row["transport"]+$row["personnel"]+$row["consumables"];
		$total_actual = $row["investments_actual"]+$row["services_actual"]+$row["transport_actual"]+$row["personnel_actual"]+$row["consumables_actual"];
		switch ($status) {
			case 1 : 
                		$html = "<tr><th>Expense</th><th>Local Currency</th><th>Euro</th></tr>".
                			"<tr><td>Investments: </td>
						<td><input id=\"investments_local\" type=\"text\" name=\"investments_local\" value=\"".$row["investments"]."\" onchange=\"update_euro('investments');\"/></td>
						<td><input id=\"investments_euro\" type=\"text\" name=\"investments_euro\" value=\"".$row["investments"]."\" onchange=\"update_local('investments');\"/></td></tr>".
                			"<tr><td>Services: </td>
						<td><input id=\"services_local\" type=\"text\" name=\"services_local\" value=\"".$row["services"]."\" onchange=\"update_euro('services');\"/></td>
						<td><input id=\"services_euro\" type=\"text\" name=\"services_euro\" value=\"".$row["services"]."\" onchange=\"update_local('services');\"/></td></tr>".
                			"<tr><td>Transport: </td>
						<td><input id=\"transport_local\" type=\"text\" name=\"transport_local\" value=\"".$row["transport"]."\" onchange=\"update_euro('transport');\"/></td>
						<td><input id=\"transport_euro\" type=\"text\" name=\"transport_euro\" value=\"".$row["transport"]."\" onchange=\"update_local('transport');\"/></td></tr>".
                			"<tr><td>Personnel: </td>
						<td><input id=\"personnel_local\" type=\"text\" name=\"personnel_local\" value=\"".$row["personnel"]."\" onchange=\"update_euro('personnel');\"/></td>
						<td><input id=\"personnel_euro\" type=\"text\" name=\"personnel_euro\" value=\"".$row["personnel"]."\" onchange=\"update_local('personnel');\"/></td></tr>".
                			"<tr><td>Consumables: </td>
						<td><input id=\"consumables_local\" type=\"text\" name=\"consumables_local\" value=\"".$row["consumables"]."\" onchange=\"update_euro('consumables');\"/></td>
						<td><input id=\"consumables_euro\" type=\"text\" name=\"consumables_euro\" value=\"".$row["consumables"]."\" onchange=\"update_local('consumables');\"/></td></tr>".
					"<tr><td> Total : </td><td> <span id=\"total_local\">".$total."</span></td><td> <span id=\"total_euro\"></span></td></tr>".
					"<tr><td></td><td> <input type=\"button\" value=\"Save\" id=\"save_1\"><span id=\"save_message\"></span></td><td> <input type=\"button\" value=\"Submit\" id=\"submit_1\"><span id=\"submit_message\"></span></td></tr>";
				break;
			case 2 :
                		$html = "<tr><th>Expense</th><th>Local Currency</th><th>Euro</th></tr>".
                			"<tr><td>Investments: </td>".
						"<td><span id=\"investments_local\">".$row["investments"]."</span> </td>".
						"<td><span id=\"investments_euro\">".$row["investments"]/$row["livexrate"]."</span> </td>".
                			"<tr><td>Services: </td>".
						"<td><span id=\"services_local\">".$row["services"]."</span> </td>".
						"<td><span id=\"services_euro\">".$row["services"]/$row["livexrate"]."</span> </td>".
                			"<tr><td>Transport: </td>".
						"<td><span id=\"transport_local\">".$row["transport"]."</span> </td>".
						"<td><span id=\"transport_euro\">".$row["transport"]/$row["livexrate"]."</span> </td>".
                			"<tr><td>Personnel: </td>".
						"<td><span id=\"personnel_local\">".$row["personnel"]."</span> </td>".
						"<td><span id=\"personnel_euro\">".$row["personnel"]/$row["livexrate"]."</span> </td>".
                			"<tr><td>Consumables: </td>".
						"<td><span id=\"consumables_local\">".$row["consumables"]."</span> </td>".
						"<td><span id=\"consumables_euro\">".$row["consumables"]/$row["livexrate"]."</span> </td>".
					"<tr style=\"font-weight:bold;\"><td class=\"topline\"> Total Requested: </td><td class=\"topline\"> <span id=\"total_local\">".$total."</span></td>".
					 		      "<td class=\"topline\"> <span id=\"total_euro\">".$total/$row["livexrate"]."</span></td></tr>".
					"<tr><td> </td><td> Received </td><td> <input id=\"received_euro\" type=\"text\"></td></tr>".
					"<tr><td> </td><td> Date Received </td><td> <input id=\"received_date\" type=\"text\"></td></tr>".
					"<tr><td> </td><td> Exchange Rate </td><td> <input id=\"received_xrate\" type=\"text\"></td></tr>".
					"<tr><td></td><td></td><td> <input type=\"button\" value=\"Submit\" id=\"submit_2\"><span id=\"submit_message\"></span></td></tr>";
				break;
			case 3 :
error_log("investments_actual: ".$row["investments_actual"]);
error_log("livexrate: ".$row["livexrate"]);
error_log("euro: ".$row["investments_actual"]/$row["livexrate"]);
                                $html = "<tr><th>Expense</th><th>Requested ".$row["currency"]."</th><th>Spent ".$row["currency"]."</th><th>Spent Euro</th></tr>".
                                        "<tr><td>Investments: </td>".
                                            "<td><span id=\"investments_requested\">".$row["investments"]."</span> </td>".
                                            "<td><input id=\"investments_local\" type=\"text\" value=\"".$row["investments_actual"]."\" onchange=\"update_euro('investments');\"/></td>".
					    "<td><span id=\"investments_euro\">".$row["investments_actual"]/$row["livexrate"]."</span> </td>".
                                        "<tr><td>Services: </td>".
                                            "<td><span id=\"services_requested\">".$row["services"]."</span> </td>".
                                            "<td><input id=\"services_local\" type=\"text\" value=\"".$row["services_actual"]."\" onchange=\"update_euro('services');\"/></td>".
					    "<td><span id=\"services_euro\">".$row["services_actual"]/$row["livexrate"]."</span> </td>".
                                        "<tr><td>Transport: </td>".
                                            "<td><span id=\"transport_requested\">".$row["transport"]."</span> </td>".
                                            "<td><input id=\"transport_local\" type=\"text\" value=\"".$row["transport_actual"]."\" onchange=\"update_euro('transport');\"/></td>".
					    "<td><span id=\"transport_euro\">".$row["transport_actual"]/$row["livexrate"]."</span> </td>".
                                        "<tr><td>Personnel: </td>".
                                            "<td><span id=\"personnel_requested\">".$row["personnel"]."</span> </td>".
                                            "<td><input id=\"personnel_local\" type=\"text\" value=\"".$row["personnel_actual"]."\" onchange=\"update_euro('personnel');\"/></td>".
					    "<td><span id=\"personnel_euro\">".$row["personnel_actual"]/$row["livexrate"]."</span> </td>".
                                        "<tr><td>Consumables: </td>".
                                            "<td><span id=\"consumables_requested\">".$row["consumables"]."</span> </td>".
                                            "<td><input id=\"consumables_local\" type=\"text\" value=\"".$row["consumables_actual"]."\" onchange=\"update_euro('consumables');\"/></td>".
					    "<td><span id=\"consumables_euro\">".$row["consumables_actual"]/$row["livexrate"]."</span> </td>".
                                        "<tr style=\"font-weight:bold;\"><td class=\"topline\"> Total: </td>".
                                            "<td class=\"topline\"> <span id=\"total_requested\">".$total."</span></td>".
                                            "<td class=\"topline\"> <span id=\"total_local\">".$total_actual."</span></td>".
					    "<td class=\"topline\"> <span id=\"total_euro\">".$total_actual/$row["livexrate"]."</span></td></tr>".
                                        "<tr><td>&nbsp</td><td>&nbsp</td><td> Unused: </td><td><span id=\"total_unused\">".($total-$total_actual)/$row["livexrate"]."</span></td></tr>".
					"<tr><td>&nbsp;</td><td>&nbsp;</td>".
                                            "<td> <input type=\"button\" value=\"Save\" id=\"save_3\"><span id=\"save_message\"></span></td>".
                                            "<td> <input type=\"button\" value=\"Submit\" id=\"submit_3\"><span id=\"submit_message\"></span></td></tr>";
				break;
			case 4 :
				$html = "<h3>Status ".$status."</h3>";
				break;
			default:
				$html = "<h3>Status ".$status."</h3>";
		}
                return $html;
        }

}

	$lt = new load_figures();

	echo $lt->query();
?>
