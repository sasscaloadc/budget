
<?php
require_once("load.php");

class load_figures_data extends load
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
		
                return " SELECT b.*, c.value as livexrate, t.currency ". //, d.xrate as prevxrate, d.received as prevreceived ".
		       " FROM budget b INNER JOIN task t on t.id = task_id ".
		       "               INNER JOIN currencies c on t.currency = c.code ".
                       " WHERE b.task_id = ".$taskid." AND b.year = ".$year." AND b.quarter = ".$quarter;
        }

        public function process_row($row) {
		$output = Array();
		$output["taskid"] = $row["task_id"];
		$output["status"] = $row["status"];
		$output["investments"] = $row["investments"];
		$output["services"] = $row["services"];
		$output["transport"] = $row["transport"];
		$output["personnel"] = $row["personnel"];
		$output["consumables"] = $row["consumables"];
		$output["investments_actual"] = $row["investments_actual"];
		$output["services_actual"] = $row["services_actual"];
		$output["transport_actual"] = $row["transport_actual"];
		$output["personnel_actual"] = $row["personnel_actual"];
		$output["consumables_actual"] = $row["consumables_actual"];
		$output["admin"] = $row["admin"];
		$output["prev_xrate"] = $row["prev_xrate"];
		$output["prev_unused"] = $row["prev_unused"];
		$output["xrate_requested"] = $row["xrate_requested"];
		$output["xrate"] = $row["xrate"];
		$output["received"] = $row["received"];
		$output["received_date"] = $row["received_date"];
		$output["unused"] = $row["unused"];
		$output["livexrate"] = $row["livexrate"];
		$output["currency"] = $row["currency"];
		$output["total"] = $row["investments"]+$row["services"]+$row["transport"]+$row["personnel"]+$row["consumables"];
		$output["total_actual"] = $row["investments_actual"]+$row["services_actual"]+$row["transport_actual"]+$row["personnel_actual"]+$row["consumables_actual"];
		return json_encode($output);
	}
}

	$lt = new load_figures_data();

	echo $lt->query();
?>
