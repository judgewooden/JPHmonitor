USE Sensors;
DROP TABLE IF EXISTS AirTemperature1;

/* The temperature of the AirSensors outside */
CREATE TABLE `AirTemperature1` (
	`Timestamp` timestamp NOT NULL,
	`Temperature` numeric(7,3),
	`Humidity` numeric(7,3),
	PRIMARY KEY (`Timestamp`),
	UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;

