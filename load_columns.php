
<?php
require_once("load.php");

class load_columns extends load
{
        public function get_sql() {
                $table = array_key_exists("table",$_GET) ?  $_GET["table"] : "";
                if (empty($table)) {
                        $table = array_key_exists("table",$_POST) ?  $_POST["table"] : "";
                }
		
                return "SELECT column_name, data_type FROM information_schema.columns WHERE table_schema='public' and table_name = '".$table."'";
        }

        public function process_row($row) {
                return "<tr><td>".$row["column_name"]." (".$row["data_type"].")</td><td><input type=\"text\"/></td></tr>";
        }

}

	$lt = new load_columns();

	echo $lt->query();
?>
