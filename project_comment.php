<?php
/****** Saving comments of projects ******/

$sid = $_POST["sid"];
$pname = $_POST["pname"];
$tComment = $_POST["tComment"];
date_default_timezone_set('Asia/Seoul');
$tNow = date('Y-m-d H:i:s');

//echo ("sid : $sid, pname : $pname, tComment : $tComment, tNow : $tNow");

include('connect_db.php');
include('dbConfig_personal.php');

$str3 = "select * from project_comm where sid = '$sid' and pname = '$pname'";

if (!($result3 = mysqli_query($conn3, $str3))) {
    echo ("Error description: " . mysqli_error($conn3) . "query:" . $str3);
}
$num = mysqli_num_rows($result3);

if ($num > 0) {
    // update
    $str3 = "update project_comm set sid = '$sid' , pname = '$pname', comment = '$tComment', date = '$tNow' where sid ='$sid' and pname = '$pname'";
    if (!($result3 = mysqli_query($conn3, $str3))) {
        echo ("Error description: " . mysqli_error($conn3) . "query:" . $str3);
    }
} else {
    // insert
    $str3 = "insert into project_comm (sid,pname,comment,date) values ('$sid','$pname','$tComment','$tNow')";
    if (!($result3 = mysqli_query($conn3, $str3))) {
        echo ("Error description: " . mysqli_error($conn3) . "query:" . $str3);
    }
}
?>