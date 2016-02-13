USE Sensors;
DROP TABLE IF EXIST ArduinoMonitor2;

/* The flow details from the Arduino */
CREATE TABLE `ArduinoMonitor2` (
	`Timestamp` timestamp NOT NULL,
	`Power` integer,
	`FlowPerSecond` numeric(7,3),
	`LitersPerMinute` numeric(7,3),
	`OverrideTime` int(10),
	`CurrentTime` int(10),
PRIMARY KEY (`Timestamp`),
UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;


