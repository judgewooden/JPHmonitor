#!/usr/bin/python
import os
import time
import datetime
import requests
import json
from datetime import datetime
from dateutil import tz
#import sys
import mysql.connector

# Loop forever
while True:

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
    add_temp = ("INSERT INTO EthereumMiningAqua "
                   "(Timestamp, hashrate, hashrate_calculated)"
                   "VALUES (%s, %s, %s)")
    url = "http://dwarfpool.com/eth/api?wallet=0x03145c9f20af9272cc87ee62c27b608c3b004f6a&email=douwe.jong@gmail.com"
    sleep_time = 60
    from_zone = tz.gettz("GMT")
    to_zone = tz.tzlocal()

    tlast = datetime.now()
    tlast = tlast.replace(tzinfo=to_zone)

    # Proccessing loop
    while True:

        try:
            response=requests.get(url, timeout=(2.0, 10.0))
            if response.headers["content-type"] != "application/json":
                raise WrongContent(response=response)

            json_data = json.loads(response.text)
            print json.dumps(json_data)

            # strup out the date.....
            thash=json_data["workers"]["AQUA"]["last_submit"]
            # load the date into python
            thash_python=datetime.strptime(thash, "%a, %d %b %Y %H:%M:%S %Z")
            # convert the timezone to local
            thash_python=thash_python.replace(tzinfo=from_zone)
            thash_local=thash_python.astimezone(to_zone)

            # Figure out if we already saved this data
            time_since_last=(thash_local-tlast).total_seconds()
            print ("Time since:", time_since_last)
            #print ("L", tlast, "N", thash_local, "S", thash_python, "R", thash)
            if time_since_last > 0:
                lastValue=(thash_local,
                    json_data["workers"]["AQUA"]["hashrate"],
                    json_data["workers"]["AQUA"]["hashrate_calculated"])
                try:
                    cursor = cnx.cursor()
                    cursor.execute(add_temp, lastValue)
                    cnx.commit()
                except:
                    print "SQL problem"
                    break
            tlast=thash_local
        except KeyError:
            print json_data
            print "Expected JSON data not found"
        except ValueError:
            print response
            print "HTTP error?????" #WrongContent(response=response)
        except requests.exceptions.ConnectionError as e:
            print "server not found."
        except requests.exceptions.ReadTimeout as t:
            print "Waited too long between bytes."
        except:
            print "unknown error"
            raise

        time.sleep(sleep_time)
