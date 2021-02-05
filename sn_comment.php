<?php
/****** Saving comments of sn ******/

$sid = $_POST["sid"];
$pname = $_POST["pname"];
$sn = $_POST["sn"];
$sComment = $_POST["sComment"];
date_default_timezone_set('Asia/Seoul');
$tNow = date('Y-m-d H:i:s');

Console_log("sid : $sid, pname : $pname, sn : $sn, sComment : $sComment, tNow : $tNow");
//echo ("sid : $sid, pname : $pname, sComment : $sComment, tNow : $tNow");

include('../connect_db.php');
include('../dbConfig_personal_leak.php');

$str3 = "select * from sensor_comm where sid = '$sid' and pname = '$pname' and sn = '$sn'";

if (!($result3 = mysqli_query($conn5, $str3))) {
    echo ("Error description: " . mysqli_error($conn5) . "query:" . $str3);
}
$num = mysqli_num_rows($result3);

if ($num > 0) {
    // update
    $str3 = "update sensor_comm set sid = '$sid' , pname = '$pname', sn = '$sn', comment = '$sComment', date = '$tNow' where sid ='$sid' and pname = '$pname' and sn = '$sn'";
    if (!($result3 = mysqli_query($conn5, $str3))) {
        echo ("Error description: " . mysqli_error($conn5) . "query:" . $str3);
    }
} else {
    // insert
    $str3 = "insert into sensor_comm (sid,pname,sn,comment,date) values ('$sid','$pname','$sn','$sComment','$tNow')";
    if (!($result3 = mysqli_query($conn5, $str3))) {
        echo ("Error description: " . mysqli_error($conn5) . "query:" . $str3);
    }
}

function Console_log($logcontent)
{
	echo "<script>console.log('$logcontent');</script>";
}

function Alert_log($logcontent)
{
	echo "<script>alert('$logcontent');</script>";
}

?>