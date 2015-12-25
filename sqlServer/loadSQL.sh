#!/bin/sh

if [ "$1" = "" ]; then
	echo "usage $0 <filename>"
	exit 1
fi

sqlpw=`cat ~/.sqlpassword`
mysql -hlocalhost -uroot -p$sqlpw < $1
