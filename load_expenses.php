<?php
require_once("db.php");

class load_expenses
{
        public function getJSON() {
                $conn = getConnection();

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
		$yearquarter = ($year * 10) + $quarter;
                $sql = "SELECT SUM(personnel_actual) AS personnel,
			       SUM(investments_actual) AS investments, 
			       SUM(consumables_actual) AS consumables, 
			       SUM(services_actual) AS services, 
			       SUM(transport_actual) AS transport,
			       SUM(admin) AS admin
			FROM budget 
			WHERE task_id = ".(empty($taskid)?0:$taskid)." 
			  AND (year * 10) + quarter < ".$yearquarter."
			  AND status = 3";

                $output = Array();
	
		$output["taskid"] = $taskid;

                $result = pg_query($conn, $sql);
                if ($result && (pg_num_rows($result) > 0)) {
                        $row = pg_fetch_array($result);
			$output["personnel"] = $row["personnel"];
			$output["investments"] = $row["investments"];
			$output["consumables"] = $row["consumables"];
			$output["services"] = $row["services"];
			$output["transport"] = $row["transport"];
			$output["admin"] = $row["admin"];
		}

		return json_encode($output);
                pg_close($conn);
                return $output;
        }

}

	$ly = new load_expenses();

	echo $ly->getJSON();
?>
