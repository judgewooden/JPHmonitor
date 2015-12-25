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
# queryLastTimestamp = ("SELECT Timestamp FROM Fan1 ORDER BY Timestamp DESC LIMIT 1")
queryLastTimestamp = ("SELECT Timestamp FROM RaspiTemp1 ORDER BY Timestamp DESC LIMIT 1")
epoch = datetime.datetime.utcfromtimestamp(0)
lastDate = epoch
fileDate = epoch

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

# while true: (forever loop)

# get last value from SQL
cursor = cnx.cursor()
cursor.execute(queryLastTimestamp)
for (lastTimestamp) in cursor:
    print ("LastTimestamp: ", lastTimestamp)
    lastDate = lastTimestamp[0]
cursor.close()

# get last list of file
speedfanPath="C:\Program Files (x86)\SpeedFan"
filesList = [f for f in os.listdir(speedfanPath) if re.match(r'SFLog+.*\.csv$', f)]
filesList.sort(key=lambda f: os.path.getmtime(os.path.join(speedfanPath, f)))
print("Order:", filesList)

#### TODO IGNORE FILES THAT ARE SMALLER THAN MY CURRENT DATE
sqlList=[]
for file in filesList:
    fileDate=datetime.datetime(int(file[5:9]),int(file[9:11]),int(file[11:13]),0,0,0)
    print ("File:", file, "Date:", fileDate.date(), "SQLdate:", lastDate, end="" )

    if (fileDate.date() < lastDate.date()):
        print (" ...skipping (already processed)")
        continue
    print (" ...processing")

    fullfile = speedfanPath + "\\" + file
    while True:

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
                        forever=False
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
                    print("sqlList:", sqlList)
                else:
                    frow=frow+1
                    # if (frow>2):
                    #     continue
                    # print ("Raw: ", columns)
                    seconds=int(columns[0])
                    x=datetime.datetime
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
                    # print ("Data DT:", fileDate, "SQL:", lastDate, end="")
                    if(fileDate<=lastDate):
                        #print (" skip")
                        continue
                    print (" add")

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
                    print ("query:", query)
                    cursor = cnx.cursor()
                    try:
                        cursor.execute(query)
                    except:
                        print ("error")
                        raise
                    cnx.commit()

                    lastDate=fileDate

        if ( file != filesList[-1]):
            print ("Process more Files to process")
            break
        if (lastDate<(datetime.datetime.now() - datetime.timedelta(seconds=10))):
            print ("No more updates (look for new files)")
            break
        print("Sleep...")
        time.sleep(5)

# write routine here to increase sleep if there is no update
print("Re-look for files, sleep(30)")
time.sleep(30)
