
<?php
require_once("db.php");

$conn = getConnection();

$taskid = array_key_exists("taskid",$_GET) ?  $_GET["taskid"] : "";
if (empty($taskid)) {
        $taskid = array_key_exists("taskid",$_POST) ?  $_POST["taskid"] : "";
}

$sql = "SELECT * FROM task WHERE id = ".$taskid;

$output = Array();

$result = pg_query($conn, $sql);
if ($result && (pg_num_rows($result) > 0)) {
        $row = pg_fetch_array($result);
	$output["completion"] = $row["completed_percentage"];
}

pg_close($conn);
echo json_encode($output);

?>
