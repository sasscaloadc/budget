<?php
require_once("load.php");

class load_task
{

        static $servername = "caprivi.sasscal.org";
        static $DBservername = "localhost";
        static $username = "postgres";
        static $password = "5455c4l_";
        static $dbname = "postgres";

        static function getConnection() {
                $database = array_key_exists("database",$_GET) ?  $_GET["database"] : "";
                if (empty($database)) {
                        $database = array_key_exists("database",$_POST) ?  $_POST["database"] : "";
                }
                load::$dbname = $database;

                // Create connection
                //$conn = pg_pconnect("host=".load::$servername." user=".load::$username." password=".load::$password);
                $conn = pg_pconnect("host=".load::$servername." dbname=".load::$dbname." user=".load::$username." password=".load::$password);
                if (!$conn) {
                        die("Database connection failed. ");
                }
                return $conn;
        }

        public function get_sql() {
                $taskid = array_key_exists("taskid",$_GET) ?  $_GET["taskid"] : "";
                if (empty($taskid)) {
                        $taskid = array_key_exists("taskid",$_POST) ?  $_POST["taskid"] : "";
                }

                $sql = "SELECT taskid, year quarter FROM budget WHERE task_id = ".(empty($taskid)?0:$taskid)." order by taskid, year, quarter";
		return $sql;
        }

        public function getJSON() {
                $conn = load_task::getConnection();

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

		//$sql = "SELECT t.*, c.value as localxrate, to_char(c.updated, 'DD-MM-YYYY HH:MIam') as dt FROM task t INNER JOIN currencies c on t.currency = c.code WHERE id = ".$taskid ;
		$sql = " SELECT t.*, b.*, c.value as localxrate, to_char(c.updated, 'DD-MM-YYYY HH:MIam') as dt ".
		       " FROM task t ".
    		       " INNER JOIN currencies c on t.currency = c.code ".
    		       " INNER JOIN (SELECT task_id, SUM(investments_actual / CASE WHEN (prev_unused = 0 AND received = 0) ". 
		       "				 THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_investments_euro, ".
		       "	SUM(services_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
		       "				THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_services_euro, ".
		       "	SUM(consumables_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
		       "				THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_consumables_euro, ".
		       "	SUM(personnel_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
		       "				THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_personnel_euro, ".
		       "	SUM(transport_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
		       "				THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_transport_euro ".
		       " 	FROM budget ".
		       " 	WHERE status = 3 ".
		       " 	GROUP BY task_id) b on b.task_id = t.id ".
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
			$output["cum_investments_euro"] = $row["cum_investments_euro"];
			$output["cum_services_euro"] = $row["cum_services_euro"];
			$output["cum_consumables_euro"] = $row["cum_consumables_euro"];
			$output["cum_personnel_euro"] = $row["cum_personnel_euro"];
			$output["cum_transport_euro"] = $row["cum_transport_euro"];
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
