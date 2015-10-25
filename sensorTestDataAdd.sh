#!/bin/sh
sqlpw=`cat .sqlpassword`

while [ 1 ]
do
	mysql -hlocalhost -uroot -p$sqlpw <sensorTestDataAdd.sql
	sleep 3
done


