<?php
include 'check_access.php';
require_once("db.php");


        $taskid = array_key_exists("taskid",$_GET) ?  $_GET["taskid"] : "";
        if (empty($taskid)) {
                $taskid = array_key_exists("taskid",$_POST) ?  $_POST["taskid"] : "";
        }
        if (empty($taskid)) {
		echo "No Task ID Specified";
		exit();
	}
        $investments_budget = array_key_exists("investments_budget",$_GET) ?  $_GET["investments_budget"] : "";
        if (empty($investments_budget)) {
                $investments_budget = array_key_exists("investments_budget",$_POST) ?  $_POST["investments_budget"] : "";
        }
        $services_budget = array_key_exists("services_budget",$_GET) ?  $_GET["services_budget"] : "";
        if (empty($services_budget)) {
                $services_budget = array_key_exists("services_budget",$_POST) ?  $_POST["services_budget"] : "";
        }
        $consumables_budget = array_key_exists("consumables_budget",$_GET) ?  $_GET["consumables_budget"] : "";
        if (empty($consumables_budget)) {
                $consumables_budget = array_key_exists("consumables_budget",$_POST) ?  $_POST["consumables_budget"] : "";
        }
        $transport_budget = array_key_exists("transport_budget",$_GET) ?  $_GET["transport_budget"] : "";
        if (empty($transport_budget)) {
                $transport_budget = array_key_exists("transport_budget",$_POST) ?  $_POST["transport_budget"] : "";
        }
        $personnel_budget = array_key_exists("personnel_budget",$_GET) ?  $_GET["personnel_budget"] : "";
        if (empty($personnel_budget)) {
                $personnel_budget = array_key_exists("personnel_budget",$_POST) ?  $_POST["personnel_budget"] : "";
        }
        $year_budget = array_key_exists("year_budget",$_GET) ?  $_GET["year_budget"] : "";
        if (empty($year_budget)) {
                $year_budget = array_key_exists("year_budget",$_POST) ?  $_POST["year_budget"] : "";
        }
        $kfw_phase_budget = array_key_exists("kfw_phase_budget",$_GET) ?  $_GET["kfw_phase_budget"] : "";
        if (empty($kfw_phase_budget)) {
                $kfw_phase_budget = array_key_exists("kfw_phase_budget",$_POST) ?  $_POST["kfw_phase_budget"] : "";
        }

	$sql = " UPDATE task SET investments_budget = ".(empty($investments_budget) ? "0" : $investments_budget).
                                  ", personnel_budget = ".(empty($personnel_budget) ? "0" : $personnel_budget).
                                   ", services_budget = ".(empty($services_budget) ? "0" : $services_budget).
                                ", consumables_budget = ".(empty($consumables_budget) ? "0" : $consumables_budget).
                                  ", transport_budget = ".(empty($transport_budget) ? "0" : $transport_budget).
                                  ", year_budget = ".(empty($year_budget) ? "0" : $year_budget).
                                  ", kfw_phase_budget = ".(empty($kfw_phase_budget) ? "0" : $kfw_phase_budget).
               " WHERE id = ".$taskid;

	$conn = getConnection();

	$result = pg_query($conn, $sql);
        if ($result) {
                echo "OK";
		$details = "[taskid:".$taskid.", investments_budget:".$investments_budget.", personnel_budget:".$personnel_budget.", services_budget:".$services_budget.", consumables_budget:".$consumables_budget.", transport_budget:".$transport_budget.", year_budget:".$year_budget.", kfw_phase_budget:".$kfw_phase_budget."]";
		err_log("UPDATE TASK FIGURES", $details);
        } else {
                echo pg_last_error($conn);
		err_log("UPDATE TASK FIGURES FAILED", pg_last_error($conn));
        }

	pg_close($conn);
	

?>
