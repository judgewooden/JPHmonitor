USE Sensors;
DROP TABLE IF EXISTS ManifoldTemp1;

/* The temperature of the Manifold */
CREATE TABLE `ManifoldTemp1` (
	`Timestamp` timestamp NOT NULL,
    `Before` numeric(7,3) NOT NULL,
    `After1` numeric(7,3) NULL,
    `After2` numeric(7,3) NULL,
	PRIMARY KEY (`Timestamp`),
	UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;

