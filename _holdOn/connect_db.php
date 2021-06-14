<?php

function connect_to_db($host, $user, $pass, $db)
{
    $conn = new mysqli($host, $user, $pass, $db);

    /* check connection */
    if ($conn->connect_errno) {
        printf("Connect failed: %s\n", $conn->connect_error);
        exit();
    }

    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }

    // if (!($con = @mysqli_connect($host, $user, $pass, $db))) {
    //     echo "ERROR mysqli : Connect to Server\n" . $ex->getMessage();
    //     exit;
    // }

    // if (!mysqli_select_db($db, $con)) {
    //     echo "ERROR mysqli : Connect to DataBase\n";
    //     exit;
    // }

    // if (!mysqli_query("SET NAMES 'utf8'", $con)) {
    //     echo "ERROR mysqli : SET NAMES 'utf8'\n";
    //     exit;
    // }

    // if (!mysqli_query("SET CHARACTER SET 'utf8'", $con)) {
    //     echo "ERROR mysqli : SET CHARACTER SET 'utf8'\n";
    //     exit;
    // }

    // if (!mysqli_query("SET CHARACTER_SET_CLIENT = 'utf8'", $con)) {
    //     echo "ERROR mysqli : SET character_set_client = 'utf8'\n";
    //     exit;
    // }

    // if (!mysqli_query("SET character_set_results = 'utf8'", $con)) {
    //     echo "ERROR mysqli : SET character_set_results = 'utf8'\n";
    //     exit;
    // }

    // if (!mysqli_query("SET character_set_connection = 'utf8'", $con)) {
    //     echo "ERROR mysqli : SET character_set_connection = 'utf8'\n";
    //     exit;
    // }

    // mb_internal_encoding("UTF-8");
    // mb_regex_encoding("UTF-8");
    return $conn;
}
