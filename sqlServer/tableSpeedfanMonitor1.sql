USE Sensors;
DROP TABLE IF EXISTS SpeedfanMonitor1;

/* Data collected from SpeedFan from a computer */
CREATE TABLE `SpeedfanMonitor1` (
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
