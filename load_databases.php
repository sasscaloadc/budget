<?php
require_once("load.php");

class load_databases extends load
{
        public function get_sql() {
                return "SELECT datname FROM pg_database WHERE datname NOT LIKE 'template%' AND datname NOT LIKE 'postgres';";
        }

        public function process_row($row) {
                return "<option value=\"".$row["datname"]."\">".$row["datname"]."</option>";
        }

}

	$lt = new load_databases();

	echo $lt->query();
?>
