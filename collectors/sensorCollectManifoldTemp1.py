#!/usr/bin/python
import os
import time
import datetime
from datetime import datetime
#import sys
import mysql.connector
#
# The files need to be installed in directory from
from ABE_ADCPi import ADCPi
from ABE_helpers import ABEHelpers

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
        cnx = mysql.connector.connect(user='root', password=sqlpassword,
                              host='localhost',
                              database='Sensors')
    except:
        print("ManifoldTemp1: Cannot Connect to SQL (retry)")
        time.sleep(5)
        continue
    break

# GLOBAL
add_temp = ("INSERT INTO ManifoldTemp1 "
               "(Timestamp, InFlow, OutFlow1, OutFlow2) "
               "VALUES (%s, %s, %s, %s)")

i2c_helper = ABEHelpers()
bus = i2c_helper.get_smbus()
adc = ADCPi(bus, 0x68, 0x69, 12)

def phobya2temp ( voltage ):
    temp=voltage * 4
    return temp

tOutFlow1=-1
tOutFlow2=-1

while True:
    tnow = datetime.now()
    tInFlow = phobya2temp(adc.read_voltage(2))
    tOutFlow1 = phobya2temp(adc.read_voltage(3))
    tOutFlow2 = phobya2temp(adc.read_voltage(4))
    print ("In-flow:", tInFlow, "Out Flow1:", tOutFlow1, "Out Flow2:", tOutFlow2)

    cursor = cnx.cursor()
    lastValue=(tnow, tInFlow, tOutFlow1, tOutFlow2)
    cursor.execute(add_temp, lastValue)
    cnx.commit()

    time.sleep(5)
