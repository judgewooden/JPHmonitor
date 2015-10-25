#!/bin/sh
sqlpw=`cat .sqlpassword`

mysql -hlocalhost -uroot -p$sqlpw <sensorTestData.sql

