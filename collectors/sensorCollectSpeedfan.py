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
    sqlpassword="xxxxxxxxxx"
except:
    print ("Unexpected error: opening .sqlpassword")
    raise

while True:
    try:
        cnx = mysql.connector.connect(user='SpeedfanMonitor1', password=sqlpassword,
                              host='192.168.0.9', port=3306,
                              database='Sensors')
    except:
        print ("SpeedfanMonitor1: Cannot Connect to SQL (retry)")
        time.sleep(5)
        raise
    break

# Get last value loaded from SQL
queryLastTimestamp = ("SELECT Timestamp FROM SpeedfanMonitor1 ORDER BY Timestamp DESC LIMIT 1")
#queryLastTimestamp = ("SELECT Timestamp FROM RaspiTemp1 ORDER BY Timestamp DESC LIMIT 1")
lastDate = datetime.datetime.utcfromtimestamp(0)
fileDate = datetime.datetime.utcfromtimestamp(0)

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
print("sqlMap:", sqlMap)

while True:

    # get last value from SQL
    cursor = cnx.cursor()
    cursor.execute(queryLastTimestamp)
    for (lastTimestamp) in cursor:
        print ("last SQL value at: ", lastTimestamp)
        lastDate = lastTimestamp[0]
    cursor.close()

    # get last list of file
    speedfanPath="C:\Program Files (x86)\SpeedFan"
    filesList = [f for f in os.listdir(speedfanPath) if re.match(r'SFLog+.*\.csv$', f)]
    filesList.sort(key=lambda f: os.path.getmtime(os.path.join(speedfanPath, f)))
    print("Files to process:", filesList)

    sqlList=[]
    for file in filesList:
        fileDate=datetime.datetime(int(file[5:9]),int(file[9:11]),int(file[11:13]),0,0,0)
        print (file, "D:", fileDate.date(), "SQL:", lastDate, end="" )

        if (fileDate.date() < lastDate.date()):
            print (" .skip")
            continue
        print (" .process")

        fullfile = speedfanPath + "\\" + file
        while True:

            with open(fullfile) as f:
                frow = True
                GPUis=-1
                sqlList.clear()
                print("Line:", line)
                for line in f:
                    columns=line.strip().split('\t')
                    if (frow):
                        frow=False
                        if (columns[0] != "Seconds"):
                            print("First column in data should be \'Seconds\' abort: ", fullfile)
                            raise
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
                        #fix a problem where GPU numbers are set to -999 when card resets
                        for x in range(0, len(columns)):
                            if columns[x]=="GPU":
                                GPUis=x
                                break
                    else:

                        seconds=int(columns[0])
                        hms=""
                        for scale in 86400, 3600, 60:
                            result, seconds = divmod(seconds, scale)
                            if hms != '' or result > 0:
                                hms += '{0:02d}:'.format(result)
                        hms += '{0:02d}'.format(seconds)
                        if (len(hms)<3):
                            hms = '0:0:' + hms
                        if (len(hms)<6):
                            hms = '0:' + hms

                        fileDate=datetime.datetime.combine(fileDate.date(), datetime.time(*map(int, hms.split(':'))))
                        if(fileDate<=lastDate):
                            continue

                        #fix a problem where GPU temps are set to -999 when card resets
                        if (GPUis>0):
                            if (float(columns[GPUis])<-99.0):
                                print("GPU at",GPUis, "has value:", columns[GPUis], "skipping")
                                continue

                        query = "INSERT INTO SpeedfanMonitor1 ("
                        query +=', '.join(str(x) for x in sqlList if x!='')
                        query += ") VALUES (\'"
                        query +=fileDate.strftime('%Y-%m-%d %H:%M:%S')
                        query +="\'"
                        for x in range(1,int(len(sqlList))):
                            if (sqlList[x]!=''):
                                query += ", "
                                query += columns[x].replace(",", ".")
                        query += ")"
                        print (query)
                        cursor = cnx.cursor()
                        try:
                            cursor.execute(query)
                        except:
                            raise
                        cnx.commit()
                        lastDate=fileDate
            if ( file != filesList[-1]):
                break
            if (lastDate<(datetime.datetime.now() - datetime.timedelta(seconds=10))):
                print ("No more updates (look for new files)")
                break
            time.sleep(5)
    # TODO write routine here to increase sleep if there is no update
    print("Re-start after sleep(30)")
    time.sleep(30)
