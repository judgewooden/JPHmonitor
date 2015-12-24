#!/usr/bin/python
# ' exec(open('c:/localdata/JPHMonitor/sensorCollectSpeedfan.py').read()) '
# ' exec(open('\\\\jphpi\\pihome/JPHMonitor/collectors/sensorCollectSpeedfan.py').read()) '
import os
import time
import datetime
import re
#import sys
import mysql.connector

# Get config info
try:
    #f = open(os.path.expanduser('~/.sqlpassword'))
    #sqlpassword=f.read().strip()
    #f.close
    sqlpassword=""
except:
    print ("Unexpected error: opening .sqlpassword")
    raise

while True:
    try:
        cnx = mysql.connector.connect(user='root', password=sqlpassword,
                              host='192.168.8.4', port=3306,
                              database='Sensors')
    except:
        print ("WindowsCollectSpeedfan: Cannot Connect to SQL (retry)")
        time.sleep(5)
        continue
    break

# Get last value loaded from SQL
queryLastTimestamp = ("SELECT Timestamp FROM Fan1 ORDER BY Timestamp DESC LIMIT 1")
#queryLastTimestamp = ("SELECT Timestamp FROM RaspiTemp1 ORDER BY Timestamp DESC LIMIT 1")
lastSecond=0
lastYear=0
lastMonth=0
lastDay=0

# SQL - Speedfan mapping
sqlMap ={}
sqlMap["Seconds"] = "Timestamp"
sqlMap["System"] = "SystemTemp"
sqlMap["CPU"] = "CPUTemp"
sqlMap["SB"] = "SBTemp"
sqlMap["NB"] = "NBTemp"
sqlMap["OPT_FAN1"] = "OPT_FAN_1"
sqlMap["GPU 0"] = "GPU1Temp"
sqlMap["GPU 1"] = "GPU2Temp"
sqlMap["GPU 2"] = "GPU3Temp"
sqlMap["GPU 3"] = "GPU4Temp"
sqlMap["Core 0"] = "Core1Temp"
sqlMap["Core 1"] = "Core2Temp"
sqlMap["Core 2"] = "Core3Temp"
sqlMap["Core 3"] = "Core4Temp"
print("sqlMap:",sqlMap)

# while true: (forever loop)
cursor = cnx.cursor()
cursor.execute(queryLastTimestamp)
for (lastTimestamp) in cursor:
    print ("LastTimestamp: ", lastTimestamp)
    lastYear = lastTimestamp[0].year
    lastMonth = lastTimestamp[0].month
    lastDay = lastTimestamp[0].day
    nowTemp = datetime.datetime.now()
    midnightTemp = nowTemp.replace(hour=0, minute=0, second=0, microsecond=0)
    lastSecond = (lastDateTime - midnightTemp).seconds
cursor.close()

speedfanPath="C:\Program Files (x86)\SpeedFan"
filesList = [f for f in os.listdir(speedfanPath) if re.match(r'SFLog+.*\.csv$', f)]
filesList.sort(key=lambda f: os.path.getmtime(os.path.join(speedfanPath, f)))
print("Order:", filesList)

#### TODO IGNORE FILES THAT ARE SMALLER THAN MY CURRENT DATE
sqlList=[]
for file in filesList:
    print ("Last Y:", lastYear, " M:", lastMonth, " D:", lastDay, " S:", lastSecond)
    fileYear=int(file[5:9])
    fileMonth=int(file[9:11])
    fileDay=int(file[11:13])
    if ( fileDay==lastDay and fileMonth==lastMonth and fileDay==lastDay ):
        filterSeconds=1
    else:
        filterSeconds=0
    print ("File:", file, " Y:", fileYear, " M:", fileMonth, " D:", fileDay, "Process Seconds:", filterSeconds)
    if ( fileYear < lastYear ):
        print ("...skipping old year")
        continue
    if ( fileMonth < lastMonth ):
        print ("...skipping old month")
        continue
    if ( fileDay < lastDay ):
        print ("...skipping old day")
        continue
    fullfile = speedfanPath + "\\" + file
    print ("...processing")
    with open(fullfile) as f:
        frow = 0
        sqlList.clear()
        for line in f:
            # print(line)
            columns=line.strip().split('\t')
            if (frow==0):
                frow=1
                if (columns[0] != "Seconds"):
                    print("First column in data should be \'Seconds\' abort: ", fullfile)
                    break
                gpuCount=0
                for x in columns:
                    # Fix column names for GPU (sequence) --- problem with speedfan
                    if (x=="GPU"):
                        x="GPU " + str(gpuCount)
                        gpuCount = gpuCount + 1
                    try:
                        sqlList.append(sqlMap[x])
                    except:
                        sqlList.append("")
                # print("sqlList:", sqlList)
            else:
                frow=frow+1
                if (frow>2):
                    continue
                print ("  Time: ", columns[0])
                if (filterSeconds==1):
                    if (int(columns[0])<lastSeconds):
                        print ("  ...skipping old second")
                        continue
                print ("  ...processing")
                if (filterSeconds==1):
                    lastSecond=columns[0]
    print ("Current Y:", lastYear, " M:", lastMonth, " D:", lastDay, " S:", lastSecond)
    if (not(lastYear==fileYear and lastMonth==fileMonth and lastDay==fileDay)):
        lastSecond=0
    lastYear=fileYear
    lastMonth=fileMonth
    lastDay=fileDay






    #time.sleep(15)

