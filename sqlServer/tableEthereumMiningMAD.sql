USE Sensors;
DROP TABLE IF EXISTS EthereumMiningMAD;

/* The flow details from the Arduino */
CREATE TABLE `EthereumMiningMAD` (
	`Timestamp` timestamp NOT NULL,
	`hashrate` numeric(7,3),
	`hashrate_calculated` numeric(7,3),
PRIMARY KEY (`Timestamp`),
UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;


