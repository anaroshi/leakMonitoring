<?php
// db details

//$dbHost='127.0.0.1';  // Local
//$dbHost ='218.155.80.171'; // Motor
//$dbHost='192.168.0.91'; // 개인개발

$dbHost='218.155.80.145'; // Leak
$dbUsername='scsol';
$dbPassword='scsol92595';
$dbName='mysql';
//echo "[LEAK]";

// Connect and select the database
$conn1 = connect_to_db($dbHost, $dbUsername, $dbPassword, $dbName);
?>