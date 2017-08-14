
# DEPRECIATED

## Active version https://github.com/judgewooden/multi-sensor/

JPHmonitor
----------

Control and monitor water cooling system using raspi

		- sqlServer		Create database tables
		- webServer		Containing client views
		- collectors		Python scripts to collect sensor data

Basic Architecture:

	 	- Any component can run on a seperate machine
	 	- All data is stored in MYSQL
	 	- All control/monitoring takes place via webServer
		- Each Unit that collect sensor data is stored in unique SQL table
