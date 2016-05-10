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
        $completion = array_key_exists("completion",$_GET) ?  $_GET["completion"] : "";
        if (empty($completion)) {
                $completion = array_key_exists("completion",$_POST) ?  $_POST["completion"] : "";
        }

	$sql = " UPDATE task SET completed_percentage = ".(empty($completion) ? "0" : $completion).
               " WHERE id = ".$taskid;

	$conn = getConnection();

	$result = pg_query($conn, $sql);
        if ($result) {
                echo "OK";
		$details = "[taskid:".$taskid.", completed_percentage:".$completion."]";
		err_log("UPDATE TASK COMPLETION", $details);
        } else {
                echo pg_last_error($conn);
		err_log("UPDATE TASK COMPLETION FAILED", pg_last_error($conn));
        }

	pg_close($conn);
	

?>
