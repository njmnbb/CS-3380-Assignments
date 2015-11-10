<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
	<title>CS 3380 Lab 5</title>
</head>

<?php
    //Connecting to database
    include '../../secure/database.php';
    $conn = pg_connect(HOST ." " . DBNAME . " " . USERNAME . " " . PASSWORD)
        or die('Could not connect:' . pg_last_error());

    //Checking to see if an input button has been pressed
    if(isset($_GET['action'])){
		if ($_GET['action'] == 'insert') {
			$query = 'SELECT name, country_code FROM lab5.country ORDER BY name;';
			$result = pg_prepare($conn, "list", $query);
			$result = pg_execute($conn, "list", array());
		
			echo "<form method=\"POST\" action=\"exec.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"insert\">";
			echo "<table border=\"1\">";
			echo	"<tr>";
			echo		"<td>Name</td>";
			echo		"<td><input type=\"text\" name=\"name\"></td>";
			echo	"</tr>";
			echo	"<tr>";
			echo		"<td>Country Code</td>";
			echo		"<td>";
			echo			"<select name=\"country_code\">";
				while($line = pg_fetch_array($result, null, PGSQL_ASSOC)){
					echo "<option value=\"".$line['country_code']."\">".$line['name']."</option>";
				}
			echo		"</select>";
			echo		"</td>";
			echo 	"</tr>";
			echo	"<tr>";
			echo		"<td>District</td>";
			echo		"<td><input type=\"text\" name=\"district\"></td>";
			echo	"</tr>";
			echo 	"<tr>";
			echo		"<td>Population</td>";
			echo		"<td><input type=\"text\" name=\"population\"></td>";
			echo	"</tr>";
			echo "</table>";
			echo "<input type=\"submit\" name=\"submit\" value=\"Save\">";
			echo "<input type=\"button\" value=\"Cancel\" onclick=\"top.location.href='index.php'\">";
			echo "</form>";
		
		}
	}

	else if(isset($_POST['action'])) {
		//Inserts a city and its required parameters into the table
		if ($_POST['action'] == 'insert') {
			$name = htmlspecialchars($_POST['name']);
			$country_code = htmlspecialchars($_POST['country_code']);
			$district = htmlspecialchars($_POST['district']);
			$population = htmlspecialchars($_POST['population']);

			$query2 = 	'INSERT INTO lab5.city (name, country_code, district, population)
						VALUES (\''.$name.'\', \''.$country_code.'\', \''.$district.'\', '.$population.');';

			pg_prepare($conn, "insert", $query2);

			if (pg_execute($conn, "insert", array())) {
				echo "Insert was successful <br/>";
				echo "Return to <a href=\"index.php\">search</a>";
			}
			else {
				echo "Insert was unsuccessful <br/>";
				echo "Return to <a href=\"index.php\">search</a>";
			}
		}
		
		//Edits a selected row from the table
		else if($_POST['action'] == 'Edit') {
			
		}

		//Removes a selected row from the table
		else if($_POST['action'] == 'Remove') {
			$table = $_POST['tbl'];
			$key = $_POST['key'];

			switch ($table) {
				case "country":
					$query = 'DELETE FROM lab5.country WHERE (country_code = \''.$key.'\');';
					break;

				case "city":
					$query = 'DELETE FROM lab5.city WHERE (id = '.$key.')';
					break;

				case "language":
					$query = 'DELETE FROM lab5.country_language WHERE (country_code = \''.$key.'\') AND (language=\''.$language.'\');';
					break;
			}
			
			pg_prepare($conn, "delete", $query);
			
			if(pg_execute($conn, "delete", array())){
				echo "Delete was successful<br/>";
				echo "Return to <a href=\"index.php\">search</a>";
			}
			else{
				echo "Delete unsuccessful<br/>";
				echo "Return to <a href=\"index.php\">search</a>";
			}
		}
	}
    
?>
</html>