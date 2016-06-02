<?php
/*
 */
 
    $all_settings = parse_ini_file("/etc/sasscal/locker.ini",true);

    $settings = $all_settings['budget'];

    $db_name = $settings['dbname'];
    $db_username = $settings['username'];
    $pw_salt = $settings['pw_salt'];
    $db_password = $settings['dbpassword'];
    $location_url = $settings['location_url'];
    $servername = $settings['servername'];

        function getConnection() {
		global $servername;
		global $db_username;
		global $db_name;
		global $db_password;

                $database = array_key_exists("database",$_GET) ?  $_GET["database"] : "";
                if (empty($database)) {
                        $database = array_key_exists("database",$_POST) ?  $_POST["database"] : "";
                }
                if (!empty($database)) {
			$dbname = $database;
		} else {
			$dbname = $db_name;
		}

                // Create connection
                $conn = pg_pconnect("host=".$servername." dbname=".$dbname." user=".$db_username." password=".$db_password);
                if (!$conn) {
			error_log("host=".$servername." dbname=".$dbname." user=".$db_username." password=".$db_password);

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
