<?php
require_once("load.php");

class load_users extends load
{

        public function get_sql() {
                $sql = "SELECT * FROM access WHERE level = 2 ORDER BY firstname, lastname";
		return $sql;
        }

	public function process_row($row) {
		return "<option value=\"".$row['username']."\">".$row['firstname']." ".$row['lastname']."</option>";
        }

}

	$lu = new load_users();

	echo $lu->query();
?>
