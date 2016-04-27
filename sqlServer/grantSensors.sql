/*
 * Edit this file to give your collector units the correct access
 *
 * update passwords and localip address that are valid for login
 */
DELETE from mysql.user WHERE user="webuser";
FLUSH PRIVILEGES;
GRANT SELECT ON Sensors.* To 'webuser'@'localhost' IDENTIFIED BY 'xxxxxxxxxx';
FLUSH PRIVILEGES;

DELETE from mysql.user WHERE user="SpeedfanMonitor1";
FLUSH PRIVILEGES;
GRANT SELECT,INSERT ON Sensors.SpeedfanMonitor1 To 'SpeedfanMonitor1'@'192.168.8.10' IDENTIFIED BY 'xxxxxxxxxx';
FLUSH PRIVILEGES;
GRANT SELECT,INSERT ON Sensors.SpeedfanMonitor1 To 'SpeedfanMonitor1'@'192.168.0.100' IDENTIFIED BY 'xxxxxxxxxx';
FLUSH PRIVILEGES;

DELETE from mysql.user WHERE user="kastpi";
FLUSH PRIVILEGES;
GRANT SELECT,INSERT ON Sensors.KastTemperature1 To 'kastpi'@'192.168.0.100' IDENTIFIED BY 'xxxxxxxxxx';
FLUSH PRIVILEGES;
GRANT SELECT,INSERT ON Sensors.AirTemperature1 To 'kastpi'@'192.168.0.100' IDENTIFIED BY 'xxxxxxxxxx';
FLUSH PRIVILEGES;

/* Cleanup historical rubish */
DELETE from mysql.user WHERE user="Sensors_admin";
DELETE from mysql.user WHERE user="aqua_admin";
FLUSH PRIVILEGES;
