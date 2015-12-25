/*
 * Edit this file to give your collector units the correct access
 *
 * update passwords to correspond with your install
 */
DELETE from mysql.user WHERE user="webuser";
FLUSH PRIVILEGES;
GRANT SELECT ON Sensors.* To 'webuser'@'localhost' IDENTIFIED BY 'xxxxxxxxxx';
FLUSH PRIVILEGES;
DELETE from mysql.user WHERE user="SpeedfanMonitor1";
FLUSH PRIVILEGES;
GRANT SELECT,INSERT ON Sensors.* To 'SpeedfanMonitor1'@'192.168.8.10' IDENTIFIED BY 'xxxxxxxxxx';
FLUSH PRIVILEGES;
GRANT SELECT,INSERT ON Sensors.* To 'SpeedfanMonitor1'@'192.168.0.101' IDENTIFIED BY 'xxxxxxxxxx';
FLUSH PRIVILEGES;

/* Cleanup historical rubish */
DELETE from mysql.user WHERE user="Sensors_admin";
DELETE from mysql.user WHERE user="aqua_admin";
FLUSH PRIVILEGES;
