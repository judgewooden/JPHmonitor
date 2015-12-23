
JPHmonitor
----------

Control and monitor water cooling system using raspi

		- sqlServer			Creates database tables
		- webServer			Containing client views
		- collectors		Python scripts to collect sensor data

Basic Architecture:

	 	- Any component can run on a seperate machine
	 	- All data is stored in MYSQL
	 	- All control takes place vi webServer
		- Each Unit that collects sensor data is stored in unique SQL table
