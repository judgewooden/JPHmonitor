#!/bin/sh



webWatch()
{
	FILES=$@

	echo "Watching: "$FILES
	inotifywait --quiet --monitor --event modify $FILES | while read line;
	do
		echo $line
		file=${line%% *}
		echo " "sudo cp $file /usr/share/nginx/www
		sudo cp $file /usr/share/nginx/www
	done

}

cd webServer
webWatch `ls -1 *html *css *php *js` 

