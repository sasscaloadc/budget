<?php
require_once("db.php");

    session_start();

    $key = $_POST['key'];
    $value = $_POST['value'];

	$acceptable_keys = array('taskid', 'year', 'quarter', 'reload');
	
	if (isset($_SESSION)) {
		if (in_array($key, $acceptable_keys)) {
			$_SESSION[$key] = $value;
			//err_log("Setting ".$key." to ".$value);
		} else {
			err_log("WARNING - attempt to change session variable: ".$key);
		}
	} 

	$output['taskid'] = isset($_SESSION['taskid']) ? $_SESSION['taskid'] : '?';
	$output['year'] = isset($_SESSION['year']) ? $_SESSION['year'] : '?';
	$output['quarter'] = isset($_SESSION['quarter']) ? $_SESSION['quarter'] : '?';

	echo json_encode($output);
?>
