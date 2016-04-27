USE Sensors;
DROP TABLE IF EXISTS RaspiTemp2;

/* The temperature of the Raspi itself */
CREATE TABLE `RaspiTemp2` (
	`Timestamp` timestamp NOT NULL,
	`Value` numeric(7,3) NOT NULL,
	PRIMARY KEY (`Timestamp`),
	UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;

