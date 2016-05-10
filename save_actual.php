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
        $prev_unused = array_key_exists("prev_unused",$_GET) ?  $_GET["prev_unused"] : "";
        if (empty($prev_unused)) {
                $prev_unused = array_key_exists("prev_unused",$_POST) ?  $_POST["prev_unused"] : "";
        }
        $prev_xrate = array_key_exists("prev_xrate",$_GET) ?  $_GET["prev_xrate"] : "";
        if (empty($prev_xrate)) {
                $prev_xrate = array_key_exists("prev_xrate",$_POST) ?  $_POST["prev_xrate"] : "";
        }
        $admin = array_key_exists("admin",$_GET) ?  $_GET["admin"] : "";
        if (empty($admin)) {
                $admin = array_key_exists("admin",$_POST) ?  $_POST["admin"] : "";
        }

	$sql = " UPDATE budget SET investments_actual = ".(empty($investments) ? "0" : $investments).
                                  ", personnel_actual = ".(empty($personnel) ? "0" : $personnel).
                                   ", services_actual = ".(empty($services) ? "0" : $services).
                                ", consumables_actual = ".(empty($consumables) ? "0" : $consumables).
                                  ", transport_actual = ".(empty($transport) ? "0" : $transport).
                                  ", admin = ".(empty($admin) ? "0" : $admin).
	       (empty($status) ? "" : ", status = ".$status) .
               " WHERE task_id = ".$taskid." AND year = ".$year." AND quarter = ".$quarter;

	//if ($status == 3) {  //THIS IS NOW TAKEN CARE OF BY THE DATABASE TRIGGER ON BUDGET UPDATE
		//$next_quarter = $quarter >= 4 ? 1 : $quarter + 1;
		//$next_year = $quarter >= 4 ? $year + 1 : $year;
		//$sql .= "; INSERT INTO budget (task_id, year, quarter, prev_unused, prev_xrate, status) ".
                        //"    VALUES (".$taskid.", ".$next_year.", ".$next_quarter.", ".$prev_unused.", ".$prev_xrate.", 1)";
	//}

	$conn = getConnection();

	$result = pg_query($conn, $sql);
        if ($result) {
                echo "OK";
		$details = "[taskid:".$taskid.", year:".$year.", quarter:".$quarter.", status:".$status.", investments:".$investments.", personnel:".$personnel.", services:".$services.", consumables:".$consumables.", transport:".$transport.", admin:".$admin."]";
		err_log("UPDATE ACTUALS", $details);
        } else {
                echo pg_last_error($conn);
		err_log("UPDATE ACTUALS FAILED", pg_last_error($conn));
        }

	pg_close($conn);
	

?>
