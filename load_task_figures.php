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
	$output["personnel_budget"] = $row["personnel_budget"];
	$output["investments_budget"] = $row["investments_budget"];
	$output["consumables_budget"] = $row["consumables_budget"];
	$output["services_budget"] = $row["services_budget"];
	$output["transport_budget"] = $row["transport_budget"];
	$output["year_budget"] = $row["year_budget"];
	$output["kfw_phase_budget"] = $row["kfw_phase_budget"];
}

pg_close($conn);
echo json_encode($output);

?>
