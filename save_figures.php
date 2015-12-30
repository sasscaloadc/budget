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
        $admin = array_key_exists("admin",$_GET) ?  $_GET["admin"] : "";
        if (empty($admin)) {
                $admin = array_key_exists("admin",$_POST) ?  $_POST["admin"] : "";
        }

	$sql = " UPDATE budget SET investments = ".(empty($investments) ? "0" : $investments).
                                  ", personnel = ".(empty($personnel) ? "0" : $personnel).
                                   ", services = ".(empty($services) ? "0" : $services).
                                ", consumables = ".(empty($consumables) ? "0" : $consumables).
                                  ", transport = ".(empty($transport) ? "0" : $transport).
                                  ", admin = ".(empty($admin) ? "0" : $admin).
	       (empty($status) ? "" : ", status = ".$status) .
               " WHERE task_id = ".$taskid." AND year = ".$year." AND quarter = ".$quarter;

	$conn = getConnection();

	$result = pg_query($conn, $sql);
        if ($result) {
                echo "OK";
        } else {
                echo pg_last_error($conn);
        }

	pg_close($conn);
	

?>
