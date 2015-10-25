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
CREATE TABLE `Fan1` (
  `Timestamp` timestamp NOT NULL,
  `Value` integer NOT NULL,
  PRIMARY KEY (`Timestamp`),	      
  UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;
