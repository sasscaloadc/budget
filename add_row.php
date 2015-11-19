<?php
/*
 *  
 */
 
	$servername = "caprivi.sasscal.org";
	$username = "postgres";
	$password = "5455c4l_";
	$dbname = "budget";


        function getConnection() {
                // Create connection
                $conn = pg_pconnect("host=".$servername." dbname=".$dbname." user=".$username." password=".$password);
                if (!$conn) {
                        die("Database connection failed. ");
                }
                return $conn;
        }


	function get_columns() {
		$conn = getConnection();

		$database = array_key_exists("database",$_GET) ?  $_GET["database"] : "";
		if (empty($database)) {
			$database = array_key_exists("database",$_POST) ?  $_POST["database"] : "";
		}
		if (empty($database)) {
		        print "<h1>No parameter \"database\" received.</h1>";
		        exit();
		}

		$table = array_key_exists("table",$_GET) ?  $_GET["table"] : "";
		if (empty($table)) {
			$table = array_key_exists("table",$_POST) ?  $_POST["table"] : "";
		}
		if (empty($table)) {
		        print "<h1>No parameter \"table\" received.</h1>";
		        exit();
		}

		$sql = "SELECT column_name, data_type FROM information_schema.columns WHERE table_name   = '".$table."' AND table_catalog = '".$database."' ";

		$output = "";
		$result = pg_query($conn, $sql);
		if ($result && (pg_num_rows($result) > 0)) {
			while($row = pg_fetch_array($result)) {
				$output .= $row["column_name"]."<br/>";
			}
		}
		pg_close($conn);
		return $output;
	}

	echo get_columns();
?>

