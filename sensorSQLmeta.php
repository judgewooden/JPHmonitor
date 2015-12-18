<?php

require '/usr/share/nginx/sensorsDBconfig.php';

$outputArray = array();
$mysqli = new mysqli($dbhostname, $dbuser, $dbpassword, $dbname);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

// TODO: Make this safe against SQL injections
//       And remove and warning messages in the PHP log


// Build a query a return the results as JSON
$query = "select TABLE_NAME, COLUMN_NAME from information_schema.columns WHERE TABLE_SCHEMA = \"Sensors\" ORDER BY TABLE_NAME ASC";

if ($result = $mysqli->query($query)) {
    while ($row = $result->fetch_array(MYSQL_ASSOC)) {
		$outputArray[] = $row;
    }
	echo json_encode($outputArray);
} else {
	printf("Query Failed: %s\n", $query);
	die;
}

$mysqli->close();

?>

