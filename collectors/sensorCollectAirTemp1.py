#!/usr/bin/python
import os
import time
import datetime
import math
from datetime import datetime
import sys
import mysql.connector
#
# The files need to be installed in directory from
import Adafruit_DHT


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
        print("AirTemp1: Cannot Connect to SQL (retry)")
        time.sleep(5)
        continue
    break

# GLOBAL
add_temp = ("INSERT INTO AirTemperature1 "
               "(Timestamp, Temperature, Humidity) "
               "VALUES (%s, %s, %s)")

while True:
    tnow = datetime.now()
    # 21 is the GPIO number for the pin connect
    import Adafruit_DHT
    humidity, temperature = Adafruit_DHT.read_retry(Adafruit_DHT.AM2302, 21)
    if humidity is None and temperature is  None:
        print ("sensors return None (retry)")
    else:
        print 'Temp={0:0.1f}*  Humidity={1:0.1f}%'.format(temperature, humidity)
        cursor = cnx.cursor()
        lastValue=(tnow, temperature, humidity)
        cursor.execute(add_temp, lastValue)
        cnx.commit()
    time.sleep(5)
