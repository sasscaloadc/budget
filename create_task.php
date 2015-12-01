<?php
require_once("db.php");


        $taskid = array_key_exists("taskid",$_GET) ?  $_GET["taskid"] : "";
        if (empty($taskid)) {
                $taskid = array_key_exists("taskid",$_POST) ?  $_POST["taskid"] : "";
        }
        if (empty($taskid)) {
		echo "No Task ID Specified";
		exit();
	}
        $firstyear = array_key_exists("firstyear",$_GET) ?  $_GET["firstyear"] : "";
        if (empty($firstyear)) {
                $firstyear = array_key_exists("firstyear",$_POST) ?  $_POST["firstyear"] : "";
        }
        $firstquarter = array_key_exists("firstquarter",$_GET) ?  $_GET["firstquarter"] : "";
        if (empty($firstquarter)) {
                $firstquarter = array_key_exists("firstquarter",$_POST) ?  $_POST["firstquarter"] : "";
        }
        $description = array_key_exists("description",$_GET) ?  $_GET["description"] : "";
        if (empty($description)) {
                $description = array_key_exists("description",$_POST) ?  $_POST["description"] : "";
        }
        $owner = array_key_exists("owner",$_GET) ?  $_GET["owner"] : "";
        if (empty($owner)) {
                $owner = array_key_exists("owner",$_POST) ?  $_POST["owner"] : "";
        }
        $currency = array_key_exists("currency",$_GET) ?  $_GET["currency"] : "";
        if (empty($currency)) {
                $currency = array_key_exists("currency",$_POST) ?  $_POST["currency"] : "";
        }
        $investments_budget = array_key_exists("investments_budget",$_GET) ?  $_GET["investments_budget"] : "";
        if (empty($investments_budget)) {
                $investments_budget = array_key_exists("investments_budget",$_POST) ?  $_POST["investments_budget"] : "";
        }
        $services_budget = array_key_exists("services_budget",$_GET) ?  $_GET["services_budget"] : "";
        if (empty($services_budget)) {
                $services_budget = array_key_exists("services_budget",$_POST) ?  $_POST["services_budget"] : "";
        }
        $consumables_budget = array_key_exists("consumables_budget",$_GET) ?  $_GET["consumables_budget"] : "";
        if (empty($consumables_budget)) {
                $consumables_budget = array_key_exists("consumables_budget",$_POST) ?  $_POST["consumables_budget"] : "";
        }
        $transport_budget = array_key_exists("transport_budget",$_GET) ?  $_GET["transport_budget"] : "";
        if (empty($transport_budget)) {
                $transport_budget = array_key_exists("transport_budget",$_POST) ?  $_POST["transport_budget"] : "";
        }
        $personnel_budget = array_key_exists("personnel_budget",$_GET) ?  $_GET["personnel_budget"] : "";
        if (empty($personnel_budget)) {
                $personnel_budget = array_key_exists("personnel_budget",$_POST) ?  $_POST["personnel_budget"] : "";
        }
	$sql = " INSERT INTO task (id, description, owner, currency, investments_budget, services_budget, consumables_budget, transport_budget, personnel_budget) VALUES ($taskid, '".$description."', '".$owner. "', '".$currency."', ".$investments_budget.", ".$services_budget.", ".$consumables_budget.", ".$transport_budget.", ".$personnel_budget."); INSERT INTO budget (task_id, year, quarter, status) VALUES (".$taskid.", ".$firstyear.", ".$firstquarter.", 1)";

	$conn = getConnection();

//error_log($sql);
	$result = pg_query($conn, $sql);
        if ($result) {
                echo "OK";
        } else {
                echo pg_last_error($conn);
        }

	pg_close($conn);
	

?>
