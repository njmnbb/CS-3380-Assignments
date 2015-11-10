<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>CS 3380 Lab 8</title>
</head>
<body>
<!-- Jump out of PHP and create a form to hold the inputs-->
<div align = "center">               
	<div id = "login">
	  <p>Please register
	  <form action="/~njmnbb/cs3380/lab8/registration.php" method='post'>
		  <label for="username">username:</label>
		  <input type="text" name="username" id="username">
		  <label for="password">password:</label>
		  <input type="password" name="password" id="password">
		  <br><br>
		  <input type="submit" name="submit" value="submit">
	  </form> 
	  </p>
	</div>

	<?php
		//Connecting to database
		include '../../secure/database.php';
		$conn = pg_connect(HOST ." " . DBNAME . " " . USERNAME . " " . PASSWORD)
			or die('Could not connect:' . pg_last_error());	

		//Start session
		session_start();

		//If 'submit' button is pressed...
		if(isset($_POST['submit'])) {
			//Create username variable
			$username = htmlspecialchars(pg_escape_string($_POST['username']));

			//Create password variable/hash
			$salt = sha1(mt_rand());
			$password_hash = sha1( htmlspecialchars(pg_escape_string($_POST['password'])) . $salt );
			echo "Password sha1: " . sha1(htmlspecialchars(pg_escape_string($_POST['password'])));
			echo "<br>Password hash: " . $password_hash;

			//Create query to check for duplicates
			$duplicate_query = "SELECT * FROM lab8.user_info WHERE username LIKE $1";
			pg_prepare($conn, "duplicate", $duplicate_query);
			$duplicate = pg_execute($conn, "duplicate", array($username));

			//If the entered username IS a duplicate(if the row for that username is already full)...
			if(pg_num_rows($duplicate) == 1) {
				//Error message
				echo "<p>The username entered has already been used</p>";
				echo "<br>";
				echo "<a href = 'index.php'>Back to login</a>";
			}

			//If the username field is empty...
			else if($_POST['username'] == NULL) {
				//Error message
				echo "<p>Please enter a username in the field above</p>";
				echo "<br>";
				echo "<a href = 'index.php'>Back to login</a>";
			}

			//If the enetered username is NOT a duplicate(if the row for that username is empty)...
			else {
				//Create 'user_info' query to send to SQL table
				$info_query = "INSERT INTO lab8.user_info(username) VALUES ($1)";
				pg_prepare($conn, "user_info", $info_query);
				$info_result = pg_execute($conn, "user_info", array($username));

				//Create 'authentication' query to send to SQL table
				$authentication_query = "INSERT INTO lab8.authentication(username, password_hash, salt) VALUES ($1, $2, $3)";
				pg_prepare($conn, "authentication", $authentication_query);
				$authentication_result = pg_execute($conn, "authentication", array($username, $password_hash, $salt));

				//Create 'ip_address' and 'action' variables
				$ip_address = $_SERVER["REMOTE_ADDR"];
				$action = 'register';

				//Create 'log' query to send to SQL table
				$log_query = "INSERT INTO lab8.log(username, ip_address, action) VALUES ($1, $2, $3)";
				pg_prepare($conn, "log", $log_query);
				$log_result = pg_execute($conn, "log", array($username, $ip_address, $action));

				//Send the enetered username through the session
				$_SESSION['username'];

				//Confirmation message
				echo "Your username and password have been saved";
				echo "<br>";
				echo "<a href = 'index.php'>Back to login</a>";
			}
		}

	?>
</div>
</body>
</html>