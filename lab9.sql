/* Nick Martini */
/* CS 3380 */

/* 1) Return the names of the states (name10) that intersect a rectangular polygon formed with a lower
left corner at 110◦W, 35◦N and an upper right corner at 109◦W, 36◦N. Order the results in descending
order of state name. */

SELECT name10 FROM tl_2010_us_state10
WHERE ST_Intersects(coords, ST_GeomFromText('POLYGON((-109 35, -110 35, -110 36, -109 36, -109 35))', 4326))
ORDER BY name10 DESC;

/* 2) Which states touch North Carolina? Return USPS code (stusps10) and the name of each state
(name10) alphabetized by the name in ascending order */

WITH northCarolina AS (
	SELECT * FROM tl_2010_us_state10
	WHERE stusps10 = 'NC'
)

SELECT tl_2010_us_state10.stusps10, tl_2010_us_state10.name10 FROM tl_2010_us_state10, northCarolina
WHERE ST_Touches(tl_2010_us_state10.coords, northCarolina.coords)
ORDER BY name10 ASC;

/* 3) Return the names (name10) of all urban areas (in alphabetical order) that are entirely contained
within Colorado. Return the results in alphabetical order. */
WITH colorado AS (
	SELECT * FROM tl_2010_us_state10
	WHERE stusps10 = 'CO'
)

SELECT tl_2010_us_uac10.name10 FROM tl_2010_us_uac10, colorado
WHERE ST_Contains(colorado.coords, tl_2010_us_uac10.coords)
ORDER BY name10 ASC;


/* 4) Return all names (name10) and the combined (land and water) area in square kilometers of all urban
areas that overlap some portion of Pennsylvania, but are not entirely contained within Pennsylvania.
The query results should be ordered in decreasing area from greatest to least. */
WITH pennsylvania AS (
	SELECT * FROM tl_2010_us_state10
	WHERE stusps10 = 'PA'
)

SELECT tl_2010_us_uac10.name10, ((tl_2010_us_uac10.aland10 + tl_2010_us_uac10.awater10) / 1000000) AS landWater FROM tl_2010_us_uac10, pennsylvania
WHERE ST_Overlaps(pennsylvania.coords, tl_2010_us_uac10.coords)
ORDER BY landWater DESC;

/* 5) Which pairs of urban areas intersect each other? Exclude self-intersections. Return the names of the
urban areas (name10). (Note: If A and B intersect one another, only return the tuple {A,B} or {B,A},
but not both. Think about how to use the gid to enforce this.) (Note #2: The straightforward query
that I wrote for this took ∼25 seconds to execute. Be patient!) */

SELECT urban1.name10 AS city1, urban2.name10 AS city2
FROM tl_2010_us_uac10 AS urban1, tl_2010_us_uac10 AS urban2
WHERE urban1.gid < urban2.gid AND ST_Intersects(urban1.coords, urban2.coords);

/* 6) . Find all urban areas that (1) have a combined land & water area of greater than 1500 square kilometers
and (2) intersect multiple states. Your query should return the urban area name (name10)
and a count of the number of states intersected. The results should be first ordered by the number of
states intersected (in descending order) and secondarily by alphabetical order of the urban area names
from (A to Z). (Note: Pay very close attention to the units used for the areas in this question.) */

SELECT urban.name10, COUNT(*) AS states_intersected FROM tl_2010_us_uac10 AS urban, tl_2010_us_state10 AS state 
WHERE ST_Intersects(urban.coords, state.coords) AND ((urban.aland10 + urban.awater10) / 1000000) > 1500 
GROUP BY urban.name10 HAVING COUNT(*) > 1 
ORDER BY states_intersected DESC, urban.name10 ASC;