#!/usr/bin/python
import os
import time
import datetime
import requests
import json
from datetime import datetime
#import sys
import mysql.connector

# Get config info
try:
    f = open(os.path.expanduser('~/.sqlpassword'))
    sqlpassword=f.read().strip()
    f.close
except:
    print ("ArduinoMonitor1: Unexpected error opening .sqlpassword")
    raise

while True:
    try:
        cnx = mysql.connector.connect(user='root', password=sqlpassword,
                              host='localhost',
                              database='Sensors')
    except:
        print ("ArduinoMonitor1: Cannot Connect to SQL (retry)")
        time.sleep(5)
        continue
    break

# GLOBAL
add_temp = ("INSERT INTO ArduinoMonitor1 "
               "(Timestamp, Power, FlowPerSecond, LitersPerMinute, OverrideTime, CurrentTime)"
               "VALUES (%s, %s, %s, %s, %s, %s)")
url = "http://192.168.8.11/X"

while True:
    tnow = datetime.now()

    try:
        response=requests.get(url, timeout=(2.0, 10.0))
        if response.headers["content-type"] != "application/json":
            raise WrongContent(response=response)
        try:
            json_data = json.loads(response.text)
            print json.dumps(json_data)
            lastValue=(tnow,
                json_data["Power"],
                json_data["FlowPerSecond"],
                json_data["LitersPerSecond"],
                json_data["OverrideTime"],
                json_data["CurrentTime"])

            cursor = cnx.cursor()
            cursor.execute(add_temp, lastValue)
            cnx.commit()
        except ValueError:
            print response
            print "HTTP error?????" #WrongContent(response=response)
    except requests.exceptions.ConnectionError as e:
        print "server not found."
    except requests.exceptions.ReadTimeout as t:
        print "Waited too long between bytes."

    time.sleep(5)
