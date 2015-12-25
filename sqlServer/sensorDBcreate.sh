#!/bin/sh

echo "This will override your database (enter to continue)"
read n
echo "Are you sure? (\"YES\" to continue)"
read n
if [ "$n" = "YES" ]; then
	sqlpw=`cat ~/.sqlpassword` 
    mysql -hlocalhost -uroot -p$sqlpw < databaseSensors.sql
	for table in `ls -1 table*sql`
	do
    	mysql -hlocalhost -uroot -p$sqlpw < $table
	done
	for procedure in `ls -1 procedure*sql`
	do
    	mysql -hlocalhost -uroot -p$sqlpw < $procedure
	done
else
	echo "abort"
	exit
fi
