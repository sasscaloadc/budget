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
        $received = array_key_exists("received",$_GET) ?  $_GET["received"] : "";
        if (empty($received)) {
                $received = array_key_exists("received",$_POST) ?  $_POST["received"] : "";
        }
        $received_date = array_key_exists("received_date",$_GET) ?  $_GET["received_date"] : "";
        if (empty($received_date)) {
                $received_date = array_key_exists("received_date",$_POST) ?  $_POST["received_date"] : "";
        }
        $xrate = array_key_exists("xrate",$_GET) ?  $_GET["xrate"] : "";
        if (empty($xrate)) {
                $xrate = array_key_exists("xrate",$_POST) ?  $_POST["xrate"] : "";
        }
	$sql = " UPDATE budget SET received = ".$received.
                                  ", received_date = '".$received_date."' ".
                                  ", xrate = ".$xrate.
	       		(empty($status) ? "" : ", status = ".$status) .
               " WHERE task_id = ".$taskid." AND year = ".$year." AND quarter = ".$quarter;

//error_log($sql);
	$conn = getConnection();

	$result = pg_query($conn, $sql);
        if ($result) {
                echo "OK";
        } else {
                echo pg_last_error($conn);
        }

	pg_close($conn);
	

?>
