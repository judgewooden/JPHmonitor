DROP DATABASE IF EXISTS Sensors;
CREATE DATABASE Sensors;
GRANT ALL PRIVILEGES ON Sensors.* TO 'Sensors_admin'@'localhost' IDENTIFIED BY 'choose_a_db_password';
FLUSH PRIVILEGES;

USE Sensors;
/* The temperature of the Raspi itself */
CREATE TABLE `RaspiTemp1` (
  `Timestamp` timestamp NOT NULL,
  `Value` numeric(7,3) NOT NULL,
  PRIMARY KEY (`Timestamp`),
  UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;

/* The flow details from the Arduino */
CREATE TABLE `ArduinoMonitor1` (
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

/*
 * TODO : All Tables above this line are final
 * REMEMBER: THEY CAN CONTAIN DATA YOU WANT
 */

/* TODO Cleanup these tables ( they are for development only) */
CREATE TABLE `AirTemperature1` (
  `Timestamp` timestamp NOT NULL,
  `Value` numeric(7,3),
  `Humidity` numeric(7,3),
  PRIMARY KEY (`Timestamp`),
  UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `WaterTemperature1` (
  `Timestamp` timestamp NOT NULL,
  `Value` numeric(7,3) NOT NULL,
  PRIMARY KEY (`Timestamp`),
  UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `FanSpeed1` (
  `Timestamp` timestamp NOT NULL,
  `Value` integer NOT NULL,
  PRIMARY KEY (`Timestamp`),
  UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;
ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE `SpeedfaniMonitor1` (
  `Timestamp` timestamp NOT NULL,
  `SystemTemp` numeric(7,3),
  `CPUTemp` numeric(7,3),
  `SBTemp` numeric(7,3),
  `NBTemp` numeric(7,3),
  `OPT_FAN_1` numeric(7,3),
  `GPU1Temp` numeric(7,3),
  `GPU2Temp` numeric(7,3),
  `GPU3Temp` numeric(7,3),
  `GPU4Temp` numeric(7,3),
  `Core1Temp` numeric(7,3),
  `Core2Temp` numeric(7,3),
  `Core3Temp` numeric(7,3),
  `Core4Temp` numeric(7,3),
  PRIMARY KEY (`Timestamp`),
  UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;
