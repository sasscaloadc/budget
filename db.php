<?php
/*
 */
 

        function getConnection() {
		$servername = "afrihost.sasscal.org";
		$username = "postgres";
		$password = "5455c4l_";
		$dbname = "budget";

                $database = array_key_exists("database",$_GET) ?  $_GET["database"] : "";
                if (empty($database)) {
                        $database = array_key_exists("database",$_POST) ?  $_POST["database"] : "";
                }
                if (!empty($database)) {
			$dbname = $database;
		}

                // Create connection
                $conn = pg_pconnect("host=".$servername." dbname=".$dbname." user=".$username." password=".$password);
                if (!$conn) {
                        die("Database connection failed. ");
                }
                return $conn;
        }

?>
