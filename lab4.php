<!--babbage.cs.missouri.edu/~njmnbb/cs3380/lab4/lab4.php-->
<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>CS 3380 Lab 4</title>
	</head>
<body>
	<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
	<select name="query">
		<option value="1">Query 1</option>
		<option value="2">Query 2</option>
		<option value="3">Query 3</option>
		<option value="4">Query 4</option>
		<option value="5">Query 5</option>
		<option value="6">Query 6</option>
		<option value="7">Query 7</option>
		<option value="8">Query 8</option>
		<option value="9">Query 9</option>
	</select>
	<input type="submit" name="submit" value="Execute" />
	</form>

	<br/>
	<hr/>
	<br/>

	<strong>Select a query from the above list</strong></body>

	<?php
	//Connecting to database
	include '../../secure/database.php';
	$conn = pg_connect(HOST ." " . DBNAME . " " . USERNAME . " " . PASSWORD)
		or die('Could not connect:' . pg_last_error());	

	//If button is pressed, do the following
	if (isset($_POST['submit'])){

		switch ($_POST['query']){

			//Find the district and population of all cities named Springfield.
			//Sort results from most populous to least populous
			case 1:
				$query = 	'SELECT district, population
							FROM lab3.city 
							WHERE name = \'Springfield\'
							ORDER BY population DESC';
				break;

			//Find the name, district, and population of each city in Brazil (country code BRA).
			//Order results by city name alphabetically
			case 2:
				$query = 	'SELECT name, district, population
							FROM lab3.city
							WHERE country_code = \'BRA\'
							ORDER BY name ASC';
				break;

			//Find the name, continent, and surface area of the smallest countries by surface area.
			//Order by surface area with smallest first. Return only 20 countries
			case 3:
				$query = 	'SELECT name, continent, surface_area
							FROM lab3.country
							ORDER BY surface_area DESC LIMIT 20';
				break;

			//Find the name, continent, form of government, and GNP of all countries having a GNP greater than 200,000.
			//Sort the output by the name of the country in alphabetical order from A to Z
			case 4:
				$query =	'SELECT name, continent, government_form, gnp
							FROM lab3.country
							WHERE gnp > 200000
							ORDER BY name ASC';
				break;

			//Find the 10 countries with the 10th through 19th best life expectancy rates.
			//You should use WHERE life expectancy IS NOT NULL to remove null values when querying this table
			case 5:
				$query = 	'SELECT name, life_expectancy
							FROM lab3.country
							WHERE life_expectancy IS NOT NULL
							ORDER BY life_expectancy DESC OFFSET 10 LIMIT 10';
				break;

			//Find all city names that start with the letter B and ends in the letter s.
			//Results should be ordered from largest to smallest population, but do not display the population field.
			case 6:
				$query = 	'SELECT name
							FROM lab3.city
							WHERE (name LIKE \'B%\') AND (name LIKE \'%s\')';
				break;

			//Return the name, name of the country, and city population of each city in the world having population greater than 6,000,000.
			//Order results by the city population with the most populous first
			case 7:
				$query = 	'SELECT country.name, city.name AS city_name, city.population
							FROM lab3.city
							INNER JOIN lab3.country USING(country_code)
							WHERE city.population > 6000000
							ORDER BY city.population DESC LIMIT 20';
				break;

			//Find the country name, language name and percent of speakers of all unofficial languages spoken in countries of population greater than 50,000,000 population.
			//Order results by percent of speakers with the most spoken language first
			case 8:
				$query = 	'SELECT country.name, country_language.language, country_language.percentage
							FROM lab3.country
							INNER JOIN lab3.country_language USING(country_code)
							WHERE country_language.is_official = \'FALSE\' AND country.population > 50000000
							ORDER BY country_language.percentage DESC';
				break;

			//Find the name, independence year, and region of all countries where English is an official language.
			//Order results by region ascending and alphabetize the results within each region by country name.
			case 9:
				$query = 	'SELECT name, indep_year, region
							FROM lab3.country
							INNER JOIN lab3.country_language USING(country_code)
							WHERE country_language.is_official = \'TRUE\' AND country_language.language =\'English\'
							ORDER BY country.region, country.name ASC';
				break;

			default:
				break;
		}
		echo "<table border = \"1\">\n";
		$result = pg_query($query)
			or die('Query failed: ' . pg_last_error());

		for ($i = 0; $i < pg_num_fields($result); $i++) {
			$head = pg_field_name($result, $i);
			echo "\t\t<th>$head</th>";
		}
		echo"\t</tr>";

		while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
			echo "\t<tr>\n";
			foreach ($line as $col_value) {
			 	echo "\t\t<td>$col_value</td>\n";
			 	}
			 echo "\t</tr>\n";
		}
		echo "</table border>\n";
		//Free resultset
		pg_free_result($result);
		//Closing connection
		pg_close($conn);

	}
	?>	

</body>
</html>