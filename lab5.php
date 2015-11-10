<!--babbage.cs.missouri.edu/~njmnbb/cs3380/lab5/index.php-->
<!DOCTYPE html>
<html>
<head>
	<meta charset=UTF-8>
	<title>CS 3380 Lab 5</title>
	<script>
	function clickAction(form, pk, tbl, action)
	{
	  document.forms[form].elements['pk'].value = pk;
	  document.forms[form].elements['action'].value = action;
	  document.forms[form].elements['tbl'].value = tbl;
	  document.getElementById(form).submit();
	}
	</script>
</head>
<body>
	<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
	    Search for a :
	    <input type="radio" name="search_by" checked="true" value="country"  />Country 
	    <input type="radio" name="search_by" value="city"  />City
	    <input type="radio" name="search_by" value="language"  />Language <br /><br />
	    That begins with: <input type="text" name="query_string" value="" /> <br /><br />
	    <input type="submit" name="submit" value="Submit" />
	</form>
	<hr />
	Or insert a new city by clicking this <a href="exec.php?action=insert">link</a>

	<?php
	//Connecting to database
	include '../../secure/database.php';
	$conn = pg_connect(HOST ." " . DBNAME . " " . USERNAME . " " . PASSWORD)
		or die('Could not connect:' . pg_last_error());	

	//If 'submit' button is pressed, do the following
	if (isset($_POST['submit'])) {
			$option = $_POST['search_by'];

			switch ($option) {
				
				//Uppercase the letters entered by the user,
				//then make a table showing all countries whose
				//names begin with the letters entered by the user
				case "country":
					/*$result = pg_prepare($conn, $query, )*/
					if ($_POST['query_string'] != null) {
						$queryString = ucwords($_POST['query_string']);
						$query =      "SELECT * FROM lab5.country 
                                      WHERE name LIKE '$queryString%'
                                      ORDER BY name ASC";
						break;
					}
					else if ($_POST['query_string'] == null) {
						$query =      "SELECT * FROM lab5.country
                                      ORDER BY name ASC";
						break;
					}

				//Uppercase the letters entered by the user,
				//then make a table showing all cities whose
				//names begin with the letters entered by the user
				case "city":
					if ($_POST['query_string'] != null) {
						$queryString = ucwords($_POST['query_string']);
						$query =      "SELECT * FROM lab5.city
                                      WHERE name LIKE '$queryString%'
                                      ORDER BY name ASC";
						break;
					}
					else if ($_POST['query_string'] == null) {
						$query =      "SELECT * FROM lab5.city
                                      ORDER BY name ASC";
						break;
					}

				//Uppercase the letters entered by the user,
				//then make a table showing all languages whose
				//names begin with the letters entered by the user
				case "language":
					if ($_POST['query_string'] != null) {
						$queryString = ucwords($_POST['query_string']);
						$query =      "SELECT * FROM lab5.country_language
                                      WHERE language LIKE '$queryString%'
                                      ORDER BY language ASC";
						break;
					}
					else if ($_POST['query_string'] == null) {
						$query =      "SELECT * FROM lab5.country_language
                                      ORDER BY language ASC";
						break;
					}

				default:
					break;
				}
		$result = pg_prepare($conn, "list", $query)
					or die('<br><br>Query failed: ' . pg_last_error());
		$result = pg_execute($conn, "list", array());

		//Start table
		echo "<table border = \"1\">\n";
		echo "<hr>";
		echo "<br>There were <strong>" . pg_num_rows($result) . " </strong>result(s) returned";
		echo "<th>Actions</th>";
		for ($i = 0; $i < pg_num_fields($result); $i++) {
			$head = pg_field_name($result, $i);
			echo "\t\t<th>$head</th>";
		}

		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			echo "\t<tr>\n";
			echo    "<td>
                        <form id = \"action_form\" action = \"exec.php\">
                        <input type = \"submit\" name = \"action\" value = \"Edit\" onclick=\"clickAction('action_form', $line[0], $option, 'edit');\">
                        <input type = \"submit\" name = \"action\" value = \"Remove\" onclick=\"clickAction('action_form', $line[0], $option, 'remove');\">
                        <input type = \"hidden\" name = \"key\" value = \"$line[0]\">
                        <input type = \"hidden\" name = \"tbl\" value = \"$option\">
                        </form>
                    </td>";
            
			foreach ($line as $col_value) {
			 	echo "\t\t<td>$col_value</td>\n";
			}
			echo "\t</tr>\n";
		}

		//End table
		echo "</table border>\n";

		//Free resultset
		pg_free_result($result);

		//Closing connection
		pg_close($conn);
	}
	?>	

</body>
</html>