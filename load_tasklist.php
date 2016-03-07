<?php
require_once("check_access.php");
require_once("load.php");


        session_start();
        if (!is_numeric($_SESSION['access'])) {
                header("Location: ".$location_url."login.php?redirect=".urlencode("/budget/index.php"));
        }


class load_tasks extends load
{
        public function get_sql() {
		switch($_SESSION['access']) {
			case 0: return "SELECT to_char(id, '000') AS id FROM task ORDER BY id";
			case 1: return "SELECT to_char(id, '000') AS id FROM task WHERE country = (SELECT country FROM access WHERE username = '".$_SESSION['username']."' ) ORDER BY id "; 
			case 2: return "SELECT to_char(id, '000') AS id FROM task WHERE owner = '".$_SESSION['username']."' ORDER BY id";

		}
        }

        public function process_row($row) {
                return "<option value=\"".$row["id"]."\">".$row["id"]."</option>";
        }

}

	$lt = new load_tasks();

	echo $lt->query();
?>
