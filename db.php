<?php
/*
 */
 
    $location_url = "https://budget.sasscal.org/"; 

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


    function err_log($update, $details) {
        $username = isset ($_SESSION) ? (array_key_exists('username', $_SESSION) ? $_SESSION['username'] : 'undefined') : 'not_in_session';

	$conn = getConnection();

	$sql = "INSERT INTO error_log (t_stamp, username, update_message, details) VALUES ('".date('Y-m-d H:i:s')."', '".$username."', '".$update."', '".$details."')";
	
	pg_query($conn, $sql);

        error_log(date('Y-m-d H:i:s').": ".$username.": ".$update.": ".$details."\n", 3, "/var/www/sasscal_secure/budget_tool/logs/audit.log");

    }

?>
