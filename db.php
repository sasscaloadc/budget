<?php
/*
 */
 

        function getConnection() {
		$servername = "caprivi.sasscal.org";
		$username = "postgres";
		$password = "5455c4l_";
		$dbname = "postgres";

                $database = array_key_exists("database",$_GET) ?  $_GET["database"] : "";
                if (empty($database)) {
                        $database = array_key_exists("database",$_POST) ?  $_POST["database"] : "";
                }
                $dbname = $database;

                // Create connection
                $conn = pg_pconnect("host=".$servername." dbname=".$dbname." user=".$username." password=".$password);
                if (!$conn) {
                        die("Database connection failed. ");
                }
                return $conn;
        }


