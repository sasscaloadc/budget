
<?php
require_once("load.php");

class load_currency extends load
{
        public function get_sql() {
                $currency = array_key_exists("currency",$_GET) ?  $_GET["currency"] : "";
                if (empty($currency)) {
                        $currency = array_key_exists("currency",$_POST) ?  $_POST["currency"] : "";
                }

                return "SELECT value, to_char(updated, 'DD-MM-YYYY HH:MIam') as dt FROM currencies WHERE code = '".$currency."'";
        }

        public function process_row($row) {
                return "<span id=\"xrateval\">".$row["value"] . "</span> <small>(".$row["dt"].")</small>";
        }

}

	$lt = new load_currency();

	echo $lt->query();
?>
