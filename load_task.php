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

		$sql = "SELECT t.*, c.value as localxrate, to_char(c.updated, 'DD-MM-YYYY HH:MIam') as dt FROM task t INNER JOIN currencies c on t.currency = c.code WHERE id = ".$taskid ;
                $result = pg_query($conn, $sql);
                if ($result && (pg_num_rows($result) > 0)) {
                        $row = pg_fetch_array($result);
			$output["currency"] = $row["currency"];
			$output["budget"] = $row["budget"];
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
