<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOTOR SENSOR VIEW</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous">
    <script src="project_list.js" defter></script>
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href='../image/scsolicon.ico'>
</head>

<body>
    <h3>Motor Sensor Detail</h3>
    <?php

    include('../connect_db.php');
    include('../dbConfig_motor_v.php');

    $str2 = "SELECT sid as SID, pname as 프로젝트 FROM motor_project ORDER BY sid, pname";
    if (!($result2 = mysqli_query($conn2, $str2))) {
        echo ("Error description: " . mysqli_error($conn2) . "query:" . $str2);
    }

    $conn2->close();

    // 테이블 상단
    $HTML_STR = "
    <table id ='tEachSensorDetail'>
        <thead class='trEach'>
            <th class ='hNO'>NO</th>
            <th class ='hSid'>SID</th>
            <th class ='hPname'>프로젝트</th>
            <th class ='hComment'>Comment</th>            
            <th class ='hTotal'>Last</th>
            <th class ='hSum'>완료</th>
            <th class ='hNow'>조회시점</th>
            <th class ='hMinute'><input type='text' class ='hhMinute' name='hMinute' size='3' value ='10'>분전</th>            
            <th class ='hOnoff'>On/Off</th>
            <th class ='hDetail'>∧</th>
        </thead>
    ";

    $no = 1;
    while ($row = mysqli_fetch_array($result2)) {
        $sid = $row[0];
        $pname = $row[1];

        include('../dbConfig_personal.php');
        $str3 = "SELECT comment FROM project_comm where sid = '$sid' and pname ='$pname'";

        // Perform a query, check for error
        if (!($result3 = mysqli_query($conn3, $str3))) {
            echo ("Error description: " . mysqli_error($conn3) . "query:" . $str3);
        }
        $conn3->close();
        $row3 = mysqli_fetch_assoc($result3);
        $tComm = $row3['comment'];

        $HTML_STR .= "
        <tbody>
        <tr class='trEach'>
            <td class=''><div class='tNo' id='tNo'>" . $no . "</div></td>
            <td class='tInicial'><div class='tSid' id='tSid'>" . $sid . "</div></td>
            <td class='tInicial'><div class='tPname tPname_$no' id='tPname' onClick='process(\"{$sid}\",\"{$pname}\",\"{$no}\")'>" . $pname . "</div></td>
            <td class='tInicial'><input type='text' class='tComment tComment_$no' name='tComment' value ='" . $tComm . "'></td>
            <td class='tInicial'><div class='tTotal' id='tTotal'></div></td>
            <td class='tInicial'><div class='tSum' id='tSum'></div></td>
            <td class='tInicial'><div class='tNow' id='tNow'></div></td>
            <td class='tInicial'><input type='text' class='tMinute tMinute_$no' name='tMinute' size='3' value ='10'>분전</td>
            <td class='tInicial'><button class='tOnoff tOnoff_$no' id='tOnoff' type='button' value='$sid' onClick='onoff($no)'>On/Off</button></td>
            <td class='tInicial'><div class='tDetail'><button class='tDetail_off tDetail_off_$no' id='tDetail_off' type='button' onClick='detail($no)'>∧</button></div></td>
        </tr>
        <tr class='sTrEach'>
            <td class='sInicial' colspan='10'><div class='tdLast tdLast_$no' id='tdLast'></div></td>
        </tr>
        </tbody>";
        $no++;
    }

    $HTML_STR .= "</table>";
    echo $HTML_STR;

    function Console_log($logcontent)
    {
        echo "<script>
        console.log('$logcontent');
    </script>";
    }

    function Alert_log($logcontent)
    {
        echo "<script>
        alert('$logcontent');
    </script>";
    }
    ?>
</body>