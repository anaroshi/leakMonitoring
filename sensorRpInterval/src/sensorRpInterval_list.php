<?php
  error_reporting(E_ALL);
  ini_set("display_errors", 1);

  date_default_timezone_set("GMT+0");

  // get file name
  function get_name($str) {  
    $pos = strripos($str, ' ');       // 문자열 뒤에서 공백을 찾는다.  
    return substr($str, $pos + 1);    // 공백에서부터 끝까지 문자열을 자른다.
  }

  include($_SERVER['DOCUMENT_ROOT'] . "/connect_db.php");
  include($_SERVER['DOCUMENT_ROOT'] . "/dbConfig_leak.php");

  $data = array();

  $sid          = trim($_POST['sid']);
  $project      = trim($_POST['project']);
  $sn           = trim($_POST['sn']);
  $srhDateFrom  = trim($_POST['srhDateFrom']);
  $srhTimeFrom  = trim($_POST['srhTimeFrom']);
  $srhDateTo    = trim($_POST['srhDateTo']);
  $srhTimeTo    = trim($_POST['srhTimeTo']);

  $dateFrom     = $srhDateFrom." ".$srhTimeFrom;
  $dateTo       = $srhDateTo." ".$srhTimeTo;

  $dbnm_report  = 'sensor_report_'.$sid.'_'.$sn;
  $dbnm_send    = 'leak_send_data_'.$sid.'_'.$sn;

  // echo($sid."/".$project."/".$sn)."<br>";
  // echo($dateFrom."~".$dateTo)."<br>";
  
  $ftp = ftp_connect("thingsware.co.kr");
  if (ftp_login($ftp, "scsol", "scsol92595")) {
    $path = "public_html/leak_data/$sid/$project/$sn/";
    $list = ftp_rawlist($ftp, $path);
  }

/**
 * 센서별 접속 시간 얻기
 * 
 */
  $outputList           = "";
  $server_complete_time = "";
  $sumTimediffValeu     = 0; // 총합구하기

  if ($srhDateFrom != '' and $srhDateTo != '') {    
    $sql_date = "AND date between '$dateFrom' AND '$dateTo' ";
  } else if ($srhDateFrom != '' and $srhDateTo == '') {
    $sql_date = "AND date >= '$dateFrom' ";
  } else if ($srhDateFrom == '' and $srhDateTo != '') {
    $sql_date = "AND date <= '$dateTo' ";
  } else {
    $sql_date = "";
  }

  $sql = "SELECT A.a_date, A.a_redate, B.fname, B.testTime, B.b_date, B.b_redate, B.complete_time, timediff(B.complete_time,A.a_date) timediffValeu, B.complete, ";
  $sql .= "A.rssi, A.batt ";
  $sql .= "FROM (SELECT date a_date, end_record_time a_redate, rssi, batt FROM `".$dbnm_report."` WHERE 1 ";
  $sql .= $sql_date;
  $sql .= ") A LEFT JOIN (SELECT right(fname,48) fname, left(right(fname,23),13) testTime, date b_date, complete_time, replace(left(right(fname,23),13),'_',' ') b_redate, complete ";
  $sql .= "FROM `".$dbnm_send."` WHERE  pname='$project' ";
  $sql .= $sql_date;
  $sql .= ") B ON A.a_redate = B.b_redate ORDER BY A.a_date DESC ";
  //echo($sql);

  if (!($result = mysqli_query($conn1, $sql))) {
    echo ("Error description: " . mysqli_error($conn1) . "query:" . $sql);
  }
  
  $i = 0;
  while ($row = mysqli_fetch_array($result)) {
    $i++;
    $fname                  = $row['fname'];
    $testTime               = $row['testTime'];
    $a_date                 = $row['a_date'];
    $a_redate               = $row['a_redate'];
    $complete_time          = $row['complete_time'];
    $timediffValeu          = $row['timediffValeu'];
    $b_redate               = $row['b_redate'];  
    $b_date                 = $row['b_date'];
    $rssi                   = $row['rssi'];
    $batt                   = $row['batt'];
    $complete               = $row['complete'];
    $mix_complete_time      = "";

    foreach ($list as $val) {
      
      $fileName = get_name($val);
      $buff     = ftp_mdtm($ftp, $path.$fileName);
      $fileTime = date("Y-m-d H:i:s", $buff);
  
      if ( $fname == $fileName ) {
        $server_complete_time = $fileTime;
        if ($complete_time == "") {
          $mix_complete_time  = $server_complete_time;
        } else {
          $mix_complete_time  = $complete_time;
        } 
      }
    }

    $filetimediffValeu = "";
    if ($a_date != "" && $mix_complete_time != "") {
      $filetimediffValeu  = strtotime($mix_complete_time) - strtotime($a_date);
      $sumTimediffValeu  += $filetimediffValeu;
      $filetimediffValeu  = date('H:i:s', $filetimediffValeu);
    }

    $outputList .= "
      <tr class='tr_sensor'>
        <td class ='b_sensor b_no'>$i</td>
        <td class ='b_sensor b_fname'>$fname</td>
        <td class ='b_sensor b_testTime'>$testTime</td>
        <td class ='b_sensor b_access'>$a_date</td>      
        <td class ='b_sensor b_completeTime'>$$mix_complete_time</td>      
        <td class ='b_sensor b_timediffValeu'>$filetimediffValeu</td>
        <td class ='b_sensor b_complete'>$complete</td>
        <td class ='b_sensor b_rssi'>$rssi</td>
        <td class ='b_sensor b_batt'>$batt</td>
      </tr>
      ";
  }
  ftp_close($ftp);
  

  $sql = "SELECT count(complete_time) times_completeTime FROM `".$dbnm_send."` WHERE complete=1 ";
  $sql .= $sql_date;

  if (!($result = mysqli_query($conn1, $sql))) {
    echo ("Error description: " . mysqli_error($conn1) . "query:" . $sql);
  }
  $row = mysqli_fetch_assoc($result);
  $times_completeTime = $row['times_completeTime'];
  $total = "통신 횟수 : ".number_format($i)."회  / Complete 횟수 : ".$times_completeTime."회  / Incomplete 횟수 : ".(number_format($i)-$times_completeTime)."회  / 시간차 합계 : ".date('H:i:s', $sumTimediffValeu);

  $data['total']      = $total;
  $data['outputList'] = $outputList;

  echo json_encode($data);

?>