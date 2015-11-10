/* Nick Martini, Lab 7, CS 3380, 3/15/15 */

/* Question 1.
	The index comes from the 'id' field, the PRIMARY KEY, from the 'banks' table.*/

/* Question 2. */
	/* 1) */ SELECT * FROM lab7.banks WHERE state = 'Missouri';
	/* 2) Sequential scan */
	/* 3) */ CREATE INDEX ON lab7.banks (state);
	/* 4) Bitmap heap scan */
	/* 5) Before: 14.622 ms
		  After: 5.138 ms
		  Change in speed: ~284% faster

/* Question 3. */
	/* 1) */ SELECT * FROM lab7.banks ORDER BY name;
	/* 2) Sequential Scan */
	/* 3) */ CREATE INDEX ON lab7.banks (name);
	/* 4) Index scan */
	/* 5) Before: 472.194ms
		  After: 76.595 ms
		  Change in speed: ~616% faster

/* Question 4. */
	CREATE INDEX ON lab7.banks (is_active);

/* Question 5. */
	SELECT * FROM lab7.banks WHERE is_active = TRUE; --uses the index.
	SELECT * FROM lab7.banks WHERE is_active = FALSE; --does not use the index.
	/* When making the index used in Question 4, where are checking whether the 'is_active' field's Boolean value
	is true. The indexes in this question, Question 5, set this Boolean value to true and false, depending on which
	index you are looking at. Since the index is created when the Boolean value is TRUE, the first query creates an index
	since it sets 'is_active' to TRUE */

/* Question 6. */
	/* 1) */ SELECT * FROM lab7.banks WHERE insured >= '2001-01-01';
	/* 2) Sequential scan */
	/* 3) */ CREATE INDEX ON lab7.banks (insured) WHERE insured != '1934-01-01';
	/* 4) Index scan */
	/* 5) Before: 12.156 ms
		  After: 3.076 ms
		  Change in speed: ~395% faster */

/* Question 7. */
	/* 1) */ SELECT id, name, city, state, assets, deposits FROM lab7.banks WHERE round(assets/deposits) <= 0.5 AND deposits != 0;
	/* 2) Sequential scan */
	/* 3) */ CREATE INDEX ratio ON lab7.banks (round((assets/deposits))) WHERE deposits != 0;
	/* 4) Bitmap heap scan */
	/* 5) Before: 56.03 ms
		  After: 0.267 ms
		  Change in speed: ~20,985% faster */