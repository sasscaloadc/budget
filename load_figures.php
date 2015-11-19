
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
		
                return "SELECT * FROM budget WHERE task_id = ".$taskid." AND year = ".$year." AND quarter = ".$quarter;
        }

        public function process_row($row) {
		$status = $row["status"];
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
					"<tr><td>Status: </td><td>".$row["status"]."</td></tr>";
				break;
			case 2 :
				break;
			case 3 :
				break;
		}
                return $html;
        }

}

	$lt = new load_figures();

	echo $lt->query();
?>
