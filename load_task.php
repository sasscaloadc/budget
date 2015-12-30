<?php
require_once("db.php");

class load_task
{
        public function getJSON() {
                $conn = getConnection();

                $taskid = array_key_exists("taskid",$_GET) ?  $_GET["taskid"] : "";
                if (empty($taskid)) {
                        $taskid = array_key_exists("taskid",$_POST) ?  $_POST["taskid"] : "";
                }

                $sql = "SELECT task_id, year, quarter FROM budget WHERE task_id = ".(empty($taskid)?0:$taskid)." order by task_id, year, quarter";

                $output = Array();
		$year = "";
	
		$output["taskid"] = $taskid;

		$output["years"] = Array();
		$yearObject["year"] = 0;

                $result = pg_query($conn, $sql);
                if ($result && (pg_num_rows($result) > 0)) {
                        while($row = pg_fetch_array($result)) {
				if ($yearObject["year"] != $row["year"]) {
					$yearObject["year"] = $row["year"];
					$yearObject["quarters"] = Array();
					array_push($output["years"], $yearObject);
				}
				array_push($output["years"][count($output["years"]) - 1]["quarters"], $row["quarter"]);
                        }
                }

		$sql = " SELECT t.*, c.value as localxrate, to_char(c.updated, 'DD-MM-YYYY HH:MIam') as dt ".
		       " FROM task t ".
    		       " INNER JOIN currencies c on t.currency = c.code ".
       		       " WHERE t.id = ".$taskid;

                $result = pg_query($conn, $sql);
                if ($result && (pg_num_rows($result) > 0)) {
                        $row = pg_fetch_array($result);
			$output["currency"] = $row["currency"];
			$output["investments_budget"] = $row["investments_budget"];
			$output["services_budget"] = $row["services_budget"];
			$output["consumables_budget"] = $row["consumables_budget"];
			$output["transport_budget"] = $row["transport_budget"];
			$output["personnel_budget"] = $row["personnel_budget"];
			$output["budget"] = $row["investments_budget"] + $row["services_budget"] + $row["consumables_budget"] + $row["transport_budget"] + $row["personnel_budget"]; 
			$output["localxrate"] = $row["localxrate"];
			$output["localxrateupdated"] = $row["dt"];
		}

		return json_encode($output);
                pg_close($conn);
                return $output;
	
        }

}

	$ly = new load_task();

	echo $ly->getJSON();
?>
