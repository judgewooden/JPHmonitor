USE Sensors;
DROP TABLE IF EXISTS KastTemperature1;

/* The temperature of the AirSensors outside */
CREATE TABLE `KastTemperature1` (
	`Timestamp` timestamp NOT NULL,
	`Value` numeric(7,3),
	`Humidity` numeric(7,3),
	PRIMARY KEY (`Timestamp`),
	UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;

