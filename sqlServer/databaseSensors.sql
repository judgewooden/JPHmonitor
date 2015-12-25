DROP DATABASE IF EXISTS Sensors;
CREATE DATABASE Sensors;

/* TODO Cleanup these tables ( they are for development only) 
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
*/
