<?php
/**
 * 누수 모니터링 리포트 정보 저장
 * table : leak_report
 */

$sid            = $_POST["sid"] ?? "";              // 1. sid
$pname          = $_POST["pname"] ?? "";            // 2. pname
$sn             = $_POST["sn"] ?? "";               // 3. 센서번호
$leakStatus     = $_POST["leakStatus"] ?? "";       // 4. 누수여부
$sensorStatus   = $_POST["sensorStatus"] ?? "";     // 5. 센서상태
$pipeInfo       = $_POST["pipeInfo"] ?? "";         // 6. 관정보
$comm           = $_POST["comm"] ?? "";             // 7. 특이사항
$fver           = $_POST["fver"] ?? "";             // 8. FIRMWARE Version
$rssi           = $_POST["rssi"] ?? "";             // 9. RSSI

// echo ("sid:".$sid.", pname:".$pname."sn:".$sn.", leakStatus:".$leakStatus."sid:".$sid.", sensorStatus:".$sensorStatus);
// echo ("pipeInfo:".$pipeInfo.", comm:".$comm.", fver:".$fver.", rssi:".$rssi );

include($_SERVER['DOCUMENT_ROOT']."/connect_db.php");
include($_SERVER['DOCUMENT_ROOT']."/dbConfig_personal_leak.php");

if ($sid !="" && $pname != "" && $sn != "") {
  $sql   = "INSERT INTO leak_report( ";
  $sql  .= "sid, pname, sn, leakStatus, sensorStatus, pipeInfo, fver, rssi, comm, inDate) ";
  $sql  .= "VALUES ('$sid','$pname','$sn','$leakStatus','$sensorStatus','$pipeInfo','$fver','$rssi','$comm',now()) ";
  $sql  .= "ON DUPLICATE KEY UPDATE ";
  $sql  .= "sid = '$sid', pname = '$pname', sn = '$sn', leakStatus = '$leakStatus', ";
  $sql  .= "sensorStatus = '$sensorStatus', pipeInfo = '$pipeInfo', fver = '$fver', rssi = '$rssi', ";
  $sql  .= "comm = '$comm', inDate = now()";
  // echo $sql;

  if (!($result = mysqli_query($conn5, $sql))) {
    echo ("Error description: ".mysqli_error($conn5)." query:".$sql."<br>");
  }

  $data = $sid."_".$pname."_".$sn." is saved.";

} else {
  $data = $sid."_".$pname."_".$sn." is failed to save about info.";
}

//echo $data;
echo "";

?>