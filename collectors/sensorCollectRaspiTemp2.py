#!/usr/bin/python
import os
import time
import datetime
from datetime import datetime
#import sys
import mysql.connector

# Get config info
try:
    f = open(os.path.expanduser('~/.sqlpassword'))
    sqlpassword=f.read().strip()
    f.close
except:
    print ("Unexpected error: opening .sqlpassword")
    raise

while True:
    try:
        cnx = mysql.connector.connect(user='kastpi', password=sqlpassword,
                              host='sqlserver',
                              database='Sensors')
    except:
        print ("RaspiTemp2: Cannot Connect to SQL (retry)")
        time.sleep(5)
        continue
    break

# GLOBAL
add_temp = ("INSERT INTO RaspiTemp2 "
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
