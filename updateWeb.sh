#!/bin/sh



webWatch()
{
	FILES=$@

	#inotifywait --quiet --monitor --event modify $FILE | while read; do
	inotifywait --quiet --monitor --event modify $FILES | while read line;
	do
		file=${line%% *}
		echo sudo cp $file /usr/share/nginx/www
		sudo cp $file /usr/share/nginx/www
	done

}

webWatch sensorGraph.js sensorGraphStyle.css sensorSQLinitial.php sensorSQLupdate.php sensorTest.html

