<?php

include($_SERVER['DOCUMENT_ROOT'] . "/connect_db.php");
include($_SERVER['DOCUMENT_ROOT'] . "/dbConfig_leak.php");
include($_SERVER['DOCUMENT_ROOT'] . "/dbConfig_personal_leak.php");

include($_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// error_reporting(E_ALL);
// ini_set("display_errors",1);


$sid    = trim($_GET["sid"]) ?? null;
$pname  = trim($_GET["pname"]) ?? null;

date_default_timezone_set('Asia/Seoul');
$tNow       = strtotime("Now");
$getTime    = date("Y-m-d H:i:s", $tNow);

for ($i = 4; $i > -2; $i--) {
  $getDate[$i] = date("y/m/d", strtotime("-" . $i . " days"));
  $setDate[$i] = date("Y-m-d", strtotime("-" . $i . " days"));
}


$date1  = $_GET["date1"] ?? null;
$date2  = $_GET["date2"] ?? null;
$date3  = $_GET["date3"] ?? null;
$date4  = $_GET["date4"] ?? null;
$date5  = $_GET["date5"] ?? null;

//echo ($sid.", ".$pname.", ".$date1.", ".$date2.", ".$date3.", ".$date4.", ".$date5);


$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();


// Set document properties
$spreadsheet->getProperties()->setCreator('jhsung')
  ->setLastModifiedBy('jhsung')
  ->setTitle('Office XLSX Sensor Monitoring Report')
  ->setSubject('Office XLSX Sensor Monitoring')
  ->setDescription('Sensor Monitoring document for Office XLSX, generated using PHP classes.')
  ->setKeywords('office openxml php')
  ->setCategory('Sensor Monitoring file');

// Font Size
$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

// Cell Merge
$sheet->mergeCells('A1:O1');
$sheet->mergeCells('A2:C2');
$sheet->mergeCells('A3:A4');
$sheet->mergeCells('B3:B4');
$sheet->mergeCells('C3:C4');
$sheet->mergeCells('D3:D4');
$sheet->mergeCells('E3:E4');
$sheet->mergeCells('F3:F4');
$sheet->mergeCells('G3:K3');
$sheet->mergeCells('L3:M3');
$sheet->mergeCells('N3:N4');
$sheet->mergeCells('O3:O4');


/**************************
 * LEAKMASTER 
 * 센서 모니터링 DATA LIST
 * table : sensor_list, sensor_report_$sid_$sn
 * 
 */

$i = 5;
// $sid    = "daeguf";
// $pname  = "padong";

// $sql    = "SELECT * FROM leakSensor_report ";
// $sql   .= "WHERE sid = '$sid' AND pname = '$pname' ORDER BY sn";

// if (!($result = mysqli_query($conn5, $sql))) {
//   echo ("Error description: " . mysqli_error($conn5) . "query:" . $sql);
// }

// $sensorNum  = mysqli_num_rows($result);

// $no = 0;
// while ($row = mysqli_fetch_array($result)) {

//   ++$no;

//   $v_no               = $row['vNo'];
//   $install            = $row['installed'];
//   $sn                 = $row['sn'];
//   $date               = $row['finalDate'];
//   $completeTime       = $row['finalReport'];
//   $gaugeBatt1         = $row['batt1'];
//   $gaugeBatt2         = $row['batt2'];
//   $gaugeBatt3         = $row['batt3'];
//   $gaugeBatt4         = $row['batt4'];
//   $gaugeBatt5         = $row['batt5'];
//   $leakStatus         = $row['leakStatus'];
//   $sensorStatus       = $row['sensorStatus'];
//   $pipe               = $row['pipe'];
//   $comm               = $row['comm'];
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

while ($row = mysqli_fetch_array($result)) {
  ++$no;
  
  $sn       = $row['sn'];                               // 센서번호
  $v_no     = $row['v_no'];                             // 밸브
  $install  = $row['install'];                          // 설치일자

  $dbname1  = "sensor_report_" . $sid . "_" . $sn;
  $str1     = "SELECT date FROM `$dbname1` where (sn, date) IN (SELECT sn, max(date) as date from `$dbname1`)";
  if (!($result1 = mysqli_query($conn1, $str1))) {
    echo ("Error description: " . mysqli_error($conn1) . "query:" . $str1);
  }
  $row1     = mysqli_fetch_assoc($result1);
  $date     = stripslashes($row1['date']);              // 최종 보고


  $dbname2  = "leak_send_data_" . $sid . "_" . $sn;
  $str2     = "SELECT max(complete_time) as complete_time from `$dbname2` where complete ='1'";
  if (!($result2 = mysqli_query($conn1, $str2))) {
    echo ("Error description: " . mysqli_error($conn1) . "query:" . $str2);
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
  $str3     = "SELECT * FROM leak_report WHERE sid = '$sid' and pname = '$pname' and sn = '$sn'";
  if (!($result3 = mysqli_query($conn5, $str3))) {
    echo ("Error description: " . mysqli_error($conn5) . "query:" . $str3);
  }
  $row3         = mysqli_fetch_array($result3);
  $leakStatus   = $row3['leakStatus'] ?? '';
  $sensorStatus = $row3['sensorStatus'] ?? '';
  $pipeInfo     = $row3['pipeInfo'] ?? '';
  $comm         = $row3['comm'] ?? '';


  // Excel File Contents
  $sheet->setCellValue("A$i", $no)
    ->setCellValue("B$i", $v_no)
    ->setCellValue("C$i", $install)
    ->setCellValue("D$i", $sn)
    ->setCellValue("E$i", $date)
    ->setCellValue("F$i", $completeTime)
    ->setCellValue("G$i", $batt1)
    ->setCellValue("H$i", $batt2)
    ->setCellValue("I$i", $batt3)
    ->setCellValue("J$i", $batt4)
    ->setCellValue("K$i", $batt5)
    ->setCellValue("L$i", $leakStatus)
    ->setCellValue("M$i", $sensorStatus)
    ->setCellValue("N$i", $pipeInfo)
    ->setCellValue("O$i", $comm);

  ++$i;
}

// Header
$spreadsheet->setActiveSheetIndex(0)
  ->setCellValue('A1', strtoupper($sid) . ' ' . strtoupper($pname) . ' 누수 센서 현황')
  ->setCellValue('A2', '센서 : ' . $no . '개')
  ->setCellValue('O2', $getTime)
  ->setCellValue('A3', 'No')
  ->setCellValue('B3', '밸브')
  ->setCellValue('C3', '센서설치일자')
  ->setCellValue('D3', '센서 번호')
  ->setCellValue('E3', '최종 보고')
  ->setCellValue('F3', '최종 데이터수집')
  ->setCellValue('G3', '배터리')
  ->setCellValue('L3', '상태')
  ->setCellValue('N3', '관정보')
  ->setCellValue('O3', '특이사항')
  ->setCellValue('G4', $date1)
  ->setCellValue('H4', $date2)
  ->setCellValue('I4', $date3)
  ->setCellValue('J4', $date4)
  ->setCellValue('K4', $date5)
  ->setCellValue('L4', '누수여부')
  ->setCellValue('M4', '센서상태')
  ->setCellValue('N4', '관정보')
  ->setCellValue('O4', '특이사항');


$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutosize(true);
$sheet->getColumnDimension('C')->setAutosize(true);
$sheet->getColumnDimension('D')->setAutosize(true);
$sheet->getColumnDimension('E')->setAutosize(true);
$sheet->getColumnDimension('F')->setAutosize(true);
$sheet->getColumnDimension('G')->setAutosize(true);
$sheet->getColumnDimension('H')->setAutosize(true);
$sheet->getColumnDimension('I')->setAutosize(true);
$sheet->getColumnDimension('J')->setAutosize(true);
$sheet->getColumnDimension('K')->setAutosize(true);
$sheet->getColumnDimension('L')->setAutosize(true);
$sheet->getColumnDimension('M')->setAutosize(true);
$sheet->getColumnDimension('N')->setAutosize(true);
$sheet->getColumnDimension('O')->setAutosize(true);

// Bold Text
$spreadsheet->getActiveSheet()->getStyle("A1:O4")->getFont()->setBold(true);

// Font Size
$spreadsheet->getActiveSheet()->getStyle("A1:O1")->getFont()->setSize(17);
$spreadsheet->getActiveSheet()->getStyle("A2:O2")->getFont()->setSize(12);

--$i;

// Background-color
$spreadsheet->getActiveSheet()->getStyle('A3:O4')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('00FFFF');
$spreadsheet->getActiveSheet()->getStyle("A5:A$i")->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FF0000FF');


// Text align : center
$sheet->getStyle("A1:O1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet->getStyle("A3:O$i")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
// Text align : right
$sheet->getStyle("O2")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

// Design Table
$sheet->getStyle("A3:O$i")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

$fileName = $sid . "_" . $pname . "_sensorReport.xlsx";
//$fileName = "sensorReport.xlsx";

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$objWriter = new Xlsx($spreadsheet);
$objWriter->save('php://output');

/**
 * Battery Value
 * table    : sensor_report_ . $sid . _ . $sn
 * $outputPipe_list
 */
function getBatt($sid,$sn,$setDate1,$setDate2,$conn1) {
  $dbname  = "sensor_report_" . $sid . "_" . $sn;
  $str     = "SELECT date, batt FROM `$dbname` WHERE date BETWEEN date('" . $setDate1 . "') AND date('" . $setDate2 . "') GROUP BY date(date) ";
  if (!($result = mysqli_query($conn1, $str))) {
    echo ("Error description: " . mysqli_error($conn1) . "query:" . $str);
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