<?php
require_once("load.php");

class load_quarters extends load
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

                $sql = "SELECT quarter FROM budget WHERE task_id = ".(empty($taskid)?0:$taskid)." AND year = ".$year." ORDER BY quarter";
		return $sql;
        }

        public function process_row($row) {
                return "<option value=\"".$row["quarter"]."\">Q".$row["quarter"]."</option>";
        }

}

	$ly = new load_quarters();

	echo $ly->query();
?>
