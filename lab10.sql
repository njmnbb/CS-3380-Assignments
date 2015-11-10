/* Nick Martini
   CS 3380
   Lab 10 */


/* 1. Write a SQL statement that drops the lab10 schema if it exists. */

DROP SCHEMA IF EXISTS lab10 CASCADE;

/* 2. Then write a SQL statement that creates a schema named lab10. */

CREATE SCHEMA lab10;

/* 3. Add a statement to your file that sets the current search path to be lab10. This ensures that all tables
and functions created below will exist within the lab10 schema. */

SET search_path = lab10;

/* 4. Write a CREATE TABLE statement to create a table named group standings that matches the definition
that follows. Be sure to include the PRIMARY KEY for your table and any NOT NULL constraints. Also,
include CHECK constraints that enforce the range of possible values. */

CREATE TABLE group_standings(
	team varchar(25) NOT NULL,
	wins smallint NOT NULL CHECK (wins >= 0),
	losses smallint NOT NULL CHECK (losses >= 0),
	draws smallint NOT NULL CHECK (draws >= 0),
	points smallint NOT NULL CHECK (points >= 0),
	PRIMARY KEY(team)
);

/* 5. Next, write a command that uses the psql \copy command to import data from the file found at
/facstaff/klaricm/public cs3380/lab10/lab10 data.csv After you load the data, there should
be 4 records in your table. */

\copy lab10.group_standings FROM /facstaff/klaricm/public_cs3380/lab10/lab10_data.csv USING DELIMITERS ',' CSV HEADER

/* 6. Now, write a pure SQL (i.e. not a PL/pgSQL function) function named calc points total that takes
two arguments that correspond to the number of wins and draws earned by a team. This function
should return the total number of points earned based on the formula in Equation 1 above. */

CREATE OR REPLACE FUNCTION calc_points_total(smallint, smallint)
RETURNS integer AS $$
	SELECT 3 * $1 + $2 AS result;

$$ LANGUAGE SQL;

/* 7. Create a PL/pgSQL function named update points total that is a trigger. This function should
update the NEW record’s points field using the calc points total function before any INSERT or
UPDATE statement. Attach this function to the table as a trigger named tr update points total.
Test this trigger with a few UPDATE and/or INSERT statements. (You don’t need to include these
INSERT/UPDATE statements in your submission.) */

CREATE OR REPLACE FUNCTION update_points_total() 
RETURNS trigger AS $$
	BEGIN
		NEW.points := calc_points_total(NEW.wins,
				NEW.draws);
		RETURN NEW;
	END;

$$ LANGUAGE plpgsql;

DROP TRIGGER tr_update_points_total ON group_standings;

CREATE TRIGGER tr_update_points_total BEFORE INSERT
OR UPDATE OF wins, draws 
ON group_standings 
FOR EACH ROW EXECUTE PROCEDURE update_points_total();

/* 8. Next, write a trigger function named disallow team name update that compares the OLD and NEW
records team fields. If they are different raise an exception that states that changing the team name is
not allowed. */

CREATE OR REPLACE FUNCTION disallow_team_name_update() 
RETURNS trigger AS $$
	BEGIN
		IF NEW.team != OLD.team THEN
			RAISE EXCEPTION 'Changing the team name is not allowed';
		END IF;	
	END;

$$ LANGUAGE plpgsql;

/* 9. Then, attach this trigger to the table with the name tr disallow team name update and specify that
it fires before any potential update of the team field in the table. Test this trigger with a few UPDATE
statements to prove that it works. (You don’t need to include these UPDATE statements in your
submission.) */

DROP TRIGGER tr_disallow_team_name_update ON group_standings;

CREATE TRIGGER tr_disallow_team_name_update AFTER UPDATE OF team 
ON group_standings 
FOR EACH ROW EXECUTE PROCEDURE disallow_team_name_update();