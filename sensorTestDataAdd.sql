USE Sensors;
DROP PROCEDURE IF EXISTS InsertRandAdd;
DELIMITER $$
CREATE PROCEDURE InsertRandAdd()
BEGIN
	DECLARE i INT;
	DECLARE startdate DATETIME;
	DECLARE tempdate DATETIME;
	DECLARE valueTemperature numeric(7,3);
	DECLARE valueHumidity numeric(7,3);
	DECLARE lastTemperature numeric(7,3);
	DECLARE lastHumidity numeric(7,3);
	DECLARE timegap INT;
	DECLARE guess INT;
	SET startdate = NOW();
	SET i = 1;
	START TRANSACTION;

		/* Simulate Air Temperature being added */
		SELECT	Value INTO lastTemperature
		FROM	AirTemperature1
		WHERE	Value IS NOT NULL
		ORDER BY Timestamp DESC
		LIMIT 1;

		IF ( lastTemperature = NULL ) THEN
			SET lastTemperature = 1 + CEIL(RAND() * (100 - 1));
		END IF;

		SET guess = CEIL(RAND() * 9); 

		IF ( guess <= 2 ) THEN
			SET valueTemperature = null;	
		ELSE
			SET valueTemperature = 1 + RAND() * (5 - 10);
			IF (lastTemperature>50) THEN
				SET valueTemperature = 1 + RAND() * (2 - 8);
			END IF;
			IF (lastTemperature<30) THEN
				SET valueTemperature = 1 + RAND() * (6 - 8);
			END IF;
			SET valueTemperature = valueTemperature + lastTemperature;
		END IF;

		/* Simulate Air Humidity being added */
		SELECT	Humidity INTO lastHumidity
		FROM	AirTemperature1
		WHERE	Humidity IS NOT NULL
		ORDER BY Timestamp DESC
		LIMIT 1;

		IF ( lastHumidity = NULL ) THEN
			SET lastHumidity = 50 + CEIL(RAND() * (100 - 50));
		END IF;

		SET guess = CEIL(RAND() * 9); 
		IF ( guess <= 2 ) THEN
			SET valueHumidity = null;	
		ELSE
			SET valueHumidity = 1 + RAND() * (3 - 1);
			IF (lastHumidity>90) THEN
				SET valueHumidity = 1 + RAND() * (2 - 8);
			END IF;
			IF (lastHumidity<80) THEN
				SET valueHumidity = 1 + RAND() * (6 - 8);
			END IF;
			SET valueHumidity = valueHumidity + lastHumidity;
		END IF;

		/* Assume sensor gets data once within 10 seconds */
		SET guess = CEIL(RAND() * 9);
		SET tempdate=DATE_ADD(startdate, INTERVAL guess SECOND);

		INSERT INTO AirTemperature1
		VALUES ( tempdate, valueHumidity, valueTemperature);

		/* Sometimes Temperature could be at the same time as other sensors */
		SET guess = CEIL(RAND() * 9); 
		IF ( guess <= 5 ) THEN
			SET startdate=tempdate;
		ELSE
			SET guess = CEIL(RAND() * 9); 
			SET startdate=DATE_ADD(tempdate, INTERVAL guess SECOND);
		END IF;

		SELECT	Value INTO lastTemperature
		FROM	WaterTemperature1
		WHERE	Value IS NOT NULL
		ORDER BY Timestamp DESC
		LIMIT 1;

		IF ( lastTemperature = NULL ) THEN
			SET lastTemperature = 1 + CEIL(RAND() * (100 - 1));
		END IF;

		SET valueTemperature = 1 + RAND() * (4 - 8);
		IF (lastTemperature>60) THEN
			SET valueTemperature = 1 + RAND() * (2 - 8);
		END IF;
		IF (lastTemperature<20) THEN
			SET valueTemperature = 1 + RAND() * (6 - 8);
		END IF;

		SET valueTemperature = valueTemperature + lastTemperature;

		INSERT INTO WaterTemperature1
		VALUES ( startdate, valueTemperature);

		/* TODO Figure out all data possibilities for FANS */


	COMMIT;
END$$
DELIMITER ;
CALL InsertRandAdd();
DROP PROCEDURE IF EXISTS InsertRand;
