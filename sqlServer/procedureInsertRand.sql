/* This creates a SQL procedure to generate random data for development */
USE Sensors;
DROP PROCEDURE IF EXISTS InsertRand;
CREATE PROCEDURE InsertRand()
BEGIN
	DECLARE i INT;
	DECLARE startdate DATETIME;
	DECLARE tempdate DATETIME;
	DECLARE value1 numeric(7,3);
	DECLARE value2 numeric(7,3);
	DECLARE timegap INT;
	DECLARE guess INT;
	SET startdate = NOW();
	SET i = 1;
	START TRANSACTION;
	WHILE i <= 30 DO

	/* Simulate Air Temperature being added */

		/* Assume sensor gets data once within 10 seconds */
		SET guess = CEIL(RAND() * 9);
		SET tempdate=DATE_ADD(startdate, INTERVAL guess SECOND);

		/* Sometimes the Temperature could be missing */
		SET guess = CEIL(RAND() * 9); 
		IF ( guess <= 2 ) THEN
			SET value1=null;	
		ELSE
			SET value1=20 + RAND() * (50 - 20);
		END IF;

		/* Sometimes the Humidity could be missing */
		SET guess = CEIL(RAND() * 9); 
		IF ( guess <= 2 ) THEN
			SET value2=null;	
		ELSE
			SET value2=1 + RAND() * (100 - 1);
		END IF;

		INSERT INTO AirTemperature1 VALUES ( tempdate, value1, value2);

	/* Simulate WaterTemperature being added */

		/* Sometimes Temperature could be at the same time as other sensors */
		SET guess = CEIL(RAND() * 9); 
		IF ( guess <= 5 ) THEN
			SET startdate=tempdate;
		ELSE
			SET guess = CEIL(RAND() * 9); 
			SET startdate=DATE_ADD(tempdate, INTERVAL guess SECOND);
		END IF;

		SET value1=20 + RAND() * (50 - 20);

		INSERT INTO WaterTemperature1 VALUES ( startdate, value1);

	/* Simulate Fan PWM / Voltage being added */

	/* TODO Figure out all data possibilities for FANS */


		SET i = i + 1;
	END WHILE;
	COMMIT;
END
