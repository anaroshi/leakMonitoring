'use strict';

$(document).ready(function () {

  let sid           = "";
  let pname         = "";
  let sn            = "";
  let leakStatus    = "";
  let sensorStatus  = "";
  let pipeInfo      = "";
  let pipeInfoTxt   = "";
  let comm          = "";
  let _tr           = "";
  let _td           = "";
  let sensorNum     = "";

  /**
   * 초기화 Button
   * refresh
   */
   $('.report_btn.report_clear').on('click', function () {     
    location.reload();
  });
  
  
  /**
   * 누수여부
   */
  $('.leakStatus_list').on('change', function (e) {
    e.preventDefault();
    _td         = $(this).closest('tr').children();
    sn          = _td.eq(3).text();
    leakStatus  = $(this).val();
    _td.eq(14).text(leakStatus);
  });
 

  /**
   * 센서상태
   */
  $('.sensorStatus_list').on('change', function (e) {
    e.preventDefault();
    _td           = $(this).closest('tr').children();
    sn            = _td.eq(3).text();
    sensorStatus  = $(this).val();
    _td.eq(15).text(sensorStatus);
  });

  $('.comm').on('focusout', function (e) {
    e.preventDefault();
    _td           = $(this).closest('tr').children();
    sn            = _td.eq(3).text();
    comm          = $(this).val();
    //  alert(`sn : ${sn}, comm : ${comm}`);
  });



  /**
   * 엑셀 다운로드 Button
   */
  $('.report_excel').on('click', function (e) {

    e.preventDefault();
    sensorNum = $('.getSensorNum').val();
    if (sensorNum < 1) {
      alert("출력할 내용이 없습니다.");
      return;
    }
    sid = $('.getSid').val();
    pname = $('.getPname').val();
    let date1 = $('.hBattery1').text();
    let date2 = $('.hBattery2').text();
    let date3 = $('.hBattery3').text();
    let date4 = $('.hBattery4').text();
    let date5 = $('.hBattery5').text();

    // alert(`sid: ${sid}, pname: ${pname}`);

    //location.href = "./download.php?sid="+sid+"&pname="+pname+"&date1="+date1+"&date2="+date2+"&date3="+date3+"&date4="+date4+"&date5="+date5;
    location.href = "./report_download.php?sid=" + sid + "&pname=" + pname + "&date1=" + date1 + "&date2=" + date2 + "&date3=" + date3 + "&date4=" + date4 + "&date5=" + date5;

  });



  /**
   * 저장 Button
   */
  $('.report_save').on('click', function () {

    sensorNum = $('.getSensorNum').val();
    if (sensorNum < 1) {
      alert("저장할 내용이 없습니다.");
      return;
    }
    sid     = $('.getSid').val();
    pname   = $('.getPname').val();
    //console.log(sid+'_'+pname);

    $('.b_sn').each(function (index, item) {
      showLoadingBar();
      _tr           = $(this).closest('tr');
      sn            = $(this).text()
      leakStatus    = _tr.find('.leakStatusTxt').text();
      sensorStatus  = _tr.find('.snStatusTxt').text();
      pipeInfo      = _tr.find('.pipeInfoTxt').text();
      comm          = _tr.find('.comm').val(); 
      console.log(`${sn}_${leakStatus}_${sensorStatus}_${pipeInfo}_${comm}`);

      $.ajax({
        type: "POST",
        url: "./project_report_save.php",
        data: {sid, pname, sn, leakStatus, sensorStatus, pipeInfo, comm },
        dataType: "html",
      })
      .done(async function (data) { 
        console.log(data);
        location.reload();
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        console.log('서버오류: ' + textStatus);
        console.log('서버오류: ' + errorThrown);
      });
    });

  });


  /**
  * 뒤로 Button
  */
  $('.backPage').on('click', function () {
    window.history.back();
  });


  // 
  $(".b_report.b_sn, .b_report.b_finalData, .b_report.b_finalReport").on("mouseenter", function () {    
    
    $(".tr_report").css('background-color','#eeeeee'); 
    $(".tr_report").css('color','#4d4d4d');
    $(".tr_report").css('border','0px');
    
    _tr = $(this).closest('tr');    
    _tr.css('background-color','#c75000');
    _tr.css('color','#ffffff');
    _tr.css('border','1px solid #c75000');
  });
  
  // $(".b_report.b_sn, .b_report.b_finalData, .b_report.b_finalReport").on("mouseleave", function () {
  //   _tr = $(this).closest('tr');
  //   _tr.css('background-color','#eeeeee'); 
  //   _tr.css('color','#4d4d4d');
  //   _tr.css('border','0px');
  // });


});

/************* LoadingBar **************/
function showLoadingBar() {
  let maskHeight = $(document).height();
  let maskWidth = window.document.body.clientWidth;
  let mask = "<div id='mask' style='position:absolute; z-index:9000; background-color:#000000; display:none; left:0; top:0;'></div>";
  let loadingImg = "";
  loadingImg += "<div id='loadingImg' style='position:absolute; left:30%; top:40%; display:none; z-index:10000;'>";
  loadingImg += " <img src='../../image/spinner.gif'/>";
  loadingImg += "</div>";
  $('body').append(mask).append(loadingImg);
  $('#mask').css({ 'width': maskWidth, 'height': maskHeight, 'opacity': '0.1' });
  $('#mask').show();
  $('#loadingImg').show();
}

function hideLoadingBar() {
  $('#mask, #loadingImg').hide();
  $('#mask, #loadingImg').remove();
}