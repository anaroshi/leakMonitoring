<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// date_default_timezone_set("GMT+0");
//date_default_timezone_set("Asia/Seoul");

$sid          = trim($_GET['sid']);
$pname        = trim($_GET['pname']);
$sn           = trim($_GET['sn']);

$sensorInfo ="<div>[ SID : $sid ]</div><div>[ PROJECT : $pname ] </div><div>[ SN : $sn ]</div>";

// $pname     = 'producttest';
// $sn        = 'SWFLB-20210408-0106-0459';
$dbnm_report  = 'sensor_report_'.$sid.'_'.$sn;
$dbnm_send    = 'leak_send_data_'.$sid.'_'.$sn;
$path         = "public_html/leak_data/'.$sid.'/'.$pname.'/'.$sn.'/";   // 서버파일위치

include($_SERVER['DOCUMENT_ROOT'] . "/connect_db.php");
include($_SERVER['DOCUMENT_ROOT'] . "/dbConfig_leak.php");

$sensorList = "";

$sumTimediffValeu = 0; // 총합구하기
$accessTimes = 0;

// 통신횟수
$sql = "SELECT count(cid) accessTimes FROM `".$dbnm_report."` ";

if (!($result = mysqli_query($conn1, $sql))) {
  echo ("Error description: " . mysqli_error($conn1) . "query:" . $sql);
}
$row = mysqli_fetch_assoc($result);
$accessTimes = $row['accessTimes'];

// Complete 횟수
$sql = "SELECT count(complete_time) completeTimes FROM `".$dbnm_send."` WHERE complete=1 ";

if (!($result = mysqli_query($conn1, $sql))) {
  echo ("Error description: " . mysqli_error($conn1) . "query:" . $sql);
}
$row = mysqli_fetch_assoc($result);
$completeTimes = $row['completeTimes'];
$incompleteTimes =  $accessTimes - $completeTimes;

$total = "통신 횟수 : ".number_format($accessTimes)."회  / Complete 횟수 : ".number_format($completeTimes)."회  / Incomplete 횟수 : ".$incompleteTimes."회  / 시간차 합계 : ".$sumTimediffValeu;

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">

    <title>센서 레포트 시간 추출</title>
    <script src="../js/sensorRpInterval.js" defer></script>
    <link rel="stylesheet" href="../css/sensorRpInterval.css">
  </head>
  <body>    

    <div class="container">
      <nav>
        <div class="sensor_sub_title1">
          <?php echo $sensorInfo ?>
        </div>
        <div class="sensor_srh sensor_all_btn">
          <div class="sensor_h srh_sid"><input type="hidden" class="sensor_sid" value=<?php echo($sid)?>></div>
          <div class="sensor_h srh_pname"><input type="hidden" class="sensor_pname" value=<?php echo($pname)?>></div>
          <div class="sensor_h srh_sn"><input type="hidden" class="sensor_sn" value=<?php echo($sn)?>></div>            
          <div class="sensor_h srh_time">
            <label for='accesstime'>AccessTime</label>
            <input type="date" class="srh_input srhDateFrom"><input type="time" class="srh_input srhTimeFrom"> ~  
            <input type="date" class="srh_input srhDateTo"><input type="time" class="srh_input srhTimeTo">
            <input type="button" class="sensor_btn sensor_search" value="조회">
          </div>
          <div class="sensor_h srh_clear"><input type="button" class="sensor_btn sensor_clear" value="초기화"></div>
          <div class="sensor_h srh_back"><input type="button" class="sensor_btn backPage" value="뒤로"></div>          
        </div>        
        <div class="sensor_sub_title2">
          <div class="sensor_sub_title sensor_head_sumTimediffValeu"><?php echo $total ?></div>
        </div>        
      </nav>
      <div>
        <table class="">
          <thead class="th_sensor">
            <th class="h_sensor h_no">NO</th>
            <th class="h_sensor h_fname">데이터파일</th>
            <th class="h_sensor h_testTime">측정일</th>          
            <th class="h_sensor h_access">통신 시작</th>
            <th class="h_sensor h_completeTime">데이터 전송 완료</th>
            <th class="h_sensor h_timediffValeu">시간차</th>
            <th class="h_sensor h_complete">complete</th>
            <th class="h_sensor h_rssi">RSSI</th>
            <th class="h_sensor h_batt">BATT</th>
          </thead>
          <tbody class="tb_sensor">
            <?php echo $sensorList ?>
          </tbody>
        <table>
      </div>
    </div>
  </body>
</html>