<?php
// db details

// $dbHost = '218.155.80.188:8096'; //개인개발(외부)

$dbHost ='192.168.0.91'; //개인개발(내부)
$dbUsername = 'scsol';
$dbPassword = 'scsol92595';
$dbName = 'motor';
// echo ("[PERSONAL]");

// Connect and select the database
$conn3 = connect_to_db($dbHost, $dbUsername, $dbPassword, $dbName);
