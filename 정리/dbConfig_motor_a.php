<?php
// db details

//$dbHost='ithingsware.com';
//$dbHost='218.155.80.145'; // Leak
//$dbHost='192.168.0.91'; // 개인개발
// $dbHost ='192.168.0.18'; // 개인개발
// $dbHost ='127.0.0.1'; // Local
//$dbUsername='root';
// $dbHost ='218.155.80.171'; // Motor

$dbHost ='192.168.0.24'; // Motor A 전류
$dbUsername='scsol';
$dbPassword='scsol92595';
$dbName='mysql';
//echo("[MOTOR]");

// Connect and select the database
$conn2 = connect_to_db($dbHost, $dbUsername, $dbPassword, $dbName);
?>