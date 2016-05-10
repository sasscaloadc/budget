<?php
include 'check_access.php';
require_once("db.php");


                $taskid = array_key_exists("taskid",$_POST) ?  $_POST["taskid"] : "";
        if (empty($taskid)) {
		echo "No Task ID Specified";
		exit();
	}
                $year = array_key_exists("year",$_POST) ?  $_POST["year"] : "";
                $quarter = array_key_exists("quarter",$_POST) ?  $_POST["quarter"] : "";
                $status = array_key_exists("status",$_POST) ?  $_POST["status"] : "";

                $investments = array_key_exists("investments",$_POST) ?  $_POST["investments"] : "";
                $personnel = array_key_exists("personnel",$_POST) ?  $_POST["personnel"] : "";
                $services = array_key_exists("services",$_POST) ?  $_POST["services"] : "";
                $consumables = array_key_exists("consumables",$_POST) ?  $_POST["consumables"] : "";
                $transport = array_key_exists("transport",$_POST) ?  $_POST["transport"] : "";
                $admin = array_key_exists("admin",$_POST) ?  $_POST["admin"] : "";

                $investments_actual = array_key_exists("investments_actual",$_POST) ?  $_POST["investments_actual"] : "";
                $personnel_actual = array_key_exists("personnel_actual",$_POST) ?  $_POST["personnel_actual"] : "";
                $services_actual = array_key_exists("services_actual",$_POST) ?  $_POST["services_actual"] : "";
                $consumables_actual = array_key_exists("consumables_actual",$_POST) ?  $_POST["consumables_actual"] : "";
                $transport_actual = array_key_exists("transport_actual",$_POST) ?  $_POST["transport_actual"] : "";

                $investments_planned = array_key_exists("investments_planned",$_POST) ?  $_POST["investments_planned"] : "";
                $personnel_planned = array_key_exists("personnel_planned",$_POST) ?  $_POST["personnel_planned"] : "";
                $services_planned = array_key_exists("services_planned",$_POST) ?  $_POST["services_planned"] : "";
                $consumables_planned = array_key_exists("consumables_planned",$_POST) ?  $_POST["consumables_planned"] : "";
                $transport_planned = array_key_exists("transport_planned",$_POST) ?  $_POST["transport_planned"] : "";

	$sql = " UPDATE budget SET investments = ".(empty($investments) ? "0" : $investments).
                                  ", personnel = ".(empty($personnel) ? "0" : $personnel).
                                   ", services = ".(empty($services) ? "0" : $services).
                                ", consumables = ".(empty($consumables) ? "0" : $consumables).
                                  ", transport = ".(empty($transport) ? "0" : $transport).
                                  ", admin = ".(empty($admin) ? "0" : $admin).
                                  	", investments_actual = ".(empty($investments_actual) ? "0" : $investments_actual).
                                  	", personnel_actual = ".(empty($personnel_actual) ? "0" : $personnel_actual).
                                   	", services_actual = ".(empty($services_actual) ? "0" : $services_actual).
                                	", consumables_actual = ".(empty($consumables_actual) ? "0" : $consumables_actual).
                                  	", transport_actual = ".(empty($transport_actual) ? "0" : $transport_actual).
                                  	", investments_planned = ".(empty($investments_planned) ? "0" : $investments_planned).
                                  	", personnel_planned = ".(empty($personnel_planned) ? "0" : $personnel_planned).
                                   	", services_planned = ".(empty($services_planned) ? "0" : $services_planned).
                                	", consumables_planned = ".(empty($consumables_planned) ? "0" : $consumables_planned).
                                  	", transport_planned = ".(empty($transport_planned) ? "0" : $transport_planned).
	       (empty($status) ? "" : ", status = ".$status) .
               " WHERE task_id = ".$taskid." AND year = ".$year." AND quarter = ".$quarter;

	$conn = getConnection();

	$result = pg_query($conn, $sql);
        if ($result) {
               	echo "OK";
		$details = "[taskid:".$taskid.", year:".$year.", quarter:".$quarter.", status:".$status.", investments:".$investments.", personnel:".$personnel.", services:".$services.", consumables:".$consumables.", transport=".$transport.", investments_actual:".$investments_actual.", personnel_actual:".$personnel_actual.", services_actual:".$services_actual.", consumables_actual:".$consumables_actual.", transport_actual:".$transport_actual.", admin:".$admin.", investments_planned:".$investments_planned.", personnel_planned:".$personnel_planned.", services_planned:".$services_planned.", consumables_planned:".$consumables_planned.", transport_planned:".$transport_planned."]";
		err_log("UPDATE LOADING", $details);
        } else {
                echo pg_last_error($conn);
		err_log("UPDATE LOADING FAILED", pg_last_error($conn));
        }

	pg_close($conn);
	

?>
