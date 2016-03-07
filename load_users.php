<?php
require_once("load.php");

class load_users extends load
{

        public function get_sql() {
                $country = array_key_exists("country",$_GET) ?  $_GET["country"] : "";
                if (empty($country)) {
                        $country = array_key_exists("country",$_POST) ?  $_POST["country"] : "";
                }

                $sql = "SELECT * FROM access WHERE level = 2 AND country = '".$country."' ORDER BY firstname, lastname";
		return $sql;
        }

	public function process_row($row) {
		return "<option value=\"".$row['username']."\">".$row['firstname']." ".$row['lastname']."</option>";
        }

}

	$lu = new load_users();

	echo $lu->query();
?>
