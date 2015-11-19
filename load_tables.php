<?php
require_once("load.php");

class load_tables extends load
{
        public function get_sql() {
                return "SELECT table_name FROM information_schema.tables WHERE table_schema='public'";
        }

        public function process_row($row) {
                return "<option>".$row["table_name"]."</option>";
        }

}

	$lt = new load_tables();

	echo $lt->query();
?>
