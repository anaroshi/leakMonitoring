<?php

date_default_timezone_set("GMT+0"); // 세계 표준시 (UTC)

include($_SERVER['DOCUMENT_ROOT'] . "/connect_db.php");
include($_SERVER['DOCUMENT_ROOT'] . "/dbConfig_leak.php");
include($_SERVER['DOCUMENT_ROOT'] . "/dbConfig_personal_leak.php");


// error_reporting(E_ALL);
// ini_set("display_errors", 1);

$sid        = trim($_GET["sid"]);
$pname      = trim($_GET["pname"]);

date_default_timezone_set('Asia/Seoul');
$tNow       = strtotime("Now");
$getTime    = date("Y-m-d H:i:s", $tNow);

for ($i = 4; $i > -2; $i--) {
  $getDate[$i] = date("y/m/d", strtotime("-" . $i . " days"));
  $setDate[$i] = date("Y-m-d", strtotime("-" . $i . " days"));
}


// 엑셀용
// $sqlDel = "DELETE FROM leakSensor_report";
// if (!($result4 = mysqli_query($conn5, $sqlDel))) {
//   echo ("Error description: " . mysqli_error($conn5) . "query:" . $sqlDel);
// }

/**
 * 센서 조회 처리
 * table  : sensor_list
 * 조건   : sid, pname, 삭제 제외
 * $outputList
 */

$sql    = "SELECT sn, v_no, install FROM sensor_list ";
$sql   .= "WHERE sid = '$sid' AND pname = '$pname' AND col_valid != '-1' GROUP BY sn ORDER BY sn";

if (!($result = mysqli_query($conn1, $sql))) {
  echo ("Error description: " . mysqli_error($conn1) . "query:" . $sql);
}

$sensorNum  = mysqli_num_rows($result);

$no = 0;
$outputList = "";

while ($row = mysqli_fetch_array($result)) {
  $sn       = $row['sn'];                               // 센서번호
  $v_no     = $row['v_no'];                             // 밸브
  $install  = $row['install'];                          // 설치일자

  $dbname1  = "sensor_report_" . $sid . "_" . $sn;
  $sql1     = "SELECT date, fver, rssi FROM `$dbname1` where (sn, date) IN (SELECT sn, max(date) as date from `$dbname1`)";
  if (!($result1 = mysqli_query($conn1, $sql1))) {
    echo ("Error description: " . mysqli_error($conn1) . "query:" . $sql1);
  }
  $row1     = mysqli_fetch_assoc($result1);
  $date     = stripslashes($row1['date']);              // 최종 보고
  $fver     = stripslashes($row1['fver']);
  $rssi     = stripslashes($row1['rssi']);
  
  $dbname2  = "leak_send_data_" . $sid . "_" . $sn;
  $sql2     = "SELECT max(complete_time) as complete_time from `$dbname2` where complete ='1'";
  if (!($result2 = mysqli_query($conn1, $sql2))) {
    echo ("Error description: " . mysqli_error($conn1) . "query:" . $sql2);
  }
  $row2     = mysqli_fetch_assoc($result2);
  $completeTime = stripslashes($row2['complete_time']); // 최종 데이터 수집
  if ($completeTime == 0) $completeTime = "none";

  // get battery value within 5days
  $batt1    = getBatt($sid, $sn, $setDate[4],  $setDate[3], $conn1);
  $batt2    = getBatt($sid, $sn, $setDate[3],  $setDate[2], $conn1);
  $batt3    = getBatt($sid, $sn, $setDate[2],  $setDate[1], $conn1);
  $batt4    = getBatt($sid, $sn, $setDate[1],  $setDate[0], $conn1);
  $batt5    = getBatt($sid, $sn, $setDate[0], $setDate[-1], $conn1);


  // 센서별 누수여부, 센서상태, 관정보, 특이사항 정보 가져옴.
  $sql3     = "SELECT * FROM leak_report WHERE sid = '$sid' and pname = '$pname' and sn = '$sn'";
  if (!($result3 = mysqli_query($conn5, $sql3))) {
    echo ("Error description: " . mysqli_error($conn5) . "query:" . $sql3);
  }
  $row3         = mysqli_fetch_array($result3);
  $leakStatus   = $row3['leakStatus'] ?? '';
  $sensorStatus = $row3['sensorStatus'] ?? '';
  $comm         = $row3['comm'] ?? '';

  $outputLeakStatus_list    = getLeakStatusList($leakStatus, $conn5);
  $outputSensorStatus_list  = getSensorStatusList($sensorStatus, $conn5); 

  $sql4     = "SELECT * FROM (SELECT sn, material FROM sensor_list where sid = '$sid' and pname = '$pname' ";
  $sql4     .= "and sn = '$sn' and col_valid != '-1' ORDER BY cid DESC) AS sensor_list GROUP BY sn ";
  
  if (!($result4 = mysqli_query($conn1, $sql4))) {
    echo ("Error description: " . mysqli_error($conn1) . "query:" . $sql4);
  }
  $row4         = mysqli_fetch_array($result4);
  $material     = $row4['material'] ?? '';

  // sensor_report_$sid_$sn
  // SELECT cid, fver FROM `sensor_report_producttest_SWFLB-20210408-0106-0459` order by cid desc limit 1

  ++$no;
  $outputList .= "  
    <tr class ='tr_report'>
      <td class ='b_report b_no'>$no</td>
      <td class ='b_report b_valve'>$v_no</td>
      <td class ='b_report b_installedDate'>$install</td>
      <td class ='b_report b_sn'>$sn</td>
      <td class ='b_report b_finalData'>$date</td>
      <td class ='b_report b_finalReport'>$completeTime</td>
      <td class ='b_report b_battery1'>$batt1</td>
      <td class ='b_report b_battery2'>$batt2</td>
      <td class ='b_report b_battery3'>$batt3</td>
      <td class ='b_report b_battery4'>$batt4</td>            
      <td class ='b_report b_battery5'>$batt5</td>
      <td class ='b_report b_leakStatus'><select class='leakStatus_list'>$outputLeakStatus_list</select></td>
      <td class ='b_report b_snStatus'><select class='sensorStatus_list'>$outputSensorStatus_list</select></td>
      <td class ='b_report b_comm'><input type='text' class='comm' name='comm' value ='" . $comm . "'></td>
      <td class ='b_report leakStatusTxt'>$leakStatus</td>
      <td class ='b_report snStatusTxt'>$sensorStatus</td>
      <td class ='b_report pipeInfoTxt'>$material</td>
      <td class ='b_report b_fver'>$fver</td>
      <td class ='b_report b_rssi'>$rssi</td>
    </tr>
  ";

  // $sql4  = "INSERT INTO leakSensor_report(sid, pname, vNo, installed, sn, finalDate, finalReport, ";
  // $sql4 .= "batt1Date, batt1, batt2Date, batt2, batt3Date, batt3, batt4Date, batt4, batt5Date, batt5, ";
  // $sql4 .= "leakStatus, sensorStatus, pipe, comm, inDate) ";
  // $sql4 .= "VALUES ( '$sid', '$pname', '$v_no', '$install', '$sn', '$date', '$completeTime', '$setDate[4]', '$batt1', ";
  // $sql4 .= "'$setDate[3]', '$batt2', '$setDate[2]', '$batt3', '$setDate[1]', '$batt4', '$setDate[0]', ";
  // $sql4 .= "'$batt5', '$leakStatus', '$sensorStatus', '$material', '$comm', now())";

  // if (!($result4 = mysqli_query($conn5, $sql4))) {
  //   echo ("Error description: " . mysqli_error($conn5) . "query:" . $sql4);
  // } 
}

$outputList .= "<tr><td colspan='15'>&nbsp;</td></tr><tr><tr><td colspan='15'>&nbsp;</td></tr>
                <tr><td colspan='15'>&nbsp;</td></tr><td colspan='15'>&nbsp;</td></tr>";

$conn1->close();
$conn5->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Project Report</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="../js/project_report.js" defer></script>
  <link rel="stylesheet" href="../css/project_report.css">
</head>

<body>
  <nav id="report_nav" class="report_nav">
    <div class="report_all_btn">
      <input type="button" class="report_btn report_clear" value="초기화">
      <input type="button" class="report_btn report_excel" value="엑셀 다운로드">
      <input type="button" class="report_btn report_save" value="저장">
      <input type="button" class="report_btn backPage" value="뒤로">
    </div>
  </nav>
  <div class="title title1">
    <h1><?php echo strtoupper($sid) . " " . strtoupper($pname) ?> 누수 센서 현황</h1>
  </div>
  <div class="title title2">
    <span>센서 : <?php echo $no ?> 개</span>
    <span><?php echo $getTime ?></span>
  </div>
  <div>
    <input class="getSid" type="hidden" value="<?php echo $sid ?>">
    <input class="getPname" type="hidden" value="<?php echo $pname ?>">
    <input class="getSensorNum" type="hidden" value="<?php echo $sensorNum ?>">
  </div>
  <div class="tableSensorDetail" overflow:auto;>
    <table id='tEachSensorDetail'>
      <thead class='trEach'>
        <tr>
          <th class='hNO' rowspan='2'>No</th>
          <th class='hValve' rowspan='2'>밸브</th>
          <th class='hInstalledDate' rowspan='2'>센서설치일자</th>
          <th class='hSN' rowspan='2'>센서 번호</th>
          <th class='hfinalData' rowspan='2'>최종 보고</th>
          <th class='hfinalReport' rowspan='2'>최종 데이터수집</th>
          <th class='hBattery' colspan='5'>배터리</th>
          <th class='hStatus' colspan='2'>상태</th>          
          <th class='hComm' rowspan='2'>특이사항</th>
          <th class='hLeakStatusTxt' rowspan='2'>누수여부</th>
          <th class='hSnStatusTxt' rowspan='2'>센서상태</th>
          <th class='hPipeInfoTxt' rowspan='2'>관정보</th>
          <th class='hFver' rowspan='2'>FW버젼</th>
          <th class='hRssi' rowspan='2'>RSSI</th>
        </tr>
        <tr>
          <th class='hBattery1'><?php echo $getDate[4] ?></th>
          <th class='hBattery2'><?php echo $getDate[3] ?></th>
          <th class='hBattery3'><?php echo $getDate[2] ?></th>
          <th class='hBattery4'><?php echo $getDate[1] ?></th>
          <th class='hBattery5'><?php echo $getDate[0] ?></th>
          <th class='hLeakStatus'>누수여부</th>
          <th class='hSnStatus'>센서상태</th>
        </tr>
      </thead>
      <tbody>
        <?php echo $outputList; ?>
      </tbody>
    </table>
  </div>  
</body>

</html>

<?php

  /**
   * 누수여부 LIST
   * table    : leakStatus_list
   * $outputLeakStatus_list
   * default  : 공백, value : 
   */
  function getLeakStatusList($leakStatus,$conn5) {
    $outputLeakStatus_list = '';
    $sql    = "select leakStatus from leakStatus_list";

    $result = mysqli_query($conn5, $sql);
    $outputLeakStatus_list .= '<option value = "">&nbsp;</option>';
    while ($row = mysqli_fetch_array($result)) {
      if ($leakStatus == $row["leakStatus"])
        $outputLeakStatus_list .= '<option value = "' . $row["leakStatus"] . '" selected>' . $row["leakStatus"] . '</option>';
      else 
        $outputLeakStatus_list .= '<option value = "' . $row["leakStatus"] . '">' . $row["leakStatus"] . '</option>';
    }
    return $outputLeakStatus_list;
  }


  /**
   * 센서상태 LIST
   * table    : sensorStatus_list
   * $outputSensorStatus_list
   * default  : 공백, value : 
   */
  function getSensorStatusList($sensorStatus,$conn5) {
    $outputSensorStatus_list = '';
    $sql = "select sensorStatus from sensorStatus_list order by sensorStatus";

    $result     = mysqli_query($conn5, $sql);
    $outputSensorStatus_list .= '<option value = "">&nbsp;</option>';
    while ($row = mysqli_fetch_array($result)) {
      if ($sensorStatus==$row["sensorStatus"])
        $outputSensorStatus_list .= '<option value = "' . $row["sensorStatus"] . '" selected>' . $row["sensorStatus"] . '</option>';
      else  
        $outputSensorStatus_list .= '<option value = "' . $row["sensorStatus"] . '">' . $row["sensorStatus"] . '</option>';
    }
    return $outputSensorStatus_list;
  }

  /**
   * Battery Value
   * table    : sensor_report_ . $sid . _ . $sn
   * $batt
   */
  function getBatt($sid,$sn,$setDate1,$setDate2,$conn1) {
    $dbname  = "sensor_report_" . $sid . "_" . $sn;
    $sql     = "SELECT date, batt FROM `$dbname` WHERE date BETWEEN date('" . $setDate1 . "') AND date('" . $setDate2 . "') GROUP BY date(date) ";
    if (!($result = mysqli_query($conn1, $sql))) {
      echo ("Error description: " . mysqli_error($conn1) . "query:" . $sql);
    }
    $battNum  = mysqli_num_rows($result);
    if ($battNum < 1) {
      $batt  = "";
    } else {
      $row   = mysqli_fetch_array($result);
      $batt  = $row['batt'] ?? '';
    }
    return $batt;
  }  


?>