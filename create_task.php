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
        $institution = array_key_exists("institution",$_GET) ?  $_GET["institution"] : "";
        if (empty($institution)) {
                $institution = array_key_exists("institution",$_POST) ?  $_POST["institution"] : "";
        }
        $country = array_key_exists("country",$_GET) ?  $_GET["country"] : "";
        if (empty($country)) {
                $country = array_key_exists("country",$_POST) ?  $_POST["country"] : "";
        }
        $thematic_area = array_key_exists("thematic_area",$_GET) ?  $_GET["thematic_area"] : "";
        if (empty($thematic_area)) {
                $thematic_area = array_key_exists("thematic_area",$_POST) ?  $_POST["thematic_area"] : "";
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
	$sql = " INSERT INTO task (id, description, owner, institution, country, thematic_area, currency, investments_budget, services_budget, consumables_budget, transport_budget, personnel_budget) VALUES ($taskid, '".$description."', '".$owner. "', '".$institution. "', '".$country. "', '".$thematic_area. "', '".$currency."', ".$investments_budget.", ".$services_budget.", ".$consumables_budget.", ".$transport_budget.", ".$personnel_budget."); INSERT INTO budget (task_id, year, quarter, status) VALUES (".$taskid.", ".$firstyear.", ".$firstquarter.", 1)";

	$conn = getConnection();

	$result = pg_query($conn, $sql);
        if ($result) {
                echo "OK";
		$details = "[taskid:".$taskid.", owner:".$owner. ", country:".$country. ", thematic_area:".$thematic_area. ", currency:".$currency.", start:Q".$firstquarter.", ".$firstyear.", description:'".$description."', institution:'".$institution."', budget: investments=".$investments_budget.", services=".$services_budget.", consumables=".$consumables_budget.", transport=".$transport_budget.", personnel=".$personnel_budget."]";
		err_log("ADD TASK", $details);
        } else {
                echo pg_last_error($conn);
		err_log("ADD TASK FAILED", pg_last_error($conn));
        }

	pg_close($conn);
	

?>
