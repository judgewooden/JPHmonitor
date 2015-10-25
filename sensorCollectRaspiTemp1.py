#!/usr/bin/python
import os
import time
import datetime
from datetime import datetime
#import sys
import mysql.connector

# Get config info
try:
#   TODO clean this up and DO it properly
    f = open('/home/pi/source/JPHmonitor/.sqlpassword')
    sqlpassword=f.read().strip()
    f.close
except:
    print ("Unexpected error: opening .sqlpassword")
    raise

try:
	cnx = mysql.connector.connect(user='root', password=sqlpassword,
                              host='localhost',
                              database='Sensors')
except:
	print ("Cannot Connect to SQL")
	raise

# GLOBAL
add_temp = ("INSERT INTO RaspiTemp1 "
               "(Timestamp, Value) "
               "VALUES (%s, %s)")

while True:
	tnow = datetime.now()
	f = os.popen('/bin/cat /sys/class/thermal/thermal_zone0/temp')
	piTemp=float(f.read())
	piTemp=piTemp/1000
	print ("Temp: %f" % (piTemp))
	
	cursor = cnx.cursor()
	lastValue=(tnow,piTemp)
	cursor.execute(add_temp, lastValue)
	cnx.commit()

	time.sleep(5)