#!/bin/sh

echo "This will override your database"
echo "(enter to continue)"
read n

sqlpw=`cat .sqlpassword`

# TODO : make conversion script once you have PROD data

mysql -hlocalhost -uroot -p$sqlpw < sensorDBcreate.sql
