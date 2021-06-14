<?php
// [PHP] FTP를 접속하여 파일을 업로드, 다운로드, FTP 초기화하는 방법
// 출처: https://nowonbun.tistory.com/699 [명월 일지]
// 리스트 정보(MM-DD-YY HH:MM <DIR> 디렉토리이름)에서 <DIR>이 포함되어 있는지 확인 함수
function is_directory($str) {
  // 0 이상이면 true, -1이면 false
  return stripos($str, '<DIR>');
}

// 리스트 정보(MM-DD-YY HH:MM <DIR> 파일이름)에서 파일이름을 취득하는 함수
function get_name($str) {
  // 문자열 뒤에서 공백을 찾는다.
  $pos = strripos($str, ' ');
  // 공백에서부터 끝까지 문자열을 자른다.
  return substr($str, $pos + 1);
}

// FTP 서버의 모든 디렉토리, 파일을 삭제한다.
function delete_all_ftp($ftp, $cwd = "") {
  // FTP서버의 리스트를 취득한다.(DETAIL)
  $list = ftp_rawlist($ftp, $cwd);
  // 리스트를 Iteration 방식으로 데이터를 받는다.
  foreach ($list as $val) {
  // 디렉토리인지 확인
  if(is_directory($val)) {
  // 디렉토리라면 재귀적 방법으로 하위 디렉토리의 파일을 삭제한다.
  delete_all_ftp($ftp, $cwd.get_name($val)."/");
  } else {
  // 파일이면 삭제한다.
  ftp_delete($ftp, $cwd.get_name($val));
  }
  }
  // 디렉토리 삭제
  if ($cwd != "") {
  ftp_rmdir($ftp, $cwd);
  }
}

// 업로드하는 함수
function upload_ftp($ftp, $name, $path) {
  // 오늘 날짜 YYYYMMDD형식으로 디렉토리를 이동한다.
  if(!@ftp_chdir($ftp, date("Ymd"))) {
  // 이동이 안되면 폴더 생성한다.
  ftp_mkdir($ftp, date("Ymd"));
  // 다시 이동한다.
  ftp_chdir($ftp, date("Ymd"));
  }
  try {
  // 파일 커넥션을 만든다.
  $fp = fopen($path,"r");
  // 파일을 ftp 서버로 업로드 한다.
  ftp_fput($ftp, $name, $fp, FTP_BINARY);
  } finally {
  // 파일 커넥션 닫는다.
  fclose($fp);
  }
  // ftp 접속 디렉토리를 root로 이동한다.
  ftp_chdir($ftp, "/");
}

// ftp에서 파일 리스트 탐색
function search_ftp($ftp, $cwd = "", $ret = []) {
  // FTP서버의 리스트를 취득한다.(DETAIL)
  $list = ftp_rawlist($ftp, $cwd);
  // 리스트를 Iteration 방식으로 데이터를 받는다.
  foreach ($list as $val) {
  // 디렉토리인지 확인
  if(is_directory($val)) {
  // 디렉토리라면 재귀적 방법으로 하위 디렉토리의 파일을 탐색한다.
  $ret = search_ftp($ftp, $cwd.get_name($val)."/", $ret);
  } else {
  // 파일이라면 결과 리스트에 파일 명을 추가한다.
  array_push($ret, $cwd.get_name($val));
  }
  }
  // 결과 리스트 반환
  return $ret;
}

// ftp에서 파일를 다운로드
function download_ftp($ftp, $path) {
  // ftp에서 파일을 다운로드하여 임시로 파일을 생성하는 경로
  $temp = "D:\\test\\".uniqid();
  try {
  // 파일 커넥션을 만든다.
  $fp = fopen($temp,"w");
  // ftp 서버로 부터 파일을 생성한다.
  ftp_fget($ftp, $fp, $path, FTP_BINARY, 0);
  } finally {
  // 파일 커넥션 닫는다.
  fclose($fp);
  }
  // 디렉토리 경로를 제외한 파일명을 변환
  $pos = strripos($path, '/');
  $name = substr($path, $pos + 1);
  // 응답 해더 재 설정
  header('Content-Description: File Transfer');
  // 다운로드 타입
  header('Content-Type: application/octet-stream');
  // 파일명 지정
  header('Content-Disposition: attachment; filename="'.basename($name).'"');
  header('Expires: 0');
  header('Cache-Control: must-revalidate');
  header('Pragma: public');
  // 파일 사이즈 설정
  header('Content-Length: ' . filesize($temp));
  // 바이너리 body에 출력
  readfile($temp);
  // 임시 파일 삭제
  unlink($temp);
  // 응답
  die();
}

// 화면 메시지 변수
$msg = "";
// select 박스에 파일 목록 리스트
$list = [];
try {
  // ftp에 접속한다.
  $ftp = ftp_connect("thingsware.co.kr");
  // 로그인을 한다.
  if (ftp_login($ftp, "scsol", "scsol92595")) {
    // Web 요청 method가 POST라면
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // FTP의 모든 파일 삭제 타입
      if($_POST["type"] === "all_delete") {
        // FTP의 모든 파일 삭제
        delete_all_ftp($ftp);
        // 완료 메시지 작성
        $msg = "All file was deleted.";
        // FTP 서버에 파일을 업로드 함.
      } else if($_POST["type"] === "upload") {
        // input type=file에 multiple를 추가하면 배열 형식으로 데이터가 온다.
        $count = count($_FILES["upload"]["name"]);
        // 배열의 개수 만큼 Iterate한다.
        for($i=0; $i<$count; $i++) {
        // 업로드를 호출한다. (파일 이름에 공백이 있으면 나중에 리스트 검색시 잘못된 데이터를 가져온다.
        // PHP는 업로드시 파일이 임시 폴더에 있다.
        upload_ftp($ftp, str_replace(' ','',$_FILES["upload"]["name"][$i]), $_FILES["upload"]["tmp_name"][$i]);
        // 업로드 성공 메시지 작성.
        $msg .= "The file was uploaded. - " . $_FILES["upload"]["name"][$i] . "<br />";
        }
      // FTP 서버에서 파일을 다운로드 함.
      } else if($_POST["type"] === "download") {
        download_ftp($ftp, $_POST["download"]);
      }
    }
    // POST, GET 상관없이 select 박스에 FTP 서버의 파일 리스트를 표시한다.
    $list = search_ftp($ftp);
  } else {
    // 로그인 실패하면 메시지를 표시한다.
    $msg = "The login was failed.";
  }
} finally {
  ftp_close($ftp);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>title</title>
<!-- Jquery 링크 -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
</head>
<body>
<!-- 결과 메시지 표시 -->
<?=$msg?>
<br />
<!-- form이 submit이 발생하면 POST형시으로 요청한다. -->
<form method="POST">
<!-- 타입을 구분하기 위한 값 -->
<input type="hidden" id="type" name="type">
<!-- 파일 전체 삭제 버튼 -->
<button onclick="$('form #type').val('all_delete');
$('form').attr('enctype','');
$('form').submit();">delete all</button>
<br /><br />
<!-- multiple 타입이여서 복수 선택이 가능한다.-->
<input type="file" name="upload[]" multiple>
<!-- 업로드 버튼 -->
<button onclick="$('form #type').val('upload');
$('form').attr('enctype','multipart/form-data');
$('form').submit();">upload</button><br />
<br /><br />
<!-- select 박스에 ftp 서버의 파일 일람을 표시함->
<select name="download">
<?php foreach($list as $item) {?>
<option value="<?=$item?>"><?=$item?></option>
<?php }?>
<?php if(count($list) < 1) { ?>
<option value="">No Item</option>
<?php }?>
</select>
<!-- 다운로드 버튼 -->
<button onclick="$('form #type').val('download');
$('form').attr('enctype','');
$('form').submit();">download</button>
</form>
</body>
</html>


