<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

date_default_timezone_set("GMT+0");
//date_default_timezone_set("Asia/Seoul");
echo date("Y-m-d H:i:s") . "<br /><br />\n\n";

include($_SERVER['DOCUMENT_ROOT'] . "/connect_db.php");
include($_SERVER['DOCUMENT_ROOT'] . "/dbConfig_leak.php");


// 리스트 정보(MM-DD-YY HH:MM <DIR> 파일이름)에서 파일이름을 취득하는 함수
function get_name($str) {
  // 문자열 뒤에서 공백을 찾는다.
  $pos = strripos($str, ' ');
  // 공백에서부터 끝까지 문자열을 자른다.
  return substr($str, $pos + 1);
}


// function search_ftp($ftp, $cwd = "public_html/leak_data/producttest/testnone/SWFLB-20210408-0106-0459/", $ret = []) {
//   // FTP서버의 리스트를 취득한다.(DETAIL)
//   $list = ftp_rawlist($ftp, $cwd);
//   // 리스트를 Iteration 방식으로 데이터를 받는다.
//   foreach ($list as $val) {
//     array_push($ret, $cwd.get_name($val));
//     $buff = ftp_mdtm($ftp, $cwd.get_name($val));
//     echo get_name($val)." : ".date("Y-m-d H:i:s", $buff)."<br>";

//     //array_push($ret, get_name($val));
//   }
  
//   return $ret;
// }

$ftp = ftp_connect("thingsware.co.kr");

if (ftp_login($ftp, "scsol", "scsol92595")) {
  echo $_SERVER['DOCUMENT_ROOT']."<br>";
  
  $cwd = "public_html/leak_data/producttest/testnone/SWFLB-20210408-0106-0459/";
  $list = ftp_rawlist($ftp, $cwd);

  // 리스트를 Iteration 방식으로 데이터를 받는다.
  foreach ($list as $val) {
    $fileName = get_name($val);
    $buff     = ftp_mdtm($ftp, $cwd.$fileName);    
    $fileTime = date("Y-m-d H:i:s", $buff);
    echo $fileName." : ".$fileTime."<br>";
  }
}	
ftp_close($ftp);

?>