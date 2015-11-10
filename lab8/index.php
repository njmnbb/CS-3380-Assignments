<!--http://babbage.cs.missouri.edu/~njmnbb/cs3380/lab8/index.php-->
<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8>
<title>CS 3380 Lab 8</title>
</head>
<body>
	<div align = "center">               
		<div id = "login">
		  	<p>Please enter credentials to log in
			  <form action="/~njmnbb/cs3380/lab8/index.php" method='post'>
				  <label for="username">username:</label>
				  <input type="text" name="username" id="username">
				  <label for="password">password:</label>
				  <input type="password" name="password" id="password">
				  <br>
				  <input type="submit" name="submit" value="submit">
			  </form> 
			  <p><a href="registration.php">Register here</a></p>
			  </p>
		</div>

	<?php
		//Checks if user is submitting data using HTTP
		if($_SERVER['SERVER_PORT'] !== 443 && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
		  header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		  exit;
		}

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

			//Create query to check if username is in database
			$login_query = "SELECT * FROM lab8.authentication WHERE username LIKE $1";
			pg_prepare($conn, "login", $login_query);
			$login = pg_execute($conn, "login", array($username));
			$login = pg_fetch_array($login, null, PGSQL_ASSOC);
			$password_hash = sha1( htmlspecialchars(pg_escape_string($_POST['password'])) . $login['salt'] );

			$no_salt = sha1($_POST['password']);
			echo "Password hash in database: " . $login['password_hash'];
			echo "<br>Password hash entered: " . $password_hash;
			echo "<br>Password entered without salt: " . $no_salt;
			echo "<br>Salt in database: " . $login['salt'];

			//If username field is empty...
			if($_POST['username'] == NULL || $_POST['password'] == NULL) {
				echo "<p>Please enter a username/password in the field above</p>";
			}

			//If username IS in database(if pg_num_rows = 1)...
			else if($login['password_hash'] == $password_hash) {
				//Create 'SESSION','ip_address', and 'action' variables
				$_SESSION['username'] = $username;
				$ip_address = $_SERVER["REMOTE_ADDR"];
				$action = 'login';

				//Create 'log' query to send to SQL table
				$log_query = "INSERT INTO lab8.log(username, ip_address, action) VALUES ($1, $2, $3)";
				pg_prepare($conn, "log", $log_query);
				$log_result = pg_execute($conn, "log", array($username, $ip_address, $action));

				//Redirect to 'home.php' page
				header('Location: ./home.php');
			}

			//If username is NOT in database(if pg_num_rows = 0)...
			else {
				echo "<p>Username was not found. Please enter a valid username or check your password</p>";
			}
		}
	?>

	</div>
</form>
</body>
</html>