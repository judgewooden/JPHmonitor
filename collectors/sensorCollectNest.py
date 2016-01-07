#!/usr/bin/python
import os
import time
import datetime
import nest
from datetime import datetime
import mysql.connector

# Get SQL config info
try:
    f = open(os.path.expanduser('~/.sqlpassword'))
    sqlpassword=f.read().strip()
    f.close
except:
    print "Unexpected error: opening .sqlpassword"
    raise

# Get NEST config info
try:
    #with open(os.path.expanduser('~/.nestpassword')) as f:
    lines=open(os.path.expanduser('~/.nestpassword')).read().splitlines()
    nestuser = lines[0]
    nestpassword = lines[1]
    print "Nest Login: %s" % nestuser
    #print "Nest password: %s" % nestpassword
except:
    print "Unexpected error: opening .nestpassword"
    raise

while True:
    try:
        cnx = mysql.connector.connect(user='root', password=sqlpassword,
                              host='localhost',
                              database='Sensors')
    except:
        print "RaspiTemp1: Cannot Connect to SQL (retry)"
        time.sleep(5)
        continue
    break

# GLOBAL
add_value = ("INSERT INTO Nest "
               "(Timestamp, Away, Temperature, Humidity ) "
               "VALUES (%s, %s, %s, %s)")


while True:
    try:
        napi = nest.Nest(nestuser, nestpassword)
    except:
        print "Nest: Cannot Connect to Nest"
        time.sleep(60)
        continue

    while True:
        tnow = datetime.now()
        nestAway=True;
        nestTemp=-80;
        nestHumidity=-1;
        for structure in napi.structures:
            for device in structure.devices:
                nestAway=structure.away
                nestTemp=float(device.temperature)
                nestHumidity=float(device.humidity)
        print "Away: %s, Temp: %f, Humidity: %f" % (str(nestAway), nestTemp, nestHumidity)
        if (nestTemp!=-80):
          	cursor = cnx.cursor()
          	lastValue=(tnow, nestAway, nestTemp, nestHumidity)
          	cursor.execute(add_value, lastValue)
          	cnx.commit()
        else:
            print "Unknown error connecting to Nest"
            break
        time.sleep(60)
    time.sleep(30)
