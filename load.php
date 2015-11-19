<?php
/*
 *  This class will maintain the connection pool to the database
 *  
 */
 
abstract class load { 
/*
 *  These details can be stored in a .conf file
 */
	static $servername = "caprivi.sasscal.org";
	static $DBservername = "localhost";
	static $username = "postgres";
	static $password = "5455c4l_";
	static $dbname = "postgres";


	abstract function get_sql();
	abstract function process_row($row);


        static function getConnection() {
                $database = array_key_exists("database",$_GET) ?  $_GET["database"] : "";
                if (empty($database)) {
                        $database = array_key_exists("database",$_POST) ?  $_POST["database"] : "";
                }
                load::$dbname = $database;

                // Create connection
                //$conn = pg_pconnect("host=".load::$servername." user=".load::$username." password=".load::$password);
                $conn = pg_pconnect("host=".load::$servername." dbname=".load::$dbname." user=".load::$username." password=".load::$password);
                if (!$conn) {
                        die("Database connection failed. ");
                }
                return $conn;
        }


	public function query() {
		$conn = load::getConnection();

		$sql = $this->get_sql();

		$output = "";
		$result = pg_query($conn, $sql);
		if ($result && (pg_num_rows($result) > 0)) {
			while($row = pg_fetch_array($result)) {
				$output .= $this->process_row($row);	
			}
		}
		pg_close($conn);
		return $output;
	}

}
