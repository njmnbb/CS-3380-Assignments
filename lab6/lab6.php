<!--babbage.cs.missouri.edu/~njmnbb/cs3380/lab6/lab6.php-->
<!DOCTYPE html>
<html>
	<head>
		<meta charset=UTF-8>
		<title>CS 3380 Lab 6</title>
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
		<option value="10">Query 10</option>
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

			//List the minimum, maximum and average surface area
			//of all countries in the database
			case 1:
				$query = 	'SELECT MIN(surface_area), MAX(surface_area), AVG(surface_area)
							FROM lab6.country';
				break;

			//List the total population, total surface area and 
			//total GNP by region; order the results from largest to smallest GNP.
			case 2:
				$query = 	'SELECT DISTINCT region, SUM(population) as total_population, SUM(surface_area) as total_area, SUM(gnp) as total_gnp
							FROM lab6.country
							GROUP BY region
							ORDER BY total_gnp DESC';
				break;

			//Generate a list of all forms of government with the count of how many countries have that form of
			//government. Also, list the most recent year in which any country became 
			//independent with that form of government. The results should be ordered by
			//decreasing count. For situations when multiple forms of government 
			//have the same count, sort these in descending order by the most recent year of
			//independence. (Note: Some countries may have NULL for the independence year.
			//Those countries should not be considered when finding the earliest independence year.)
			case 3:
				$query = 	'SELECT DISTINCT government_form, COUNT(government_form), MAX(indep_year) AS most_recent_indep_year
							FROM lab6.country
							WHERE indep_year IS NOT NULL
							GROUP BY government_form
							ORDER BY count DESC';
				break;

			//For each country with at least one hundred cities in the database,
			//list the total number of cities it contains.
			//Order the results in ascending order of the number of cities.
			case 4:
				$query =	'SELECT country.name, COUNT(city.name) AS city_count
							FROM lab6.country 
							INNER JOIN lab6.city USING(country_code)
							GROUP BY (country.name)
							HAVING COUNT(city.name) > 100
							ORDER BY COUNT(city.name)';
				break;

			//List the country name, it’s population, and the sum of the populations of all cities in that country.
			//Add a fourth field to your query that calculates the percent of urban population for each country. (For
			//the purposes of this example, assume that the sum of the populations of all cities listed for a country
			//represent that country’s entire urban population.) Order the results of this query in increasing order
			//of urban population percentage.
			case 5:
				$query = 	'SELECT country.name, country_population, urban_population, CAST(((urban_population/country_population) * 100) AS FLOAT) AS urban_percent
							FROM (SELECT country.name as name, max(country.population) AS country_population, CAST(SUM(city.population) AS FLOAT) AS urban_population
                			FROM lab6.country JOIN lab6.city USING (country_code)
               				GROUP BY(country.name))AS pop, lab6.country WHERE pop.name = country.name
                			ORDER BY urban_percent';
				break;

			//For each country, list the largest population of any of its cities and the
			//name of that city. Order the results in decreasing order of city populations.
			case 6:
				$query = 	'SELECT country_pop.name, city_pop.name AS largest_city, country_pop.population
							FROM (SELECT country.name AS name, MAX(city.population) AS population
								FROM lab6.country 
								INNER JOIN lab6.city USING (country_code)
								GROUP BY country.name) AS country_pop, lab6.city AS city_pop
							WHERE country_pop.population = city_pop.population
							ORDER BY country_pop.population DESC';
				break;

			//List the countries in descending order beginning with the country with
			//the largest number of cities in the database and ending with the country
			//with the smallest number of cities in the database. Cities that have the
			//same number of cities should be sorted alphabetically from A to Z.
			case 7:
				$query = 	'SELECT country.name, COUNT(city.name)
							FROM lab6.country
							INNER JOIN lab6.city USING (country_code)
							GROUP BY country.name
							ORDER BY COUNT(city.name) DESC';
				break;

			//For each country with 8-12 languages, list the number of languages spoken, in descending order by
			//number of languages as well as the name of the capital for that country
			case 8:
				$query = 	'SELECT country.name, capitals.name AS capital, COUNT(language) AS lang_count
							FROM lab6.country 
							INNER JOIN (SELECT city.name AS name, city.country_code AS country_code FROM lab6.city, lab6.country WHERE city.id = country.capital) AS capitals
							ON (capitals.country_code = country.country_code)
							INNER JOIN lab6.country_language ON (country.country_code = country_language.country_code)
							GROUP BY country.name, capitals.name
							HAVING COUNT(language) > 7 AND COUNT(language) < 13
							ORDER BY lang_count DESC';
				break;

			//Using SQL window functions, write a query that calculates a running total of the sum of all city
			//populations with each country. This running total should be calculated by accumulating the city
			//populations from largest to smallest. The resulting output should be sorted first by country name and
			//secondarily by the running total column. Also display the city name and city population in each row.
			case 9:
				$query = 	'SELECT country.name AS country_name, city.name AS city_name, city.population, SUM(city.population) OVER (PARTITION BY city.country_code ORDER BY city.population) AS running_total
							FROM lab6.country, lab6.city
							WHERE country.country_code = city.country_code
							ORDER BY country.name, running_total DESC';
				break;

			//Again, using window functions rank the popularity of each language within each country. We’ll assume
			//that the percent of speakers of a language in the country is a measure of it’s popularity. For each record,
			//show the name of the country, the name of the language and it’s popularity rank. The most popular
			//language should be ranked 1, the second most popular 2, etc.
			case 10:
				$query =	'SELECT country.name, country_language.language, 
							rank() OVER (PARTITION BY country.name ORDER BY percentage DESC) AS popularity_rank
							FROM lab6.country
							INNER JOIN lab6.country_language USING (country_code)
							WHERE country_language.country_code = country.country_code';
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