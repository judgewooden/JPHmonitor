USE Sensors;
DROP TABLE IF EXISTS ManifoldTemp1;

/* The temperature of the Manifold */
CREATE TABLE `ManifoldTemp1` (
	`Timestamp` timestamp NOT NULL,
    `InFlow` numeric(7,3) NOT NULL,
    `OutFlow1` numeric(7,3) NULL,
    `OutFlow2` numeric(7,3) NULL,
	PRIMARY KEY (`Timestamp`),
	UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;

