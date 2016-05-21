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

sys.path.append("/home/jphmonitor")
sys.path.append('/home/jphmonitor/ABElectronics_Python_Libraries/ADCPi')
try:
    from ABE_ADCPi import ADCPi
    from ABE_helpers import ABEHelpers
except ImportError:
    print ("in sensors, importing ABE_ADCPi failed")
    raise

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
        print("RadiatorTemp1: Cannot Connect to SQL (retry)")
        time.sleep(5)
        continue
    break

# GLOBAL
add_temp = ("INSERT INTO RadiatorTemp1 "
               "(Timestamp, InFlow, OutFlow) "
               "VALUES (%s, %s, %s)")

i2c_helper = ABEHelpers()
bus = i2c_helper.get_smbus()
adc = ADCPi(bus, 0x68, 0x69, 12)

def phobya2temp ( voltageOut ):
    ohm=(5-voltageOut)/voltageOut*16800/1000
    temp=(0.0755*math.pow(ohm,2))-4.2327*ohm+60.589
    return temp


while True:
    tnow = datetime.now()
    tInFlowBefore = adc.read_voltage(2)
    tInFlow=phobya2temp(tInFlowBefore)
    tOutFlowBefore = adc.read_voltage(1)
    tOutFlow=phobya2temp(tOutFlowBefore)

    print ("In-flow", tInFlowBefore, "->", tInFlow, \
           "Out-flow-1", tOutFlowBefore, "->", tOutFlow )

    cursor = cnx.cursor()
    lastValue=(tnow, tInFlow, tOutFlow)
    cursor.execute(add_temp, lastValue)
    cnx.commit()

    time.sleep(5)
