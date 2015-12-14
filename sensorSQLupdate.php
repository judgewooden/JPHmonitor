<?php

require '/usr/share/nginx/sensorsDBconfig.php';

$outputArray = array();
$mysqli = new mysqli($dbhostname, $dbuser, $dbpassword, $dbname);

if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

// TODO: Make this safe against SQL injections
//       And remove any warning messages in the PHP log

// Get the value table
if ($_GET["query"]) {
	$data = $_GET["query"];
} else {
    printf("Param error. -query- missing\n");
    die;
}

//echo "Origin:$data<br />\n";
$myArray = array();
$myArray = json_decode( $data );
// echo Print_r($myArray) . "<br />\n";

// Build a query a return the results as JSON
$query = "";
foreach ( $myArray as $row ) {
	if ( $row->key > "0" ) {
		$query = $query . " UNION "	;
	}
	$query = $query . "SELECT \"" . $row->key . "\",";
	$query = $query . " Timestamp AS timestamp, " . $row->column . " AS value";
	$query = $query . " FROM " . $row->table . " WHERE " . $row->column . " IS NOT NULL";
	$query = $query . " AND CONVERT_TZ(Timestamp, \"SYSTEM\", \"UTC\") >";
	$query = $query . " \"" . $row->time . "\"";
}
$query = $query . " ORDER BY Timestamp";
// echo "query: " . $query . "<BR />\n";

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

