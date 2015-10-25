
JPHmonitor
----------

Control and monitor water cooling using raspi

		- mysql				Containing tables with all the monitor data
		- webserver			Containing client views for access the SQL data
		- python collect	Collect data based on http (store data in SQL)
		- python sensor		Collect local sensor data (store data in SQL)
		- python control	Modify a control component (store data in SQL)

The user interface is based on D3 and various displays can be created using paramaters.

Basic Architecture:

	 	- Each components can run on a seperate raspi
	 	- All data is stored in Sybase
	 	- All control data is available on the http server
	 	- All displays are available on the http server
	 	- All python is installed on /usr/local/JPHmonitor
		- Each Sensor has its own Table (to avoid locking) 