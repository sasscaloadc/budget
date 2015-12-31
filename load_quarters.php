<?php
require_once("db.php");
include 'check_access.php';

        $taskid = array_key_exists("taskid",$_GET) ?  $_GET["taskid"] : "";
        if (empty($taskid)) {
                $taskid = array_key_exists("taskid",$_POST) ?  $_POST["taskid"] : "";
        }
        if (empty($taskid)) {
                die();
        }

        $year = array_key_exists("year",$_GET) ?  $_GET["year"] : "";
        if (empty($year)) {
                $year = array_key_exists("year",$_POST) ?  $_POST["year"] : "";
        }
        if (empty($year)) {
                die();
        }

        $sql = "";
        if ($_SESSION['access'] == 1) {
                $sql = "SELECT quarter FROM budget WHERE task_id = ".$taskid." AND year = ".$year." AND status >= 3 ORDER BY quarter ";
        } else {
                $sql = "SELECT quarter FROM budget b INNER JOIN task t on b.task_id = t.id
			WHERE task_id = ".$taskid." 
			  AND year = ".$year."
			  AND owner = '".$_SESSION['username']."'
			  AND status >= 3
			ORDER BY quarter ";

        }

        $conn = getConnection();
        $result = pg_query($conn, $sql);
        if ($result && (pg_num_rows($result) > 0)) {
                while ($row = pg_fetch_array($result)) {
                        echo "<option value=\"".$row["quarter"]."\">Q".$row["quarter"]."</option>";
                }
        }
        pg_close($conn);
?>

