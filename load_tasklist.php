<?php
require_once("load.php");


        session_start();
        if (!is_numeric($_SESSION['access'])) {
                header("Location: http://caprivi.sasscal.org/budget/login.php?redirect=".urlencode("/budget/index.php"));
        }


class load_tasks extends load
{
        public function get_sql() {
		if ($_SESSION['access'] == 1) {
	                return "Select to_char(id, '000') as id from task order by id";
		} else {
	                return "Select to_char(id, '000') as id from task where owner = '".$_SESSION['username']."' order by id";
		}
        }

        public function process_row($row) {
                return "<option value=\"".$row["id"]."\">".$row["id"]."</option>";
        }

}

	$lt = new load_tasks();

	echo $lt->query();
?>
