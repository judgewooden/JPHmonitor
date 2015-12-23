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
if ($_GET["source"]) {
    $DBtable= $_GET["source"];
} else {
    printf("Param error. -source- missing\n");
    die;
}

// Get the value column
if ($_GET["column"]) {
    $DBcolumn = $_GET["column"];
} else {
    printf("Param error. -column- missing\n");
    die;
}

// Get the value column
if ($_GET["UpdateGapSeconds"]) {
    $DBseconds = $_GET["UpdateGapSeconds"];
} else {
    printf("Param error. -UpdateGapSeconds- missing\n");
    die;
}

$query = "";
$query = $query . "SELECT Timestamp AS timestamp, " . $DBcolumn . " AS value";
$query = $query . " FROM " . $DBtable . " WHERE " . $DBcolumn . " IS NOT NULL";
$query = $query . " AND timestamp > DATE_SUB(NOW(), INTERVAL " . $DBseconds . " SECOND)";
$query = $query . " ORDER BY Timestamp DESC LIMIT 1";
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

