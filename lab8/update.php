<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>CS 3380 Lab 8</title>
</head>
<body>
<form method="POST" action="/~njmnbb/cs3380/lab8/update.php">
<div align="center">	
<tr>
	<br>
	<td><strong>Description</strong></td>
	<td><input type="text" name="description" value=""/></td>
</tr></table>
		<input type="submit" name="submit" value="Save" /><p><a href="?logout=true">Click here to logout</a></p></div></form>

	<?php
		//Connecting to database
		include '../../secure/database.php';
		$conn = pg_connect(HOST ." " . DBNAME . " " . USERNAME . " " . PASSWORD)
			or die('Could not connect:' . pg_last_error());

		//Start session
		session_start();

		//If there is no session...
		if (!isset($_SESSION['username'])) {
			//Redirect back to index page
			header('Location: ./index.php');
		}

		//Displaying username
		echo "<p align = 'center'>USERNAME: " . $_SESSION['username'] . "</p>";

		//If the 'submit' button is pressed...
		if (isset($_POST['submit'])) {
			//Get username and description variables
			$username = $_SESSION['username'];
			$description = $_POST['description'];



			//Set a description in the SQL table
			$description_query = "UPDATE lab8.user_info SET description = $1 WHERE username = $2";
			pg_prepare($conn, "description", $description_query);
			$description_result = pg_execute($conn, "description", array($description, $username));

			//Send an 'action' to the SQL table
			$ip_address = $_SERVER["REMOTE_ADDR"];
			$action = 'update';

			$log_query = "INSERT INTO lab8.log(username, ip_address, action) VALUES ($1, $2, $3)";
			pg_prepare($conn, "log", $log_query);
			$log_result = pg_execute($conn, "log", array($username, $ip_address, $action));

			//Redirect to home page
			header('Location: ./home.php');
		}
	?>
</body>
</html>