
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
		
                $sql = " SELECT b.*, c.value as livexrate, t.currency, d.* ". 
		       " FROM budget b INNER JOIN task t on t.id = task_id ".
		       "               INNER JOIN currencies c on t.currency = c.code ".
                       " 	       LEFT OUTER JOIN (SELECT task_id, SUM(investments_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
                       "                                 THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_investments_euro, ".
                       "       		 SUM(services_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
                       "                                THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_services_euro, ".
                       "        	 SUM(consumables_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
                       "                                THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_consumables_euro, ".
                       "      		 SUM(personnel_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
                       "                                THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_personnel_euro, ".
                       "                 SUM(transport_actual / CASE WHEN (prev_unused = 0 AND received = 0) ".
                       "                                THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_transport_euro, ".
                       "                 SUM(admin / CASE WHEN (prev_unused = 0 AND received = 0) ".
                       "                                THEN 1 ELSE (prev_unused + received) / ((prev_unused / prev_xrate) + (received / xrate)) END) AS cum_admin_euro, ".
                       "                 SUM(investments_actual) AS cum_investments, ".
                       "                 SUM(services_actual) AS cum_services, ".
                       "                 SUM(consumables_actual) AS cum_consumables, ".
                       "                 SUM(personnel_actual) AS cum_personnel, ".
                       "                 SUM(transport_actual) AS cum_transport, ".
                       "                 SUM(admin) AS cum_admin ".
                       "                     FROM budget ".
                       "                     WHERE status = 3 ".
                       "                        AND ((year * 10) + quarter) <= ((".$year." * 10) + ".$quarter.") ".
                       "                        AND task_id = ".$taskid.
                       "                     GROUP BY task_id) d on d.task_id = t.id ".

                       " WHERE b.task_id = ".$taskid." AND b.year = ".$year." AND b.quarter = ".$quarter;

//error_log($sql);
		return $sql;
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
                $output["cum_personnel"] = $row["cum_personnel"];
                $output["cum_investments"] = $row["cum_investments"];
                $output["cum_consumables"] = $row["cum_consumables"];
                $output["cum_services"] = $row["cum_services"];
                $output["cum_transport"] = $row["cum_transport"];
                $output["cum_admin"] = $row["cum_admin"];
                $output["cum_personnel_euro"] = $row["cum_personnel_euro"];
                $output["cum_investments_euro"] = $row["cum_investments_euro"];
                $output["cum_consumables_euro"] = $row["cum_consumables_euro"];
                $output["cum_services_euro"] = $row["cum_services_euro"];
                $output["cum_transport_euro"] = $row["cum_transport_euro"];
                $output["cum_admin_euro"] = $row["cum_admin_euro"];
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
