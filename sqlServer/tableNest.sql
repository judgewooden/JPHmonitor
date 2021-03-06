USE Sensors;
DROP TABLE IF EXISTS Nest;

/* The temperature of the AirSensors outside */
CREATE TABLE `Nest` (
	`Timestamp` timestamp NOT NULL,
    `Away` tinyint(1),
	`Temperature` numeric(7,3),
	`Humidity` numeric(7,3),
	PRIMARY KEY (`Timestamp`),
	UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;

