USE Sensors;
DROP TABLE IF EXISTS Power1;

/* The temperature of the AirSensors outside */
CREATE TABLE `Power1` (
	`Timestamp` timestamp NOT NULL,
    `Power1` numeric(7,3),
    `Power2` numeric(7,3),
	`Energy` numeric(7,3),
	PRIMARY KEY (`Timestamp`),
	UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;

