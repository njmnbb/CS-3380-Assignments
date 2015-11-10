DROP SCHEMA IF EXISTS lab2 CASCADE;
CREATE SCHEMA lab2;
SET search_path = lab2;

CREATE TABLE building (
	name 						varchar (50),
	address 					varchar (25),
	city 						varchar (25),
	state 						varchar (2),
	zipcode 					varchar (10),
	PRIMARY KEY(address, zipcode)
);

CREATE TABLE office (
	room_number 				varchar (50),
	waiting_room_capacity 		varchar (10),
	building_address 			varchar (50),
	building_zipcode			varchar (50),
	PRIMARY KEY(room_number),
	FOREIGN KEY(building_address, building_zipcode) REFERENCES building(address, zipcode)
);

CREATE TABLE doctor (
	first_name 					varchar (50),
	last_name 					varchar (25),
	medical_license_num	 		varchar (50),
	office_num 					varchar (50),
	PRIMARY KEY(medical_license_num),
	FOREIGN KEY(office_num) REFERENCES office(room_number)
);

CREATE TABLE insurance (
	insurer 					varchar (25),
	policy_num 					varchar (10),
	PRIMARY KEY(policy_num)
);

CREATE TABLE labwork (
	test_name 					varchar (25),
	test_timestamp 				time,
	test_value 					varchar (25),
	PRIMARY KEY(test_name, test_timestamp)
);

CREATE TABLE condition (
	icd10						varchar (20),
	description					varchar (100),
	PRIMARY KEY(icd10)
);

CREATE TABLE patient (
	ssn 						varchar (15),
	first_name 					varchar (25),
	last_name 					varchar (25),
	patient_insurance			varchar (10),
	lab_name					varchar (50),
	lab_timestamp				time,
	condition_icd 				varchar (50),
	PRIMARY KEY(ssn),
	FOREIGN KEY(patient_insurance) REFERENCES insurance(policy_num),
	FOREIGN KEY(lab_name, lab_timestamp) REFERENCES labwork(test_name, test_timestamp),
	FOREIGN KEY(condition_icd) REFERENCES condition(icd10)
);

CREATE TABLE appointment (
	appt_date					date,
	appt_time					time,
	doctor_license_num					varchar (50),
	patient_ssn				varchar (15),
	FOREIGN KEY(doctor_license_num) REFERENCES doctor(medical_license_num),
	FOREIGN KEY(patient_ssn) REFERENCES patient(ssn)
);

/*Inserting building values*/
INSERT INTO building(name, address, city, state, zipcode) 
	VALUES('The Building', '123 Seasame Street', 'St. Louis', 'MO', '63129');
INSERT INTO building(name, address, city, state, zipcode) 
	VALUES('The Hospital', '456 Elm Street', 'St. Charles', 'MO', '63301');
INSERT INTO building(name, address, city, state, zipcode) 
	VALUES('The Hospital Building', '789 Cherry Street', 'St. Paul', 'MN', '55101');
SELECT * FROM building;

/*Inserting office values*/
INSERT INTO office(room_number, waiting_room_capacity, building_address, building_zipcode)
	VALUES('314', '30', '123 Seasame Street', '63129');
INSERT INTO office(room_number, waiting_room_capacity, building_address, building_zipcode)
	VALUES('315', '40', '456 Elm Street', '63301');
INSERT INTO office(room_number, waiting_room_capacity, building_address, building_zipcode)
	VALUES('316', '50', '789 Cherry Street', '55101');
SELECT * FROM office;

/*Inserting doctor values*/
INSERT INTO doctor(first_name, last_name, medical_license_num, office_num)
	VALUES('Jack', 'Johnson', '111', '314');
INSERT INTO doctor(first_name, last_name, medical_license_num, office_num)
	VALUES('John', 'Jackson', '222', '315');
INSERT INTO doctor(first_name, last_name, medical_license_num, office_num)
	VALUES('Jim', 'Johnson', '333', '316');
SELECT * FROM doctor;

/*Inserting insurance values*/
INSERT INTO insurance(insurer, policy_num)
	VALUES('AAA Insurance', '626');
INSERT INTO insurance(insurer, policy_num)
	VALUES('BBB Insurance', '627');
INSERT INTO insurance(insurer, policy_num)
	VALUES('CCC Insurance', '628');
SELECT * FROM insurance;

/*Inserting labwork values*/
INSERT INTO labwork(test_name, test_timestamp, test_value)
	VALUES('Test 1', '4:00 PM', 'False');
INSERT INTO labwork(test_name, test_timestamp, test_value)
	VALUES('Test 2', '5:00 PM', 'True');
INSERT INTO labwork(test_name, test_timestamp, test_value)
	VALUES('Test 3', '6:00 PM', 'False');
SELECT * FROM labwork;

/*Inserting condition values*/
INSERT INTO condition(icd10, description)
	VALUES('s06.0x1A', 'Concussion with loss of consciousness');
INSERT INTO condition(icd10, description)
	VALUES('W20.8xxA', 'Struck by falling object');
INSERT INTO condition(icd10, description)
	VALUES('G44.311', 'Post traumatic headache');
SELECT * FROM condition;

/*Inserting patient values*/
INSERT INTO patient(ssn, first_name, last_name, patient_insurance, lab_name, lab_timestamp, condition_icd)
	VALUES('123-45-6789', 'Will', 'Minard', '626', 'Test 1', '4:00 PM', 's06.0x1A');
INSERT INTO patient(ssn, first_name, last_name, patient_insurance, lab_name, lab_timestamp, condition_icd)
	VALUES('987-65-4321', 'Dan', 'Hart', '627', 'Test 2', '5:00 PM', 'W20.8xxA');
INSERT INTO patient(ssn, first_name, last_name, patient_insurance, lab_name, lab_timestamp, condition_icd)
	VALUES('777-77-7777', 'Dustin', 'Rios', '628', 'Test 3', '6:00 PM', 'G44.311');
SELECT * FROM patient;

/*Inserting appointment values*/
INSERT INTO appointment(appt_date, appt_time, doctor_license_num, patient_ssn)
	VALUES(CURRENT_DATE, '4:00 PM', '111', '123-45-6789');
INSERT INTO appointment(appt_date, appt_time, doctor_license_num, patient_ssn)
	VALUES(CURRENT_DATE, '5:00 PM', '222', '987-65-4321');
INSERT INTO appointment(appt_date, appt_time, doctor_license_num, patient_ssn)
	VALUES(CURRENT_DATE, '6:00 PM', '333', '777-77-7777');
SELECT * FROM appointment;