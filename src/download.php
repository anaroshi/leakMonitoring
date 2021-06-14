<?php

include($_SERVER['DOCUMENT_ROOT'] . "/connect_db.php");
include($_SERVER['DOCUMENT_ROOT'] . "/dbConfig_scsolERP.php");

include($_SERVER['DOCUMENT_ROOT'] . "/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;



$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();


// Set document properties
$spreadsheet->getProperties()->setCreator('jhsung')
  ->setLastModifiedBy('jhsung')
  ->setTitle('Office XLSX PCBA List')
  ->setSubject('Office XLSX PCBA List')
  ->setDescription('PCBA List document for Office XLSX, generated using PHP classes.')
  ->setKeywords('office openxml php')
  ->setCategory('PCBA List file');

// Font Size
$spreadsheet->getDefaultStyle()->getFont()->setSize(10);

// Cell Merge
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


// Header
$spreadsheet->setActiveSheetIndex(0)
  ->setCellValue('A3','No')
  ->setCellValue('B3', '밸브')
  ->setCellValue('C3', '센서설치일자')  
  ->setCellValue('D3', '센서 번호')
  ->setCellValue('E3', '최종 데이터수집')
  ->setCellValue('F3', '최종 보고')
  ->setCellValue('G3', '배터리')
  ->setCellValue('H3', '상태')
  ->setCellValue('I3', '관정보')
  ->setCellValue('J3', '특이사항')
  ->setCellValue('G4', $date1)
  ->setCellValue('H4', $date2)
  ->setCellValue('I4', $date3)
  ->setCellValue('J4', $date4)
  ->setCellValue('K4', $date5)
  ->setCellValue('L4', '누수여부')
  ->setCellValue('M4', '센서상태');


/**
 * PCBA 조회 처리
 * table : trad_part_pcba
 * 조건 : pcba_sn, pcba_tradDateFrom, pcba_tradDateTo, pcba_status
 * $outputList
 */
/*
$sn             = trim($_GET["pcba_sn"]);               // PCBA SN
$tradDateFrom   = trim($_GET["pcba_tradDateFrom"]);     // 가입 일자 시작
$tradDateTo     = trim($_GET["pcba_tradDateTo"]);       // 가입 일자 까지
$status         = trim($_GET["pcba_status"]);           // 상태

// echo 'sn: '.$sn;
// echo 'tradDateFrom: '.$tradDateFrom;
// echo 'tradDateTo: '.$tradDateTo;
// echo 'status: '.$status;

$sql = "select * FROM trad_part_pcba ";
$sql .= "WHERE 1 ";
$sql .= " and flag != 4 ";
if ($sn != "") {
  $sql .= "and pcba_sn like '%$pcba_sn%' ";
}
if ($tradDateFrom != "" || $tradDateTo != "") {
  $sql .= "and tradDate between '$tradDateFrom' and '$tradDateTo' ";
}
if ($status == 1) {
  $sql .= "and status = 'used' ";
} elseif ($status == 2) {
  $sql .= "and status = 'not used' ";
}
$sql .= "ORDER BY id ";

// echo($sql);

if (!($result = mysqli_query($conn_11, $sql))) {
  echo ("Error description: " . mysqli_error($conn_11) . "query:" . $sql);
}

mysqli_set_charset($conn_11, "utf8");

$outputList = "";
$no = 0;
$i = 1;
while ($row = mysqli_fetch_assoc($result)) {

  $no++;
  $i++;
  $id                   = $row['id'];
  $pcba_sn              = $row['pcba_sn'];
  $tradDate             = $row['tradDate'];
  $tradId               = $row['tradId'];
  $version              = $row['version'];
  $type                 = $row['type'];
  $status               = $row['status'];
  $sn                   = $row['sn'];
  $hostcnt              = $row['hostcnt'];
  $mcucnt               = $row['mcucnt'];
  $modemcnt             = $row['modemcnt'];
  $battcnt              = $row['battcnt'];
  $ssorcnt              = $row['ssorcnt'];
  $ldo                  = $row['ldo'];
  $radio                = $row['radio'];
  $buz                  = $row['buz'];
  $adc                  = $row['adc'];
  $memory               = $row['memory'];
  $issue                = $row['issue'];
  $comment              = $row['comment'];
  $etc                  = $row['etc'];
  $img_radio            = $row['img_radio'];
  $img_adc              = $row['img_adc'];

*/
  /**
   * Excel file contents
   */
  /*
  $sheet->setCellValue("A$i", $no)
    ->setCellValue("B$i", $pcba_sn)
    ->setCellValue("C$i", $tradDate)
    ->setCellValue("D$i", $tradId)
    ->setCellValue("E$i", $version)
    ->setCellValue("F$i", $type)
    ->setCellValue("G$i", $status)
    ->setCellValue("H$i", $sn)
    ->setCellValue("I$i", $hostcnt)
    ->setCellValue("J$i", $mcucnt)
    ->setCellValue("K$i", $modemcnt)
    ->setCellValue("L$i", $battcnt)
    ->setCellValue("M$i", $ssorcnt)
    ->setCellValue("N$i", $ldo)
    ->setCellValue("O$i", $radio)
    ->setCellValue("P$i", $buz)
    ->setCellValue("Q$i", $adc)
    ->setCellValue("R$i", $memory)
    ->setCellValue("S$i", $issue)
    ->setCellValue("T$i", $comment)
    ->setCellValue("U$i", $etc)
    ->setCellValue("V$i", $img_radio)
    ->setCellValue("W$i", $img_adc);
}

$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);
$sheet->getColumnDimension('G')->setAutoSize(true);
$sheet->getColumnDimension('H')->setAutoSize(true);
$sheet->getColumnDimension('I')->setAutoSize(true);
$sheet->getColumnDimension('J')->setAutoSize(true);
$sheet->getColumnDimension('K')->setAutoSize(true);
$sheet->getColumnDimension('L')->setAutoSize(true);
$sheet->getColumnDimension('M')->setAutoSize(true);
$sheet->getColumnDimension('N')->setAutoSize(true);
$sheet->getColumnDimension('O')->setAutoSize(true);
$sheet->getColumnDimension('P')->setAutoSize(true);
$sheet->getColumnDimension('Q')->setAutoSize(true);
$sheet->getColumnDimension('R')->setAutoSize(true);
$sheet->getColumnDimension('S')->setAutoSize(true);
$sheet->getColumnDimension('T')->setAutoSize(true);
$sheet->getColumnDimension('U')->setAutoSize(true);
$sheet->getColumnDimension('V')->setAutoSize(true);
$sheet->getColumnDimension('W')->setAutoSize(true);

// text를 굵게
$spreadsheet->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('C1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('D1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('E1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('F1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('G1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('H1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('I1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('J1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('K1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('L1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('M1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('N1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('O1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('P1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('Q1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('R1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('S1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('T1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('U1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('V1')->getFont()->setBold(true);
$spreadsheet->getActiveSheet()->getStyle('W1')->getFont()->setBold(true);

$no++;
// 헤더 칼럼 가운데 정렬
$sheet->getStyle("A1:W$no")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

// 표 그리기
$sheet->getStyle("A1:W$no")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
*/
$fileName = 'pcba_list.xlsx';

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$objWriter = new Xlsx($spreadsheet);
$objWriter->save('php://output');
