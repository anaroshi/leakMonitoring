<?php

$sid = $_POST["sid"];
$pname = $_POST["pname"];
$intervalMinute = $_POST["intervalMinute"];
$iMinute = $_POST["iMinute"];
$selector = $_POST["selector"];
$data = array();

date_default_timezone_set('Asia/Seoul');
$timestamp = strtotime($iMinute);
$tNow = strtotime("Now");

//echo ("sid : $sid,  pname : $pname,  iMinute : $iMinute");

if (isset($pname)) {
    include('connect_db.php');
    include('dbConfig_motor_v.php');

    $output = '';
    /************************************/
    //$str = "select sn from mecha_sensor_list where sid = '$sid' and pname = '$pname' and col_valid != '-1' GROUP BY sn ORDER BY sn";
    $str = "select sn from mecha_sensor_list where sid = '$sid' and pname = '$pname' and col_valid != '-1' and chk_install = 1 GROUP BY sn ORDER BY sn";
    if (!($result = mysqli_query($conn2, $str))) {
        echo ("Error description: " . mysqli_error($conn2) . "query:" . $str);
    }
    $no1 = mysqli_num_rows($result);
    // Console_log("-- 1 no1 :" . $no1);

    $no = 1;
    $actnm1 = 0;
    $actnm2 = 0;

    while ($row = mysqli_fetch_array($result)) {

        $n = sprintf('%02d', $no);

        if ($no1 > 0) {

            $sn = stripslashes($row[0]);
            
            if (substr($pname,0,5) =='a_dae') {

                // 전류센서 DB
                include('dbConfig_motor_a.php');
                
            } else if (substr($pname,0,5) !='v_dae') {
                // 전동도 진동도 아닌 그 외 프로젝트
                goto exception;

            } 

                
                $dbname = "sensor_report_" . $sid . "_" . $sn;
            
                // SELECT * FROM `sensor_report_mdaejeon_STFMB-20200312-0102-0001` where (sn, date) IN (SELECT sn, max(date) as date from `sensor_report_mdaejeon_STFMB-20200312-0102-0001` where sid = 'mdaejeon' and project = 'v_daejeon_1' and sn = 'STFMB-20200312-0102-0001')
                $str1 = "SELECT * FROM `$dbname` where (sn, date) IN (SELECT sn, max(date) as date from `$dbname` where sid = '$sid' and project = '$pname' and sn = '$sn')";
                if (!($result1 = mysqli_query($conn2, $str1))) {
                    echo ("Error description: " . mysqli_error($conn2) . "query:" . $str1);
                }

                $no4 = mysqli_num_rows($result1);

                $row1 = mysqli_fetch_assoc($result1);

                $tdate = strtotime($row1['date']);
                $date = stripslashes($row1['date']);
                $px = stripslashes($row1['px']);
                $py = stripslashes($row1['py']);
                $t1 = stripslashes($row1['time1']);
                $t2 = stripslashes($row1['time2']);
                $t3 = stripslashes($row1['time3']);
                $ert = stripslashes($row1['end_record_time']);
                $fm = stripslashes($row1['fm']);
                $fver = stripslashes($row1['fver']);
                $rssi = stripslashes($row1['rssi']);
                $status = stripslashes($row1['status']);
                $sample = stripslashes($row1['sample']);
                $period = stripslashes($row1['period']);
                $batt = stripslashes($row1['batt']);

                if (strlen($batt) < 1) $batt = '-';
                $dbname2 = "leak_send_data_" . $sid . "_" . $sn;
                $str2 = "select fname, complete, complete_time, fnum from `$dbname2` where sid = '$sid' and sn='$sn' and pname = '$pname' order by cid desc limit 1";
                
                $result2 = mysqli_query($conn2, $str2) or die(mysqli_error($conn2));
                $no2 = mysqli_num_rows($result2);
                if ($no2 > 0) {
                    $row2 = mysqli_fetch_assoc($result2);
                    $fname = stripslashes($row2['fname']);
                    $complete = stripslashes($row2['complete']);
                    $complete_time = stripslashes($row2['complete_time']);
                    $fnum = stripslashes($row2['fnum']);

                    if (!strcmp($complete, "1") && $fnum >= 160) $dmsg = "데이터 수집완료 : $complete_time";
                    else $dmsg = "데이터 수집실패";
                }

                /************************************/
                $dbname3 = "leak_send_data_" . $sid . "_" . $sn;
                $str3 = "SELECT max(complete_time) as complete_time from `$dbname3` where pname = '$pname' and sid = '$sid' and sn ='$sn' and complete ='1'";
                $result3 = mysqli_query($conn2, $str3) or die(mysqli_error($conn2));
                $no3 = mysqli_num_rows($result3);
                $row = mysqli_fetch_assoc($result3);
                $completeTime = stripslashes($row['complete_time']);
   
            //$conn2->close();

            if ($completeTime == 0) $completeTime = "none";
            $dmsg1 = "데이터 수집완료";
            /************************************/

            // 수집시간 기준
            if ($timestamp > $tdate) {
                $sLcolor = 'red';   // 비동작
            } else {
                $sLcolor = 'green'; // 동작
                $actnm1++;
            }

            // compcomplete_time 기준
            if ($timestamp > strtotime($completeTime)) {
                $sCcolor = 'red';   // 비동작
            } else {
                $sCcolor = 'green'; // 동작
                $actnm2++;
            }

            include('dbConfig_personal.php');
            $str3 = "SELECT comment FROM sensor_comm where sid = '$sid' and pname ='$pname' and sn ='$sn'";

            // Perform a query, check for error
            if (!($result3 = mysqli_query($conn3, $str3))) {
                echo ("Error description: " . mysqli_error($conn3) . "query:" . $str3);
            }

            //            $conn3->close();
            $row3 = mysqli_fetch_assoc($result3);
            $sComm = $row3['comment'];
            $output .= "
            <table class='tMain'>
            <tr class='sTrEach'><td>
            <table>
                <tr>
                    <td class='sInicial'><div class='sNo' id='sNo'> # $n</div></td>
                    <td class='sInicial'><div class='sSid hide' id='sSid'>" . $sid . "</div></td>
                    <td class='sInicial'><div class='sPanme hide' id='sPanme'>" . $pname . "</div></td>
                    <td class='sInicial'><div class='sSn' id='sSn'>" . $sn . "</div></td>
                    <td class='sInicial'><input type='text' class='sComment sComment_$no' name='sComment' value ='" . $sComm . "' onfocusout = 'sComm(\"{$sid}\",\"{$pname}\",\"{$sn}\",this.value)'></td>
                    <td class='sInicial'><div class='' id=''>
                        <table class ='tSub'>
                            <tr id='sub'>
                                <td class ='sInfoDate'>-------------&emsp;" . $date . "&emsp;-------------</td>
                            </tr>
                            <tr class='sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>위도 : " . $px . " 경도 : " . $py . "</td>
                            </tr>
                            <tr class='sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>동작시간 : " . $t1 . $t2 . $t3 . "</td>
                            </tr>
                            <tr class='sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>최종동작시간 : " . $ert . "</td>
                            </tr>
                            <tr class='sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>FM : " . $fm . " RSSI : " . $rssi . " F/W Ver : " . $fver . " BATT : " . $batt . "</td>
                            </tr>
                            <tr class='sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>Status : " . $status . " SAMPLE : " . $sample . " PERIOD : " . $period . "</td>
                            </tr>
                            <tr class='sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>" . $dmsg . "</td>
                            </tr>
                        </table>
                    </div></td>
                    <td class='sInicial'><div class='sGraph' id='sGraph'></div></td>
                    <td class='sInicial' style='background-color:$sLcolor;'><div class='sLcolor' id='sLcolor'>&emsp;</div></td>
                    <td class='sInicial'><div class='tCtime' id='tCtime'>
                        <table class ='tSub'>
                            <tr id='sub'>
                                <td class ='sInfoDate'>-------------&emsp;" . $completeTime . "&emsp;-------------</td>
                            </tr>
                            <tr class='sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>위도 : " . $px . " 경도 : " . $py . "</td>
                            </tr>
                            <tr class='sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>동작시간 : " . $t1 . $t2 . $t3 . "</td>
                            </tr>
                            <tr class=sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>최종동작시간 : " . $ert . "</td>
                            </tr>
                            <tr class='sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>FM : " . $fm . " RSSI : " . $rssi . " F/W Ver : " . $fver . " BATT : " . $batt . "</td>
                            </tr>
                            <tr class='sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>Status : " . $status . " SAMPLE : " . $sample . " PERIOD : " . $period . "</td>
                            </tr>
                            <tr class='sub1 id='sub1'>
                                <td class='sInfo sInfo_$selector' id='sInfo'>" . $dmsg1 . "</td>
                            </tr>
                        </table>
                    </div></td>
                    <td class='sInicial' style='background-color:$sCcolor;'><div class='sCcolor' id='sCcolor'>&emsp;</div></td>
                </tr>
            </table>      
            ";

        } else {
            exception:            
            $output .= "
            <table class='tMain'>
                <tr class='sTrEach'>
                    <td class='sInicial'><div class='sNo' id='sNo'> # $n</div></td>
                    <td class='sInicial'><div class='sSid hide' id='sSid'>" . $sid . "</div></td>
                    <td class='sInicial'><div class='sPanme hide' id='sPanme'>" . $pname . "</div></td>
                    <td class='sInicial'><div class='sSn' id='sSn'>" . $sn . "</div></td>
                    <td class='sInicial nodata' colspan='5'>NO DATA</td>
                    <td class='sInicial'><div class='sGraph' id='sGraph'></div></td>
                </tr>
            </table>
            ";
        }
        ++$no;
        /************************************/
    }
}

$lastNum = "<span style='color: #e0115f; font-weight:900'>" . $actnm1 . "</spen><span style='color: #4d4d4d'>/" . $no1 . "</spen>";
$completeNum = "<span style='color: #e0115f; font-weight:900'>" . $actnm2 . "</spen><span style='color: #4d4d4d'>/" . $no1 . "</spen>";
$data['lastNum'] = $lastNum;
$data['completeNum'] = $completeNum;
$data['tNow'] = date("Y-m-d H:i:s", $tNow);
$data['output'] = $output;
$data['intervalMinute'] = $intervalMinute;
$data['total'] = $no1;
echo json_encode($data);

function Console_log($logcontent)
{
    echo "<script>
        console.log('$logcontent');
        </script>";
}

// function Alert_log($logcontent)
// {
//     echo "<script>
//         alert('$logcontent');
//         </script>";
// }
