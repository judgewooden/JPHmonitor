USE Sensors;
DROP TABLE IF EXISTS RadiatorTemp1;

/* The temperature of the Manifold */
CREATE TABLE `RadiatorTemp1` (
	`Timestamp` timestamp NOT NULL,
    `InFlow` numeric(7,3) NOT NULL,
    `OutFlow` numeric(7,3) NULL,
    PRIMARY KEY (`Timestamp`),
	UNIQUE KEY `Timestamp` (`Timestamp`)
)
ENGINE=MyISAM DEFAULT CHARSET=latin1;

