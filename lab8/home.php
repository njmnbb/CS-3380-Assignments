<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>CS 3380 Lab 5</title>
</head>
<body>
<!-- Center the column with the user information -->
<div align = "center">               
	<div id = "login">
	  <?php
	  	//Connecting to database
		include '../../secure/database.php';
		$conn = pg_connect(HOST ." " . DBNAME . " " . USERNAME . " " . PASSWORD)
			or die('Could not connect:' . pg_last_error());

		//Start session
		session_start();

		//Get username from session
		echo "Username: " . $_SESSION['username'];

		//Get IP address from SQL table
		$log_query = "SELECT * FROM lab8.log WHERE username = $1 AND action = 'register'";
		pg_prepare($conn, "ip", $log_query);
		$log_result = pg_execute($conn, "ip", array($_SESSION['username']));
		$log_result = pg_fetch_array($log_result, null, PGSQL_ASSOC);

		echo "<br><br>IP Address: " . $log_result['ip_address'];


		//Get registration date and description from SQL table
		$registration_query = "SELECT registration_date, description FROM lab8.user_info WHERE username = $1";
		pg_prepare($conn, "registration", $registration_query);
		$registration_result = pg_execute($conn, "registration", array($_SESSION['username']));
		$registration_result = pg_fetch_array($registration_result, null, PGSQL_ASSOC);

		echo "<br><br>Registration date: " . $registration_result['registration_date'];
		echo "<br><br>Description: " . $registration_result['description'];

		//Get table data
		$table_query = "SELECT * FROM lab8.log WHERE username = $1";
		pg_prepare($conn, "table", $table_query);
		$table_result = pg_execute($conn, "table", array($_SESSION['username']));

		$numRows = pg_num_rows($table_result); //Get number of rows for easy table creation

		$table_result = pg_fetch_array($table_result, null, PGSQL_ASSOC);

	  ?>
	  <p>There were <em>11</em> rows returned<br/><br/>

	<table border="1">
	<tr>
		<td align="center"><strong>action</strong></td>
		<td align="center"><strong>ip_address</strong></td>
		<td align="center"><strong>log_date</strong></td>
	</tr>

	<?php
	echo "Number of rows: " . $numRows;
		//Display data(action, ip_address, and log_date) in table format
		for ($i = 0; $i < $numRows; $i++) {
			echo "\n\t<tr>";
			echo "\n\t\t<td>" . $table_result['action'] . "</td>";
			echo "\n\t\t<td>" . $table_result['ip_address'] . "</td>";
			echo "\n\t\t<td>" . $table_result['log_date'] . "</td>";
			echo "\n\t</tr>";
		}
	?>
	
	</table>

	<!--Links to update or log out of the page-->
	 </p>
	  <p><a href="update.php">Click to update page.</a></p>
	  <p><a href="logout.php">Click here to logout</a></p>
	</div>
</div>
</body>
</html>