<?php
	//Connecting to database
	include '../../secure/database.php';
	$conn = pg_connect(HOST ." " . DBNAME . " " . USERNAME . " " . PASSWORD)
		or die('Could not connect:' . pg_last_error());

	//Start session
	session_start();

	//Send 'logout' action to SQL table
	$username = $_SESSION['username'];
	$ip_address = $_SERVER["REMOTE_ADDR"];
	$action = 'logout';
	
	$log_query = "INSERT INTO lab8.log(username, ip_address, action) VALUES ($1, $2, $3)";
	pg_prepare($conn, "log", $log_query);
	$log_result = pg_execute($conn, "log", array($username, $ip_address, $action));

	//Destroy session
	session_destroy();

	//Redirect to index page
	header('Location: ./index.php');
?>