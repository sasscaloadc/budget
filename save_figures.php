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
        $year = array_key_exists("year",$_GET) ?  $_GET["year"] : "";
        if (empty($year)) {
                $year = array_key_exists("year",$_POST) ?  $_POST["year"] : "";
        }
        $quarter = array_key_exists("quarter",$_GET) ?  $_GET["quarter"] : "";
        if (empty($quarter)) {
                $quarter = array_key_exists("quarter",$_POST) ?  $_POST["quarter"] : "";
        }
        $status = array_key_exists("status",$_GET) ?  $_GET["status"] : "";
        if (empty($status)) {
                $status = array_key_exists("status",$_POST) ?  $_POST["status"] : "";
        }
        $investments = array_key_exists("investments",$_GET) ?  $_GET["investments"] : "";
        if (empty($investments)) {
                $investments = array_key_exists("investments",$_POST) ?  $_POST["investments"] : "";
        }
        $personnel = array_key_exists("personnel",$_GET) ?  $_GET["personnel"] : "";
        if (empty($personnel)) {
                $personnel = array_key_exists("personnel",$_POST) ?  $_POST["personnel"] : "";
        }
        $services = array_key_exists("services",$_GET) ?  $_GET["services"] : "";
        if (empty($services)) {
                $services = array_key_exists("services",$_POST) ?  $_POST["services"] : "";
        }
        $consumables = array_key_exists("consumables",$_GET) ?  $_GET["consumables"] : "";
        if (empty($consumables)) {
                $consumables = array_key_exists("consumables",$_POST) ?  $_POST["consumables"] : "";
        }
        $transport = array_key_exists("transport",$_GET) ?  $_GET["transport"] : "";
        if (empty($transport)) {
                $transport = array_key_exists("transport",$_POST) ?  $_POST["transport"] : "";
        }
        $investments_planned = array_key_exists("investments_planned",$_GET) ?  $_GET["investments_planned"] : "";
        if (empty($investments_planned)) {
                $investments_planned = array_key_exists("investments_planned",$_POST) ?  $_POST["investments_planned"] : "";
        }
        $personnel_planned = array_key_exists("personnel_planned",$_GET) ?  $_GET["personnel_planned"] : "";
        if (empty($personnel_planned)) {
                $personnel_planned = array_key_exists("personnel_planned",$_POST) ?  $_POST["personnel_planned"] : "";
        }
        $services_planned = array_key_exists("services_planned",$_GET) ?  $_GET["services_planned"] : "";
        if (empty($services_planned)) {
                $services_planned = array_key_exists("services_planned",$_POST) ?  $_POST["services_planned"] : "";
        }
        $consumables_planned = array_key_exists("consumables_planned",$_GET) ?  $_GET["consumables_planned"] : "";
        if (empty($consumables_planned)) {
                $consumables_planned = array_key_exists("consumables_planned",$_POST) ?  $_POST["consumables_planned"] : "";
        }
        $transport_planned = array_key_exists("transport_planned",$_GET) ?  $_GET["transport_planned"] : "";
        if (empty($transport_planned)) {
                $transport_planned = array_key_exists("transport_planned",$_POST) ?  $_POST["transport_planned"] : "";
        }

	$sql = " UPDATE budget SET investments = ".(empty($investments) ? "0" : $investments).
                                  ", personnel = ".(empty($personnel) ? "0" : $personnel).
                                   ", services = ".(empty($services) ? "0" : $services).
                                ", consumables = ".(empty($consumables) ? "0" : $consumables).
                                  ", transport = ".(empty($transport) ? "0" : $transport).
	       (empty($status) ? "" : ", status = ".$status) .
               " WHERE task_id = ".$taskid." AND year = ".$year." AND quarter = ".$quarter;

	$conn = getConnection();

	$result = pg_query($conn, $sql);
        if ($result) {
		$details = "[taskid:".$taskid.", year:".$year.", quarter:".$quarter.", status:".$status.", investments:".$investments.", personnel:".$personnel.", services:".$services.", consumables:".$consumables.", transport=".$transport."]";
	        error_log($_SESSION['username'].": UPDATE ESTIMATES: ".$details."\n", 3, "/var/www/sasscal_secure/budget_tool/logs/audit.log");

		// this would also mean that the database trigger will have created the next quarter
		$nexty = $quarter == 4 ? $year + 1 : $year;
		$nextq = $quarter == 4 ? 1 : $quarter + 1;
		$sql = " UPDATE budget SET investments_planned = ".(empty($investments_planned) ? "0" : $investments_planned).
                                  	", personnel_planned = ".(empty($personnel_planned) ? "0" : $personnel_planned).
                                   	", services_planned = ".(empty($services_planned) ? "0" : $services_planned).
                                	", consumables_planned = ".(empty($consumables_planned) ? "0" : $consumables_planned).
                                  	", transport_planned = ".(empty($transport_planned) ? "0" : $transport_planned).
	       		(empty($status) ? "" : ", status = ".$status) .
               		" WHERE task_id = ".$taskid." AND year = ".$nexty." AND quarter = ".$nextq;
		$result = pg_query($conn, $sql);
        	if ($result) {
                	echo "OK";
			$details = "[taskid:".$taskid.", year:".$nexty.", quarter:".$nextq.", status:".$status.", investments_planned:".$investments_planned.", personnel_planned:".$personnel_planned.", services_planned:".$services_planned.", consumables_planned:".$consumables_planned.", transport_planned=".$transport_planned."]";
			err_log("UPDATE PLANNED", $details);
        	} else {
                	echo pg_last_error($conn);
			err_log("UPDATE PLANNED FAILED", pg_last_error($conn));
        	};
        } else {
                echo pg_last_error($conn);
		error_log($_SESSION['username'].": UPDATE ESTIMATES FAILED ".pg_last_error($conn)."\n", 3, "/var/www/sasscal_secure/budget_tool/logs/audit.log");
        }

	pg_close($conn);
	

?>
