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

	$sql = "";
	if ($_SESSION['access'] == 1) {
                $sql = "SELECT year FROM budget WHERE task_id = ".$taskid." AND status >= 3 GROUP BY year ORDER BY year ";
	} else {
                $sql = "SELECT year FROM budget b INNER JOIN task t on b.task_id = t.id 
			WHERE task_id = ".$taskid." 
			  AND owner = '".$_SESSION['username']."'
			  AND status >= 3 
			GROUP BY year
			ORDER BY year ";
	}
error_log("==>".$sql);

	$conn = getConnection();
        $result = pg_query($conn, $sql);
        if ($result && (pg_num_rows($result) > 0)) {
                while ($row = pg_fetch_array($result)) {
                	echo "<option value=\"".$row["year"]."\">".$row["year"]."</option>";
		}
        }
	pg_close($conn);
?>
