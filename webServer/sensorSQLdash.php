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
if ($_GET["id"]) {
    $DBid = $_GET["id"];
} else {
    $DBid = 0;
}

$query = "";
$query = $query . "SELECT \"" . $DBid . "\" AS id, Timestamp AS timestamp, " . $DBcolumn . " AS value";
$query = $query . " FROM " . $DBtable . " WHERE " . $DBcolumn . " IS NOT NULL";
$query = $query . " ORDER BY Timestamp DESC LIMIT 1";
// echo "query: " . $query . "<BR />\n";

if ($result = $mysqli->query($query)) {
    $ans=0;
    while ($row = $result->fetch_array(MYSQL_ASSOC)) {
        $ans=$ans+1;
        $outputArray[] = $row;
    }
    if ($ans==0) {
        echo "[{\"id\":\"" . $DBid . "\"}]";
    } else {
        echo json_encode($outputArray);
    }
} else {
    printf("Query Failed: %s\n", $query);
    die;
}

$mysqli->close();

?>

