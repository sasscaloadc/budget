<?php
require_once("load.php");

class load_tasks extends load
{
        public function get_sql() {
                return "Select to_char(id, '000') as id from task order by id";
        }

        public function process_row($row) {
                return "<option value=\"".$row["id"]."\">".$row["id"]."</option>";
        }

}

	$lt = new load_tasks();

	echo $lt->query();
?>
