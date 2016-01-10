#!/usr/bin/env python

import logging
import sys, os
import resource
import time
import datetime
import mysql.connector

#logging.getLogger('openzwave').addHandler(logging.NullHandler())
#logging.basicConfig(level=logging.DEBUG)
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger('openzwave')
import openzwave
from openzwave.node import ZWaveNode
from openzwave.network import ZWaveNetwork
from openzwave.option import ZWaveOption

device="/dev/ttyACM0"
log="Info"

#Define some manager options
options = ZWaveOption(device, config_path=os.path.expanduser("~/zwaveconfig"), \
  user_path=".", cmd_line="")
#options.set_log_file("OZW_Log.log")
#options.set_append_log_file(False)
options.set_console_output(False)
#options.set_save_log_level(log)
#options.set_save_log_level('Info')
#options.set_logging(False)
options.lock()


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
        print ("PowerCollect1: Cannot Connect to SQL (retry)")
        time.sleep(5)
        continue
    break

# GLOBAL
add_temp = ("INSERT INTO Power1 "
               "(Timestamp, Power1, Power2, Energy) "
               "VALUES (%s, %s, %s, %s)")

#Create a network object
network = ZWaveNetwork(options, log=None, autostart=True)

time_started = 0
print "------------------------------------------------------------"
print "Waiting for network awaked : "
print "------------------------------------------------------------"
for i in range(0,300):
    if network.state>=network.STATE_AWAKED:
        print(" done")
        break
    else:
        sys.stdout.write(".")
        sys.stdout.flush()
        time_started += 1
        time.sleep(1.0)
if network.state<network.STATE_AWAKED:
    print "."
    print "Network is not awake but continue anyway"

print "------------------------------------------------------------"
print "Use openzwave library : %s" % network.controller.ozw_library_version
print "Use python library : %s" % network.controller.python_library_version
print "Use ZWave library : %s" % network.controller.library_description
print "Network home id : %s" % network.home_id_str
print "Controller node id : %s" % network.controller.node.node_id
print "Controller node version : %s" % (network.controller.node.version)
print "Nodes in network : %s" % network.nodes_count

mynodeid=-1
for node in network.nodes:
    print "%s - Product name / id / type : %s / %s / %s" % (network.nodes[node].node_id,network.nodes[node].product_name, network.nodes[node].product_id, network.nodes[node].product_type)
    print "%s - Name : %s" % (network.nodes[node].node_id,network.nodes[node].name)
    print "%s - Manufacturer name / id : %s / %s" % (network.nodes[node].node_id,network.nodes[node].manufacturer_name, network.nodes[node].manufacturer_id)
    print "%s - Version : %s" % (network.nodes[node].node_id, network.nodes[node].version)
    #print "%s - Command classes : %s" % (network.nodes[node].node_id,network.nodes[node].command_classes_as_string)
    print "%s - Capabilities : %s" % (network.nodes[node].node_id,network.nodes[node].capabilities)
    if "FGWPE Wall Plug"==network.nodes[node].product_name:
        print "%s - Using this device" % (network.nodes[node].node_id)
        mynodeid=network.nodes[node].node_id
        mynode=ZWaveNode(mynodeid, network)
        mynode.set_field("name", "JPH")
        break

if mynodeid==-1:
    print "Could not find FGWPE Wall Plug device on Zwave network"
    raise

while True:

    mynode.refresh_info()
    myPower1=-1;
    myPower2=-1;
    myEnergy=-1
    tnow=datetime.datetime.now()
    for val in network.nodes[node].get_sensors() :
        #print("node/name/index/instance : %s/%s/%s/%s" % (node,
        #                                                        network.nodes[node].name,
        #                                                        network.nodes[node].values[val].index,
        #                                                        network.nodes[node].values[val].instance))
        #print("%s/%s %s %s" % (network.nodes[node].values[val].label,
        #                                    network.nodes[node].values[val].help,
        #                                    network.nodes[node].get_sensor_value(val),
        #                                    network.nodes[node].values[val].units))
        if network.nodes[node].values[val].index==4:
            myPower1=network.nodes[node].get_sensor_value(val)
        if network.nodes[node].values[val].index==8:
            myPower2=network.nodes[node].get_sensor_value(val)
        if network.nodes[node].values[val].index==0:
            myEnergy=network.nodes[node].get_sensor_value(val)

    if (myPower1==-1 or myPower2==-1 or myEnergy==-1):
        print "Failed to obtain all values"
        raise

    print("Time: %s  Values: power(%0.2f/%0.2f)W energy(%0.2f)kWh" % (tnow, myPower1, myPower2, myEnergy))

    cursor = cnx.cursor()
    lastValue=(tnow, myPower1, myPower2, myEnergy)
    cursor.execute(add_temp, lastValue)
    cnx.commit()

    time.sleep(5)

network.stop()
